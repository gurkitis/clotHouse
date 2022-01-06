<?php

namespace Database\Factories\Org;

use App\Models\Org\Organization as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Org\OrgUser;

class Organization extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique->company
        ];
    }

    /**
     * @return Organization
     */
    public function configure(): Organization
    {
        return $this->afterMaking(function (Model $organization) {
            OrgUser::factory()->make([
                'is_admin' => TRUE,
                'is_owner' => TRUE,
                'organization' => $organization->getAttribute('id')
            ]);
        })->afterCreating(function (Model $organization) {
            OrgUser::factory()->create([
                'is_admin' => TRUE,
                'is_owner' => TRUE,
                'organization' => $organization->getAttribute('id')
            ]);
        });
    }
}
