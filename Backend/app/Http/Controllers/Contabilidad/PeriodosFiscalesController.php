<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Contabilidad\CentrosCostosIngresos;
use App\Models\Contabilidad\NivelesCuentas;
use App\Models\Contabilidad\PeriodosFiscales;
use App\Models\Contabilidad\PeriodosMeses;
use App\Models\Contabilidad\TasasCambios;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

//use App\Models\CajaBancoTasasCambios;
class PeriodosFiscalesController extends Controller
{
    /**
     * Obtener
     *
     * @access  public
     * @param Request $request
     * @param PeriodosFiscales $periodos
     * @return JsonResponse
     * @author octaviom
     */

    public function obtener(Request $request, PeriodosFiscales $periodos)
    {
        $periodos = $periodos->obtener($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $periodos->total(),
                'rows' => $periodos->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener
     *
     * @access  public
     * @param Request $request
     * @param PeriodosFiscales $periodos
     * @return JsonResponse
     */

    public function obtenerTodos(Request $request, PeriodosFiscales $periodos)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $niveles_cuenta = NivelesCuentas::where('activo', 1)->where('id_empresa', $usuario_empresa->id_empresa)->orderby('id_nivel_cuenta', 'asc')->get();
        $periodos = PeriodosFiscales::select('id_periodo_fiscal', 'periodo')->where('id_empresa', $usuario_empresa->id_empresa)->orderby('periodo', 'desc')->with('mesesPeriodo')->get();
        $centro_costos_ingresos = CentrosCostosIngresos::select('id_centro', 'descripcion')->where('id_empresa', $usuario_empresa->id_empresa)->where('estado', true)->get();

//        $listado_reportes = Menus::select('admon.menus.id_menu')->join('admon.roles_menus','admon.menus.id_menu','admon.roles_menus.id_menu')
//            ->join('admon.roles','admon.roles.id_rol','admon.roles_menus.id_rol')
//            ->where('admon.roles.id_rol',Auth::user()->id_rol)
//            ->where('admon.menus.activo',1)
//            ->orderBy('admon.menus.secuencia')
//            ->get();

        return response()->json([
            'status' => 'success',
            'result' => [
                'niveles_cuenta' => $niveles_cuenta,
                'periodos' => $periodos,
                'centros' => $centro_costos_ingresos,
//                'lista_reportes'=>$listado_reportes
            ],
            'messages' => null
        ]);
    }

    /**
     * obtener
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerPeriodo(Request $request)
    {
        $rules = [
            'id_periodo_fiscal' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $periodo = PeriodosFiscales::find($request->id_periodo_fiscal);

            if (!empty($periodo)) {
                return response()->json([
                    'status' => 'success',
                    'result' => $periodo,
                    'messages' => null
                ]);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_periodo_fiscal' => ["Datos no encontrados"]),
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
     * Registrar
     *
     * @access  public
     * @param
     * @return JsonResponse
     */


    public function registrar(Request $request)
    {
        $rules = [
            'descripcion' => 'required',
            'estado' => 'required|integer|min:0',
            'periodo' => 'required|integer|unique:pgsql.contabilidad.periodos_fiscales,periodo|min:2021|max:2035',
            /*  'salario_mensual_techo' => 'required|regex:/^\d*(\.\d{1,2})?$/|numeric|min:1',
              'porcentaje_inss_base' => 'required|regex:/^\d*(\.\d{1,4})?$/|numeric|min:1',
              'porcentaje_ir_base' => 'required|regex:/^\d*(\.\d{1,4})?$/|numeric|min:1',*/
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            try {
                DB::beginTransaction();
                $periodo = new PeriodosFiscales();
                $periodo->descripcion = $request->descripcion;
                $periodo->periodo = $request->periodo;
                /* $periodo->salario_mensual_techo = $request->salario_mensual_techo;
                 $periodo->porcentaje_inss_base = $request->porcentaje_inss_base;
                 $periodo->porcentaje_ir_base = $request->porcentaje_ir_base;*/
                $periodo->id_empresa = $usuario_empresa->id_empresa;
                $periodo->estado = $request->estado;
                $periodo->u_creacion = Auth::user()->name;
                $periodo->save();

                $meses_periodo1 = new PeriodosMeses();
                $meses_periodo1->descripcion = 'ENERO/' . $request->periodo;
                $meses_periodo1->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo1->mes = 1;
                $meses_periodo1->u_creacion = Auth::user()->name;
                $meses_periodo1->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo1->save();

                $meses_periodo2 = new PeriodosMeses;
                $meses_periodo2->descripcion = 'FEBRERO/' . $request->periodo;
                $meses_periodo2->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo2->mes = 2;
                $meses_periodo2->u_creacion = Auth::user()->name;
                $meses_periodo2->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo2->save();

                $meses_periodo3 = new PeriodosMeses;
                $meses_periodo3->descripcion = 'MARZO/' . $request->periodo;
                $meses_periodo3->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo3->mes = 3;
                $meses_periodo3->u_creacion = Auth::user()->name;
                $meses_periodo3->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo3->save();

                $meses_periodo4 = new PeriodosMeses;
                $meses_periodo4->descripcion = 'ABRIL/' . $request->periodo;
                $meses_periodo4->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo4->mes = 4;
                $meses_periodo4->u_creacion = Auth::user()->name;
                $meses_periodo4->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo4->save();

                $meses_periodo5 = new PeriodosMeses;
                $meses_periodo5->descripcion = 'MAYO/' . $request->periodo;
                $meses_periodo5->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo5->mes = 5;
                $meses_periodo5->u_creacion = Auth::user()->name;
                $meses_periodo5->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo5->save();

                $meses_periodo6 = new PeriodosMeses;
                $meses_periodo6->descripcion = 'JUNIO/' . $request->periodo;
                $meses_periodo6->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo6->mes = 6;
                $meses_periodo6->u_creacion = Auth::user()->name;
                $meses_periodo6->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo6->save();

                $meses_periodo7 = new PeriodosMeses;
                $meses_periodo7->descripcion = 'JULIO/' . $request->periodo;
                $meses_periodo7->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo7->mes = 7;
                $meses_periodo7->u_creacion = Auth::user()->name;
                $meses_periodo7->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo7->save();

                $meses_periodo8 = new PeriodosMeses;
                $meses_periodo8->descripcion = 'AGOSTO/' . $request->periodo;
                $meses_periodo8->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo8->mes = 8;
                $meses_periodo8->u_creacion = Auth::user()->name;
                $meses_periodo8->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo8->save();

                $meses_periodo9 = new PeriodosMeses;
                $meses_periodo9->descripcion = 'SEPTIEMBRE/' . $request->periodo;
                $meses_periodo9->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo9->mes = 9;
                $meses_periodo9->u_creacion = Auth::user()->name;
                $meses_periodo9->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo9->save();

                $meses_periodo10 = new PeriodosMeses;
                $meses_periodo10->descripcion = 'OCTUBRE/' . $request->periodo;
                $meses_periodo10->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo10->mes = 10;
                $meses_periodo10->u_creacion = Auth::user()->name;
                $meses_periodo10->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo10->save();

                $meses_periodo11 = new PeriodosMeses;
                $meses_periodo11->descripcion = 'NOVIEMBRE/' . $request->periodo;
                $meses_periodo11->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo11->mes = 11;
                $meses_periodo11->u_creacion = Auth::user()->name;
                $meses_periodo11->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo11->save();

                $meses_periodo12 = new PeriodosMeses;
                $meses_periodo12->descripcion = 'DICIEMBRE/' . $request->periodo;
                $meses_periodo12->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $meses_periodo12->mes = 12;
                $meses_periodo12->u_creacion = Auth::user()->name;
                $meses_periodo12->id_empresa = $usuario_empresa->id_empresa;
                $meses_periodo12->save();

                $num_dias = $this->cal_days_in_year($request->periodo);

                $tasas = [];
                for ($dia = 0; $dia < $num_dias; $dia++) {
                    $fecha = date('Y-m-d', strtotime('01/01/' . $request->periodo . ' + ' . $dia . ' days'));

                    $tasas[] = array('fecha' => $fecha, 'tasa' => 0, 'tasa_paralela' => 0, 'id_empresa' => $usuario_empresa->id_empresa);
                }
                TasasCambios::insert($tasas);
//                print_r('tasas de cambio'. $tasas);

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'result' => 'Error de base de datos',
                    'messages' => $e
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

    function cal_days_in_year($year)
    {
        $days = 0;
        for ($month = 1; $month <= 12; $month++) {
            $days += cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        return $days;
    }

    /**
     * Actualizar
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_periodo_fiscal' => 'required',
            'descripcion' => 'required',
            'estado' => 'required|integer|min:0',
            //'periodo' => 'required|integer|unique:pgsql.routes.periodos_fiscales,periodo|min:2015',
            /* 'salario_mensual_techo' => 'required|regex:/^\d*(\.\d{1,2})?$/|numeric|min:1',
             'porcentaje_inss_base' => 'required|regex:/^\d*(\.\d{1,4})?$/|numeric|min:1',
             'porcentaje_ir_base' => 'required|regex:/^\d*(\.\d{1,4})?$/|numeric|min:1',*/
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $periodo = PeriodosFiscales::find($request->id_periodo_fiscal);
            $periodo->descripcion = $request->descripcion;
            //$periodo->periodo = $request->periodo;
            /* $periodo->salario_mensual_techo = $request->salario_mensual_techo;
             $periodo->porcentaje_inss_base = $request->porcentaje_inss_base;
             $periodo->porcentaje_ir_base = $request->porcentaje_ir_base;*/
            $periodo->estado = $request->estado;
            $periodo->save();

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


    public function cerrarMes(Request $request)
    {
        $rules = [
            'id_periodo' => 'required|integer|min:1',
            'id_mes' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            try {
                DB::beginTransaction();
                $periodo_mes = PeriodosMeses::find($request->id_mes);

                $meses_antes = PeriodosMeses::where('mes', '<', $periodo_mes->mes)->where('estado', 1)->where('id_periodo_fiscal', $periodo_mes->id_periodo_fiscal)->first();

                $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                //print_r($meses_antes);
                if ($request->modo === 1) {
                    if (empty($meses_antes)) {

                        $periodo_mes->estado = 2;
                        $periodo_mes->save();

                        DB::select("SELECT contabilidad.consolidar_saldos(?,?)", [$request->id_periodo, $periodo_mes['mes']]);
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'result' => 'Existen meses anteriores que no han sido cerrados',
                            'messages' => null
                        ]);
                    }
                }else {
                    DB::select("SELECT contabilidad.consolidar_saldos(?,?)", [$request->id_periodo, $periodo_mes['mes']]);
                }
                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);

            } catch (Exception $e) {
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
