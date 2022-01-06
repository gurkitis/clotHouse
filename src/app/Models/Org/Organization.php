<?php

namespace App\Models\Org;

use App\Models\House\OrgHouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\Org\Organization as OrganizationFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static firstWhere(string $string, mixed $orgName)
 */
class Organization extends Model
{
    use HasFactory;
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

    /**
     * @return OrganizationFactory
     */
    protected static function newFactory(): OrganizationFactory
    {
        return OrganizationFactory::new();
    }

    /**
     * @return HasMany
     */
    public function orgUsers(): HasMany
    {
        return $this->hasMany(OrgUser::class, 'organization');
    }

    /**
     * @return HasMany
     */
    public function orgHouses(): HasMany
    {
        return $this->hasMany(OrgHouse::class, 'organization');
    }
}
