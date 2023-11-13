<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FuncionarioForma
 * 
 * @property int $ID_Funcionario
 * @property int $ID_Paquete
 * 
 * @property FuncionarioAlmacen $funcionario_almacen
 * @property Paquete $paquete
 *
 * @package App\Models
 */
class FuncionarioForma extends Model
{
	protected $table = 'funcionario_forma';
	protected $primaryKey = 'ID_Paquete';
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'ID_Funcionario' => 'int',
		'ID_Paquete' => 'int'
	];

	protected $fillable = [
		'ID_Funcionario'
	];

	public function funcionario_almacen()
	{
		return $this->belongsTo(FuncionarioAlmacen::class, 'ID_Funcionario');
	}

	public function paquete()
	{
		return $this->belongsTo(Paquete::class, 'ID_Paquete');
	}
}
