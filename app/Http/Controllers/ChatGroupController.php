<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ChatGroupMessage;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class ChatGroupController extends Controller
{
    private function ensureTablesExist(): void
    {
        if (!Schema::hasTable('chat_groups')) {
            Schema::create('chat_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('avatar')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('chat_group_members')) {
            Schema::create('chat_group_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('chat_groups')->cascadeOnDelete();
                $table->unsignedBigInteger('user_id');
                $table->string('role', 20)->default('member');
                $table->timestamp('last_read_at')->nullable();
                $table->timestamps();

                $table->unique(['group_id', 'user_id']);
                $table->index('user_id');
            });
        }

        if (!Schema::hasTable('chat_group_messages')) {
            Schema::create('chat_group_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('chat_groups')->cascadeOnDelete();
                $table->unsignedBigInteger('sender_id');
                $table->text('body')->nullable();
                $table->string('attachment')->nullable();
                $table->string('attachment_name')->nullable();
                $table->timestamps();

                $table->index(['group_id', 'created_at']);
            });
        }
    }
    private function avatarUrl(?string $avatar): string
    {
        $defaultUrl = asset('storage/uploads/attachments/avtar.png');
        if (empty($avatar)) {
            return $defaultUrl;
        }
        if (Str::startsWith($avatar, ['http://', 'https://'])) {
            return $avatar;
        }
        $fileName = ltrim($avatar, '/');
        if (!file_exists(storage_path('uploads/avatar/' . $fileName))) {
            return $defaultUrl;
        }
        $profile = Utility::get_file('uploads/avatar/');
        return rtrim($profile, '/') . '/' . $fileName;
    }

    public function index()
    {
        $this->ensureTablesExist();
        $user = Auth::user();
        $userId = $user->id;

        $groups = ChatGroup::whereHas('members', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            })
            ->withCount('members')
            ->with(['messages' => function ($q) {
                $q->latest('id')->limit(1)->with('sender:id,name');
            }])
            ->get()
            ->map(function ($group) use ($userId) {
                $pivot = DB::table('chat_group_members')
                    ->where('group_id', $group->id)
                    ->where('user_id', $userId)
                    ->first();

                $lastMsg = $group->messages->first();
                $unreadCount = ChatGroupMessage::where('group_id', $group->id)
                    ->where('sender_id', '!=', $userId)
                    ->when($pivot && $pivot->last_read_at, function ($q) use ($pivot) {
                        $q->where('created_at', '>', $pivot->last_read_at);
                    })
                    ->count();

                return [
                    'id'            => $group->id,
                    'name'          => $group->name,
                    'members_count' => $group->members_count,
                    'unread_count'  => $unreadCount,
                    'last_message'  => $lastMsg ? ($lastMsg->body ?: ('📎 ' . ($lastMsg->attachment_name ?? 'Attachment'))) : 'No messages yet',
                    'last_sender'   => $lastMsg && $lastMsg->sender ? explode(' ', trim($lastMsg->sender->name))[0] : null,
                    'last_time'     => $lastMsg ? $lastMsg->created_at->diffForHumans() : $group->created_at->diffForHumans(),
                    'sort_ts'       => $lastMsg ? $lastMsg->created_at->timestamp : $group->created_at->timestamp,
                ];
            })
            ->sortByDesc('sort_ts')
            ->values();

        $terminatedEmployeeIds = \App\Models\Termination::pluck('employee_id')->toArray();
        $terminatedUserIds = \App\Models\Employee::whereIn('id', $terminatedEmployeeIds)
            ->pluck('user_id')
            ->filter()
            ->toArray();

        $users = User::where('id', '!=', $userId)
            ->where('name', '!=', 'Super Admin')
            ->where('type', '!=', 'super admin')
            ->when(!empty($terminatedUserIds), function ($q) use ($terminatedUserIds) {
                $q->whereNotIn('id', $terminatedUserIds);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'avatar', 'type'])
            ->map(function ($u) {
                return [
                    'id'     => $u->id,
                    'name'   => $u->name,
                    'type'   => $u->type,
                    'avatar' => $this->avatarUrl($u->avatar),
                ];
            });

        return response()->json([
            'success' => true,
            'groups'  => $groups,
            'users'   => $users,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureTablesExist();
        $creator = Auth::user();
        if ($creator->type !== 'company') {
            return response()->json([
                'success' => false,
                'message' => 'Only company users can create groups.',
            ], 403);
        }

        $request->validate([
            'name'       => 'required|string|max:120',
            'member_ids' => 'required|array|min:1',
            'member_ids.*' => 'integer|exists:users,id',
        ]);

        $memberIds = collect($request->member_ids)
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id === (int) $creator->id)
            ->unique()
            ->values();

        $group = DB::transaction(function () use ($request, $creator, $memberIds) {
            $group = ChatGroup::create([
                'name'       => trim($request->name),
                'created_by' => $creator->id,
            ]);

            $attachData = [
                $creator->id => ['role' => 'admin', 'last_read_at' => now()],
            ];
            foreach ($memberIds as $mId) {
                $attachData[$mId] = ['role' => 'member', 'last_read_at' => null];
            }
            $group->members()->attach($attachData);

            return $group;
        });

        $recipientIds = $memberIds->all();
        if (!empty($recipientIds)) {
            $groupName = $group->name;
            $creatorName = $creator->name;
            $chatUrl = url('/chats');
            dispatch(function () use ($recipientIds, $groupName, $creatorName, $group, $chatUrl) {
                app(\App\Services\FcmPushService::class)->sendToUsers(
                    $recipientIds,
                    $groupName,
                    $creatorName . ' added you to the group "' . $groupName . '"',
                    [
                        'type'     => 'group_chat',
                        'group_id' => (string) $group->id,
                        'url'      => $chatUrl,
                    ]
                );
            })->afterResponse();
        }

        return response()->json([
            'success' => true,
            'group'   => [
                'id'            => $group->id,
                'name'          => $group->name,
                'members_count' => $memberIds->count() + 1,
            ],
        ]);
    }

    public function messages(ChatGroup $group)
    {
        $userId = Auth::id();
        $isMember = DB::table('chat_group_members')
            ->where('group_id', $group->id)
            ->where('user_id', $userId)
            ->exists();

        if (!$isMember) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        DB::table('chat_group_members')
            ->where('group_id', $group->id)
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);

        $messages = ChatGroupMessage::where('group_id', $group->id)
            ->with('sender:id,name,avatar')
            ->orderBy('id')
            ->limit(200)
            ->get()
            ->map(function ($msg) use ($userId) {
                $isMine = (int) $msg->sender_id === (int) $userId;
                $attUrl = $msg->attachment ? asset('storage/chat_groups/' . $msg->attachment) : null;
                $ext = $msg->attachment ? strtolower(pathinfo($msg->attachment, PATHINFO_EXTENSION)) : '';
                $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp']);

                return [
                    'id'              => $msg->id,
                    'is_mine'         => $isMine,
                    'sender_id'       => $msg->sender_id,
                    'sender_name'     => $msg->sender ? $msg->sender->name : 'User',
                    'sender_avatar'   => $this->avatarUrl($msg->sender?->avatar),
                    'body'            => $msg->body,
                    'attachment_url'  => $attUrl,
                    'attachment_name' => $msg->attachment_name,
                    'is_image'        => $isImage,
                    'time'            => $msg->created_at->format('h:i A'),
                    'date_label'      => $msg->created_at->isToday() ? 'Today' : ($msg->created_at->isYesterday() ? 'Yesterday' : $msg->created_at->format('d M Y')),
                ];
            });

        $members = $group->members()
            ->get(['users.id', 'users.name', 'users.avatar'])
            ->map(fn ($m) => [
                'id'     => $m->id,
                'name'   => $m->name,
                'avatar' => $this->avatarUrl($m->avatar),
                'role'   => $m->pivot->role,
            ]);

        $myRole = DB::table('chat_group_members')
            ->where('group_id', $group->id)
            ->where('user_id', $userId)
            ->value('role');

        return response()->json([
            'success'  => true,
            'group'    => [
                'id'      => $group->id,
                'name'    => $group->name,
                'my_role' => $myRole,
            ],
            'members'  => $members,
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request, ChatGroup $group)
    {
        $user = Auth::user();
        $isMember = DB::table('chat_group_members')
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isMember) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'nullable|string|max:5000',
            'file'    => 'nullable|file|max:20480',
        ]);

        if (empty(trim((string) $request->message)) && !$request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'Message cannot be empty.'], 422);
        }

        $storedFile = null;
        $origName = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $origName = $file->getClientOriginalName();
            $storedFile = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $destDir = storage_path('app/public/chat_groups');
            if (!is_dir($destDir)) {
                mkdir($destDir, 0775, true);
            }
            $file->move($destDir, $storedFile);
        }

        $msg = ChatGroupMessage::create([
            'group_id'        => $group->id,
            'sender_id'       => $user->id,
            'body'            => trim((string) $request->message) ?: null,
            'attachment'      => $storedFile,
            'attachment_name' => $origName,
        ]);

        DB::table('chat_group_members')
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        $otherMemberIds = DB::table('chat_group_members')
            ->where('group_id', $group->id)
            ->where('user_id', '!=', $user->id)
            ->pluck('user_id')
            ->toArray();

        if (!empty($otherMemberIds)) {
            $groupName = $group->name;
            $senderName = explode(' ', trim($user->name))[0];
            $preview = $msg->body ? Str::limit($msg->body, 80) : ('Sent an attachment: ' . ($origName ?? 'File'));
            $chatUrl = url('/chats');
            $groupId = $group->id;

            dispatch(function () use ($otherMemberIds, $groupName, $senderName, $preview, $groupId, $chatUrl) {
                app(\App\Services\FcmPushService::class)->sendToUsers(
                    $otherMemberIds,
                    $groupName,
                    $senderName . ': ' . $preview,
                    [
                        'type'     => 'group_chat',
                        'group_id' => (string) $groupId,
                        'url'      => $chatUrl,
                    ]
                );
            })->afterResponse();
        }

        return response()->json(['success' => true, 'message_id' => $msg->id]);
    }

    public function update(Request $request, ChatGroup $group)
    {
        $user = Auth::user();
        $myRole = DB::table('chat_group_members')
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->value('role');

        if ($myRole !== 'admin' && !in_array($user->type, ['company', 'hr'])) {
            return response()->json(['success' => false, 'message' => 'Only group admins can update this group.'], 403);
        }

        if ($request->filled('name')) {
            $group->update(['name' => trim($request->name)]);
        }

        if ($request->has('add_member_ids') && is_array($request->add_member_ids)) {
            foreach ($request->add_member_ids as $mId) {
                DB::table('chat_group_members')->updateOrInsert(
                    ['group_id' => $group->id, 'user_id' => (int) $mId],
                    ['role' => 'member', 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        if ($request->filled('remove_member_id')) {
            $removeId = (int) $request->remove_member_id;
            if ($removeId !== (int) $group->created_by) {
                DB::table('chat_group_members')
                    ->where('group_id', $group->id)
                    ->where('user_id', $removeId)
                    ->delete();
            }
        }

        return response()->json(['success' => true]);
    }

    public function leave(ChatGroup $group)
    {
        DB::table('chat_group_members')
            ->where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['success' => true]);
    }
}

