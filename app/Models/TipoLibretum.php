<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TipoLibretum
 * 
 * @property string $Tipo
 * @property int $PesoMaximoKg
 * 
 * @property Collection|ChoferTipoLibretum[] $chofer_tipo_libreta
 *
 * @package App\Models
 */
class TipoLibretum extends Model
{
	protected $table = 'tipo_libreta';
	protected $primaryKey = 'Tipo';
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'PesoMaximoKg' => 'int'
	];

	protected $fillable = [
		'PesoMaximoKg'
	];

	public function chofer_tipo_libreta()
	{
		return $this->hasMany(ChoferTipoLibretum::class, 'Tipo');
	}
}
