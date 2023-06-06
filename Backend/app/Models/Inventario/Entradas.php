<?php

namespace App\Models\Inventario;

use App\Models\Inventario\Proveedores;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Inventario\EntradaProductos;

class Entradas extends Model
{
    protected $table = 'inventario.entradas';
    protected $primaryKey='id_entrada';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['codigo_entrada','fecha_entrada','descripcion','estado'];
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';


    /**
     * Obtener Lista de entradas
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerEntradas($request)
    {
       // $entradas = InventarioEntradas::with('entradaProveedor');
        $entradas = $this->select(['*']);
        if (!empty($request->search['field'])) {
           $searchField = $request->search['field'];
           $searchValue = $request->search['value'];
           $entradas->where($searchField, 'ilike', '%' . $searchValue . '%');
       }
/*      if(Auth::user()->id_sucursal>0){
      $entradas->whereRaw('(select b.id_sucursal from inventario.bodegas b where b.id_bodega = inventario.entradas.id_bodega) = '.Auth::user()->id_sucursal);
      }*/

       $entradas->with('entradaProductos');
//       $entradas->with('entradaProveedor');
       $entradas->with('entradaBodega');
       $entradas->with('entradaTipo');



       //$entradas->orderBy('fecha_entrada', 'desc');
        $entradas->orderBy('id_entrada', 'desc');

        return $entradas->paginate($request->limit);
    }

    public function obtenerEntrada($request)
    {
        $entrada = $this->select(['*']);
        $entrada->where('id_entrada', $request->id_entrada);
       $entrada->with(['entradaProductos' => function($query) {
           $query->orderBy("id_entrada_producto", 'asc');
                $query->with(['bodegaProducto' => function($query2) {
                    $query2->with(['productoSimple'=> function($query3) {
//                        $query3->with('productoDetallesBaterias');
                    }]);
                }]);
/*            $query->with(['entradaProductoBaterias' => function($query2) {
                $query2->with('bateria');
            }]);*/
        }])->with('entradaSalidaBodega');




//        $entrada->with('entradaProveedor');
        $entrada->with('entradaBodega');
        $entrada->with('entradaTipo');
        return $entrada->first();
    }

    public function obtenerProductosEntrada($request)
    {
        $productos = $this->select(['inventario.productos.id_producto','inventario.productos.codigo_barra','inventario.productos.descripcion'
            ,DB::raw("CONCAT(inventario.productos.descripcion,' (',inventario.productos.codigo_barra,')') AS text")]);
        $productos->leftJoin('inventario.entrada_productos', 'inventario.entrada_productos.id_entrada', 'inventario.entradas.id_entrada');
        $productos->leftJoin('inventario.bodega_productos', 'inventario.bodega_productos.id_bodega_producto','inventario.entrada_productos.id_bodega_producto');
        $productos->leftJoin('inventario.productos', 'inventario.productos.id_producto','inventario.bodega_productos.id_producto');
//        $productos->leftJoin('inventario.baterias_detalles', 'inventario.productos.id_producto','inventario.baterias_detalles.id_producto');
//        $productos->leftJoin('inventario.baterias_submarcas', 'inventario.baterias_detalles.id_submarca','inventario.baterias_submarcas.id_submarca');
//        $productos->leftJoin('inventario.baterias_marcas', 'inventario.baterias_marcas.id_marca','inventario.baterias_submarcas.id_marca');

        $productos->Where('inventario.entradas.id_entrada', $request->id_entrada);
        $productos->Where('inventario.productos.id_tipo_producto', 3)->where('inventario.productos.condicion',1);
        if ((!empty($request->q))) {
            $searchValue = $request->q;
            $productos->where(function($query) use($searchValue) {
                $query->Where('inventario.productos.codigo_barra', 'ILIKE', '%' . $searchValue . '%')->orWhere('inventario.productos.descripcion', 'ILIKE', '%' . $searchValue . '%')
                    ->orWhere('inventario.productos.codigo_sistema', 'ILIKE', '%' . $searchValue . '%');
            });
        }
        $productos->orderBy('inventario.entrada_productos.id_entrada_producto', 'asc');
            return $productos->limit(10)->get();

    }

    public function obtenerEntradaPorCodigo($request)
    {
        $entrada = $this->select(['*']);
        $entrada->where('inventario.entradas.codigo_entrada', '=', $request->codigo_entrada);
        $entrada->whereIn('inventario.entradas.estado', array(2));
        $entrada->whereIn('inventario.entradas.id_tipo_entrada', array(1,3));
        $entrada->whereRaw('(select count(*) from inventario.entradas ie2 where ie2.id_entrada_dev = inventario.entradas.id_entrada and ie2.estado in (1) ) = 0');
        $entrada->with('entradasProductos');
        $entrada->with('entradaProveedor');
        $entrada->with('entradaBodega');
        $entrada->with('entradaTipo');

        return $entrada->get();
    }

    public function entradaProductos()
    {
        return $this->hasMany(EntradaProductos::class,'id_entrada');
    }

    public function entradaSalidaBodega()
    {
        return $this->belongsTo('App\Models\Inventario\Salidas','id_salida')->select('id_salida','id_bodega');
    }

    public function entradaProveedor()
    {
        return $this->belongsToMany(Proveedores::class,'inventario.entradas','id_proveedor','id_proveedor');
    }

    public function entradaProveedores()
    {
        $identificadores_proveedores = array_map('intval', explode(',', (string)'id_proveedor'));
        return Proveedores::whereIn('id_proveedor', $identificadores_proveedores)->get();
    }
    public function entradaBodega()
    {
        return $this->belongsTo('App\Models\Inventario\Bodegas','id_bodega');
    }

    public function entradaTipo()
    {
        return $this->belongsTo('App\Models\Inventario\TipoEntrada','id_tipo_entrada')->select('id_tipo_entrada','descripcion');
    }
}
