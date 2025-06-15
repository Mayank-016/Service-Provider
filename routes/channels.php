<?php

use App\Constants\Role;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function ($user, $id) {
    return ((int) $user->id === (int) $id && $user->role == Role::User);
});

Broadcast::channel('supplier.{id}', function ($user, $id) {
    return ((int) $user->id === (int) $id && $user->role == Role::Provider);
});