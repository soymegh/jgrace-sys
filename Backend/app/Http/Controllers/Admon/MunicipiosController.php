<?php

namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\Municipios;
use App\Models\Admon\Paises;
use Illuminate\Http\JsonResponse;
use Validator,Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class MunicipiosController extends Controller
{
    /**
     * Obtener una lista de municipios
     *
     * @access  public
     * @param Request $request
     * @param Municipios $Municipios
     * @return JsonResponse
     * @author octaviom
     */

    public function obtener(Request $request, Municipios $Municipios)
    {
        $municipios = $Municipios->obtener($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $municipios->total(),
                'rows' => $municipios->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Municipios sin ningun filtro
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function obtenerTodosMunicipios(Request $request, Municipios $municipios)
    {
        $municipios = Municipios::select('*')->get();
        return response()->json([
            'status' => 'success',
            'result' => $municipios,
            'messages' => null
        ]);
    }

    /**
     * Obtener registro de municipio especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function obtenerMunicipio(Request $request)
    {
        $rules = [
            'id_municipio' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $municipio = Municipios::where('id_municipio', '=', $request->id_municipio)->with('departamentoMunicipio')->first();

            if(!empty($municipio)){
                return response()->json([
                    'status' => 'success',
                    'result' => $municipio,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_municipio'=>["Datos no encontrados"]),
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
     * Crear un nuevo municipio
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
                Rule::unique('pgsql.public.municipios'),
                ],
            'departamento' => 'required|array|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $municipio = new Municipios();
            $municipio->descripcion = $request->descripcion;
            $municipio->id_departamento = $request->departamento['id_departamento'];
            $municipio->save();

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
     * Actualizar municipio existente
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author octaviom
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_municipio' => 'required|integer|min:1',
            'descripcion' => [
                'required',
                'string',
                'max:20',
                Rule::unique('pgsql.public.municipios')->ignore($request->id_municipio,'id_municipio'),
                ],
            'departamento_municipio' => 'required|array|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $municipio = Municipios::find($request->id_municipio);
            $municipio->descripcion = $request->descripcion;
            $municipio->id_departamento = $request->departamento_municipio['id_departamento'];
            $municipio->save();

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
