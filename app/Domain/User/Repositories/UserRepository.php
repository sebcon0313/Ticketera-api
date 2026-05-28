<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Models\User;
use App\Domain\User\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /* public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    } */

    public function findById(int $id): ?User
    {
        return User::with('role')->find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}