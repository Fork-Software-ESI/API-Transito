<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Persona
 * 
 * @property int $ID
 * @property int $CI
 * @property string $Nombre
 * @property string $Apellido
 * @property string $Correo
 * 
 * @property Administrador $administrador
 * @property Chofer $chofer
 * @property Cliente $cliente
 * @property FuncionarioAlmacen $funcionario_almacen
 * @property GerenteAlmacen $gerente_almacen
 * @property Collection|PersonaTelefono[] $persona_telefonos
 * @property Collection|Usuario[] $usuarios
 *
 * @package App\Models
 */
class Persona extends Model
{
	protected $table = 'persona';
	protected $primaryKey = 'ID';
	public $timestamps = true;

	protected $casts = [
		'CI' => 'int'
	];

	protected $fillable = [
		'CI',
		'Nombre',
		'Apellido',
		'Correo'
	];

	public function administrador()
	{
		return $this->hasOne(Administrador::class, 'ID');
	}

	public function chofer()
	{
		return $this->hasOne(Chofer::class, 'ID');
	}

	public function cliente()
	{
		return $this->hasOne(Cliente::class, 'ID');
	}

	public function funcionario_almacen()
	{
		return $this->hasOne(FuncionarioAlmacen::class, 'ID');
	}

	public function gerente_almacen()
	{
		return $this->hasOne(GerenteAlmacen::class, 'ID_Gerente');
	}

	public function persona_telefonos()
	{
		return $this->hasMany(PersonaTelefono::class, 'ID');
	}

	public function usuarios()
	{
		return $this->belongsToMany(Usuario::class, 'persona_usuario', 'ID', 'NomUsuario');
	}
}
