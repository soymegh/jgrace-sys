<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\Contabilidad\NivelesCuentas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class NivelesCuentasController extends Controller
{
    /**
     * Obtener una lista de Estados Financieros
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerNivelesCuenta(Request $request, NivelesCuentas $niveles_cuenta)
    {
        $niveles_cuenta = $niveles_cuenta->obtenerNivelesCuenta($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $niveles_cuenta->total(),
                'rows' => $niveles_cuenta->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Roles sin ningun filtro
     *
     * @access  public
     * @param Request $request
     * @param NivelesCuentas $niveles_cuenta
     * @return JsonResponse
     */

    public function obtenerTodosNivelesCuenta(Request $request, NivelesCuentas $niveles_cuenta)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $niveles_cuenta = NivelesCuentas::where('id_nivel_cuenta','>',1)->where('activo','true')->where('id_empresa','=', $usuario_empresa->id_empresa)->orderBy('id_nivel_cuenta')->get();
        return response()->json([
            'status' => 'success',
            'result' => $niveles_cuenta,
            'messages' => null
        ]);
    }

    /**
     * obtener Estado Finaciero Especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerNivelCuenta(Request $request)
    {
        $rules = [
            'id_nivel_cuenta' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $nivel_cuenta = NivelesCuentas::find($request->id_nivel_cuenta);
            if(!empty($nivel_cuenta)){
                return response()->json([
                    'status' => 'success',
                    'result' => $nivel_cuenta,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_nivel_cuenta'=>["Datos no encontrados"]),
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
     * Actualizar Nivel de cuenta existente
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_nivel_cuenta'  => 'required|integer|min:1',
            'descripcion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.contabilidad.niveles_cuentas')->ignore($request->id_nivel_cuenta,'id_nivel_cuenta')
            ]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $nivel_cuenta = NivelesCuentas::find($request->id_nivel_cuenta);
            $nivel_cuenta->descripcion = $request->descripcion;
            $nivel_cuenta->save();

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
