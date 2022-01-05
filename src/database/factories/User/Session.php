<?php

namespace Database\Factories\User;

use App\Models\User\Session as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User\User as UserModel;

class Session extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
        return [
            'session_id' => hash('sha256', $this->faker->unique->password),
            'last_request_at' => $this->faker->dateTime(),
            'user' => UserModel::factory()
        ];
    }
}
