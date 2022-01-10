<?php

namespace App\Models\Cloth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\Cloth\Category as CategoryFactory;

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
}
