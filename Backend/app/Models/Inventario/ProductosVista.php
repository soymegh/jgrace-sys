<?php

namespace App\Models\Inventario;

use Illuminate\Support\Facades\DB, Illuminate\Database\Eloquent\Model;

class ProductosVista extends Model
{
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'inventario.v_productos';
    protected $primaryKey='id_producto';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['codigo','descripcion','descripcion','precio_sugerido','estado','id_tipo_producto'];

    /*public function buscarProductosBodega($request)
    {
        $productos = $this->select(['inventario.productos.id_producto','inventario.productos.codigo_barra','inventario.productos.descripcion'
            ,DB::raw("CONCAT(inventario.productos.descripcion,' (',inventario.productos.codigo_barra,')') AS text")]);
        if ((!empty($request->id_bodega))) {
            $productos->leftJoin('inventario.bodegas_productos', 'inventario.bodegas_productos.id_producto','inventario.productos.id_producto');
            $productos->Where('inventario.bodegas_productos.id_bodega', $request->id_bodega);

            if ((!empty($request->q))) {
                $searchValue = $request->q;
                $productos->where(function($query) use($searchValue) {
                    $query->Where('inventario.productos.codigo_barra', 'ILIKE', '%' . $searchValue . '%')->orWhere('inventario.productos.descripcion', 'ILIKE', '%' . $searchValue . '%')
                        ->orWhere('inventario.productos.codigo_sistema', 'ILIKE', '%' . $searchValue . '%');
                });
            }
            $productos->orderBy('inventario.productos.codigo_sistema', 'asc');
            return $productos->limit(10)->get();
        }else{
            return $productos->limit(0)->get();
        }
    }*/
}
