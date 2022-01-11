<?php

namespace App\Models\Cloth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\Cloth\Clothing as ClothingFactory;
use App\Models\Cloth\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Org\Organization;
use App\Models\Cloth\ClothingUnit;

/**
 * @method static where(string $string, mixed $get)
 * @method static orderByDesc(string $string)
 */
class Clothing extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'clothing';

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
        'image',
        'name',
        'category',
        'organization'
    ];

    /**
     * @var null[]
     */
    protected $attributes = [
        'image' => NULL
    ];

    /**
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * @return ClothingFactory
     */
    public static function newFactory(): ClothingFactory
    {
        return ClothingFactory::new();
    }

    /**
     * @return HasOne|Model|object
     */
    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category')->first();
    }

    /**
     * @return HasOne|Model|object
     */
    public function organization()
    {
        return $this->hasOne(Organization::class, 'id', 'organization')->first();
    }

    /**
     * @return HasMany
     */
    public function clothingUnits(): HasMany
    {
        return $this->hasMany(ClothingUnit::class, 'clothing');
    }
}
