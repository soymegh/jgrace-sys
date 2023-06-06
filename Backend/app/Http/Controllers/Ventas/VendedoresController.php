<?php



namespace App\Http\Controllers\Ventas;

use App\Models\Admon\Departamentos;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\Marcas;
//use App\Models\RRHHTrabajadores;
use App\Models\Ventas\Vendedores;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class VendedoresController extends Controller
{


    /**
     * Busqueda de Clientes
     *
     * @access  public
     * @param
     * @return \Illuminate\Http\JsonResponse
     */

    public function buscar(Request $request, Vendedores $vendedores)
    {
        $vendedores = $vendedores->buscar($request);
        return response()->json([
            'results' => $vendedores
        ]);
    }

    public function obtenerVendedor(Request $request, Vendedores $vendedores){
        $rules = [
            'id_vendedor' => 'required|integer|min:1'
        ];
        $validator = Validator::make($request->all(),$rules);
        if(!$validator->fails()){
            $vendedor = Vendedores::find($request->id_vendedor);
            if(!empty($vendedor)){
                return response()->json([
                   'status' => 'success',
                    'result' => $vendedor,
                    'messages' => null
                ]);
            }else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_vendedor'=>["Datos no encontrados"]),
                    'messages' => null
                ]);
            }
        }else{
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    /**
     * Obtener una lista de Clientes
     *
     * @access  public
     * @param
     * @return \Illuminate\Http\JsonResponse
     */

    public function obtener(Request $request, Vendedores $vendedores)
    {
        $vendedores = $vendedores->obtenerVendedores($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $vendedores->total(),
                'rows' => $vendedores->items()
            ],
            'messages' => null
        ]);
    }


    /**
     * obtener Cliente Especifico
     *
     * @access  public
     * @param
     * @return \Illuminate\Http\JsonResponse
     */

    public function obtenerCliente(Request $request)
    {
        $rules = [
            'id_cliente' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $cliente = Vendedores::
            where('id_cliente',$request->id_cliente)
                ->with('zonaCliente')->with('tipoCliente')->with('municipioCliente')->with('impuestoCliente')->get();

            $departamentos = Departamentos::with('municipiosDepartamento')->get();
            /*$zonas = PublicZonas::select(['id_zona','descripcion'])->get();*/
            /*$impuestos = PublicImpuestos::select(['id_impuesto','descripcion'])->get();*/
           /* $tipos_clientes = VentaTiposCliente::select(['id_tipo_cliente','descripcion'])->get();*/

            if(!empty($cliente[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'cliente' => $cliente[0],
                        'departamentos' => $departamentos,
                       /* 'impuestos' => $impuestos,
                        'tipos_clientes' => $tipos_clientes,
                        'zonas' => $zonas*/
                    ],
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_cliente'=>["Datos no encontrados"]),
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
     * @return \Illuminate\Http\JsonResponse
     */

    public function registrar(Request $request)
    {

        $rules = [
            'nombre_completo' => 'required|string|max:100',
            'direccion' => 'nullable|string|max:100',
            'telefono' => 'nullable|integer',
            'email' => 'nullable|string|max:50',
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try{
                DB::beginTransaction();

                $vendedor = new Vendedores();
                $vendedor->nombre_completo = $request->nombre_completo;
                $vendedor->direccion = $request->direccion;
                $vendedor->email = $request->email;
                $vendedor->telefono = $request->telefono;
                $vendedor->u_creacion = Auth::user()->name;
                $vendedor->id_empresa = $usuario_empresa->id_empresa;
                $vendedor->estado = 1;
                $vendedor->save();

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);
            } catch (Exception $e){
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'result' => 'Error de base de datos',
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
            'nombre_completo' => 'required|string|max:100',
            'direccion' => 'nullable|string|max:100',
            'telefono' => 'nullable|integer',
            'email' => 'nullable|string|max:50',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $vendedor = Vendedores::find($request->id_vendedor);
            $vendedor->nombre_completo = $request->nombre_completo;
            $vendedor->direccion = $request->direccion;
            $vendedor->email = $request->email;
            $vendedor->telefono = $request->telefono;
            $vendedor->u_modificacion = Auth::user()->name;
            $vendedor->save();

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
            'id_vendedor' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $vendedor = Vendedores::find($request->id_vendedor);
            $vendedor->estado = 0;
            $vendedor->u_modificacion = Auth::user()->name;
            $vendedor->save();

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
            'id_vendedor' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $vendedor = Vendedores::find($request->id_vendedor);
            $vendedor->estado = 1;
            $vendedor->u_modificacion = Auth::user()->name;
            $vendedor->save();

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

    public function nuevo(Request $request)
    {
        $trabajadores = RRHHTrabajadores::select('id_trabajador','primer_apellido','primer_nombre','segundo_apellido','segundo_nombre','codigo',DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS nombre_completo"))->with('trabajadorDetalles')->get();

        if(!empty($request->id_trabajador))
        {
            $trabajador = RRHHTrabajadores::where('id_trabajador',$request->id_trabajador)->select('id_trabajador','primer_apellido','primer_nombre','segundo_apellido','segundo_nombre','codigo',DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS nombre_completo"))->with('trabajadorDetalles')->first();
            return response()->json([
                'status' => 'success',
                'result' => [
                    'trabajadores' => $trabajadores,
                    'trabajador' => $trabajador,

                ],
                'messages' => null
            ]);
        }
        else{
            return response()->json([
                'status' => 'success',
                'result' => [
                    'trabajadores' => $trabajadores,
                ],
                'messages' => null
            ]);
        }
    }

}
