<?php



namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Contabilidad\CentrosCostosIngresos;
use App\Models\Inventario\BodegaProductos;
use App\Models\Inventario\Bodegas;
use App\Models\Inventario\ProductosVistaVenta;
use App\Models\Inventario\TipoBodega;
use App\Models\Admon\Sucursales;
use App\Models\VentaClientes;
use App\Models\Ventas\Clientes;
use Illuminate\Support\Facades\Auth;
use PHPJasper\PHPJasper;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule,DB;
class BodegasController extends Controller
{
    public function buscar(Request $request, Bodegas $bodegas)
    {
        $bodegas = $bodegas->buscar($request);
        return response()->json([
            'results' => $bodegas
        ]);
    }
    /**
     * Obtener una lista de bodegas
     *
     * @access  public
     * @param
     * @return \Illuminate\Http\JsonResponse
     */

    public function obtener(Request $request, Bodegas $bodegas)
    {
        $bodegas = $bodegas->obtener($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $bodegas->total(),
                'rows' => $bodegas->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Roles sin ningun filtro
     *
     * @access  public
     * @param Request $request
     * @param Bodegas $bodegas
     * @return \Illuminate\Http\JsonResponse
     */

    public function obtenerTodas(Request $request, Bodegas $bodegas)
    {
        $bodegas = Bodegas::where('estado', 1)->get();
        return response()->json([
            'status' => 'success',
            'result' => $bodegas,
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Roles sin ningun filtro
     *
     * @access  public
     * @param
     * @return \Illuminate\Http\JsonResponse
     */

    public function obtenerBodegaProductos(Request $request)
    {

        /*if(Auth::user()->id_sucursal>0){
            $bodegas_entrantes =Bodegas::select('id_bodega','descripcion','estado','id_tipo_bodega')->where('estado', 1)->with(['productosBodega' => function($query) {
                $query->with('producto')->where('cantidad','>',0);
            }])  //->where('id_sucursal',Auth::user()->id_sucursal)->orWhere('id_bodega',1)
                ->whereNotIn('id_tipo_bodega',array(6,3))->orderby('descripcion')->get();
        }else{
            $bodegas_entrantes =Bodegas::select('id_bodega','descripcion','estado','id_tipo_bodega')->where('estado', 1)->with(['productosBodega' => function($query) {
                $query->with('producto')->where('cantidad','>',0);
            }])->whereNotIn('id_tipo_bodega',array(6,3))->orderby('descripcion')->get();
        }

        if(Auth::user()->id_sucursal>0){
            $bodegas =Bodegas::select('id_bodega','descripcion','estado','id_tipo_bodega')->where('estado', 1)->with(['productosBodega' => function($query) {
                $query->with('producto')->where('cantidad','>',0);
            }])->whereNotIn('id_tipo_bodega',array(6,4,3))
                ->where('id_sucursal',Auth::user()->id_sucursal)

                ->orderby('descripcion')->get();
        }else{
            $bodegas =Bodegas::select('id_bodega','descripcion','estado','id_tipo_bodega')->where('estado', 1)->with(['productosBodega' => function($query) {
                $query->with('producto')->where('cantidad','>',0);
            }])->whereNotIn('id_tipo_bodega',array(6,4,3))->get();
        }*/

        if(Auth::user()->id_sucursal>0){
            $bodegas =Bodegas::select('id_bodega','descripcion','estado','id_tipo_bodega')->where('estado', 1)->with(['productosBodega' => function($query) {
                $query->with('producto')->where('cantidad','>',0);
            }])  //->where('id_sucursal',Auth::user()->id_sucursal)->orWhere('id_bodega',1)
            ->whereNotIn('id_tipo_bodega',array(6,4,3))->orderby('descripcion')->get();
        }else{
            $bodegas =Bodegas::select('id_bodega','descripcion','estado','id_tipo_bodega')->where('estado', 1)->with(['productosBodega' => function($query) {
                $query->with('producto')->where('cantidad','>',0);
            }])->whereNotIn('id_tipo_bodega',array(6,4,3))->orderby('descripcion')->get();
        }

        if(Auth::user()->id_sucursal>0){
            $bodegas_entrantes =Bodegas::select('id_bodega','descripcion','estado','id_tipo_bodega')->where('estado', 1)->with(['productosBodega' => function($query) {
                $query->with('producto')->where('cantidad','>',0);
            }])->whereNotIn('id_tipo_bodega',array(6,3))
                ->where('id_sucursal',Auth::user()->id_sucursal)

                ->orderby('descripcion')->get();
        }else{
            $bodegas_entrantes =Bodegas::select('id_bodega','descripcion','estado','id_tipo_bodega')->where('estado', 1)->with(['productosBodega' => function($query) {
                $query->with('producto')->where('cantidad','>',0);
            }])->whereNotIn('id_tipo_bodega',array(6,3))->get();
        }


        $bodegasConsigna =Bodegas::select('id_bodega','descripcion','estado','id_tipo_bodega')->where('estado', 1)->where('id_tipo_bodega',6)->get();

        $clientes = Clientes::select(['id_cliente','estado','codigo','tipo_persona','numero_cedula','numero_ruc','nombre_comercial','plazo_credito',
            'id_vendedor'])
        ->where('estado',1)->get();

        return response()->json([
            'status' => 'success',
            'result' =>[
                'bodegas' => $bodegas,
                'bodegas_consigna' => $bodegasConsigna,
                'bodegas_entrantes' => $bodegas_entrantes,
                'clientes'=> $clientes
            ] ,
            'messages' => null
        ]);
    }

    public function obtenerBodegaProductosGarantia(Request $request, InventarioBodegaProductos $productos)
    {


        $productos = $productos->productosBodegaGarantia($request);


        return response()->json([
            'status' => 'success',
            'result' => [
                'productos' => $productos,
            ],
            'messages' => null
        ]);


    }

    public function obtenerBodegaProductosVenta(Request $request, BodegaProductos $productos, ProductosVistaVenta $producto_venta)
    {

        // Obtener productos para venta original
//        $productos = $productos->productosBodegaVenta($request);
        // Obtener productos para venta desde vista - modificado
        $producto_venta = $producto_venta->productosBodegaVenta($request);
        $servicios = new ProductosVistaVenta();
        $servicios = $servicios->serviciosVenta($request);


        return response()->json([
            'status' => 'success',
            'result' => [

                'productos' => $producto_venta,
                'servicios' => $servicios
            ],
            'messages' => null
        ]);
    }

    public function obtenerBodegaProductosRecuperados(Request $request, BodegaProductos $productos)
    {

        $productos = $productos->productosBodegaRecuperados($request);

        return response()->json([
            'status' => 'success',
            'result' => [
                'productos' => $productos,
            ],
            'messages' => null
        ]);
    }


    public function obtenerBodegaProductosObsoletos(Request $request, BodegaProductos $productos)
    {

        $productos = $productos->productosBodegaObsoletos($request);

        return response()->json([
            'status' => 'success',
            'result' => [
                'productos' => $productos,
            ],
            'messages' => null
        ]);
    }


    public function productosBodegaConsignacionCliente(Request $request, InventarioBodegaProductos $productos)
    {

        $productos = $productos->productosBodegaConsignacionCliente($request);
        $servicios = new InventarioProductosVistaVenta();

        // $servicios = $servicios->serviciosVenta($request);

        return response()->json([
            'status' => 'success',
            'result' => [
                'productos' => $productos,
                //     'servicios' => $servicios
            ],
            'messages' => null
        ]);
    }


    /**
     * obtener bodega Especifica
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerBodega(Request $request)
    {
        $rules = [
            'id_bodega' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $bodega = Bodegas::where('id_bodega',$request->id_bodega)
                ->with('sucursalBodega')
                ->with('tipoBodega')
                ->first();
            $sucursales = Sucursales::select(['id_sucursal','descripcion'])->get();
            $tipos_bodega = TipoBodega::select(['id_tipo_bodega','descripcion'])->where('estado', 1)->get();


            if(!empty($bodega)){
                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'bodega' => $bodega,
                        'sucursales' => $sucursales,
                        'tipos_bodega' => $tipos_bodega
                    ],
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_bodega'=>["Datos no encontrados"]),
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

    public function nuevo(Request $request)
    {
        $sucursales = Sucursales::select(['id_sucursal','descripcion'])->where('estado', 1)->get();
        $tipos_bodega = TipoBodega::select(['id_tipo_bodega','descripcion'])->where('estado', 1)->get();
        $bodegas = Bodegas::select(['id_bodega','descripcion'])->where('estado',1)->get();
        return response()->json([
            'status' => 'success',
            'result' => [
                'sucursales'=>$sucursales,
                'tipos_bodega'=>$tipos_bodega,
                'bodegas' => $bodegas
            ],
            'messages' => null
        ]);

    }

    public function obtenerBodegasConsignacion(Request $request)
    {
        $clientes = VentaClientes::select(['id_cliente','estado','codigo','tipo_persona','numero_cedula','numero_ruc','nombre_comercial','plazo_credito','permite_consignacion','aprobacion_consignacion','id_vendedor'
        ])->where('estado',1)->where('aprobacion_consignacion',2)->where('permite_consignacion',true)->get();

        return response()->json([
            'status' => 'success',
            'result' => [
                'clientes'=>$clientes,
            ],
            'messages' => null
        ]);

    }


    /**
     * Crear un nuevo rol
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function registrar(Request $request)
    {
        $rules = [
            'permite_venta' => 'required|boolean',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.inventario.bodegas')],
            'tipo_bodega' => 'required|array|min:1',
            'tipo_bodega.id_tipo_bodega' => 'required|integer|min:1',
            'sucursal' => 'required|array|min:1',
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $bodega = new Bodegas;
            $bodega->id_tipo_bodega = $request->tipo_bodega['id_tipo_bodega'];
            $bodega->permite_venta = $request->permite_venta;
            $bodega->descripcion = $request->descripcion;
            $bodega->id_sucursal = $request->sucursal['id_sucursal'];
            $bodega->id_empresa = $usuario_empresa->id_empresa;
            $bodega->u_creacion = Auth::user()->name;
            $bodega->estado = 1;
            $bodega->save();

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
     * Actualizar Rol existente
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_bodega'  => 'required|integer|min:1',
            'tipo_bodega' => 'required|array|min:1',
            'tipo_bodega.id_tipo_bodega' => 'required|integer|min:1',
            'permite_venta' => 'required|boolean',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.inventario.bodegas')->ignore($request->id_bodega,'id_bodega')],
            'sucursal_bodega' => 'required|array|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $bodega = Bodegas::find($request->id_bodega);
            $bodega->id_tipo_bodega = $request->tipo_bodega['id_tipo_bodega'];
            $bodega->permite_venta = $request->permite_venta;
            $bodega->descripcion = $request->descripcion;
            $bodega->id_sucursal = $request->sucursal_bodega['id_sucursal'];
            $bodega->u_modificacion = Auth::user()->name;
            $bodega->save();

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
     * Desactiva rol
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_bodega' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $bodega = Bodegas::find($request->id_bodega);
            $bodega->estado = 0;
            $bodega->u_modificacion = Auth::user()->usuario;
            $bodega->save();

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


    public function activar(Request $request)
    {
        $rules = [
            'id_bodega' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $bodega = Bodegas::find($request->id_bodega);
            $bodega->estado = 1;
            $bodega->u_modificacion = Auth::user()->usuario;
            $bodega->save();

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
    public function generarReporte($ext)
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
            $input = env('APP_URL_REPORTES') . 'Bodegas';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'Bodegas';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'Bodegas';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'Bodegas';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'Bodegas.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }
}
