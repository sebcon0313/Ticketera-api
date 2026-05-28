<?php 

namespace App\Domain\User\Repositories\Contracts;

use App\Domain\User\Models\User;

interface UserRepositoryInterface
{
    /* public function paginate(int $perPage = 15); */

    public function findById(int $id): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function delete(User $user): bool;
}