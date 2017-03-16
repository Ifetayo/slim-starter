<?php
namespace SlimStarter\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
	protected $table = 'users';


	protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'password',
    ];

    protected $loginNames = ['email', 'username'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');
    protected $guarded = array('password');

    public function activation()
    {
        return $this->hasOne(Activation::class);
    }

    public function fullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}