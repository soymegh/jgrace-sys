<?php

namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\Departamentos;
use App\Models\Admon\Paises;
use Illuminate\Http\JsonResponse;
use Validator,Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class DepartamentosController extends Controller
{
    /**
     * Obtener una lista de departamentos
     *
     * @access  public
     * @param Request $request
     * @param Departamentos $departamentos
     * @return JsonResponse
     * @author octaviom
     */

    public function obtener(Request $request, Departamentos $departamentos)
    {
        $departamentos = $departamentos->obtenerDepartamentos($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $departamentos->total(),
                'rows' => $departamentos->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de departamentos sin ningun filtro
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function obtenerTodosDepartamentos(Request $request, Departamentos $departamentos)
    {
        $departamentos = Departamentos::with('municipiosDepartamento')->with('sectoresDepartamento')->get();
        return response()->json([
            'status' => 'success',
            'result' => $departamentos,
            'messages' => null
        ]);
    }

    /**
     * Obtener registro de departamento especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function obtenerDepartamento(Request $request)
    {
        $rules = [
            'id_departamento' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $departamento = Departamentos::find($request->id_departamento);

            if(!empty($departamento)){
                return response()->json([
                    'status' => 'success',
                    'result' => $departamento,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_departamento'=>["Datos no encontrados"]),
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
     * Crear un nuevo departamento
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function registrar(Request $request)
    {
        $rules = [
            'descripcion' => [
                'required',
                'string',
                'max:20',
                Rule::unique('pgsql.public.departamentos')]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $departamento = new Departamentos();
            $departamento->descripcion = $request->descripcion;
            $departamento->save();

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
     * Actualizar departamento existente
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_departamento' => 'required|integer|min:1',
            'descripcion' => [
                'required',
                'string',
                'max:20',
                Rule::unique('pgsql.public.departamentos')->ignore($request->id_departamento,'id_departamento')]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $departamento = Departamentos::find($request->id_departamento);
            $departamento->descripcion = $request->descripcion;
            $departamento->save();

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
