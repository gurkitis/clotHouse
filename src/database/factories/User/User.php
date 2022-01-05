<?php

namespace Database\Factories\User;

use App\Models\User\User as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\House\Warehouse as Warehouse;

class User extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'surname' => $this->faker->name,
            'email' => $this->faker->unique->email,
            'password' => hash('sha256', $this->faker->password),
            'warehouse' => Warehouse::factory()
        ];
    }
}
