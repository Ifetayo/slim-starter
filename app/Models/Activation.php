<?php
namespace SlimStarter\Models;

use SlimStarter\Models\User;
use Illuminate\Database\Eloquent\Model;

class Activation extends Model
{
    public  $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token_hash', 'user_id', 'resent_count', 'created_at', 'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'token_hash',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}