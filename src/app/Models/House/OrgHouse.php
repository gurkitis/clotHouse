<?php

namespace App\Models\House;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\House\OrgHouse as OrgHouseFactory;

class OrgHouse extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'organization_warehouse';

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
        'warehouse',
        'organization'
    ];

    /**
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * @return OrgHouseFactory
     */
    public static function newFactory(): OrgHouseFactory
    {
        return OrgHouseFactory::new();
    }
}
