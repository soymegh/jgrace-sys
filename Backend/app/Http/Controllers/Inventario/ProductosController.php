<?php



namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\BodegasProductos;
use App\Models\Inventario\Bodegas;
use App\Models\Inventario\EntradaProductos;
use App\Models\Inventario\Entradas;
use App\Models\Inventario\Marcas;
use App\Models\Inventario\Productos;
use App\Models\Inventario\Proveedores;
use App\Models\Inventario\UnidadMedida;
use App\Models\Admon\Areas;
use App\Models\Admon\Impuestos;
use App\Models\Admon\Sucursales;
use App\Models\RRHH\Trabajadores;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Image;
use PHPJasper\PHPJasper;
//use ValidatorAuth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\Console\Input\Input;

class ProductosController extends Controller
{

    public function nueva(Request $request)
    {

//        $proveedores = Proveedores::where('activo', 1)->orderby('id_proveedor')->select('id_proveedor','nombre_comercial','codigo','numero_ruc','numero_cedula')->get();
        $bodegas =Bodegas::where('estado', 1)->select('id_bodega','descripcion')->get();
        $unidades_medida = UnidadMedida::select('id_unidad_medida','unidad_medida','descripcion','activo')->where('estado',1)->get();


        return response()->json([
            'status' => 'success',
            'result' => [
                'bodegas'=> $bodegas,
                'unidades_medida'=> $unidades_medida,


            ],
            'messages' => null
        ]);
    }

    public function nuevoPS(Request $request)
    {

//        $proveedores = Proveedores::where('activo', 1)->orderby('id_proveedor')->select('id_proveedor','nombre_comercial','codigo','numero_ruc','numero_cedula')->get();
//        $bodegas =Bodegas::where('activo', 1)->select('id_bodega','descripcion')->get();
//        $impuestos = PublicImpuestos::select(['id_impuesto','descripcion'])->get();
//        $rubros = PublicRubrosServiciosMateriales::select(['id_rubro','descripcion','codigo','rubro_full'])->orderby('codigo')->get();
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $unidades_medida = UnidadMedida::select('id_unidad_medida','siglas','descripcion','estado')->where('estado',1)->where('id_empresa',$usuario_empresa->id_empresa)->get();
        $marcas = Marcas::select('id_marca','descripcion','estado')->where('estado',1)->where('id_empresa',$usuario_empresa->id_empresa)->get();


        return response()->json([
            'status' => 'success',
            'result' => [
//                'proveedores' => $proveedores,
//                'bodegas'=> $bodegas,
//                'impuestos'=> $impuestos,
//                'rubros'=> $rubros,
                'unidades_medida'=> $unidades_medida,
                'marcas'=> $marcas,
            ],
            'messages' => null
        ]);
    }

    public function buscarProductosBodega(Request $request, BodegasProductos $productos)
    {
        $productos = $productos->buscarProductosBodega($request);
        return response()->json([
            'results' => $productos
        ]);
    }

    public function buscarProductosBodegaVenta(Request $request, BodegasProductos $productos)
    {
        $productos = $productos->buscarProductosBodegaVenta($request);
        return response()->json([
            'results' => $productos
        ]);
    }


    /**
     * Busqueda de productos
     *
     * @access  public
     * @param Request $request
     * @param Productos $productos
     * @return JsonResponse
     * @author octaviom
     */

    public function buscarProductos(Request $request, Productos $productos)
    {
        $productos = $productos->buscarProductos($request);
        return response()->json([
            'results' => $productos
        ]);
    }

    public function buscarPS(Request $request, Productos $productos)
    {
        $productos = $productos->buscarPS($request);
        return response()->json([
            'results' => $productos
        ]);
    }

    /**
     * obtener productos
     *
     * @access  public
     * @param Request $request
     * @param Productos $productos
     * @return JsonResponse
     * @author octaviom
     */

    public function obtenerProductos(Request $request, Productos $productos)
    {
        $productos = $productos->obtenerProductos($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $productos->total(),
                'rows' => $productos->items()
            ],
            'messages' => null
        ]);
    }


    /**
     * Obtener una lista de productos de una bodega
     *
     * @access  public
     * @param Request $request
     * @param Productos $productos
     * @return JsonResponse
     * @author octaviom
     */

    public function obtenerProductosBodega(Request $request, Productos $productos)
    {
        $productos = $productos->obtenerProductosBodega($request);
        return response()->json([
            'status' => 'success',
            'result' => $productos,
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de productos de una bodega
     *
     * @access  public
     * @param Request $request
     * @param Productos $productos
     * @return JsonResponse
     */

    public function obtenerProductosValidos(Request $request, Productos $productos)
    {
        $productos = $productos->obtenerProductosValidos($request);
        return response()->json([
            'status' => 'success',
            'result' => $productos,
            'messages' => null
        ]);
    }


    /**
     * Obtener productos validos
     *
     * @access  public
     * @param Request $request
     * @param Productos $productos
     * @return JsonResponse
     * @author octaviom
     */

    public function obtenerProducto(Request $request, Productos $productos)
    {
        $rules = [
            'id_producto' => 'required|integer|min:1'
        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $productos = $productos->obtenerProducto($request);

            if(!empty($productos[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => $productos[0],
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_producto'=>["Datos no encontrados"]),
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
     * Registrar un nuevo Producto | Servicio
     *
     * @access    public
     * @param Request $request
     * @return JsonResponse
     * @author octaviom
     */

    public function registrarPS(Request $request)
    {
        $message =[
            'nombre_comercial.required' => 'El campo nombre del producto es requerido.',
            'descripcion.required' => 'El campo descripción de producto es requerido.',
            'unidad_medida.required_if' => 'La unidad de medida es requerida cuando el tipo de producto es "Producto".',
            'unidad_medida.id_unidad_medida.required_if' => 'El campo unidad de medida es requerido.'
        ];
        $rules = [
            'codigo_sistema' => 'required|string|max:50',
            'codigo_consecutivo'=> 'required_if:id_tipo_producto,1|integer|min:0',
            'nombre_comercial' => 'required|string|max:50',
            'descripcion' => 'required|string|max:100',

            'numero_parte' => 'nullable|string|max:30',
            //'dai' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            //'isc' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',

            'costo_estandar' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'precio_sugerido' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'precio_compra' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'precio_distribuidor' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'tipo_producto' => 'required|integer|min:1|max:4',
            'existencia_min' => 'required_if:tipo_producto,1|integer|min:0',
            'existencia_max' => 'required_if:tipo_producto,1|integer|min:0',
            'cantidad_inicial' => 'required_if:tipo_producto,1|integer|min:0',
            'codigo_barra'=> 'string|max:50|nullable',
            'imagen' => 'array|nullable',
            'unidad_medida' => 'required_if:tipo_producto,1|array|min:1',
            'unidad_medida.id_unidad_medida' => 'required_if:tipo_producto,1|integer|min:1',
            'marca' => 'required_if:id_tipo_producto,==,1|array',
            'marca.id_marca' => 'required_if:id_tipo_producto,1|integer|min:1',
            'porcentaje_ganancia' => 'required|numeric|min:0.0'

        ];

        $validator = Validator::make($request->all(), $rules,$message);
        if (!$validator->fails()) {

            try{
                DB::beginTransaction();
                $producto = new Productos;
                $producto->codigo_consecutivo = $request->codigo_consecutivo;
                $producto->codigo_sistema = $request->codigo_sistema;
                $producto->nombre_comercial = $request->nombre_comercial;
                $producto->descripcion = $request->descripcion;
                $producto->precio_distribuidor = $request->precio_distribuidor;
                $producto->precio_compra = $request->precio_compra;
                $producto->costo_estandar = $request->costo_estandar;
                $producto->costo_estandar_me = $request->costo_estandar;
                $producto->precio_sugerido = $request->precio_sugerido;
                $producto->id_unidad_medida = $request->unidad_medida['id_unidad_medida'];
                if ($request->tipo_producto === 1){
                    $producto->id_marca = $request->marca['id_marca'];
                }
                $producto->existencia_min = $request->existencia_min;
                $producto->existencia_max = $request->existencia_max;
                $producto->cantidad_inicial = $request->cantidad_inicial;
                $producto->id_tipo_producto = $request->tipo_producto; // 1- producto | 2-servicio
                $producto->tipo_servicio = $request->tipo_servicio; // 1- procesamiento e instalacion | 2- asesoria e importaciones
                $producto->codigo_barra = $request->codigo_barra;
                $producto->porcentaje_ganancia = $request->porcentaje_ganancia;
                $producto->id_impuesto = 2; // IVA
                $producto->u_creacion =Auth::user()->name;

                if ($request->avatar){
                    $name = time().'.' . explode('/', explode(':', substr($request->avatar, 0, strpos($request->avatar, ';')))[1])[1];
                    \Image::make($request->avatar)->save(public_path('img/products/').$name);
                    $request->merge(['avatar' => $name]);
                    $producto->imagen = $name;
                }


                $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();
                $producto->id_empresa = $usuario_empresa->id_empresa;
                $producto->estado = 1;
                $producto->save();

                if($request->tipo_producto === 1 && $producto->cantidad_inicial > 0) {

                    $nueva_bodega_sub = new BodegasProductos;
                    $nueva_bodega_sub->id_bodega=$request->bodega_inicial['id_bodega'];
                    $nueva_bodega_sub->id_producto=$producto->id_producto;
                    $nueva_bodega_sub->cantidad=0;
                    $nueva_bodega_sub->u_creacion =$producto->u_creacion;
                    $nueva_bodega_sub->save();


                    $entrada = new Entradas;
                    $entrada->codigo_entrada = Entradas::max('id_entrada') + 1;
                    $entrada->id_tipo_entrada = 1;
                    $entrada->fecha_entrada = date('Y-m-d');
                    $entrada->id_proveedor = null;
                    $entrada->id_bodega = $nueva_bodega_sub->id_bodega;
                    $entrada->descripcion_entrada = 'Registramos Inventario Inicial del producto  ' . $producto->nombre_comercial;
                    $entrada->u_creacion = Auth::user()->name;
                    $entrada->estado = 1;
                    $entrada->save();

                    $entrada_producto = new EntradaProductos;
                    $entrada_producto->id_entrada = $entrada->id_entrada;
                    $entrada_producto->id_bodega_producto = $nueva_bodega_sub->id_bodega_producto;
                    $entrada_producto->codigo_producto = $producto->codigo_sistema;
                    $entrada_producto->descripcion_producto = $producto->descripcion;
                    $entrada_producto->unidad_medida = 'UNDS';
                    $entrada_producto->precio_unitario = $producto->costo_estandar;
                    $entrada_producto->cantidad_solicitada = $producto->cantidad_inicial;
                    $entrada_producto->cantidad_recibida = 0;
                    $entrada_producto->cantidad_faltante = 0;
                    $entrada_producto->u_creacion = $entrada->u_creacion;
                    $entrada_producto->save();


                    /*$movimiento_producto = new InventarioMovimientosProductos();
                    $movimiento_producto->id_bodega_producto =$nueva_bodega_sub->id_bodega_producto;
                    $movimiento_producto->fecha_movimiento =  date("Y-m-d H:i:s");
                    $movimiento_producto->descripcion_movimiento = 'Inventario Inicial ' . $producto->codigo_sistema;
                    $movimiento_producto->identificador_origen_movimiento = $producto->id_producto;
                    $movimiento_producto->tipo_movimiento = 3;
                    $movimiento_producto->cantidad_movimiento = $producto->cantidad_inicial;
                    $movimiento_producto->costo = $request->costo_estandar;
                    $movimiento_producto->usuario_registra = $producto->u_creacion;
                    $movimiento_producto->save();*/
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


    /**
     * obtener Codigo Producto
     *
     * @access  public
     * @param Request $request
     * @param Productos $productos
     * @return JsonResponse
     * @author octaviom
     */

    public function obtenerCodigoProducto(Request $request, Productos $productos)
    {
        $rules = [
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $codigo = $productos->obtenerCodigoProducto($request);
            return response()->json([
                'status' => 'success',
                'result' => $codigo[0],
                'messages' => null
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    /**
     * Actualizar producto
     *
     * @access    public
     * @param Request $request
     * @return JsonResponse
     * @author octaviom
     */

    public function actualizarPS(Request $request)
    {
        $rules = [
            'id_producto' => 'required|integer|min:1',

            'nombre_comercial' => 'required|string|max:50',
            'descripcion' => 'required|string|max:100',

            'costo_estandar' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'precio_sugerido' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'precio_compra' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'precio_distribuidor' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'id_tipo_producto' => 'required|integer|min:1|max:2',
            'existencia_min' => 'required_if:tipo_producto,1|integer|min:0',
            'existencia_max' => 'required_if:tipo_producto,1|integer|min:0',
            'codigo_barra'=> 'string|max:50|nullable',
            'unidad_medida' => 'required_if:tipo_producto,1|array|min:1',
            'unidad_medida.id_unidad_medida' => 'required_if:tipo_producto,1|integer|min:1',
            'marca' => 'required_if:id_tipo_producto,==,1|min:0',
            'marca.id_marca' => 'required_if:id_tipo_producto,1|integer|min:0',
            'porcentaje_ganancia' => 'required|numeric|min:0.0',
        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try{
                DB::beginTransaction();
                $producto = Productos::findOrFail($request->id_producto);

                $producto->codigo_sistema = $request->codigo_sistema;
                $producto->nombre_comercial = $request->nombre_comercial;
                $producto->descripcion = $request->descripcion;

                $producto->costo_estandar = $request->costo_estandar;
                $producto->costo_estandar_me = $request->costo_estandar;
                $producto->precio_distribuidor = $request->precio_distribuidor;
                $producto->precio_compra = $request->precio_compra;
                $producto->precio_sugerido = $request->precio_sugerido;
                $producto->existencia_min = $request->existencia_min;
                $producto->existencia_max = $request->existencia_max;
                $producto->codigo_barra = $request->codigo_barra;
                $producto->porcentaje_ganancia = $request->porcentaje_ganancia;
                $producto->id_unidad_medida = $request->unidad_medida['id_unidad_medida'];
                if ($request->id_tipo_producto === 1){
                    $producto->id_marca = $request->marca['id_marca'];
                }

                //guardar imagen funcional
/*                if ($request->imagen){
                    $name = time().'.' . explode('/', explode(':', substr($request->imagen, 0, strpos($request->imagen, ';')))[1])[2];
                    \Image::make($request->imagen)->save(public_path('img/products/').$name);
                    $request->merge(['imagen' => $name]);
                }
                $producto->imagen = $name;*/

                if ($request->avatar && $request->imagen_nueva === true){
                    $name = time().'.' . explode('/', explode(':', substr($request->avatar, 0, strpos($request->avatar, ';')))[1])[1];
                    \Image::make($request->avatar)->save(public_path('img/products/').$name);
                    $request->merge(['avatar' => $name]);
                    $producto->imagen = $name;
                }



                $producto->save();

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

    /**
     * Activa Producto
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     * @author octaviom
     */
    public function activaProducto(Request $request)
    {
        $rules = [
            'id_producto' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $producto = Productos::find($request->id_producto);
            $producto->estado = 1;
            $producto->save();

            return response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    /**
     * Desactiva Producto
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     * @author octaviom
     */
    public function desactivaProducto(Request $request)
    {
        $rules = [
            'id_producto' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $producto = Productos::find($request->id_producto);
            $producto->estado = 0;
            $producto->save();

            return response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    public function generarReporte($ext, Request $request)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;
            $input = env('APP_URL_REPORTES') . 'ArticulosServicios';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'ArticulosServicios';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'ArticulosServicios';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'ArticulosServicios';

            if($ext == 'xls'){
                $input = $input.'XLS.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES').$logo_empresa->valor,
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
            /*header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext ,$hora_actual. 'ArticulosyServicios.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }


    public function generarReporteFechaActivacion(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'anioInicial' => 'required|integer',
            'anioFinal'=> 'required|integer'
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os))  {
                $hora_actual = time();
                // $input = 'C:/xampp/htdocs/resources/reports/FechaActivacion';
                //$output = 'C:/xampp/htdocs/resources/reports/' . $hora_actual . 'FechaActivacion';
                $input = '/var/www/html/resources/reports/FechaActivacion';
                $output = '/var/www/html/resources/reports/'.$hora_actual.'FechaActivacion';
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/FechaActivacion';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'FechaActivacion';


                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'AnioInicial' => $request->anioInicial,
                        'AnioFinal' => $request->anioFinal
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

                /*header('Content-Description: File Transfer');
                header('Content-Type: multipart/form-data;boundary="boundary"');
                header('Content-Disposition: form-data; filename=' . $hora_actual. 'Accesos.' . $ext);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Length: ' . filesize($output . '.' . $ext));
                flush();
                readfile($output . '.' . $ext);*/
                // unlink($output . '.' . $ext);

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                    print_r($output);*/

                return response()->download($output . '.' . $request->extension, $hora_actual . 'FechaActivacion.' . $request->extension, $headers);
            }else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        }else {
            return '';
        }
    }



    public function generarReporteExistenciaProducto(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'id_sucursal' => 'required|integer'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                // $input = 'C:/xampp/htdocs/resources/reports/ExistenciaPorSucursal';
                //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ExistenciaPorSucursal';
                $input = '/var/www/html/resources/reports/ExistenciaPorSucursal';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'ExistenciaPorSucursal';


                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'id_sucursal' => $request->id_sucursal,
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
                /*header('Content-Description: File Transfer');
                header('Content-Type: multipart/form-data;boundary="boundary"');
                header('Content-Disposition: form-data; filename=' . $hora_actual. 'Accesos.' . $ext);
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Content-Length: ' . filesize($output . '.' . $ext));
                flush();
                readfile($output . '.' . $ext);*/
                // unlink($output . '.' . $ext);

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ExistenciaPorSucursal.' . $request->extension, $headers);

                /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                    print_r($output);*/
            }  else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
        }else {
            return '';
        }
    }




}
