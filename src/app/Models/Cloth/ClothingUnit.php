<?php

namespace App\Models\Cloth;

use App\Models\Trans\Exchange;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\Cloth\ClothingUnit as ClothingUnitFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Cloth\Status;
use App\Models\Cloth\Clothing;
use App\Models\House\Warehouse;
use App\Models\Org\Organization;

/**
 * @method static orderByDesc(string $string)
 * @method static find(mixed $get)
 * @method static where(string $string, mixed $id)
 */
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
        'identificator',
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

    /**+
     * @return HasMany
     */
    public function exchanges(): HasMany
    {
        return $this->hasMany(Exchange::class, 'clothing_unit', 'id');
    }
}
