<?php

namespace App\Models\Inventario;

use  Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventario\SalidaProductos;
use Illuminate\Support\Facades\DB;
use App\Models\Inventario\Bodegas;

class Salidas extends Model
{
    protected $table = 'inventario.salidas';
    protected $primaryKey='id_salida';
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['codigo_salida','fecha_salida','estado'];
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';


    /**
     * Obtener Lista de Salidas
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerSalidas($request)
    {
        $salidas = $this->select(['*']);
        if (!empty($request->search['field'])) {

            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $salidas->where($searchField, 'ilike', '%' . $searchValue . '%');
        }

        if(Auth::user()->id_sucursal>0){
            $salidas->whereRaw('(select b.id_sucursal from inventario.bodegas b where b.id_bodega = inventario.salidas.id_bodega) = '.Auth::user()->id_sucursal)
           // ->OrwhereRaw('(select b.id_sucursal from inventario.bodegas b where b.id_bodega = inventario.salidas.id_bodega_entrante) = '.Auth::user()->id_sucursal)
            ;
        }
       // if ((!empty($request->search['estado']))&&(!empty($request->search['estado']))>=0) {
            if($request->search['estado']<>100){
                if($request->search['estado']===1){
                $salidas->whereIn('estado',array(1,99));
                }else{
                $salidas->where('estado',$request->search['estado']);
                }
            }
       // }

        $salidas->with('salidaProductos');
        $salidas->with('salidaProveedor');
        $salidas->with('salidaBodega');
        $salidas->with('salidaCliente');
        $salidas->with('salidaTipo');
        $salidas->orderBy('id_salida', 'desc');

        return $salidas->paginate($request->limit);
    }

    public function obtenerSalida($request)
    {
        $salida = $this->select(['*']);
        $salida->where('id_salida', $request->id_salida);

        $salida->with(['salidaProductos' => function($query) {
            $query->with(['bodegaProducto' => function($query2) {
                $query2->with(['productoSimple'=> function($query3) {

                }]);
            }]);

        }]);

        $salida->with('salidaProveedor');
        $salida->with(['salidaBodega' => function($query) {
            $query->with(['productosBodega' => function($query2){
                $query2->with('producto');
            }]);
        }]);
        $salida->with('salidaBodegaEntrante');
        $salida->with('salidaCliente');
        $salida->with('salidaTipo');
        return $salida->first();
    }

    public function obtenerProductosSalida($request)
    {
        $productos = $this->select(['inventario.productos.id_producto','inventario.productos.codigo_barra','inventario.productos.descripcion',
        DB::raw("CONCAT(inventario.productos.descripcion,' (',inventario.bodega_productos.no_documento,')') AS text")]);
        $productos->leftJoin('inventario.salida_productos', 'inventario.salida_productos.id_salida', 'inventario.salidas.id_salida');
        $productos->Join('inventario.bodega_productos', 'inventario.bodega_productos.id_bodega_producto','inventario.salida_productos.id_bodega_producto');
        $productos->leftJoin('inventario.productos', 'inventario.productos.id_producto','inventario.bodega_productos.id_producto');
        $productos->Where('inventario.salidas.id_salida', $request->id_salida);
        $productos->Where('inventario.productos.id_tipo_producto', array(1,2,3))->where('inventario.productos.condicion',1);
        if ((!empty($request->q))) {
            $searchValue = $request->q;
            $productos->where(function($query) use($searchValue) {
                $query->Where('inventario.productos.codigo_barra', 'ILIKE', '%' . $searchValue . '%')->orWhere('inventario.productos.descripcion', 'ILIKE', '%' . $searchValue . '%')
                    ->orWhere('inventario.productos.codigo_sistema', 'ILIKE', '%' . $searchValue . '%');
            });
        }
        $productos->orderBy('inventario.productos.codigo_sistema', 'asc');
        return $productos->limit(10)->get();

    }

    public function obtenerSalidaPorCodigo($request)
    {
        $salida = $this->select(['*']);
        $salida->where('inventario.salidas.codigo_salida', '=', $request->codigo_salida);
        $salida->whereIn('inventario.salidas.estado', array(2));
        $salida->whereNotIn('inventario.salidas.id_tipo_salida', array(1,2));
        $salida->whereRaw('(select count(*) from inventario.salidas is2 where is2.id_salida_dev = inventario.salidas.id_salida and is2.estado in (1) ) = 0');
        $salida->with('salidaProductos');
        $salida->with('salidaBodegaEntrada');
        $salida->with('salidaBodega');
        $salida->with('salidaTipo');
        return $salida->get();
    }

    public function salidaProductos()
    {
        return $this->hasMany(SalidaProductos::class,'id_salida');
    }

    public function salidaProveedor()
    {
        return $this->belongsTo('App\Models\Inventario\Proveedores','id_proveedor');
    }

    public function salidaCliente()
    {
        return $this->belongsTo('App\Models\Ventas\Clientes','id_cliente');
    }

    public function salidaBodega()
    {
        return $this->belongsTo(Bodegas::class,'id_bodega');
    }

    public function salidaTipo()
    {
        return $this->belongsTo('App\Models\Inventario\TipoSalida','id_tipo_salida');
    }

    public function salidaBodegaEntrante()
    {
        return $this->belongsTo('App\Models\Inventario\Bodegas','id_bodega_entrante')->select('id_bodega','descripcion');
    }
}

