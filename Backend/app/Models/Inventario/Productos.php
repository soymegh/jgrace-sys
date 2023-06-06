<?php

namespace App\Models\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;
use App\Models\Inventario\UnidadMedida;

class Productos extends Model {

    use HasFactory;

    protected $table = 'inventario.productos';
    protected $primaryKey='id_producto';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = ['codigo','nombre_producto','descripcion','costo_estandar','precio_sugerido','existencia_Min','estado'];
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';


    /**
     * Buscar productos
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function buscarProductos($request)
    {
        $productos = $this->select(['*','descripcion AS text']);
        $searchValue = $request->q;
        $productos->where('estado', 1);
        $productos->where('descripcion', 'ILIKE', '%' . $searchValue . '%');
        $productos->orderBy('descripcion', 'asc');
        return $productos->limit(10)->get();
    }


    public function buscarPS($request)
    {
        $productos = $this->select(['id_producto','codigo_barra','codigo_consecutivo','codigo_sistema','condicion','costo_estandar','descripcion AS text']);
        $searchValue = $request->q;
        $productos->where('activo', 1);
        $productos->whereIn('id_tipo_producto', array( 1,3));
        $productos->where('descripcion', 'ILIKE', '%' . $searchValue . '%')
            ->Orwhere('codigo_barra', $searchValue)->Orwhere('codigo_sistema', $searchValue);
        $productos->orderBy('descripcion', 'asc');
        return $productos->limit(10)->get();
    }



    public function obtenerProductosEntrada($request)
    {
        $productos = $this->select(['*','descripcion AS text']);
        $searchValue = $request->q;
        $productos->where('activo', 1);
        $productos->where('descripcion', 'ILIKE', '%' . $searchValue . '%');
        $productos->orderBy('descripcion', 'asc');
        return $productos->limit(10)->get();
    }




    public function generarCodigoBateria($id_proveedor,$id_marca,$id_submarca,$id_aplicacion)
    {
        $codigo = $this->select([DB::raw("COALESCE(max(codigo_consecutivo),0)+1 as secuencia")])
            ->join('inventario.baterias_detalles', 'inventario.productos.id_producto', '=', 'inventario.baterias_detalles.id_producto')
            ->join('inventario.baterias_submarcas', 'inventario.baterias_detalles.id_submarca', '=', 'inventario.baterias_submarcas.id_submarca')
            ->join('inventario.baterias_subaplicaciones', 'inventario.baterias_detalles.id_subaplicacion', '=', 'inventario.baterias_subaplicaciones.id_subaplicacion');
        if((!empty($id_proveedor))&&(!empty($id_marca))&&(!empty($id_submarca))&&(!empty($id_aplicacion))){
            $codigo->where('condicion',1);
            $codigo->where('id_proveedor',$id_proveedor);
            $codigo->where('inventario.baterias_submarcas.id_marca',$id_marca);
            $codigo->where('inventario.baterias_submarcas.id_submarca',$id_submarca);
            $codigo->where('inventario.baterias_subaplicaciones.id_aplicacion',$id_aplicacion);
        }
        return $codigo->first();
    }

    /**
     * Obtener Lista de productos
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerProductos($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $productos = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $productos->where($searchField, 'ilike', '%' . $searchValue . '%');
            $productos->where('id_empresa',$usuario_empresa->id_empresa);

            if($statusValue === false)
            {
                $productos -> where('estado',1);
            }
            //$productos->where('activo',true);
            //$productos->whereIn('id_producto',array(2, 4, 7, 8, 10, 11, 12, 13, 18, 21, 22, 24, 25, 26, 27, 28, 29, 59, 65, 118, 135, 142, 143, 144, 145, 146, 147, 148, 149, 158, 161, 162, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175));
            $productos->whereIn('id_tipo_producto',array(1,2,4));
            $productos->with('unidadMedida');
            $productos->with('marca');
            $productos->orderBy('id_producto', 'asc');
        }
        return $productos->paginate($request->limit);

    }


    /**
     * Obtener codigo estructurado
     *
     * @access 	public
     * @param
     * @return 	array
     */

    public function obtenerCodigoProducto($request)
    {
        $codigo = $this->select([DB::raw("COALESCE(max(codigo_consecutivo),0)+1 as codigo_siguiente")]);
        return $codigo->get();
    }

    public function obtenerProducto($request)
    {
        $productos = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $productos->where('id_producto', $request->id_producto);
        $productos->where('id_empresa',$usuario_empresa->id_empresa);
        /*$productos->with(['proveedoresLista' => function($query) {
            $query->with('proveedores');
        }]);*/
        $productos->with('unidadMedida');
        $productos->with('marca');
        return $productos->get();
    }

    public function obtenerBateria($request)
    {
        $productos = $this->select(['*']);
        $productos->where('id_producto', $request->id_producto);

        $productos->with('unidadMedida');
        $productos->with('rubroProducto');
        $productos->with('impuestoProducto');

        $productos->with(['productoDetallesBaterias' => function($query) {
            $query->with('bateriaMaterial');
            $query->with('bateriaLineaProducto');
            $query->with('bateriaSubAplicacion');
            $query->with('bateriaSubMarca');
        }]);

        return $productos->get();
    }

    public function obtenerProductosBodega($request)
    {
        $productos = $this->select(['inventario.productos.codigo_sistema','inventario.productos.id_unidad_medida','inventario.productos.nombre_comercial as nom_prod','inventario.productos.id_producto',
            'inventario.bodega_productos.id_bodega_producto as id_bodega_producto','inventario.productos.costo_estandar as precio_sugerido',
            DB::raw("concat(inventario.productos.codigo_sistema,' - ',inventario.productos.nombre_comercial) as nombre_producto"),
            DB::raw("(SELECT COALESCE(sum(iep.cantidad_recibida),0) as entradas FROM inventario.entradas_productos iep  left join inventario.entradas ie on ie.id_entrada=iep.id_entrada
        where ie.estado in (2) and iep.id_bodega_producto = inventario.bodega_productos.id_bodega_producto) -(SELECT COALESCE(sum(isp.cantidad_despachada),0) as salidas FROM inventario.salidas_productos isp
        left join inventario.salidas ie on ie.id_salida=isp.id_salida where ie.estado in (2) and isp.id_bodega_producto = inventario.bodega_productos.id_bodega_producto) as inventario")
        ]);
        $productos->leftJoin('inventario.bodega_productos', 'inventario.productos.id_producto', '=', 'inventario.bodega_productos.id_producto');
        $productos->where('inventario.bodega_productos.id_bodega', '=', $request->id_bodega);
        $productos->where('inventario.productos.estado', '=', 1);
        $productos->groupBy('inventario.productos.codigo_sistema','inventario.productos.id_unidad_medida', 'inventario.productos.nombre_comercial','inventario.productos.id_producto','inventario.bodega_productos.id_bodega_producto');
        //$productos->having('inventario', '>', 0);
        $productos->havingRaw('(SELECT COALESCE(sum(iep.cantidad_recibida),0) as entradas FROM inventario.entradas_productos iep  left join inventario.entradas ie on ie.id_entrada=iep.id_entrada
        where ie.estado in (2) and iep.id_bodega_producto = inventario.bodega_productos.id_bodega_producto) -(SELECT COALESCE(sum(isp.cantidad_despachada),0) as salidas FROM inventario.salidas_productos isp
        left join inventario.salidas ie on ie.id_salida=isp.id_salida where ie.estado in (2) and isp.id_bodega_producto = inventario.bodega_productos.id_bodega_producto) > 0');

        $productos->with('unidadMedida');
        $productos->with('tipoProductos');
        return $productos->get();
    }

    public function obtenerProductosValidos($request)
    {
        $productos = $this->select(['*']);
        $productos->where('estado', 1)->whereIn('id_tipo_producto',array(1,3))->with('unidadMedida')->orderby('id_producto');
        return $productos->get();
    }

    /*public function proveedoresLista()
    {
        return $this->hasMany('App\Models\InventarioProductoProveedor','id_producto');
    }*/

    public function entradasProductos()
    {
        return $this->hasMany('App\Models\Inventario\EntradaProductos','id_producto');
    }


    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class,'id_unidad_medida')->select('id_unidad_medida','siglas','descripcion');
    }

    /*Relación producto - marca*/

    public function marca()
    {
        return $this->belongsTo(Marcas::class,'id_marca')->select('id_marca','descripcion','estado');
    }


    public function productosEnBodega()
    {
        //return $this->hasMany('App\Models\InventarioBodegaProductos','id_producto');
        return $this->belongsToMany('App\Models\Inventario\Productos', 'inventario.bodega_productos', 'id_producto', 'id_bodega')->using('App\Models\InventarioBodegaProductos');
    }

    public function  tipoProductos(){
        return $this->belongsTo('App\Models\Inventario\TipoProductos', 'id_tipo_producto');
    }

}
