<?php

namespace App\Models\Trans;

use App\Models\Cloth\ClothingUnit;
use App\Models\House\Warehouse;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\Trans\Exchange as ExchangeFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static orderByDesc(string $string)
 * @method static where(string $string, $id)
 */
class Exchange extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'exchange';

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
        'date',
        'information',
        'clothing_unit',
        'receiver_warehouse',
        'issuer_warehouse',
        'facilitator',
    ];

    /**
     * @var null[]
     */
    protected $attributes = [
        'information' => NULL,
        'receiver_warehouse' => NULL,
        'issuer_warehouse' => NULL,
    ];

    /**
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * @return ExchangeFactory
     */
    public static function newFactory(): ExchangeFactory
    {
        return ExchangeFactory::new();
    }

    /**
     * @return HasOne
     */
    public function clothingUnit(): HasOne
    {
        return $this->hasOne(ClothingUnit::class, 'id', 'clothing_unit')->first();
    }

    /**
     * @return HasOne
     */
    public function facilitator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'facilitator')->first();
    }

    /**
     * @return HasOne
     */
    public function issuerWarehouse(): HasOne
    {
        return $this->hasOne(Warehouse::class, 'id', 'issuer_warehouse')->first();
    }

    /**
     * @return HasOne
     */
    public function receiverWarehouse(): HasOne
    {
        return $this->hasOne(Warehouse::class, 'id', 'receiver_warehouse')->first();
    }
}
