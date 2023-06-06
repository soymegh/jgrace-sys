<?php



namespace App\Http\Controllers\Ventas;

use App\Models\Admon\Departamentos;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\Marcas;
//use App\Models\RRHHTrabajadores;

use App\Models\Ventas\TipoCliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class TipoClienteController extends Controller
{/**
 * Obtener una lista de tipos de tipos proveedores
 *
 * @access  public
 * @param
 * @return JsonResponse
 */

    public function obtener(Request $request, TipoCliente $tipos_cliente)
    {
        $tipos_cliente = $tipos_cliente->obtenerTiposClientes($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $tipos_cliente->total(),
                'rows' => $tipos_cliente->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de tipos de tipos proveedores sin ningun filtro
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerTodos(Request $request, TipoCliente $tipos_cliente)
    {
        $tipos_cliente = TipoCliente::all();
        return response()->json([
            'status' => 'success',
            'result' => $tipos_cliente,
            'messages' => null
        ]);
    }

    /**
     * obtener tipo de tipos proveedores Especifico
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     */

    public function obtenerTipoCliente(Request $request)
    {
        $rules = [
            'id_tipo_cliente' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_cliente = TipoCliente::find($request->id_tipo_cliente)
                ->get();
            if(!empty($tipo_cliente[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => $tipo_cliente[0],
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_tipo_cliente'=>["Datos no encontrados"]),
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
     * @param Request $request
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $rules = [
            'descripcion' =>  [
                'required',
                'string',
                'max:50',
                ],
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_cliente = new TipoCliente;
            $tipo_cliente->descripcion = $request->descripcion;
            $tipo_cliente->id_empresa = $usuario_empresa->id_empresa;
            $tipo_cliente->estado = 1;
            $tipo_cliente->u_grabacion = Auth::user()->name;
            $tipo_cliente->save();

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
            'id_tipo_cliente' => 'required|integer|min:1',
            'descripcion' =>  [
                'required',
                'string',
                'max:50',]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_cliente = TipoCliente::find($request->id_tipo_cliente);
            $tipo_cliente->descripcion = $request->descripcion;
            $tipo_cliente->u_modificacion = Auth::user()->name;
            $tipo_cliente->save();

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
            'id_tipo_cliente' =>  'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_cliente = TipoCliente::find($request->id_tipo_cliente);
            $tipo_cliente->u_modificacion = Auth::user()->name;
            $tipo_cliente->estado = 0;
            $tipo_cliente->save();

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
            'id_tipo_cliente' =>  'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tipo_cliente = TipoCliente::find($request->id_tipo_cliente);
            $tipo_cliente->u_modificacion = Auth::user()->name;
            $tipo_cliente->estado = 1;
            $tipo_cliente->save();

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
