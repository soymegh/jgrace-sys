<?php



namespace App\Http\Controllers\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\Marcas;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule,DB;
use App\Http\Controllers\Controller;
class MarcasController extends Controller
{
    /**
     * Obtener una lista de cargos
     *
     * @access  public
     * @param Request $request
     * @param Marcas $marcas
     * @return JsonResponse
     */

    public function obtener(Request $request, Marcas $marcas)
    {
        $marcas = $marcas->obtener($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $marcas->total(),
                'rows' => $marcas->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de cargos sin ningun filtro
     *
     * @access  public
     * @param Request $request
     * @param Marcas $marcas
     * @return JsonResponse
     */

    public function obtenerTodasMarcas(Request $request, Marcas $marcas)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $marcas = Marcas::where('id_empresa', $usuario_empresa->id_empresa)->where('estado',1)->get();
        return response()->json([
            'status' => 'success',
            'result' => $marcas,
            'messages' => null
        ]);
    }

    /**
     * obtener tipo de Salida Especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerMarca(Request $request)
    {
        $rules = [
            'id_marca' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cargo = Marcas::find($request->id_marca);

            if(!empty($cargo)){
                return response()->json([
                    'status' => 'success',
                    'result' => $cargo,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_marca'=>["Datos no encontrados"]),
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
     * Crear un nuevo tipo de Salida
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $rules = [
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.inventario.marcas')]
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cargo = new Marcas;
            $cargo->id_empresa = $usuario_empresa->id_empresa;
            $cargo->descripcion = $request->descripcion;
            $cargo->u_creacion = Auth::user()->name;
            $cargo->estado = 1;
            $cargo->save();

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
     * @param Request $request
     * @return JsonResponse
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_marca' => 'required|integer|min:1',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.inventario.marcas')->ignore($request->id_marca,'id_marca')]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cargo = Marcas::find($request->id_marca);
            $cargo->descripcion = $request->descripcion;
            $cargo->u_modificacion = Auth::user()->name;
            $cargo->save();

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

    public function activar(Request $request){
        $rules = [
          'id_marca' => 'required|integer|min:1',
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()){
            $marca = Marcas::find($request->id_marca);
            $marca->estado = 1;
            $marca->u_modificacion = Auth::user()->name;
            $marca->save();

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

    public function desactivar(Request $request){
        $rules = [
          'id_marca' => 'required|integer|min:1',
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()){
            $marca = Marcas::find($request->id_marca);
            $marca->estado = 0;
            $marca->u_modificacion = Auth::user()->name;
            $marca->save();

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
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteMarcas';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteMarcas';
            $input = '/var/www/html/resources/reports/ReporteMarcas';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteMarcas';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteMarcas.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }
}
