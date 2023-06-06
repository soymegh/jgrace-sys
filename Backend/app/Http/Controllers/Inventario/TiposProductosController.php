<?php



namespace App\Http\Controllers\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\TipoProductos;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use  Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
class TiposProductosController extends Controller
{
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param Request $request
     * @param TipoProductos $datos
     * @return JsonResponse
     */

    public function obtener(Request $request, TipoProductos $datos)
    {
        $datos = $datos->obtenerTipoProducto($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $datos->total(),
                'rows' => $datos->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de zonas sin paginacion
     *
     * @access  public
     * @param Request $request
     * @param TipoProductos $datos
     * @return JsonResponse
     */

    public function obtenerTodas(Request $request, TipoProductos $datos)
    {
        $datos = TipoProductos::orderby('id_tipo_producto')->get();
        return response()->json([
            'status' => 'success',
            'result' => $datos,
            'messages' => null
        ]);
    }

    /**
     * obtener Rol Especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerTipoProducto(Request $request)
    {
        $rules = [
            'id_tipo_producto'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $datos = TipoProductos::find($request->id_tipo_producto);

            if(!empty($datos)){
                return response()->json([
                    'status' => 'success',
                    'result' => $datos,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_tipo_producto'=>["Datos no encontrados"]),
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
     * Crear un nuevo rol
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $rules = [
            'descripcion' => 'required|string|max:100|'
        ];$usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tiposProductos = new TipoProductos();
            $tiposProductos->descripcion = $request->descripcion;

           /* $codigo_nuevo = $zona->obtenerCodigoZona();
            $str_length = 3;
            $str = substr("00{$codigo_nuevo['secuencia']}", -$str_length);*/

            /*$zona->codigo = $str;*/
            $tiposProductos->id_empresa = $usuario_empresa->id_empresa;
            $tiposProductos->u_creacion = Auth::user()->name;
            $tiposProductos->u_modificacion = Auth::user()->name;
            $tiposProductos->estado = 1;
            $tiposProductos->save();

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
     * @return JsonResponse
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'descripcsion' => [
                'required',
                'string',
                'max:100',
            ],
        ];



        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipoProducto = TipoProductos::find($request->id_tipo_producto);
            $tipoProducto->descripcion = $request->descripcion;
            $tipoProducto->id_empresa = 1;
            $tipoProducto->u_crecion = Auth::user()->name;
            $tipoProducto->u_modificacion = Auth::user()->name;
            $tipoProducto->save();

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
     * @return JsonResponse
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_tipo_producto' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipoProducto = TipoProductos::find($request->id_tipo_producto);
            $tipoProducto->estado = 0;
            $tipoProducto->save();

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
     * Activa rol
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     */

    public function activar(Request $request)
    {
        $rules = [
            'id_tipo_producto' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipoProducto = TipoProductos::find($request->id_tipo_producto);
            $tipoProducto->estado = 1;
            $tipoProducto->save();

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
        // $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteZonas';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteZonas';
            $input = '/var/www/html/resources/reports/ReporteZonas';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteZonas';

            $options = [
                'format' => [$ext],
                'locale' => 'en',
                'params' => [],
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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteZonas.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }
}
