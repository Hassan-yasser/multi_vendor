<?php

namespace App\Contracts\Repositories;

use App\Models\User;


interface UserRepositoryContract
{
 
    public function createUser(string $name, string $email, string $plainPassword): User;


    public function persistPasswordAndRotateRememberToken(User $user, string $plainPassword): void;
}
    