<?php

namespace App\Models\Inventario;

use DB, Illuminate\Database\Eloquent\Model;

class EntradaProductos extends Model {

	protected $table = 'inventario.entrada_productos';
	protected $primaryKey='id_entrada_producto';
	const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';


    public function bodegaProducto()
    {
        return $this->belongsTo('App\Models\Inventario\BodegaProductos','id_bodega_producto');
    }


}
