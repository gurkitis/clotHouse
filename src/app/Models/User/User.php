<?php

namespace App\Models\User;

use App\Models\House\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
    /**
     * @var string
     */
    protected $table = 'user';

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
        'name',
        'surname',
        'email',
        'password',
        'warehouse'
    ];

    /**
     * @return HasOne
     */
    public function warehouse(): HasOne
    {
        return $this->hasOne(Warehouse::class, 'warehouse', 'id');
    }

    protected $hidden = [
        'password'
    ];
}
