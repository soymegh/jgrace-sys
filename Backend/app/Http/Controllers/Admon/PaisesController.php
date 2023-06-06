<?php

namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\Paises;
use Illuminate\Http\JsonResponse;
use Validator,Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class PaisesController extends Controller
{
    /**
     * Obtener una lista de Paises
     *
     * @access  public
     * @param Request $request
     * @param Paises $paises
     * @return JsonResponse
     */

    public function obtener(Request $request, Paises $paises)
    {
        $paises = $paises->obtenerPaises($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $paises->total(),
                'rows' => $paises->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Paises sin ningun filtro
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerTodosPaises(Request $request, Paises $paises)
    {
        $paises = Paises::where('estado', 1)->get();
        return response()->json([
            'status' => 'success',
            'result' => $paises,
            'messages' => null
        ]);
    }

    /**
     * Obtener registro de pais especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerPais(Request $request)
    {
        $rules = [
            'id_pais' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $pais = Paises::find($request->id_pais);

            if(!empty($pais)){
                return response()->json([
                    'status' => 'success',
                    'result' => $pais,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_pais'=>["Datos no encontrados"]),
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
     * Crear un nuevo país
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $rules = [
            'codigo' => [
                'required',
                'string',
                'max:3',
                Rule::unique('pgsql.public.paises')/*->ignore($request->id_pais,'id_pais')*/],
            'descripcion' => [
                'required',
                'string',
                'max:60',
                Rule::unique('pgsql.public.paises')/*->ignore($request->id_pais,'id_pais')*/]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $pais = new Paises();
            $pais->descripcion = $request->descripcion;
            $pais->codigo = $request->codigo;
            $pais->estado = 1;
            $pais->save();

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
            'id_pais' => 'required|integer|min:1',
            'codigo' => [
                'required',
                'string',
                'max:3',
                Rule::unique('pgsql.public.paises')->ignore($request->id_pais,'id_pais')],
            'descripcion' => [
                'required',
                'string',
                'max:60',
                Rule::unique('pgsql.public.paises')->ignore($request->id_pais,'id_pais')]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $pais = Paises::find($request->id_pais);
            $pais->descripcion = $request->descripcion;
            $pais->codigo = $request->codigo;
            $pais->save();

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
            'id_pais' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $pais = Paises::find($request->id_pais);
            $pais->estado = 0;
            $pais->save();

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
            'id_pais' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $pais = Paises::find($request->id_pais);
            $pais->estado = 1;
            $pais->save();

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
