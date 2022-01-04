<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    /**
     * @var string
     */
    protected $table = 'organization';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $incrementing = TRUE;

    /**
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name'
    ];
}
