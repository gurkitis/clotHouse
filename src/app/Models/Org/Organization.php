<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\Org\Organization as OrganizationFactory;

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
}
