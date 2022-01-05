<?php

namespace App\Models\User;

use App\Models\House\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Database\Factories\User\User as UserFactory;

class User extends Model
{
    use HasFactory;

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

    /**
     * @var string[]
     */
    protected $hidden = [
        'password'
    ];

    /**
     * @return UserFactory
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
