<?php

namespace App\Models\House;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\House\Warehouse as WarehouseFactory;

/**
 * @method static firstWhere(string $string, mixed $warehouse)
 */
class Warehouse extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'warehouse';

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
        'address'
    ];

    /**
     * @var null[]
     */
    protected $attributes = [
        'address' => NULL
    ];

    /**
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * @return WarehouseFactory
     */
    public static function newFactory(): WarehouseFactory
    {
        return WarehouseFactory::new();
    }
}
