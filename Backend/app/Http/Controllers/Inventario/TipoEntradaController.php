<?php



namespace App\Http\Controllers\Inventario;


use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\TipoEntrada;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule,DB;
use App\Http\Controllers\Controller;
class TipoEntradaController extends Controller
{
    /**
     * Obtener una lista de tipos de entrada
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtener(Request $request, TipoEntrada $tipos_entrada)
    {
        $tipos_entrada = $tipos_entrada->obtenerTiposEntrada($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $tipos_entrada->total(),
                'rows' => $tipos_entrada->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de tipos de entrada sin ningun filtro
     *
     * @access  public
     * @param Request $request
     * @param TipoEntrada $tipos_entrada
     * @return JsonResponse
     */

    public function obtenerTodosTiposEntrada(Request $request, TipoEntrada $tipos_entrada)
    {
        $tipos_entrada = TipoEntrada::where('estado', 1)->whereIn('id_tipo_entrada', array(1))->get();
        return response()->json([
            'status' => 'success',
            'result' => $tipos_entrada,
            'messages' => null
        ]);
    }

    /**
     * obtener tipo de entrada Especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerTipoEntrada(Request $request)
    {
        $rules = [
            'id_tipo_entrada' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_entrada = TipoEntrada::where('id_tipo_entrada',$request->id_tipo_entrada)
                ->get();
            if(!empty($tipo_entrada[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => $tipo_entrada[0],
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_tipo_entrada'=>["Datos no encontrados"]),
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
     * Crear un nuevo tipo de entrada
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function registrar(Request $request)
    {
        $rules = [
            'descripcion' =>  [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.inventario.tipos_entradas')],
        ];

        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_entrada = new TipoEntrada;
            $tipo_entrada->descripcion = $request->descripcion;
            $tipo_entrada->estado = 1;
            $tipo_entrada->id_empresa  = $usuario_empresa->id_empresa;
            $tipo_entrada->u_grabacion = Auth::user()->name;
            $tipo_entrada->save();

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
            'id_tipo_entrada' => 'required|integer|min:1',
            'descripcion' =>  [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.inventario.tipos_entradas')->ignore($request->id_tipo_entrada,'id_tipo_entrada')],
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_entrada = TipoEntrada::find($request->id_tipo_entrada);
            $tipo_entrada->descripcion = $request->descripcion;
            $tipo_entrada->u_modificacion = Auth::user()->name;
            $tipo_entrada->save();

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
            'id_tipo_entrada' =>  'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_entrada = TipoEntrada::find($request->id_tipo_entrada);
            $tipo_entrada->estado = 0;
            $tipo_entrada->u_modificacion = Auth::user()->name;
            $tipo_entrada->save();

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
            'id_tipo_entrada' =>  'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_entrada = TipoEntrada::find($request->id_tipo_entrada);
            $tipo_entrada-> u_modificacion = Auth::user()->name;
            $tipo_entrada->estado = 1;
            $tipo_entrada->save();

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
            $input = env('APP_URL_REPORTES') . 'TipoEntradas';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'TipoEntradas';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'TipoEntradas';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'TipoEntradas';

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
