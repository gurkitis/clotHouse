<?php

namespace Database\Factories\Org;

use App\Models\Org\OrgUser as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User\User;
use App\Models\Org\Organization;

class OrgUser extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
        return [
            'is_admin' => $this->faker->boolean,
            'is_owner' => FALSE,
            'user' => User::factory(),
            'organization' => Organization::factory()
        ];
    }
}
