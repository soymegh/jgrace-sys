<?php



namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\Proveedores;
use App\Models\Inventario\TipoProveedor;
use App\Models\Admon\Departamentos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\PublicDepartamentos;
use Illuminate\Http\JsonResponse;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule,DB;
class ProveedoresControllers extends Controller
{


    /**
     * Busqueda de Cuentas Contables
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function buscar(Request $request, Proveedores $proveedores)
    {
        $proveedores = $proveedores->buscar($request);
        return response()->json([
            'results' => $proveedores
        ]);
    }

    public function generarReporte($ext)
    {
        // echo $ext;
        // $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/ListadoProveedores';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ListadoProveedores';
            $input = '/var/www/html/resources/reports/ListadoProveedores';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ListadoProveedores';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ListadoProveedores.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

    /**
     * Obtener una lista de Proveedores
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtener(Request $request, Proveedores $proveedores)
    {
        $proveedores = $proveedores->obtener($request);
        $tipos_proveedor = TipoProveedor::where('estado', 1)->orderBy('descripcion')->get();
        return response()->json([
            'status' => 'success',
            'result' => [
                'tipos_proveedor' => $tipos_proveedor,
                'total' => $proveedores->total(),
                'rows' => $proveedores->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Proveedores
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerProveedoresProducto(Request $request, Proveedores $proveedores)
    {
        $proveedores = $proveedores->obtenerProveedoresProducto($request);
        return response()->json([
            'status' => 'success',
            'result' => $proveedores,
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Roles sin ningun filtro
     *
     * @access  public
     * @param Request $request
     * @param Proveedores $proveedores
     * @return JsonResponse
     */

    public function obtenerTodos(Request $request, Proveedores $proveedores)
    {
        $proveedores = Proveedores::where('estado', 1)->orderby('id_proveedor')/*->where('id_proveedor','!=', 1)*/->get();
        return response()->json([
            'status' => 'success',
            'result' => $proveedores,
            'messages' => null
        ]);
    }


    /**
     * obtener Rol Especifico
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerProveedor(Request $request)
    {
        $rules = [
            'id_proveedor' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $proveedor = Proveedores::
            where('id_proveedor',$request->id_proveedor)
                ->with('proveedorTipo')->with('municipioProveedor')->get();

            $departamentos = Departamentos::with('municipiosDepartamento')->get();

            if(!empty($proveedor[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'proveedor' => $proveedor[0],
                        'departamentos' => $departamentos
                    ],
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_proveedor'=>["Datos no encontrados"]),
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
     * Crear un nuevo proveedor
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function registrar(Request $request)
    {
        $messages = [
            'numero_cedula.required_if' => 'El campo cédula es requerido para tipo de persona Natural',
            'numero_ruc.required_if' => 'El campo RUC es requerido para tipo de persona Juridíca',
        ];

        $rules = [
            'razon_social' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:200',
            'contacto_proveedor' => 'nullable|string|max:100',
            'tipo_persona' => 'required|integer|min:1|max:2',
            // 'numero_cedula' => 'nullable|required_if:tipo_persona,1|string|max:14',
            //'numero_ruc' => 'nullable||string|max:14',

            'numero_ruc' => [
                'nullable',
                'required_if:tipo_persona,2',
                'string',
                'max:14',
                Rule::unique('pgsql.inventario.proveedores')],

            'numero_cedula' => [
                'nullable',
                'required_if:tipo_persona,1',
                'string',
                'max:16',
                Rule::unique('pgsql.inventario.proveedores')],

            /*'tipo_proveedor' => 'required|array|min:1',
            'tipo_proveedor.id_tipo_proveedor' => 'required|integer|min:1',*/
            /*'clasificacion_contable' => 'required|integer|min:1|max:2',*/
            'nombre_comercial' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'telefono_contacto' => 'nullable|string|max:20',
            'correo_contacto' => 'nullable|string|max:50',
            'paguese_a' => 'required|string|max:100',
            'observaciones' => 'nullable|string|max:100',
            'municipio' => 'required|array|min:1',
            'municipio.id_municipio' => 'required|integer|min:1',
            'plazo_credito' => 'required|integer|min:0',
            /*'es_extranjero' => 'required|boolean',*/
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $proveedor = new Proveedores;
            $proveedor->direccion = $request->direccion;
            $proveedor->contacto_proveedor = $request->contacto_proveedor;

            if( $request->numero_ruc == null){
                $proveedor->numero_ruc = '';
            }else{
                $proveedor->numero_ruc = $request->numero_ruc;
            }
            $proveedor->es_extranjero = $request->es_extranjero;
            /*$proveedor->clasificacion_contable = $request->clasificacion_contable;*/
            $proveedor->clasificacion_contable = 1;
            /*$proveedor->id_tipo_proveedor = $request->tipo_proveedor['id_tipo_proveedor'];*/
            $proveedor->id_tipo_proveedor = 1;

            $codigo_nuevo = $proveedor->obtenerCodigoProveedor($request->clasificacion_contable);
            $str_length = 4;
            $str_length2= 2;
            $str = ($proveedor->es_extranjero?'I':'N').substr("0{$proveedor->id_tipo_proveedor}", -$str_length2).substr("000{$codigo_nuevo['secuencia']}", -$str_length);
            $proveedor->secuencia = $codigo_nuevo['secuencia'];
            if($request->clasificacion_contable == 1){
                $proveedor->codigo = 'P'.$str;
            } else if($request->clasificacion_contable == 2){
                $proveedor->codigo = 'A'.$str;
            }

            $proveedor->razon_social = $request->razon_social;

            $proveedor->id_municipio = $request->municipio['id_municipio'];
            $proveedor->numero_cedula = $request->numero_cedula;
            $proveedor->nombre_comercial = $request->nombre_comercial;
            $proveedor->telefono = $request->telefono;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->correo_contacto = $request->correo_contacto;
            $proveedor->paguese_a = $request->paguese_a;
            $proveedor->id_empresa = $usuario_empresa->id_empresa;
            $proveedor->observaciones = $request->observaciones;
            $proveedor->tipo_persona = $request->tipo_persona;
            $proveedor->plazo_credito = $request->plazo_credito;


            $proveedor->u_creacion = Auth::user()->name;
            $proveedor->estado = 1;
            $proveedor->save();

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
     * Actualizar Proveedor existente
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function actualizar(Request $request)
    {
        $messages = [
            'numero_cedula.required_if' => 'El campo cédula es requerido para tipo de persona Natural',
            'numero_ruc.required_if' => 'El campo RUC es requerido para tipo de persona Juridíca',
        ];


        $rules = [
            'id_proveedor' => 'required|integer|min:1',
            'razon_social' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:200',
            'contacto_proveedor' => 'nullable|string|max:100',
            'tipo_persona' => 'required|integer|min:1|max:2',
            'numero_cedula' => 'nullable|required_if:tipo_persona,1|string|max:16',
            'numero_ruc' => 'nullable|required_if:tipo_persona,2|string|max:14',

            'clasificacion_contable' => 'required|integer|min:1|max:2',
            'nombre_comercial' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'telefono_contacto' => 'nullable|string|max:20',
            'correo_contacto' => 'nullable|string|max:50',
            'paguese_a' => 'required|string|max:100',
            'observaciones' => 'nullable|string|max:100',
            'municipio_proveedor' => 'required|array|min:1',
            'municipio_proveedor.id_municipio' => 'required|integer|min:1',
            'plazo_credito' => 'required|integer|min:0',

            'es_extranjero' => 'required|boolean',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $proveedor = Proveedores::find($request->id_proveedor);
            $proveedor->direccion = $request->direccion;
            $proveedor->contacto_proveedor = $request->contacto_proveedor;
            if( $request->numero_ruc == null){
                $proveedor->numero_ruc = '';
            }else{
                $proveedor->numero_ruc = $request->numero_ruc;
            }

            $proveedor->razon_social = $request->razon_social;
            $proveedor->u_modificacion = Auth::user()->name;

            $proveedor->id_municipio = $request->municipio_proveedor['id_municipio'];
            $proveedor->numero_cedula = $request->numero_cedula;
            $proveedor->nombre_comercial = $request->nombre_comercial;
            $proveedor->telefono = $request->telefono;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->correo_contacto = $request->correo_contacto;
            $proveedor->paguese_a = $request->paguese_a;
            $proveedor->observaciones = $request->observaciones;
            $proveedor->tipo_persona = $request->tipo_persona;
            $proveedor->plazo_credito = $request->plazo_credito;
            $proveedor->es_extranjero = $request->es_extranjero;

            $proveedor->save();

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
     * Desactiva Proveedor
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_proveedor' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $proveedor = Proveedores::find($request->id_proveedor);
            $proveedor->estado = 0;
            $proveedor->u_modificacion = Auth::user()->name;
            $proveedor->save();

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
            'id_proveedor' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $proveedor = Proveedores::find($request->id_proveedor);
            $proveedor->estado = 1;
            $proveedor->u_modificacion = Auth::user()->name;
            $proveedor->save();

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

}
