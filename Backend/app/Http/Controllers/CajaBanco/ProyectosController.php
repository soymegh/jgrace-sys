<?php



namespace App\Http\Controllers\CajaBanco;

use App\Models\Admon\Departamentos;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\CajaBanco\Bancos;
use App\Models\CajaBanco\Proyectos;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ProyectosController extends Controller
{
    /**
     * Obtener una lista de proyectos
     *
     * @access  public
     * @param Request $request
     * @param Proyectos $proyectos
     * @return JsonResponse
     */

    public function obtenerProyectos(Request $request, Proyectos $proyectos)
    {
        $proyectos = $proyectos->obtenerProyectos($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $proyectos->total(),
                'rows' => $proyectos->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de proyecto sin ningún filtro
     *
     * @access  public
     * @param Request $request
     * @param Proyectos $proyectos
     * @return JsonResponse
     */

    public function obtenerTodosProyectos(Request $request, Proyectos $proyectos)
    {
        $proyectos = Proyectos::where('estado', 1)->get();

        return response()->json([
            'status' => 'success',
            'result' => $proyectos,
            'messages' => null
        ]);
    }

    /**
     * Obtener recibos de clientes por proyecto
     * @param Request $request
     * @param Proyectos $proyectos
     * @return JsonResponse
     */
    public function obtenerProyectosCliente(Request $request, Proyectos $proyectos)
    {
        $proyectosClientes = $proyectos->obtenerProyectosCliente($request);

        return response()->json([
            'status' => 'success',
            'result' => $proyectosClientes,
            'messages' => null
        ]);
    }

    /**
     * obtener proyecto especifico
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     */

    public function obtenerProyecto(Request $request)
    {
        $rules = [
            'id_proyecto' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $proyecto = Proyectos::where('id_proyecto',$request->id_proyecto)->with('clienteProyecto')->first();

            if(!empty($proyecto)){
                return response()->json([
                    'status' => 'success',
                    'result' => $proyecto,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_proyecto'=>["Datos no encontrados"]),
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
     * Crear un nuevo proyecto
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
                'max:50',
                Rule::unique('pgsql.cuentasxcobrar.proyectos')/*->ignore($request->id_banco,'id_banco')*/]
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $proyecto = new Proyectos();
            $proyecto->descripcion = $request->descripcion;
            $proyecto->id_cliente = $request->cliente['id_cliente'];
            $proyecto->monto = $request->monto;
            $proyecto->observacion = $request->observacion;
            $proyecto->estado = 1;
            $proyecto->u_creacion = Auth::user()->name;
            $proyecto->id_empresa = $usuario_empresa->id_empresa;
            $proyecto->save();

            return response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
        }

        return response()->json([
            'status' => 'error',
            'result' => $validator->messages(),
            'messages' => null
        ]);
    }

    /**
     * Actualizar proyecto existente
     * @access  public
     * @param Request $request
     * @return JsonResponse
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_proyecto' => 'required|integer|min:1',
            'descripcion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.cuentasxcobrar.proyectos')->ignore($request->id_proyecto,'id_proyecto')]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $proyecto = Proyectos::find($request->id_proyecto);
            $proyecto->descripcion = $request->descripcion;
            $proyecto->id_cliente = $request->cliente_proyecto['id_cliente'];
            $proyecto->monto = $request->monto;
            $proyecto->observacion = $request->observacion;
            $proyecto->u_modificacion = Auth::user()->name;
            $proyecto->save();

            return response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
        }

        return response()->json([
            'status' => 'error',
            'result' => $validator->messages(),
            'messages' => null
        ]);
    }

    /**
     * Desactiva proyecto
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_proyecto' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $proyecto = Proyectos::find($request->id_proyecto);
            $proyecto->estado = 0;
            $proyecto->u_modificacion = Auth::user()->name;
            $proyecto->save();

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
            'id_proyecto' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $proyecto = Proyectos::find($request->id_proyecto);
            $proyecto->estado = 1;
            $proyecto->u_modificacion = Auth::user()->name;
            $proyecto->save();

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
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteBancos';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteBancos';
            $input = '/var/www/html/resources/reports/ReporteBancos';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteBancos';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteBancos.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }
}
