<?php



namespace App\Http\Controllers\CajaBanco;

use App\Models\Admon\Departamentos;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\CajaBanco\Bancos;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class BancosController extends Controller
{
    /**
     * Obtener una lista de bancos
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerBancos(Request $request, Bancos $bancos)
    {
        $bancos = $bancos->obtenerBancos($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $bancos->total(),
                'rows' => $bancos->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de bancos sin ningun filtro
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerTodosBancos(Request $request, Bancos $bancos)
    {
        $bancos = Bancos::where('activo', 1) ->with(['cuentasBancariasBanco' => function($query) {
            $query->with('monedaCuentaBancaria');}])->get();

        return response()->json([
            'status' => 'success',
            'result' => $bancos,
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

    public function obtenerBanco(Request $request)
    {
        $rules = [
            'id_banco' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $banco = Bancos::find($request->id_banco);

            if(!empty($banco)){
                return response()->json([
                    'status' => 'success',
                    'result' => $banco,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_banco'=>["Datos no encontrados"]),
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
     * @param
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $rules = [
            'descripcion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.cjabnco.bancos')/*->ignore($request->id_banco,'id_banco')*/]
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $banco = new Bancos;
            $banco->descripcion = $request->descripcion;
            $banco->estado = 1;
            $banco->u_creacion = Auth::user()->name;
            $banco->id_empresa = $usuario_empresa->id_empresa;
            $banco->save();

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
            'id_banco' => 'required|integer|min:1',
            'descripcion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.cjabnco.bancos')->ignore($request->id_banco,'id_banco')]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $banco = Bancos::find($request->id_banco);
            $banco->descripcion = $request->descripcion;
            $banco->u_modificacion = Auth::user()->name;
            $banco->save();

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
            'id_banco' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $banco = Bancos::find($request->id_banco);
            $banco->estado = 0;
            $banco->u_modificacion = Auth::user()->name;
            $banco->save();

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
            'id_banco' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $banco = Bancos::find($request->id_banco);
            $banco->estado = 1;
            $banco->u_modificacion = Auth::user()->name;
            $banco->save();

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
