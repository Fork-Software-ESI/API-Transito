<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PersonaTelefono
 * 
 * @property int $ID
 * @property string $Telefono
 * 
 * @property Persona $persona
 *
 * @package App\Models
 */
class PersonaTelefono extends Model
{
	protected $primaryKey = ['ID', 'Telefono'];
	protected $table = 'persona_telefono';
	public $incrementing = false;
	public $timestamps = true;
	
	protected $fillable = [
		'Telefono'
	];
	protected $casts = [
		'ID' => 'int'
	];

	public function persona()
	{
		return $this->belongsTo(Persona::class, 'ID');
	}
}
