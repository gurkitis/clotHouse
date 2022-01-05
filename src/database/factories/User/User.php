<?php

namespace Database\Factories\User;

use App\Models\User\User as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\House\Warehouse as Warehouse;
use App\Models\User\Session;

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

    /**
     * @return User
     */
    public function configure(): User
    {
        return $this->afterMaking(function (Model $user) {
            Session::factory()->make([
                'user' => $user->getAttribute('id')
            ]);
        })->afterCreating(function (Model $user) {
            Session::factory()->create([
                'user' => $user->getAttribute('id')
            ]);
        });
    }
}
