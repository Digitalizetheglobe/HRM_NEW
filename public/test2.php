<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$userIds = [68, 69, 70, 71, 72, 91, 92, 94];
$users = \App\Models\User::whereIn('id', $userIds)->get(['id', 'type'])->toArray();
echo json_encode($users);
