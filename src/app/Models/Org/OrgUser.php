<?php

namespace App\Models\Org;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Database\Factories\Org\OrgUser as OrgUserFactory;

class OrgUser extends Model
{
    use HasFactory;
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

    /**
     * @return OrgUserFactory
     */
    protected static function newFactory(): OrgUserFactory
    {
        return OrgUserFactory::new();
    }
}
