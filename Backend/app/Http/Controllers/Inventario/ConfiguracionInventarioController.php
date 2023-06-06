<?php



namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\CuentasContablesVista;
use App\Models\Contabilidad\CentrosCostosIngresos;
use App\Models\Contabilidad\CuentasContables;
use App\Models\CuentasXCobrar\CatAuxiliar;
use App\Models\Inventario\ConfiguracionInventario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class ConfiguracionInventarioController extends Controller
{
    /**
     * Get List of Importaciones
     *
     * @access  public
     * @param Request $request
     * @param ConfiguracionInventario $importacion_config
     * @return \Illuminate\Http\JsonResponse
     */

    public function obtener(Request $request, ConfiguracionInventario $importacion_config)
    {
        $cuentas_contables = CuentasContablesVista::orderBy('cta_contable')->where('estado',1)->select('id_cuenta_contable','cta_contable','nombre_cuenta','nombre_cuenta_completo','requiere_aux','id_centro_costo','codigo_centro_costo','codigo_auxiliar')->get();
        $importacion_config = $importacion_config->obtener($request);

        $centro_ingreso = CentrosCostosIngresos::where('tipo_centro', 1)->where('estado', 1)->get();
        $centro_costo = CentrosCostosIngresos::where('tipo_centro', 2)->where('estado', 1)->get();
        /*$auxiliares = CatAuxiliar::where('estado',1)->get();*/
        //pendiente agregar centros auxiliares - desarrollo fechas posteriores

        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $importacion_config->total(),
                'rows' => $importacion_config->items(),
                'cuentas_contables' => $cuentas_contables,
                'centro_ingreso' => $centro_ingreso,
                'centro_costo' => $centro_costo,
                /*'auxiliares' => $auxiliares,*/
            ],
            'messages' => null
        ]);
    }



    public function actualizar(Request $request)
    {
        $messages = [
            'ajustes.required' => 'Se requiere agregar un producto por lo menos.',
            'ajustes.min' => 'Se requiere agregar un producto por lo menos.',
            'ajustes.*.id_cuenta_contaable.required' => 'Seleccione un producto válido',
            'ajustes.*.descripcion_movimiento.required' => 'Se requiere una descripcion del producto',
        ];

        $rules = [

            'ajustes' => 'required|array|min:2',
            'ajustes.*.id_configuracion_comprobante' => 'required|integer',
            'ajustes.*.id_cuenta_contaable' => 'required|integer|exists:pgsql.contabilidad.cuentas_contables,id_cuenta_contable',
            'ajustes.*.descripcion_movimiento' => 'required|string|max:100',

        ];

        $validator = Validator::make($request->all(), $rules,$messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();

                foreach ($request->ajustes as $configuracion) {
                    $importacion_config = ConfiguracionInventario::find($configuracion['id_configuracion_comprobante']);
                    $importacion_config->id_cuenta_contaable = $configuracion['configuracion_importacioncuenta_contable']['id_cuenta_contable'];
                    $importacion_config->descripcion_movimiento = $configuracion['descripcion_movimiento'];


                    $importacion_config_cuenta = CuentasContables::find($configuracion['id_cuenta_contaable']);

                    if($importacion_config_cuenta->requiere_aux === 1) //codigo auxiliar
                    {
                        $importacion_config->id_centro_costo = null;
                        $importacion_config->codigo_centro_costo = null;
                        if (empty($configuracion['configuracion_auxiliares'])){
                            DB::rollBack();
                            return response()->json([
                                'status' => 'array_empty',
                                'result' => 'validation_error',
                                'messages' => 'Verifique que seleccionó un auxiliar para las cuentas que lo requieren!'
                            ]);
                        }else{
                            $importacion_config->id_cat_auxiliar_cxc = $configuracion['configuracion_auxiliares']['id_cat_auxiliar_cxc'];
                            $importacion_config->codigo_auxiliar = $configuracion['configuracion_auxiliares']['codigo'];
                        }
                    }else if($importacion_config_cuenta->requiere_aux === 2) //centro de costo
                    {
                        if (empty($configuracion['configuracion_centro_costo'])){
                            DB::rollBack();
                            return response()->json([
                                'status' => 'array_empty',
                                'result' => 'validation_error',
                                'messages' => 'Verifique que seleccionó un centro de costo para las cuentas que lo requiren'
                            ]);
                        } else{
                            $importacion_config->id_centro_costo = $configuracion['configuracion_centro_costo']['id_centro'];
                            $importacion_config->codigo_centro_costo = $configuracion['configuracion_centro_costo']['codigo'];
                        }
                        $importacion_config->id_cat_auxiliar_cxc = null;
                        $importacion_config->codigo_auxiliar = null;

                    }else if($importacion_config_cuenta->requiere_aux === 3) //centro de costo
                    {
                        if (empty($configuracion['configuracion_centro_costo'])){
                            return response()->json([
                                'status' => 'array_empty',
                                'result' => 'validation_error',
                                'messages' => 'Verifique que seleccionó un centro de costo para las cuentas que lo requieren'
                            ]);
                        }else{
                            $importacion_config->id_centro_costo = $configuracion['configuracion_centro_costo']['id_centro'];
                            $importacion_config->codigo_centro_costo = $configuracion['configuracion_centro_costo']['codigo'];
                        }
                        $importacion_config->id_cat_auxiliar_cxc = null;
                        $importacion_config->codigo_auxiliar = null;
                    }else if($importacion_config_cuenta->requiere_aux === 0) //no requiere centro de costo
                    {
                        $importacion_config->id_centro_costo = null;
                        $importacion_config->codigo_centro_costo = null;
                        $importacion_config->id_cat_auxiliar_cxc = null;
                        $importacion_config->codigo_auxiliar = null;
                    }
                    $importacion_config->save();
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
}
