<?php

namespace Mocks;

use Router\Enum\StatusCode;

final class UserController
{
    public function index(?int $id): array
    {
        if ($id) {
            return ['name' => 'Rhuan Gabriel'];
        }

        return [
            ['name' => 'Rhuan Gabriel'],
            ['name' => 'Eloah Hadassa']
        ];
    }

    public function create(array $user): array
    {
        return [
            'status' => StatusCode::CREATED,
            'message' => "User has created.",
            'user' => $user
        ];
    }

    public function update(int $id, array $user): array
    {
        return [
            'status' => StatusCode::SUCCESS,
            'message' => "User with id $id, has updated.",
            'user' => $user
        ];
    }

    public function delete(int $id): array
    {
        return [
            'status' => StatusCode::SUCCESS,
            'message' => "User with id $id, has deleted."
        ];
    }
}
