<?php

namespace App\Models\Cloth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\Cloth\ClothingUnit as ClothingUnitFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Cloth\Status;
use App\Models\Cloth\Clothing;
use App\Models\House\Warehouse;
use App\Models\Org\Organization;

class ClothingUnit extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'clothing_unit';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $incrementing = TRUE;

    /**
     * @var array
     */
    protected $fillable = [
        'idnetificator',
        'status',
        'clothing',
        'warehouse',
        'organization'
    ];

    /**
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * @return ClothingUnitFactory
     */
    public static function newFactory(): ClothingUnitFactory
    {
        return ClothingUnitFactory::new();
    }

    /**
     * @return HasOne|Model|object
     */
    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'status')->first();
    }

    /**
     * @return HasOne|Model|object
     */
    public function clothing()
    {
        return $this->hasOne(Clothing::class, 'id', 'clothing')->first();
    }

    /**
     * @return HasOne|Model|object
     */
    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse')->first();
    }

    /**
     * @return HasOne|Model|object
     */
    public function organization()
    {
        return $this->hasOne(Organization::class, 'id', 'organization')->first();
    }
}
