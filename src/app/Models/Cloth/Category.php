<?php

namespace App\Models\Cloth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\Cloth\Category as CategoryFactory;
use App\Models\Cloth\Clothing;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Org\Organization;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static orderByDesc(string $string)
 * @method static find(mixed $get)
 * @method static where(string $string, mixed $id)
 */
class Category extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'category';

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
        'name',
        'organization'
    ];

    /**
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * @return CategoryFactory
     */
    public static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }

    /**
     * @return HasMany
     */
    public function clothes(): HasMany
    {
        return $this->hasMany(Clothing::class, 'category');
    }

    /**
     * @return HasOne|Model|object
     */
    public function organization()
    {
        return $this->hasOne(Organization::class, 'id', 'organization')->first();
    }
}
