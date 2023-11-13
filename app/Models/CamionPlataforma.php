<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CamionPlataforma
 * 
 * @property int $ID_Camion
 * @property int $ID_Almacen
 * @property int $Numero_Plataforma
 * @property Carbon $Fecha_Hora_Llegada
 * 
 * @property Camion $camion
 * @property Plataforma $plataforma
 * @property CamionPlataformaSalida $camion_plataforma_salida
 *
 * @package App\Models
 */
class CamionPlataforma extends Model
{
	protected $table = 'camion_plataforma';
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'ID_Camion' => 'int',
		'ID_Almacen' => 'int',
		'Numero_Plataforma' => 'int',
		'Fecha_Hora_Llegada' => 'datetime'
	];

	protected $fillable = [
		'Fecha_Hora_Llegada'
	];

	public function camion()
	{
		return $this->belongsTo(Camion::class, 'ID_Camion');
	}

	public function plataforma()
	{
		return $this->belongsTo(Plataforma::class, 'ID_Almacen', 'ID_Almacen')
					->where('plataforma.ID_Almacen', '=', 'camion_plataforma.ID_Almacen')
					->where('plataforma.Numero', '=', 'camion_plataforma.Numero_Plataforma');
	}

	public function camion_plataforma_salida()
	{
		return $this->hasOne(CamionPlataformaSalida::class, 'ID_Camion');
	}
}
