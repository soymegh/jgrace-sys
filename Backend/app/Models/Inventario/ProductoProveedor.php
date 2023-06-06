<?php

namespace App\Models\Inventario;

use DB, Illuminate\Database\Eloquent\Model;

class ProductoProveedor extends Model {

	protected $table = 'inventario.productos_proveedores';
	protected $primaryKey='id_producto_proveedor';
	public $timestamps = false;

	public function proveedores()
    {
        return $this->belongsTo('App\Models\Inventario\Proveedores','id_proveedor');
    }
}
