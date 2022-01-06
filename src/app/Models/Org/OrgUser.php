<?php

namespace App\Models\Org;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Database\Factories\Org\OrgUser as OrgUserFactory;

/**
 * @method static firstWhere()
 * @method static where(string $string)
 */
class OrgUser extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    const ROLES = [
        1 => 'user',
        2 => 'admin',
        3 => 'owner'
    ];

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
        return $this->hasOne(User::class, 'id', 'user');
    }

    /**
     * @return HasOne
     */
    public function organization(): HasOne
    {
        return $this->hasOne(Organization::class, 'id', 'organization');
    }

    /**
     * @return OrgUserFactory
     */
    protected static function newFactory(): OrgUserFactory
    {
        return OrgUserFactory::new();
    }

    /**
     * Gets user role in organization
     *
     * @return string
     */
    public function getRole(): string
    {
        $role = 1;
        if ($this->getAttribute('is_admin') == TRUE) $role++;
        if ($this->getAttribute('is_owner') == TRUE) $role++;
        return self::ROLES[$role];
    }

    /**
     * Validates $role against $targetRole
     *
     * @param $role
     * @param $targetRole
     * @return bool
     */
    public static function validateRole($role, $targetRole): bool
    {
        return array_search($role, self::ROLES) >= array_search($targetRole, self::ROLES);
    }
}
