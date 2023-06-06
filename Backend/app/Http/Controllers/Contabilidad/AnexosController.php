<?php



namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use Validator,Auth;
use App\Models\Contabilidad\Anexos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule,DB;
class AnexosController extends Controller
{
    /**
     * Obtener una lista de Anexos a los Estados Financieros CON PAGINACION
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerAnexos(Request $request, Anexos $anexos)
    {
        $anexos = $anexos->obtenerAnexos($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $anexos->total(),
                'rows' => $anexos->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Anexos a los Estados Financieros SIN PAGINACION
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerTodosAnexos(Request $request, Anexos $anexos)
    {
        $anexos = Anexos::all();
        return response()->json([
            'status' => 'success',
            'result' => $anexos,
            'messages' => null
        ]);
    }


    /**
     * Obtener una lista de  Anexos POR Estados Financieros
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerAnexosPorEstadoFinanciero(Request $request, Anexos $anexos)
    {
        $rules = [
            'id_estado_financiero' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $anexos = Anexos::where('id_estado_financiero',$request->id_estado_financiero)->where('estado',1)->get();
            return response()->json([
                'status' => 'success',
                'result' => $anexos,
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
     * obtener Anexo Especifico
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerAnexo(Request $request)
    {
        $rules = [
            'id_anexo'  => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $anexo = Anexos::where('id_anexo',$request->id_anexo)->with('anexosEstadoFinanciero')->get();
            if(!empty($anexo[0])){

                return response()->json([
                    'status' => 'success',
                    'result' => $anexo[0],
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_anexo'=>["Datos no encontrados"]),
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
     * Crear un nuevo anexo
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function registrar(Request $request)
    {
        $rules = [
            'nombre_anexo' => 'required|string|max:50|unique:pgsql.routes.anexos,nombre_anexo',
            'anexos_estado_financiero' => 'required|array|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $anexo = new Anexos;
            $anexo->nombre_anexo = $request->nombre_anexo;
            $anexo->id_estado_financiero = $request->anexos_estado_financiero['id_estado_financiero'];
            $anexo->posicion_anexo = 0;
            $anexo->usuario_registra = Auth::user()->usuario;
            $anexo->estado = 1;
            $anexo->save();

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
     * Actualizar Menu existente
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function actualizarOrdenAnexos(Request $request)
    {
        $rules = [
            'anexos' => 'required|array|min:1',
            'anexos.*.id_anexo' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            try{
                DB::beginTransaction();
                $contador = 1;
                foreach ($request->anexos as $anexo) {
                    $anexoX = Anexos::find($anexo['id_anexo']);
                    $anexoX->posicion_anexo = $contador;
                    $anexoX->save();
                    $contador++;
                    // echo  'nombre: '.$anexoX->nombre_anexo .' secuencia: '. $anexoX->posicion_anexo;
                }
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
     * Actualizar Anexo existente
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_anexo'  => 'required|integer|min:1',
            // 'descripcion' => 'required|string|max:100|unique:pgsql.admon.roles,descripcion',
            'nombre_anexo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.routes.anexos')->ignore($request->id_anexo,'id_anexo')
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $anexo = Anexos::find($request->id_anexo);
            $anexo->nombre_anexo = $request->nombre_anexo;
            $anexo->save();

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
     * Desactiva anexo
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_anexo'  => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $anexo = Anexos::find($request->id_anexo);
            $anexo->estado = 0;
            $anexo->save();

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
     * Activa anexo
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function activar(Request $request)
    {
        $rules = [
            'id_anexo'  => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $anexo = Anexos::find($request->id_anexo);
            $anexo->estado = 1;
            $anexo->save();

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
