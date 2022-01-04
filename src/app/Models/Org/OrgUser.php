<?php

namespace App\Models\Org;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrgUser extends Model
{
    /**
     * @var string
     */
    protected $table = 'organization_user';

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
        'is_admin',
        'is_owner',
        'user',
        'organization'
    ];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'user', 'id');
    }

    /**
     * @return HasOne
     */
    public function organization(): HasOne
    {
        return $this->hasOne(Organization::class, 'organization', 'id');
    }
}
