<?php

namespace App\Models\Inventario;

use DB, Illuminate\Database\Eloquent\Model;

class MovimientosProductos extends Model {

	const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
	protected $table = 'inventario.movimientos_productos';
	protected $primaryKey='id_movimiento_producto';

}
