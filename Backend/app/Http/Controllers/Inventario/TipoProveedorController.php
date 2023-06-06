<?php



namespace App\Http\Controllers\Inventario;


use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\TipoProveedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule,DB;
use App\Http\Controllers\Controller;
class TipoProveedorController extends Controller
{
    /**
     * Obtener una lista de tipos de tipos proveedores
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtener(Request $request, TipoProveedor $tipos_proveedor)
    {
        $tipos_proveedor = $tipos_proveedor->obtenerTiposProveedor($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $tipos_proveedor->total(),
                'rows' => $tipos_proveedor->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de tipos de tipos proveedores sin ningun filtro
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerTodos(Request $request, TipoProveedor $tipos_proveedor)
    {
        $tipos_proveedor = TipoProveedor::where('estado', 1)->orderBy('id_tipo_proveedor')/*->where('id_tipo_proveedor','!=', 1)->where('id_tipo_proveedor','!=', 2)*/->get();
        return response()->json([
            'status' => 'success',
            'result' => $tipos_proveedor,
            'messages' => null
        ]);
    }

    /**
     * obtener tipo de tipos proveedores Especifico
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerTipoProveedor(Request $request)
    {
        $rules = [
            'id_tipo_proveedor' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_proveedor = TipoProveedor::find($request->id_tipo_proveedor);

            if(!empty($tipo_proveedor)){
                return response()->json([
                    'status' => 'success',
                    'result' => $tipo_proveedor,
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_tipo_proveedor'=>["Datos no encontrados"]),
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
     * Crear un nuevo tipo de tipos proveedores
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
                Rule::unique('pgsql.inventario.tipo_proveedores')],
            'clasificacion_contable' => [
                'required',
                'integer',
                'min : 1',
                'max : 2'
            ],
        ];

        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_proveedor = new TipoProveedor;
            $tipo_proveedor->descripcion = $request->descripcion;
            $tipo_proveedor->clasificacion_contable = $request->clasificacion_contable;
            $tipo_proveedor->secuencia = 1;
            $tipo_proveedor->id_empresa = $usuario_empresa->id_empresa;
            $tipo_proveedor->estado = 1;
            $tipo_proveedor->u_grabacion = Auth::user(0)->name;
            $tipo_proveedor->save();

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
            'id_tipo_proveedor' => 'required|integer|min:1',
            'descripcion' =>  [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.inventario.tipo_proveedores')->ignore($request->id_tipo_proveedor,'id_tipo_proveedor')],
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_proveedor = TipoProveedor::find($request->id_tipo_proveedor);
            $tipo_proveedor->clasificacion_contable = $request->clasificacion_contable;
            $tipo_proveedor->descripcion = $request->descripcion;
            $tipo_proveedor->u_modificacion = Auth::user()->name;
            $tipo_proveedor->save();

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
            'id_tipo_proveedor' =>  'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_proveedor = TipoProveedor::find($request->id_tipo_proveedor);
            $tipo_proveedor->estado = 0;
            $tipo_proveedor->save();

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
            'id_tipo_proveedor' =>  'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_proveedor = TipoProveedor::find($request->id_tipo_proveedor);
            $tipo_proveedor->estado = 1;
            $tipo_proveedor->save();

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
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteTipoProveedor';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteTipoProveedor';
            $input = '/var/www/html/resources/reports/ReporteTipoProveedor';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteTipoProveedor';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteTipoProveedor.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }
}
