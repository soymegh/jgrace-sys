<?php

namespace App\Models\CajaBanco;

use  Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProformasDetalles extends Model {

	public $timestamps = false;
	protected $table = 'cjabnco.proformas_detalles';
	protected $primaryKey='id_proforma_detalle';
	/*const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';*/

    public function bodegaProducto()
    {
        return $this->belongsTo('App\Models\Inventario\BodegaProductos','id_bodega_producto')
            ->select(['inventario.v_productos_venta.id_producto','inventario.v_productos_venta.codigo_sistema','inventario.v_productos_venta.unidad_medida','inventario.v_productos_venta.tasa_impuesto','inventario.v_productos_venta.codigo_barra','inventario.v_productos_venta.descripcion','inventario.bodega_productos.id_bodega_producto'
                ,DB::raw("CONCAT(inventario.v_productos_venta.descripcion,' (',inventario.v_productos_venta.codigo_barra,')') AS text"),
                'inventario.bodega_productos.cantidad as cantidad_disponible','inventario.v_productos_venta.id_tipo_producto',
                /* DB::raw("(case when inventario.v_productos_venta.tipo_producto = 3 then
                inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,1)
             else inventario.bodegas_productos.cantidad end)::INTEGER as cantidad_disponible"),*/
                /*    DB::raw("inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,8) as recuperadas"),
               DB::raw("inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,6) as obsoletas"),
        */
                /*'inventario.bodegas_productos.cantidad as cantidad_disponible',*/

                'inventario.v_productos_venta.precio_sugerido', 'inventario.v_productos_venta.precio_distribuidor','inventario.v_productos_venta.costo_promedio','inventario.v_productos_venta.costo_promedio_me','inventario.v_productos_venta.id_tipo_producto'])
                ->leftJoin('inventario.v_productos_venta', 'inventario.bodega_productos.id_producto','inventario.v_productos_venta.id_producto');;
    }

    public function afectacionProducto()
    {
        return $this->belongsTo('App\Models\CajaBanco\Afectaciones','id_afectacion');
    }


}
