<?php

namespace Database\Factories\Cloth;

use App\Models\Cloth\ClothingUnit as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cloth\Status;
use App\Models\Cloth\Clothing;
use App\Models\House\Warehouse;
use App\Models\Org\Organization;
use App\Models\House\OrgHouse;

class ClothingUnit extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
    	return [
    	    'identificator' => $this->faker->unique()->md5,
            'organization' => Organization::factory(),
            'status' => function (array $attributes) {
                return Status::factory()->create([
                    'organization' => $attributes['organization']
                ])['id'];
            },
            'clothing' => function (array $attributes) {
                return Clothing::factory()->create([
                    'organization' => $attributes['organization']
                ])['id'];
            },
            'warehouse' => function (array $attributes) {
                return OrgHouse::factory()->create([
                    'organization' => $attributes['organization']
                ])['warehouse'];
            },
    	];
    }
}
