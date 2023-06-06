<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BodegaProductos extends Model
{

    protected $table = 'inventario.bodega_productos';
    protected $primaryKey = 'id_bodega_producto';
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';

    public function buscarProductosBodega($request)
    {
        $productos = $this->select(['inventario.v_productos.*', 'inventario.v_productos.codigo_barra', 'inventario.v_productos.descripcion', 'inventario.bodega_productos.id_bodega_producto'
            , DB::raw("CONCAT(inventario.v_productos.descripcion,' (',inventario.v_productos.codigo_barra,')') AS text"), 'inventario.bodega_productos.cantidad as cantidad_disponible']);
        $productos->leftJoin('inventario.v_productos', 'inventario.bodega_productos.id_producto', 'inventario.v_productos.id_producto');
        if ((!empty($request->id_bodega))) {
            $productos->Where('inventario.bodega_productos.id_bodega', $request->id_bodega);
            $productos->Where('inventario.bodega_productos.cantidad', '>', 0);
            if ((!empty($request->q))) {
                $searchValue = $request->q;
                $productos->where(function ($query) use ($searchValue) {
                    $query->Where('inventario.v_productos.codigo_barra', 'ILIKE', '%' . $searchValue . '%')->orWhere('inventario.v_productos.descripcion', 'ILIKE', '%' . $searchValue . '%')
                        ->orWhere('inventario.v_productos.codigo_sistema', 'ILIKE', '%' . $searchValue . '%');
                });
            }
            $productos->orderBy('inventario.v_productos.codigo_sistema', 'asc');
            return $productos->limit(10)->get();
        } else {
            return $productos->limit(0)->get();
        }
    }

    public function buscarProductosBodegaVenta($request)
    {
        $productos = $this->select(['inventario.v_productos_venta.unidad_medida', 'inventario.v_productos_venta.tasa_impuesto', 'inventario.v_productos_venta.codigo_barra', 'inventario.v_productos_venta.descripcion', 'inventario.bodega_productos.id_bodega_producto'
            , DB::raw("CONCAT(inventario.v_productos_venta.descripcion,' (',inventario.v_productos_venta.codigo_barra,')') AS text"), 'inventario.bodega_productos.cantidad as cantidad_disponible', 'inventario.v_productos_venta.precio_sugerido', 'inventario.v_productos_venta.costo_promedio']);
        $productos->leftJoin('inventario.v_productos_venta', 'inventario.bodega_productos.id_producto', 'inventario.v_productos_venta.id_producto');
        if ((!empty($request->id_bodega))) {
            $productos->Where('inventario.bodega_productos.id_bodega', $request->id_bodega);
            $productos->Where('inventario.bodega_productos.cantidad', '>', 0);
            if ((!empty($request->q))) {
                $searchValue = $request->q;
                $productos->where(function ($query) use ($searchValue) {
                    $query->Where('inventario.v_productos_venta.codigo_barra', 'ILIKE', '%' . $searchValue . '%')->orWhere('inventario.v_productos_venta.descripcion', 'ILIKE', '%' . $searchValue . '%')
                        ->orWhere('inventario.v_productos_venta.codigo_sistema', 'ILIKE', '%' . $searchValue . '%');
                });
            }
            $productos->orderBy('inventario.v_productos_venta.codigo_sistema', 'asc');
            return $productos->limit(10)->get();
        } else {
            return $productos->limit(0)->get();
        }
    }

//    public function productosBodegaVenta($request)
//    {
//        $productos = $this->select(['inventario.v_productos_venta.id_producto', 'inventario.v_productos_venta.codigo_sistema', 'inventario.v_productos_venta.unidad_medida', 'inventario.v_productos_venta.tasa_impuesto', 'inventario.v_productos_venta.codigo_barra', 'inventario.v_productos_venta.descripcion', 'inventario.bodega_productos.id_bodega_producto'
//            , DB::raw("CONCAT(inventario.v_productos_venta.descripcion,' (',inventario.bodega_productos.no_documento,')') AS text"),
//            'inventario.bodega_productos.cantidad as cantidad_disponible',
//            /* DB::raw("(case when inventario.v_productos_venta.tipo_producto = 3 then
//            inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,1)
//         else inventario.bodegas_productos.cantidad end)::INTEGER as cantidad_disponible"),*/
//            /*    DB::raw("inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,8) as recuperadas"),
//           DB::raw("inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,6) as obsoletas"),
//    */
//            /*'inventario.bodegas_productos.cantidad as cantidad_disponible',*/
//
//            'inventario.v_productos_venta.precio_sugerido', 'inventario.v_productos_venta.precio_distribuidor', 'inventario.v_productos_venta.costo_promedio', 'inventario.v_productos_venta.costo_promedio_me', 'inventario.v_productos_venta.id_tipo_producto']);
//        $productos->Join('inventario.v_productos_venta', 'inventario.bodega_productos.id_producto', 'inventario.v_productos_venta.id_producto');
//        if ((!empty($request->id_bodega))) {
//            $productos->Where('inventario.bodega_productos.id_bodega', $request->id_bodega);
//            $productos->Where('inventario.bodega_productos.cantidad', '>', 0);
//            /*$productos->WhereRaw("(case when inventario.v_productos_venta.tipo_producto = 3 then
//           inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,1)
//        else inventario.bodegas_productos.cantidad end)::INTEGER > 0");*/
//            $productos->orderBy('inventario.v_productos_venta.descripcion', 'asc');
//            $productos->groupBy('inventario.bodega_productos.no_documento', 'inventario.v_productos_venta.id_producto', 'inventario.v_productos_venta.tasa_impuesto', 'inventario.v_productos_venta.codigo_sistema', 'inventario.v_productos_venta.unidad_medida', 'inventario.v_productos_venta.codigo_barra', 'inventario.v_productos_venta.descripcion', 'inventario.bodega_productos.id_bodega_producto', 'inventario.v_productos_venta.precio_sugerido', 'inventario.v_productos_venta.precio_distribuidor','inventario.v_productos_venta.costo_promedio','inventario.v_productos_venta.costo_promedio_me','inventario.v_productos_venta.id_tipo_producto');
//            return $productos->get();
//        } else {
//            return $productos->limit(0)->get();
//        }
//    }
    public function productosBodegaRecuperados($request)
    {
        $productos = $this->select(['inventario.v_productos_venta.id_producto', 'inventario.v_productos_venta.codigo_sistema', 'inventario.v_productos_venta.unidad_medida', 'inventario.v_productos_venta.tasa_impuesto', 'inventario.v_productos_venta.codigo_barra', 'inventario.v_productos_venta.descripcion', 'inventario.bodega_productos.id_bodega_producto'
            , DB::raw("CONCAT(inventario.v_productos_venta.descripcion,' (',inventario.v_productos_venta.codigo_barra,')') AS text"),
            'inventario.bodega_productos.cantidad_recuperadas as cantidad_disponible',
            /*  DB::raw("(case when inventario.v_productos_venta.tipo_producto = 3 then
             inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,1)
          else inventario.bodegas_productos.cantidad end)::INTEGER as cantidad_disponible"),*/
            // DB::raw("inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,8)::INTEGER as cantidad_disponible"),
            /* DB::raw("inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,6) as obsoletas"),
 */
            /*'inventario.bodegas_productos.cantidad as cantidad_disponible',*/

            'inventario.v_productos_venta.precio_sugerido', 'inventario.v_productos_venta.costo_promedio', 'inventario.v_productos_venta.id_tipo_producto']);
        $productos->leftJoin('inventario.v_productos_venta', 'inventario.bodega_productos.id_producto', 'inventario.v_productos_venta.id_producto');
        if ((!empty($request->id_bodega))) {
            $productos->Where('inventario.bodega_productos.id_bodega', $request->id_bodega);
            $productos->Where('inventario.bodega_productos.cantidad_recuperadas', '>', 0);
            /*$productos->WhereRaw("(case when inventario.v_productos_venta.tipo_producto = 3 then
           inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,8)
        else inventario.bodegas_productos.cantidad end)::INTEGER > 0");*/
            $productos->orderBy('inventario.v_productos_venta.descripcion', 'asc');
            return $productos->get();
        } else {
            return $productos->limit(0)->get();
        }
    }

    public function productosBodegaObsoletos($request)
    {
        $productos = $this->select(['inventario.v_productos_venta.id_producto', 'inventario.v_productos_venta.codigo_sistema', 'inventario.v_productos_venta.unidad_medida', 'inventario.v_productos_venta.tasa_impuesto', 'inventario.v_productos_venta.codigo_barra', 'inventario.v_productos_venta.descripcion', 'inventario.bodegas_productos.id_bodega_producto'
            , DB::raw("CONCAT(inventario.v_productos_venta.descripcion,' (',inventario.v_productos_venta.codigo_barra,')') AS text"),

            /*     DB::raw("(case when inventario.v_productos_venta.tipo_producto = 3 then
                inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,1)
             else inventario.bodegas_productos.cantidad end)::INTEGER as cantidad_disponible"),
                 DB::raw("inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,8) as recuperadas"),
                */
            //DB::raw("inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,6) as cantidad_disponible"),

            'inventario.bodega_productos.cantidad_obsoletas as cantidad_disponible',

            'inventario.v_productos_venta.precio_sugerido', 'inventario.v_productos_venta.costo_promedio', 'inventario.v_productos_venta.id_tipo_producto']);
        $productos->leftJoin('inventario.v_productos_venta', 'inventario.bodega_productos.id_producto', 'inventario.v_productos_venta.id_producto');
        if ((!empty($request->id_bodega))) {
            $productos->Where('inventario.bodega_productos.id_bodega', $request->id_bodega);
            $productos->Where('inventario.bodega_productos.cantidad_obsoletas', '>', 0);
            /*$productos->WhereRaw("(case when inventario.v_productos_venta.tipo_producto = 3 then
           inventario.obtener_unidades_disponibles(inventario.bodegas_productos.id_bodega,inventario.bodegas_productos.id_producto,6)
        else inventario.bodegas_productos.cantidad end)::INTEGER > 0");*/
            $productos->orderBy('inventario.v_productos_venta.descripcion', 'asc');
            return $productos->get();
        } else {
            return $productos->limit(0)->get();
        }
    }

    public function productosBodegaGarantia($request)
    {
        $productos = $this->select(['inventario.v_productos_venta.id_producto', 'inventario.baterias_detalles.bci', 'inventario.v_productos_venta.codigo_sistema', 'inventario.v_productos_venta.unidad_medida', 'inventario.v_productos_venta.tasa_impuesto', 'inventario.v_productos_venta.codigo_barra', 'inventario.v_productos_venta.descripcion', 'inventario.bodegas_productos.id_bodega_producto'
            , DB::raw("CONCAT(inventario.v_productos_venta.descripcion,' (',inventario.v_productos_venta.codigo_barra,')') AS text"), 'inventario.bodegas_productos.cantidad as cantidad_disponible', 'inventario.v_productos_venta.precio_sugerido', 'inventario.v_productos_venta.costo_promedio', 'inventario.v_productos_venta.id_tipo_producto']);
        $productos->leftJoin('inventario.v_productos_venta', 'inventario.bodega_productos.id_producto', 'inventario.v_productos_venta.id_producto');
        $productos->leftJoin('inventario.baterias_detalles', 'inventario.baterias_detalles.id_producto', 'inventario.v_productos_venta.id_producto');

        if ((!empty($request->id_bodega))) {
            $productos->Where('inventario.bodega_productos.id_bodega', $request->id_bodega);
            $productos->Where('inventario.bodega_productos.cantidad', '>', 0);
            $productos->orderBy('inventario.v_productos_venta.descripcion', 'asc');
            return $productos->get();
        } else {
            return $productos->limit(0)->get();
        }
    }


    public function productosBodegaConsignacionCliente($request)
    {
        $productos = $this->select(['inventario.v_productos_consignados.id_producto', 'inventario.v_productos_consignados.codigo_sistema', 'inventario.v_productos_consignados.unidad_medida', 'inventario.v_productos_consignados.tasa_impuesto', 'inventario.v_productos_consignados.codigo_barra', 'inventario.v_productos_consignados.nombre_comercial', 'inventario.bodega_productos.id_bodega_producto'
            , DB::raw("CONCAT(inventario.v_productos_consignados.nombre_comercial,' (',inventario.v_productos_consignados.codigo_barra,')') AS text"), 'v_productos_consignados.cantidad_disponible as cantidad_disponible', 'inventario.v_productos_consignados.precio_sugerido', 'inventario.v_productos_consignados.costo_promedio', 'inventario.v_productos_consignados.id_tipo_producto']);
        $productos->leftJoin('inventario.v_productos_consignados', 'inventario.bodega_productos.id_bodega_producto', 'inventario.v_productos_consignados.id_bodega_producto');
        if ((!empty($request->id_cliente))) {
            $productos->Where('inventario.v_productos_consignados.id_cliente', $request->id_cliente);
            $productos->Where('inventario.bodega_productos.id_bodega', $request->id_bodega);///17 manual
            $productos->Where('inventario.v_productos_consignados.cantidad_disponible', '>', 0);
            $productos->Where('inventario.bodegas_producto.cantidad', '>', 0);
            $productos->orderBy('inventario.v_productos_consignados.nombre_comercial', 'asc');
            return $productos->get();
        } else {
            return $productos->limit(0)->get();
        }
    }


    public function producto()
    {
        return $this->belongsTo(ProductosVista::class, 'id_bodega_producto', 'id_bodega_producto');
    }

    public function productoVenta()
    {
        return $this->belongsTo('App\Models\Inventario\ProductosVistaVenta', 'id_producto');
    }

    public function productoSimple()
    {
        return $this->belongsTo(Productos::class, 'id_producto')->select(['id_producto', 'codigo_sistema', 'descripcion', 'id_tipo_producto', 'condicion']);
    }

    public function bodega()
    {
        return $this->belongsTo('App\Models\Inventario\Bodegas', 'id_bodega');
    }
}
