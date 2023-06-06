<?php

namespace App\Models\Inventario;


use DB, Illuminate\Database\Eloquent\Model;

class MovimientosProductosVista extends Model {

	public $timestamps = false;
	protected $table = 'inventario.v_movimiento_productos';
	protected $primaryKey='id_movimiento';

    public function obtenerMovimientos($request)
    {
        $movimientos = $this->select(['*']);
        $movimientos->where('id_producto',$request->productoB['id_producto']);
        if($request->bodega['id_bodega']>0){
            $movimientos->where('id_bodega',$request->bodega['id_bodega']);
        }

        return $movimientos->get();
    }

}
