<?php

namespace Database\Factories\Cloth;

use App\Models\Cloth\ClothingUnit as Model;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cloth\Status;
use App\Models\Cloth\Clothing;
use App\Models\House\Warehouse;
use App\Models\Org\Organization;

class ClothingUnit extends Factory
{
    protected $model = Model::class;

    public function definition(): array
    {
    	return [
    	    'identificator' => $this->faker->unique()->md5,
            'status' => Status::factory(),
            'clothing' => Clothing::factory(),
            'warehouse' => Warehouse::factory(),
            'organization' => Organization::factory()
    	];
    }
}
