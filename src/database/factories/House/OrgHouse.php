<?php

namespace Database\Factories\House;

use App\Models\House\OrgHouse as Model;
use App\Models\Org\Organization;
use App\Models\House\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrgHouse extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
        return [
            'organization' => Organization::factory(),
            'warehouse' => Warehouse::factory()
        ];
    }
}
