<?php



namespace App\Http\Controllers\Inventario;

use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\UnidadMedida;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule,DB;
use App\Http\Controllers\Controller;
class UnidadMedidaController extends Controller
{
    /**
     * Obtener una lista de tipos de entrada
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtener(Request $request, UnidadMedida $unidades_medida)
    {
        $unidades_medida = $unidades_medida->obtener($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $unidades_medida->total(),
                'rows' => $unidades_medida->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de tipos de entrada sin ningun filtro
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerTodos(Request $request, UnidadMedida $unidades_medida)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
        $unidades_medida = UnidadMedida::where('estado',1)->where('id_empresa',$usuario_empresa->id_empresa)->get();
        return response()->json([
            'status' => 'success',
            'result' => $unidades_medida,
            'messages' => null
        ]);
    }

    /**
     * obtener tipo de entrada Especifico
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     */

    public function obtenerUnidadMedida(Request $request)
    {
        $rules = [
            'id_unidad_medida' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $unidad_medida = UnidadMedida::where('id_unidad_medida',$request->id_unidad_medida)
                ->get();

            if(!empty($unidad_medida[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => $unidad_medida[0],
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_unidad_medida'=>["Datos no encontrados"]),
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
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $rules = [
            'siglas' => [
                'required',
                'string',
                'max:3',
                Rule::unique('pgsql.inventario.unidades_medidas')],
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.inventario.unidades_medidas')],
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $unidad_medida = new UnidadMedida;
            $unidad_medida->siglas = $request->siglas;
            $unidad_medida->descripcion = $request->descripcion;
            $unidad_medida->estado = 1;
            $unidad_medida->id_empresa = $usuario_empresa->id_empresa;
            $unidad_medida->u_grabacion = Auth::user()->name;
            $unidad_medida->save();

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
            'id_unidad_medida' => 'required|integer|min:1',
            'siglas' => [
                'required',
                'string',
                'max:3',
                Rule::unique('pgsql.inventario.unidades_medidas')->ignore($request->id_unidad_medida,'id_unidad_medida')],
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.inventario.unidades_medidas')->ignore($request->id_unidad_medida,'id_unidad_medida')],
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $unidad_medida = UnidadMedida::find($request->id_unidad_medida);
            $unidad_medida->siglas = $request->siglas;
            $unidad_medida->descripcion = $request->descripcion;
            $unidad_medida->estado = 1;
            $unidad_medida->u_modificacion = Auth::user()->name;
            $unidad_medida->save();

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
            'id_unidad_medida' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $unidad_medida = UnidadMedida::find($request->id_unidad_medida);
            $unidad_medida->estado = 0;
            $unidad_medida->u_modificacion = Auth::user()->name;
            $unidad_medida->save();

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
            'id_unidad_medida' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $unidad_medida = UnidadMedida::find($request->id_unidad_medida);
            $unidad_medida->estado = 1;
            $unidad_medida->u_modificacion = Auth::user()->name;
            $unidad_medida->save();
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
            $input = env('APP_URL_REPORTES') . 'UnidadesMedidas';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'UnidadesMedidas';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'UnidadesMedidas';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'UnidadesMedidas';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'UnidadesMedidas.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }
}
