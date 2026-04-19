<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryContract;
use App\Models\User;
use Illuminate\Support\Str;

final class UserRepository implements UserRepositoryContract
{
    public function createUser(string $name, string $email, string $plainPassword): User
    {
        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $plainPassword,
        ]);
    }

    public function persistPasswordAndRotateRememberToken(User $user, string $plainPassword): void
    {
        $user->forceFill([
            'password' => $plainPassword,
            'remember_token' => Str::random(60),
        ]);

        $user->save();
    }
}
