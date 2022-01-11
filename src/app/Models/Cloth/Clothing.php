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
     * @return HasOne
     */
    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category');
    }

    /**
     * @return HasOne
     */
    public function organization(): HasOne
    {
        return $this->hasOne(Organization::class, 'id', 'organization');
    }

    /**
     * @return HasMany
     */
    public function clothingUnits(): HasMany
    {
        return $this->hasMany(ClothingUnit::class, 'clothing');
    }
}