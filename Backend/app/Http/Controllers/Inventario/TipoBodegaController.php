<?php



namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\TipoBodega;
use App\Models\PublicDepartamentos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule,DB;
class TipoBodegaController extends Controller
{


    /**
     * Obtener una lista de tipos de bodega
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtener(Request $request, TipoBodega $tipos_bodega)
    {
        $tipos_bodega = $tipos_bodega->obtenerTiposBodega($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $tipos_bodega->total(),
                'rows' => $tipos_bodega->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de tipos de bodega sin ningun filtro
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerTodos(Request $request, TipoBodega $tipos_bodega)
    {
        $tipos_bodega = TipoBodega::where('estado', 1)/*->where('id_tipo_bodega','!=', 1)->where('id_tipo_bodega','!=', 2)*/->get();
        return response()->json([
            'status' => 'success',
            'result' => $tipos_bodega,
            'messages' => null
        ]);
    }

    /**
     * obtener tipo de bodega Especifico
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerTipoBodega(Request $request)
    {
        $rules = [
            'id_tipo_bodega' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_bodega = TipoBodega::where('id_tipo_bodega',$request->id_tipo_bodega)->get();
            if(!empty($tipo_bodega[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => $tipo_bodega[0],
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_tipo_bodega'=>["Datos no encontrados"]),
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
     * Crear un nuevo tipo de bodega
     *
     * @access  public
     * @param
     * @return \Illuminate\Http\JsonResponse
     */

    public function registrar(Request $request)
    {
        $rules = [
            'descripcion' =>  [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.inventario.tipo_bodegas')],
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_bodega = new TipoBodega;
            $tipo_bodega->descripcion = $request->descripcion;
            $tipo_bodega->id_empresa = 1;
            $tipo_bodega->estado = $usuario_empresa->id_empresa;
            $tipo_bodega->u_grabacion = Auth::user()->name;
            $tipo_bodega->save();

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
     * @return \Illuminate\Http\JsonResponse
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_tipo_bodega' => 'required|integer|min:1',
            'descripcion' =>  [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.inventario.tipo_bodegas')->ignore($request->id_tipo_bodega,'id_tipo_bodega')],
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_bodega = TipoBodega::find($request->id_tipo_bodega);
            $tipo_bodega->descripcion = $request->descripcion;
            $tipo_bodega->u_modificacion = Auth::user()->usuario;
            $tipo_bodega->save();

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
     * @return \Illuminate\Http\JsonResponse
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_tipo_bodega' =>  'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_bodega = TipoBodega::find($request->id_tipo_bodega);
            $tipo_bodega->estado = 0;
            $tipo_bodega->save();

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
            'id_tipo_bodega' =>  'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_bodega = TipoBodega::find($request->id_tipo_bodega);
            $tipo_bodega->estado = 1;
            $tipo_bodega->save();

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
            //$input = 'C:/xampp/htdocs/resources/reports/TipoBodega';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'TipoBodega';
            $input = '/var/www/html/resources/reports/TipoBodega';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'TipoBodega';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteTipoBodega.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

}
