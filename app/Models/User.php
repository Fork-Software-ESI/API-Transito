<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements Authenticatable
{
	use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

	protected $table = 'users';
	protected $primaryKey = 'ID';
	public $incrementing = true;
	public $timestamps = true;
	protected $dates = ['deleted_at'];

	protected $fillable = [
		'username',
		'password',
		'remember_token',
	];

	public function getAuthIdentifierName()
	{
		return 'ID';
	}

	public function getAuthIdentifier()
	{
		return $this->ID;
	}

	public function getAuthPassword()
	{
		return $this->password;
	}

	public function getRememberToken()
	{
		return 'remember_token';
	}

	public function getRememberTokenName()
	{
		return 'remember_token';
	}
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}


	public function personas()
	{
		return $this->belongsToMany(Persona::class, 'persona_usuario', 'username', 'ID');
	}
}