<?php

namespace App\Models\CajaBanco;

use Illuminate\Support\Facades\DB, Illuminate\Database\Eloquent\Model;
use App\Models\Inventario\BodegaProductos;

class FacturaDetalle extends Model {

	public $timestamps = false;
	protected $table = 'cjabnco.facturas_detalles';
	protected $primaryKey='id_factura_detalle';
	/*const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';*/

    public function bodegaProducto()
    {
        return $this->belongsTo(BodegaProductos::class,'id_bodega_producto');
    }
}
