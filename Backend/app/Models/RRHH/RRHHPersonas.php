<?php 

namespace App\Models;

use DB, Illuminate\Database\Eloquent\Model;

class RRHHPersonas extends Model {

	protected $table = 'rrhh.personas';
	protected $primaryKey='id_persona';
	protected $fillable = ['nombre','primer_apellido','segundo_apellido','cedula','email','direccion','telefono','estado'];
    const CREATED_AT = 'f_creacion';
	const UPDATED_AT = 'f_modificacion';

	protected $hidden = [
        'id_persona', 'f_creacion','f_modificacion','estado',
    ];

}