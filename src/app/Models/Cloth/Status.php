<?php

namespace App\Models\Cloth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\Cloth\Status as StatusFactory;
use App\Models\Org\Organization;

class Status extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'status';

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
        'status',
        'organization'
    ];

    /**
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * @return StatusFactory
     */
    public static function newFactory(): StatusFactory
    {
        return StatusFactory::new();
    }

    public function organization()
    {
        $this->hasOne(Organization::class, 'id', 'organization');
    }
}
