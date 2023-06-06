<?php

namespace App\Models\CajaBanco;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductosVistaVenta extends Model
{
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'inventario.v_productos_venta';
    protected $primaryKey='id_producto';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['codigo','descripcion','descripcion','precio_sugerido','estado'];

    public function serviciosVenta($request)
    {
        $productos = $this->select([
            'inventario.v_productos_venta.id_producto',
            'inventario.v_productos_venta.codigo_sistema',
            'inventario.v_productos_venta.unidad_medida',
            'inventario.v_productos_venta.tasa_impuesto',
            'inventario.v_productos_venta.codigo_barra',
            'inventario.v_productos_venta.descripcion',
            DB::raw("0 AS id_bodega_producto"),
            DB::raw("CONCAT(inventario.v_productos_venta.descripcion,' (',inventario.v_productos_venta.codigo_barra,')') AS text"),DB::raw("100 AS cantidad_disponible"),'inventario.v_productos_venta.precio_sugerido', 'inventario.v_productos_venta.precio_distribuidor','inventario.v_productos_venta.costo_estandar AS costo_promedio','inventario.v_productos_venta.id_tipo_producto']);
        $productos->where('id_tipo_producto',2);
        $productos->orderBy('inventario.v_productos_venta.descripcion', 'asc');
        return $productos->get();
    }
}
