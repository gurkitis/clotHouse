<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Database\Factories\User\Session as SessionFactory;

class Session extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'session';

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
        'session_id',
        'last_request_at',
        'user'
    ];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'user', 'id');
    }

    protected static function newFactory()
    {
        return SessionFactory::new();
    }
}
