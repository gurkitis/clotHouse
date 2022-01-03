<?php

namespace App\Models\House;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
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

    protected $attributes = [
        'address' => NULL
    ];

    public $timestamps = FALSE;
}
