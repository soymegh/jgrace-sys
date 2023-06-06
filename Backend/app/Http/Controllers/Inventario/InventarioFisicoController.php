<?php



namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\Areas;
use App\Models\Admon\Sucursales;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\Bodegas;
use App\Models\Inventario\InventarioFisico;
use App\Models\Inventario\InventarioFisicoProductos;
use App\Models\Inventario\Productos;
//use App\Models\RRHH\Trabajadores;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class InventarioFisicoController extends Controller
{

    /**
     * Get List of Entradas
     *
     * @access  public
     * @param Request $request
     * @param InventarioFisico $inventarioFisico
     * @return JsonResponse
     */

    public function obtener(Request $request, InventarioFisico $inventarioFisico)
    {
        $inventarioFisico = $inventarioFisico->obtener($request);


        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $inventarioFisico->total(),
                'rows' => $inventarioFisico->items()
            ],
            'messages' => null
        ]);
    }


    public function nuevo(Request $request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
        $sucursales = Sucursales::select(['id_sucursal','serie','descripcion'])->where('id_empresa',$usuario_empresa->id_empresa)->with('sucursalBodegasTodas')->get();
        $bodegas = Bodegas::where('estado', 1)->where('id_empresa',$usuario_empresa->id_empresa)->get();
        $areas = Areas::select('id_area','descripcion','estado')->where('estado', 1)->where('id_empresa',$usuario_empresa->id_empresa)->orderby('id_area')->get();
        $productos = Productos::select(['id_producto','codigo_barra','codigo_consecutivo','codigo_sistema','condicion','costo_estandar','descripcion','precio_sugerido as precio_info', \Illuminate\Support\Facades\DB::raw("CONCAT(inventario.productos.nombre_comercial,'-(',inventario.productos.codigo_barra,')') AS text")])
            ->where('estado', 1)->where('id_empresa',$usuario_empresa->id_empresa)->whereIn('id_tipo_producto', array( 1,3))->where('condicion',1)->orderBy('descripcion', 'asc')
            ->get();
//        $usuario_actual = User::select('id_empleado')->where('usuario',Auth::user()->name)->first();
//        $trabajador_actual = RRHHTrabajadores::select(['id_area'])->where('id_trabajador',$usuario_actual['id_empleado'])->where('activo',true)->first();
//        $area_actual = PublicAreas::select('id_area','descripcion','activo')->where('activo', 1)->where('id_area',$trabajador_actual['id_area'])->first();
        $area_actual = Areas::select('id_area','descripcion','estado')->where('estado', 1)->where('id_empresa',$usuario_empresa->id_empresa)->get();//->where('id_area',$trabajador_actual['id_area'])->first();

        return response()->json([
            'status' => 'success',
            'result' => [
                'sucursales' => $sucursales,
                'bodegas' => $bodegas,
                'areas' => $areas,
                'productos' => $productos,
//                'productos_usados' => $productos_usados,
                'area_actual'=>$area_actual[0]
            ],
            'messages' => null
        ]);
    }


    /**
     * Get List of Entrada
     *
     * @access  public
     * @param Request $request
     * @param InventarioFisico $inventarioFisico
     * @return JsonResponse
     */


    public function obtenerConteo(Request $request, InventarioFisico $inventarioFisico)
    {
        $rules = [
            'id_inventario_fisico' => 'required|integer|min:1',
            'cargar_dependencias' => 'required|boolean',
        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $inventarioFisico = $inventarioFisico->obtenerConteo($request);

            if(!empty($inventarioFisico)){

                if($request->cargar_dependencias){

                    $sucursales = Sucursales::where('estado',1)->orderby('id_sucursal')->get();
                    $bodegas = Bodegas::where('estado', 1)->get();
                    $areas = Areas::where('estado', 1)->orderby('id_area')->get();
                    $productos = Productos::select(['id_producto','codigo_barra','codigo_consecutivo','codigo_sistema','condicion','costo_estandar','descripcion',DB::raw("CONCAT(inventario.productos.nombre_comercial,' (',inventario.productos.codigo_barra,')') AS text")])
                        ->where('estado', 1)->whereIn('id_tipo_producto', array( 1,3))->orderBy('descripcion', 'asc')
                        ->get();

                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                    $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();
                    $direccion_empresa = Ajustes::where('id_ajuste', 5)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
                    $telefono_empresa = Ajustes::where('id_ajuste', 6)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'conteo' => $inventarioFisico,
                            'productos' => $productos,
                            'sucursales' => $sucursales,
                            'bodegas' => $bodegas,
                            'areas' => $areas,
                            'nombre_empresa' => $nombre_empresa->valor,
                            'direccion_empresa' => $direccion_empresa->valor,
                            'telefono_empresa' => $telefono_empresa->valor,
                        ],
                        'messages' => null
                    ]);
                }else{
                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'conteo' => $inventarioFisico
                        ],
                        'messages' => null
                    ]);
                }
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_inventario_fisico'=>["Datos no encontrados"]),
                    'messages' => null
                ]);
            }


        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    /**
     * Create a New User
     *
     * @access 	public
     * @param
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {


        /* timestamp without time zone,
         character varying COLLATE pg_catalog."default",
         character varying COLLATE pg_catalog."default",
        num_conteo smallint,
        u_creacion character varying(30) COLLATE pg_catalog."default",
        u_modificacion character varying(30) COLLATE pg_catalog."default",
        f_creacion timestamp with time zone,
        f_modificacion timestamp without time zone,
        estado smallint,
        */

        $messages = [
            'conteo_productos.min' => 'Se requiere agregar un producto por lo menos.',
            'conteo_productos.*.productox.id_producto.required' => 'Seleccione un producto válido',
            'conteo_productos.*.precio_info.min' => 'El precio debe ser mayor que cero',
            'conteo_productos.*.cantidad.min' => 'La cantidad debe ser mayor que cero',
            'hora_fin.required_if' => 'Se debe definir una hora de finalización para emitir el conteo',
        ];


        $rules = [
            'hora_inicio' => 'string|max:50',
            'hora_fin' => 'required_if:es_borrador,false|string|max:50|nullable',
            'es_borrador' => 'required|boolean',
            'f_inventario' => 'required|date',

            'conteo_bodega' => 'nullable|array|min:1',
            'conteo_bodega.id_bodega' => 'required|integer|min:1',

            'conteo_sucursal' => 'required|array|min:1',
            'conteo_sucursal.id_sucursal' => 'required|integer|min:1',

            'conteo_area' => 'required|array|min:1',
            'conteo_area.id_area' => 'required|integer|min:1',

            'conteo_productos' => 'required|array|min:1',
            'conteo_productos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'conteo_productos.*.cantidad' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',

        ];

        $validator = Validator::make($request->all(), $rules,$messages);
        if (!$validator->fails()) {

            try{

                DB::beginTransaction();
                $inventarioFisico = new InventarioFisico;
                $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                $inventarioFisico->id_bodega = $request->conteo_bodega['id_bodega'];
                $inventarioFisico->id_sucursal = $request->conteo_sucursal['id_sucursal'];

                $inventarioFisico->id_area = $request->conteo_area['id_area'];

                $inventarioFisico->hora_inicio = $request->hora_inicio;
                $inventarioFisico->hora_fin = $request->hora_fin;
                $inventarioFisico->f_inventario = $request->f_inventario;

                $inventarioFisico->u_creacion = Auth::user()->name;
                $request->es_borrador == 'true' ? $inventarioFisico->estado = 99:  $inventarioFisico->estado = 1;
                $inventarioFisico->id_empresa = $usuario_empresa->id_empresa;
                $inventarioFisico->save();

                $i = 1;
                foreach ($request->conteo_productos as $producto) {
                    $inventarioFisico_producto = new InventarioFisicoProductos;
                    $inventarioFisico_producto->posicion = $i;
                    $inventarioFisico_producto->id_inventario_fisico = $inventarioFisico->id_inventario_fisico;
                    $inventarioFisico_producto->id_producto = $producto['productox']['id_producto'];
                    $inventarioFisico_producto->descripcion = $producto['productox']['text'];
                    $inventarioFisico_producto->u_medida = 'UNDS';
                    $inventarioFisico_producto->id_empresa = $usuario_empresa->id_empresa;;
                    if(!empty($producto['productox']['codigo_barra'])){
                        $inventarioFisico_producto->codigo_barra = $producto['productox']['codigo_barra'];
                    }else{
                        $inventarioFisico_producto->codigo_barra = '';
                    }
                    $inventarioFisico_producto->cantidad = $producto['cantidad'];
                    $inventarioFisico_producto->save();
                    $i++;
                }
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);
            } catch (Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'result' => 'Error de base de datos',
                    'messages' => null
                ]);
            }


        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }


    public function actualizar(Request $request)
    {
        $messages = [
            'conteo_productos.min' => 'Se requiere agregar un producto por lo menos.',
            'conteo_productos.*.id_producto.required' => 'Seleccione un producto válido',
            'conteo_productos.*.precio_info.min' => 'El precio debe ser mayor que cero',
            'conteo_productos.*.cantidad.min' => 'La cantidad debe ser mayor que cero',
            'hora_fin.required_if' => 'Se debe definir una hora de finalización para emitir el conteo',
            'hora_fin.string' => 'Se debe definir una hora de finalización',
        ];

        $rules = [
            'id_inventario_fisico' => 'required|integer|exists:pgsql.inventario.inventarios_fisicos,id_inventario_fisico',
            'hora_inicio' => 'string|max:50',
            'hora_fin' => 'string|max:50|required_if:es_borrador,false',
            'es_borrador' => 'required|boolean',
            'f_inventario' => 'required|date',

            'conteo_bodega' => 'nullable|array|min:1',
            'conteo_bodega.id_bodega' => 'required|integer|min:1',

            'conteo_sucursal' => 'required|array|min:1',
            'conteo_sucursal.id_sucursal' => 'required|integer|min:1',

            'conteo_area' => 'required|array|min:1',
            'conteo_area.id_area' => 'required|integer|min:1',

            'conteo_productos' => 'required|array|min:1',
            'conteo_productos.*.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'conteo_productos.*.cantidad' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',

        ];

        $validator = Validator::make($request->all(), $rules,$messages);
        if (!$validator->fails()) {

            try{


                DB::beginTransaction();
                $inventarioFisico = InventarioFisico::find($request->id_inventario_fisico);

                if($inventarioFisico->estado == 99){

                    $inventarioFisico->id_sucursal = $request->conteo_sucursal['id_sucursal'];
                    $inventarioFisico->id_area = $request->conteo_area['id_area'];
                    $inventarioFisico->id_bodega = $request->conteo_bodega['id_bodega'];

                    $inventarioFisico->hora_inicio = $request->hora_inicio;
                    $inventarioFisico->hora_fin = $request->hora_fin;
                    $inventarioFisico->f_inventario = $request->f_inventario;

                    $inventarioFisico->u_modificacion = Auth::user()->usuario;
                    $request->es_borrador == 'true' ? $inventarioFisico->estado = 99:  $inventarioFisico->estado = 1;
                    $inventarioFisico->save();

                    InventarioFisicoProductos::where('id_inventario_fisico', $request->id_inventario_fisico)->delete();

                    $i = 1;
                    foreach ($request->conteo_productos as $producto) {
                        $inventarioFisico_producto = new InventarioFisicoProductos;
                        $inventarioFisico_producto->posicion = $i;
                        $inventarioFisico_producto->id_inventario_fisico = $inventarioFisico->id_inventario_fisico;
                        $inventarioFisico_producto->id_producto = $producto['id_producto'];
                        $inventarioFisico_producto->descripcion = $producto['descripcion'];
                        $inventarioFisico_producto->u_medida = 'UNDS';
                        $inventarioFisico_producto->codigo_barra = $producto['codigo_barra'];
                        $inventarioFisico_producto->cantidad = $producto['cantidad'];
                        $inventarioFisico_producto->save();
                        $i++;
                    }

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'result' => null,
                        'messages' => null
                    ]);

                }else{
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'result' => 'El conteo de inventario ha sido modificado previamente, no se pueden grabar los cambios',
                        'messages' => null
                    ]);
                }

            } catch (Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'result' => 'Error de base de datos',
                    'messages' => null
                ]);
            }


        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }


    public function reporte($ext, $id_inventario_fisico)
    {
        // echo $ext;
        //$ext = 'pdf';
        $os = array("pdf");
        if (in_array($ext, $os, true)) {
            $hora_actual = time();
            // Rutas para descarga Reportes local
            $input = env('APP_URL_REPORTES') . 'ReporteConteoFisicoInventario';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'ReporteConteoFisicoInventario';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'ReporteEntradainventario';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'ReporteEntradainventario';

            //Ajustes generales del sistema

            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'id_inventario_fisico' => $id_inventario_fisico,
                    /*'logo_empresa' => env('APP_URL_IMAGES') . $logo_empresa->valor,*/
                    'nombre_empresa' => $nombre_empresa->valor,
                    'id_empresa' => $usuario_empresa->id_empresa,
                ],
                'db_connection' => [
                    'driver' => 'postgres',
                    'username' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'host' => env('DB_HOST'),
                    'database' => env('DB_DATABASE'),
                    'port' => env('DB_PORT')
                ]
            ];

            $jasper = new PHPJasper;

            $jasper->process($input, $output, $options)->execute();


            //print_r( env('APP_URL_REPORTS').$logo_empresa->valor);
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext, $hora_actual . 'ReporteConteoFisicoVPrevia.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
            print_r($output);*/
        } else {
            return '';
        }
    }


    public function reporteComparativo($ext, $id_inventario_fisico)
    {
        // echo $ext;
        //$ext = 'pdf';
        $os = array("pdf");
        if (in_array($ext, $os, true)) {
            $hora_actual = time();
            // Rutas para descarga Reportes local
            $input = env('APP_URL_REPORTES') . 'ReporteConteoFisicoInventarioComparativo';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'ReporteConteoFisicoInventarioComparativo';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'ReporteEntradainventario';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'ReporteEntradainventario';

            //Ajustes generales del sistema

            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'id_inventario_fisico' => $id_inventario_fisico,
                    /*'logo_empresa' => env('APP_URL_IMAGES') . $logo_empresa->valor,*/
                    'nombre_empresa' => $nombre_empresa->valor,
                    'id_empresa' => $usuario_empresa->id_empresa,
                ],
                'db_connection' => [
                    'driver' => 'postgres',
                    'username' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'host' => env('DB_HOST'),
                    'database' => env('DB_DATABASE'),
                    'port' => env('DB_PORT')
                ]
            ];

            $jasper = new PHPJasper;

            $jasper->process($input, $output, $options)->execute();


            //print_r( env('APP_URL_REPORTS').$logo_empresa->valor);
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext, $hora_actual . 'ReporteEntradaInventario.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
            print_r($output);*/
        } else {
            return '';
        }
    }
}
