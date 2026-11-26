<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ChatGroupMessage;
use App\Models\Employee;
use App\Models\Termination;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WhatsAppChatController extends Controller
{
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

    private function getTerminatedUserIds(): array
    {
        $terminatedEmpIds = Termination::pluck('employee_id')->toArray();
        if (empty($terminatedEmpIds)) {
            return [];
        }

        return Employee::whereIn('id', $terminatedEmpIds)
            ->pluck('user_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    public function index($id = null)
    {
        $initialUserId = is_numeric($id) ? (int) $id : null;
        return view('messenger.index', compact('initialUserId'));
    }

    public function data()
    {
        $me = Auth::user();
        $myId = (int) $me->id;
        $terminatedUserIds = $this->getTerminatedUserIds();

        $activeUsers = User::where('id', '!=', $myId)
            ->where('name', '!=', 'Super Admin')
            ->where('type', '!=', 'super admin')
            ->when(!empty($terminatedUserIds), function ($q) use ($terminatedUserIds) {
                $q->whereNotIn('id', $terminatedUserIds);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'avatar', 'type'])
            ->map(function ($u) use ($myId) {
                $lastMsg = DB::table('ch_messages')
                    ->where(function ($q) use ($myId, $u) {
                        $q->where('from_id', $myId)->where('to_id', $u->id);
                    })
                    ->orWhere(function ($q) use ($myId, $u) {
                        $q->where('from_id', $u->id)->where('to_id', $myId);
                    })
                    ->orderByDesc('created_at')
                    ->first();

                $unreadCount = DB::table('ch_messages')
                    ->where('from_id', $u->id)
                    ->where('to_id', $myId)
                    ->where('seen', 0)
                    ->count();

                $lastText = 'Click to start chatting';
                $lastTime = '';
                $sortTs = 0;

                if ($lastMsg) {
                    $sortTs = strtotime($lastMsg->created_at) ?: 0;
                    $lastTime = \Carbon\Carbon::parse($lastMsg->created_at)->diffForHumans(null, true);
                    if (!empty($lastMsg->body)) {
                        $lastText = trim(strip_tags($lastMsg->body));
                    } elseif (!empty($lastMsg->attachment)) {
                        $att = json_decode($lastMsg->attachment);
                        $lastText = '📎 ' . ($att->old_name ?? 'Attachment');
                    }
                    if ((int) $lastMsg->from_id === $myId) {
                        $lastText = 'You: ' . $lastText;
                    }
                }

                return [
                    'id'           => (int) $u->id,
                    'name'         => $u->name,
                    'type'         => $u->type,
                    'avatar'       => $this->avatarUrl($u->avatar),
                    'last_message' => Str::limit($lastText, 40),
                    'last_time'    => $lastTime,
                    'unread_count' => $unreadCount,
                    'sort_ts'      => $sortTs,
                ];
            })
            ->sortByDesc('sort_ts')
            ->values();

        $groups = collect();
        if (Schema::hasTable('chat_groups') && Schema::hasTable('chat_group_members') && Schema::hasTable('chat_group_messages')) {
            $groups = ChatGroup::whereHas('members', function ($q) use ($myId) {
                    $q->where('users.id', $myId);
                })
                ->withCount('members')
                ->with(['messages' => function ($q) {
                    $q->latest('id')->limit(1)->with('sender:id,name');
                }])
                ->get()
                ->map(function ($group) use ($myId) {
                    $pivot = DB::table('chat_group_members')
                        ->where('group_id', $group->id)
                        ->where('user_id', $myId)
                        ->first();

                    $lastMsg = $group->messages->first();
                    $unreadCount = ChatGroupMessage::where('group_id', $group->id)
                        ->where('sender_id', '!=', $myId)
                        ->when($pivot && $pivot->last_read_at, function ($q) use ($pivot) {
                            $q->where('created_at', '>', $pivot->last_read_at);
                        })
                        ->count();

                    return [
                        'id'            => (int) $group->id,
                        'name'          => $group->name,
                        'members_count' => $group->members_count,
                        'unread_count'  => $unreadCount,
                        'last_message'  => $lastMsg ? ($lastMsg->body ?: ('📎 ' . ($lastMsg->attachment_name ?? 'Attachment'))) : 'No messages yet',
                        'last_sender'   => $lastMsg && $lastMsg->sender ? explode(' ', trim($lastMsg->sender->name))[0] : null,
                        'last_time'     => $lastMsg ? $lastMsg->created_at->diffForHumans(null, true) : $group->created_at->diffForHumans(null, true),
                        'sort_ts'       => $lastMsg ? $lastMsg->created_at->timestamp : $group->created_at->timestamp,
                    ];
                })
                ->sortByDesc('sort_ts')
                ->values();
        }

        return response()->json([
            'success' => true,
            'users'   => $activeUsers,
            'groups'  => $groups,
        ]);
    }

    public function directMessages(User $user)
    {
        $myId = (int) Auth::id();
        $otherId = (int) $user->id;

        DB::table('ch_messages')
            ->where('from_id', $otherId)
            ->where('to_id', $myId)
            ->where('seen', 0)
            ->update(['seen' => 1]);

        $messages = DB::table('ch_messages')
            ->where(function ($q) use ($myId, $otherId) {
                $q->where('from_id', $myId)->where('to_id', $otherId);
            })
            ->orWhere(function ($q) use ($myId, $otherId) {
                $q->where('from_id', $otherId)->where('to_id', $myId);
            })
            ->orderBy('created_at')
            ->limit(200)
            ->get()
            ->map(function ($msg) use ($myId) {
                $attUrl = null;
                $attName = null;
                $isImage = false;

                if (!empty($msg->attachment)) {
                    $att = json_decode($msg->attachment);
                    $fileName = $att->new_name ?? null;
                    $attName = $att->old_name ?? 'Attachment';
                    if ($fileName) {
                        if (file_exists(storage_path('app/public/attachments/' . $fileName))) {
                            $attUrl = asset('storage/attachments/' . $fileName);
                        } elseif (file_exists(storage_path('uploads/attachments/' . $fileName))) {
                            $attUrl = asset('storage/uploads/attachments/' . $fileName);
                        } else {
                            $attUrl = asset('storage/attachments/' . $fileName);
                        }
                        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp']);
                    }
                }

                $created = \Carbon\Carbon::parse($msg->created_at);

                return [
                    'id'              => $msg->id,
                    'is_mine'         => (int) $msg->from_id === $myId,
                    'body'            => $msg->body ? trim(strip_tags($msg->body)) : null,
                    'attachment_url'  => $attUrl,
                    'attachment_name' => $attName,
                    'is_image'        => $isImage,
                    'seen'            => (bool) $msg->seen,
                    'time'            => $created->format('h:i A'),
                    'date_label'      => $created->isToday() ? 'Today' : ($created->isYesterday() ? 'Yesterday' : $created->format('d M Y')),
                ];
            });

        return response()->json([
            'success'  => true,
            'user'     => [
                'id'     => $user->id,
                'name'   => $user->name,
                'avatar' => $this->avatarUrl($user->avatar),
                'type'   => ucfirst($user->type ?? 'Employee'),
            ],
            'messages' => $messages,
        ]);
    }

    public function sendDirectMessage(Request $request, User $user)
    {
        $me = Auth::user();
        $request->validate([
            'message' => 'nullable|string|max:5000',
            'file'    => 'nullable|file|max:20480',
        ]);

        $body = trim((string) $request->message);
        if ($body === '' && !$request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'Message cannot be empty.'], 422);
        }

        $attachmentJson = null;
        $origName = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $origName = $file->getClientOriginalName();
            $storedFile = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $destDir = storage_path('app/public/attachments');
            if (!is_dir($destDir)) {
                mkdir($destDir, 0775, true);
            }
            $file->move($destDir, $storedFile);
            $attachmentJson = json_encode([
                'new_name' => $storedFile,
                'old_name' => $origName,
            ]);
        }

        $msgId = mt_rand(100000, 999999999) + time();
        DB::table('ch_messages')->insert([
            'id'         => $msgId,
            'from_id'    => $me->id,
            'to_id'      => $user->id,
            'body'       => $body,
            'attachment' => $attachmentJson,
            'seen'       => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $recipientId = (int) $user->id;
        $senderName = $me->name;
        $preview = $body !== '' ? Str::limit($body, 80) : ('Sent an attachment: ' . ($origName ?? 'File'));
        $chatUrl = url('/chats/' . $me->id);

        dispatch(function () use ($recipientId, $senderName, $preview, $chatUrl) {
            app(\App\Services\FcmPushService::class)->sendToUsers(
                [$recipientId],
                $senderName,
                $preview,
                [
                    'type' => 'chat',
                    'url'  => $chatUrl,
                ]
            );
        })->afterResponse();

        return response()->json(['success' => true, 'message_id' => $msgId]);
    }
}

