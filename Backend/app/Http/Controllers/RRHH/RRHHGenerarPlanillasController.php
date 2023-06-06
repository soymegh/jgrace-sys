<?php 

namespace App\Http\Controllers;

use App\Models\ContabilidadDocumentosContables;
use App\Models\ContabilidadDocumentosMovimientos;
use App\Models\ContabilidadPeriodoFiscal;
use App\Models\ContabilidadPeriodoMeses;
use App\Models\ContabilidadTiposDocumentos;
use App\Models\RRHHContratoTipos;
use App\Models\RRHHDatosMedicos;
use App\Models\RRHHGenerarPlanilla;
use App\Models\RRHHPlanillaControl;
use App\Models\RRHHPlanillaHistorico;
use App\Models\RRHHPlanillasControles;
use Illuminate\Http\JsonResponse;
use PHPJasper\PHPJasper;
use Validator,Auth,DB;
use App\Models\RRHHNivelesAcademicos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use DateTime;
class RRHHGenerarPlanillasController extends Controller
{

    /**
     * obtener planilla especifica con parametros
     *
     * @access  public
     * @param Request $request
     * @param RRHHGenerarPlanilla $planilla
     * @return JsonResponse
     */

    public function obtenerPlanilla(Request $request, RRHHGenerarPlanilla $planilla)
    {
        $rules = [
            'id_planilla_control'=> 'required|integer|min:1',
            //'id_nomina'=> 'required|integer|min:1'
        ];

        $planilla = $planilla->obtenerPlanilla($request);
        return response()->json([
            'status' => 'success',
            'result' => $planilla,
            'messages' => null
        ]);
    }

    public function obtenerPlanillaProcesar(Request $request)
    {
        $rules = [
            'id_planilla_control'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $planilla = RRHHGenerarPlanilla::where('id_planilla_control', $request->id_planilla_control)->get();
            $control_planilla = RRHHPlanillasControles::where('id_planilla_control',$request->id_planilla_control)->first();

            $total_salarios_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_salarios"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',4)->get(); /*id_cat_ingreso_deduccion = 4 ~ Salario básico*/

            $total_salarios_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_salarios"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',4)->get(); /*id_cat_ingreso_deduccion = 4 ~ Salario básico*/

            $total_inss_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_inss"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',1)->get(); /*id_cat_ingreso_deduccion = 1 ~ inss*/

            $total_inss_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_inss"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',1)->get(); /*id_cat_ingreso_deduccion = 1 ~ inss*/

            $total_ir_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_ir"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',2)->get(); /*id_cat_ingreso_deduccion = 2 ~ ir*/

            $total_ir_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_ir"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',2)->get(); /*id_cat_ingreso_deduccion = 2 ~ ir*/

            $total_inatec_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_inatec"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',3)->get(); /*id_cat_ingreso_deduccion = 3 ~ inatec*/

            $total_inatec_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_inatec"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',3)->get(); /*id_cat_ingreso_deduccion = 3 ~ inatec*/

            $total_vacaciones_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_vacaciones"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',10)->get(); /*id_cat_ingreso_deduccion = 10 ~ vacaciones*/

            $total_vacaciones_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_vacaciones"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',10)->get(); /*id_cat_ingreso_deduccion = 10 ~ vacaciones*/

            $total_treceavomes_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_treceavomes"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',11)->get(); /*id_cat_ingreso_deduccion = 11 ~ treceavomes*/

            $total_treceavomes_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_treceavomes"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',11)->get(); /*id_cat_ingreso_deduccion = 11 ~ treceavomes*/

            $total_comisiones_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_comisiones"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',12)->get(); /*id_cat_ingreso_deduccion = 12 ~ treceavomes*/

            $total_comisiones_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_comisiones"))
                ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$request->id_planilla_control)
                ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',12)->get(); /*id_cat_ingreso_deduccion = 12 ~ treceavomes*/

            $total_general = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as monto_neto"))->where('id_planilla_control',$request->id_planilla_control)->get();

            if(!empty($planilla)){
                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'planilla' => $planilla,
                        'control_planilla' => $control_planilla,
                        'total_salarios_administrativos' => $total_salarios_administrativo,
                        'total_salarios_comercial' => $total_salarios_comercial,
                        'total_inss_administrativo' => $total_inss_administrativo,
                        'total_inss_comercial' => $total_inss_comercial,
                        'total_ir_administrativo' => $total_ir_administrativo,
                        'total_ir_comercial' => $total_ir_comercial,
                        'total_inactec_administrativo' => $total_inatec_administrativo,
                        'total_inactec_comercial' => $total_inatec_comercial,
                        'total_vacaciones_administrativo' => $total_vacaciones_administrativo,
                        'total_vacaciones_comercial' => $total_vacaciones_comercial,
                        'total_treceavomes_administrativo' => $total_treceavomes_administrativo,
                        'total_treceavomes_comercial' => $total_treceavomes_comercial,
                        'total_comisiones_administrativo' => $total_comisiones_administrativo,
                        'total_comisiones_comercial' => $total_comisiones_comercial,
                        'total_general' => $total_general,
                    ],
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_planilla_control'=>["Datos no encontrados"]),
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

    public function registrar(Request $request) //Guardar datos de la planilla generada
    {
        $rules = [
            'id_planilla_control'=> 'required|integer|min:1',
            /*'id_sucursal' => 'required|integer|min:1',
            'anio' => 'required|integer|min:1',
            'mes' => 'required|integer|min:1',
            'id_trabajador' => 'required|integer|min:1',
            'id_cat_ingreso_deduccion_trabajador' => 'required|integer|min:1',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date',
            'monto' => 'required|numeric'*/
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails())
        {
            try{
                DB::beginTransaction();
                //Cambio de estado al control de planillas de "activo" a "planilla generada"
                $control_de_planilla = RRHHPlanillaControl::select('id_planilla_control','estado','f_modificacion','u_modificacion')->where('id_planilla_control',$request->id_planilla_control)->first();
                $control_de_planilla->estado = 2;
                $control_de_planilla->u_modificacion = Auth::user()->usuario;
                $control_de_planilla->save();

                //Si ya existen datos de la planilla a generar, eliminar los anteriores y sobreescribir los nuevos
                RRHHGenerarPlanilla::where('id_planilla_control',$request->id_planilla_control)->delete();
                //Guardar datos de la planilla
                foreach ($request->planilla as $planillax) {

                    $planilla =  new RRHHGenerarPlanilla();
                    $planilla_control = RRHHPlanillaControl::select('id_planilla_control','anio_proceso','mes_proceso','f_desde','f_hasta')->where('id_planilla_control',$request->id_planilla_control)->first();
                    
                    $planilla->id_sucursal = $planillax['id_sucursal'];
                    $planilla->anio = $planilla_control['anio_proceso'];
                    $planilla->mes = $planilla_control['mes_proceso'];
                    $planilla->id_trabajador = $planillax['id_trabajador'];
                    $planilla->id_cat_ingreso_deduccion_trabajador = $planillax['id_cat_ingreso_deduccion'];
                    $planilla->fecha_desde = $planilla_control['f_desde'];
                    $planilla->fecha_hasta = $planilla_control['f_hasta'];
                    $planilla->monto = $planillax['monto'];
                    $planilla->u_grabacion = Auth::user()->usuario;
                    $planilla->f_grabacion = date('Y-m-d');
                    $num_colilla_nuevo = $planilla->obtenerCodigo($planillax['id_trabajador']);
                    $planilla->num_colilla = $num_colilla_nuevo['secuencia'];
                    $planilla->id_planilla_control = $planilla_control['id_planilla_control'];
                    $planilla->save();


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
        }else
            {
                return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
                ]);
            }
    }

    public function procesarPlanilla(Request $request) //procesar planilla, grabando en historico y creación de documento contable
    {
        $rules = [
            'id_planilla_control'=> 'required|integer|min:1',

        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails())
        {
            try {

                DB::beginTransaction();
                //Cambio de estado a la planilla de generada -> procesada
                $control_de_planilla = RRHHPlanillaControl::select('id_planilla_control','estado','f_modificacion','u_modificacion')->where('id_planilla_control',$request->id_planilla_control)->first();
                $control_de_planilla->estado = 3;
                $control_de_planilla->u_modificacion = Auth::user()->usuario;
                $control_de_planilla->save();

                //Grabar información de planilla en la tabla de historicos
                foreach ($request->planilla as $planillax) {

                    $planilla_historico =  new RRHHPlanillaHistorico();
                    $planilla_control = RRHHPlanillaControl::select('id_planilla_control','anio_proceso','mes_proceso','f_desde','f_hasta')->where('id_planilla_control',$request->id_planilla_control)->first();

                    $planilla_historico->id_sucursal = $planillax['id_sucursal'];
                    $planilla_historico->anio = $planillax['anio'];
                    $planilla_historico->mes = $planillax['mes'];
                    $planilla_historico->id_trabajador = $planillax['id_trabajador'];
                    $planilla_historico->id_cat_ingreso_deduccion_trabajador = $planillax['id_cat_ingreso_deduccion_trabajador'];
                    $planilla_historico->fecha_desde = $planillax['fecha_desde'];
                    $planilla_historico->fecha_hasta = $planillax['fecha_hasta'];
                    $planilla_historico->monto = $planillax['monto'];
                    $planilla_historico->u_grabacion = Auth::user()->usuario;
                    //$planilla_historico->f_grabacion = date('Y-m-d');
                    $planilla_historico->num_colilla = $planillax['num_colilla'];
                    $planilla_historico->id_planilla_control = $planillax['id_planilla_control'];
                    $planilla_historico->id_planilla = $planillax['id_planilla'];
                    $planilla_historico->save();


                }

                //creación del documento contable
                /*INICIA movimiento contable - planilla*/

                $documento = new ContabilidadDocumentosContables;
                $tipo = ContabilidadTiposDocumentos::find(14);
                $fecha= date("Y-m-d H:i:s");
                $codigo = $documento->obtenerCodigoDocumento(array('id_tipo_doc'=>14,'fecha_doc'=>$fecha));

                //DB::commit();

                $nuevo_codigo = json_decode($codigo[0]);

                date_default_timezone_set('America/Managua');

                $documento->num_documento = $tipo->prefijo.'-'.$nuevo_codigo->secuencia;
                $documento->fecha_emision =  date('Y-m-d H:i:s');
                $documento->codigo_documento = $nuevo_codigo->secuencia;


                $date = DateTime::createFromFormat("Y-m-d H:i:s", $documento->fecha_emision);

                $periodo = ContabilidadPeriodoFiscal::where('periodo',$date->format("Y"))->first();

                if(empty($periodo)){
                    return response()->json([
                        'status' => 'error',
                        'result' =>   array('fecha_emision'=>["El periodo ".$date->format("Y")." no se encuentra registrado, por favor consulte al administrador"]),
                        'messages' => null
                    ]);
                    exit;
                }

                if($periodo->estado){
                    return response()->json([
                        'status' => 'error',
                        'result' =>   array('fecha_emision'=>["El periodo ".$date->format("Y")." es inválido, ya que se encuentra en estado COMPLETADO"]),
                        'messages' => null
                    ]);
                    exit;
                }

                $periodo_mes = ContabilidadPeriodoMeses::where('id_periodo_fiscal',$periodo->id_periodo_fiscal)->where('mes',$date->format("n"))->first();
                if(empty($periodo_mes)){
                    return response()->json([
                        'status' => 'error',
                        'result' =>   array('fecha_emision'=>["El mes ".$date->format("F")." no se encuentra registrado, por favor consulte al administrador"]),
                        'messages' => null
                    ]);
                    exit;
                }

                if($periodo_mes->estado == 2 ){
                    return response()->json([
                        'status' => 'error',
                        'result' =>   array('fecha_emision'=>["El mes ".config('global.meses')[$periodo_mes->mes-1]." es inválido, ya que se encuentra en estado COMPLETADO"]),
                        'messages' => null
                    ]);
                    exit;
                }

                //obtener total de planilla
                $total_general = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as monto_neto"))->where('id_planilla_control',$planillax['id_planilla_control'])->first();
                $valor = $total_general['monto_neto'];

                $documento->id_periodo_fiscal = $periodo->id_periodo_fiscal;
                $documento->id_tipo_doc = 14;
                $documento->valor = $valor;
                $documento->concepto = 'Registramos planilla con fecha '.$planilla_control->mes_proceso.'/'.$planilla_control->anio_proceso;
                $documento->id_moneda = 1;
                $documento->u_creacion = Auth::user()->usuario;
                $documento->estado = 1;
                $documento->save();
                ContabilidadTiposDocumentos::find($documento->id_tipo_doc)->increment('secuencia');

                //Documento cuenta contable sueldos y salarios ~ Area administrativa

                //Obtener totalizado de salarios administrativos
                $total_salarios_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_salarios"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',4)->first(); /*id_cat_ingreso_deduccion = 4 ~ Salario básico*/

                $debe_salario_administrativo = $total_salarios_administrativo['total_salarios']?$total_salarios_administrativo['total_salarios']:0;

                $documento_cuenta_contable_suelos = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_suelos->id_documento = $documento->id_documento;
                $documento_cuenta_contable_suelos->id_moneda= 1;
                $documento_cuenta_contable_suelos->concepto = $documento->concepto;
                $documento_cuenta_contable_suelos->debe = $debe_salario_administrativo;
                $documento_cuenta_contable_suelos->haber = 0;
                $documento_cuenta_contable_suelos->debe_org = $debe_salario_administrativo;
                $documento_cuenta_contable_suelos->haber_org = 0;
                $documento_cuenta_contable_suelos->id_centro =  null;
                $documento_cuenta_contable_suelos->id_cuenta_contable = 144;
                $documento_cuenta_contable_suelos->cta_contable = '6121-01-000';
                $documento_cuenta_contable_suelos->cta_contable_padre = '6121-00-000';
                $documento_cuenta_contable_suelos->save();

                //Documento cuenta contable horas extras ~ Area administrativa

                //Obtener totalizado de horas extras administrativos
                $total_horas_extras_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_horas"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',9)->first(); /*id_cat_ingreso_deduccion = 5 ~ horas extras*/

                $debe_horas_extras_administrativo = $total_horas_extras_administrativo['total_horas']?$total_horas_extras_administrativo['total_horas']:0;

                $documento_cuenta_contable_horas_extras = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_horas_extras->id_documento = $documento->id_documento;
                $documento_cuenta_contable_horas_extras->id_moneda= 1;
                $documento_cuenta_contable_horas_extras->concepto = $documento->concepto;
                $documento_cuenta_contable_horas_extras->debe = $debe_horas_extras_administrativo;
                $documento_cuenta_contable_horas_extras->haber = 0;
                $documento_cuenta_contable_horas_extras->debe_org = $debe_horas_extras_administrativo;
                $documento_cuenta_contable_horas_extras->haber_org = 0;
                $documento_cuenta_contable_horas_extras->id_centro =  null;
                $documento_cuenta_contable_horas_extras->id_cuenta_contable = 195;
                $documento_cuenta_contable_horas_extras->cta_contable = '6121-01-003';
                $documento_cuenta_contable_horas_extras->cta_contable_padre = '6121-00-000';
                $documento_cuenta_contable_horas_extras->save();

                //Documento cuenta contable vacaciones ~ Area administrativa

                //Obtener totalizado de vacaciones administrativos
                $total_vacaciones_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_vacaciones"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',10)->first(); /*id_cat_ingreso_deduccion = 5 ~ horas extras*/

                $debe_vacaciones_administrativo = $total_vacaciones_administrativo['total_vacaciones']?$total_vacaciones_administrativo['total_vacaciones']:0;

                $documento_cuenta_contable_vacaciones = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_vacaciones->id_documento = $documento->id_documento;
                $documento_cuenta_contable_vacaciones->id_moneda= 1;
                $documento_cuenta_contable_vacaciones->concepto = $documento->concepto;
                $documento_cuenta_contable_vacaciones->debe = $debe_vacaciones_administrativo;
                $documento_cuenta_contable_vacaciones->haber = 0;
                $documento_cuenta_contable_vacaciones->debe_org = $debe_vacaciones_administrativo;
                $documento_cuenta_contable_vacaciones->haber_org = 0;
                $documento_cuenta_contable_vacaciones->id_centro =  null;
                $documento_cuenta_contable_vacaciones->id_cuenta_contable = 196;
                $documento_cuenta_contable_vacaciones->cta_contable = '6121-01-004';
                $documento_cuenta_contable_vacaciones->cta_contable_padre = '6121-00-000';
                $documento_cuenta_contable_vacaciones->save();

                //Documento cuenta contable treceavo mes ~ Area administrativa

                //Obtener totalizado de treceavo mes administrativos
                $total_treceavomes_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_treceavomes"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',11)->first(); /*id_cat_ingreso_deduccion = 5 ~ horas extras*/

                $debe_treceavomes_administrativo = $total_treceavomes_administrativo['total_treceavomes']?$total_treceavomes_administrativo['total_treceavomes']:0;

                $documento_cuenta_contable_treceavomes = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_treceavomes->id_documento = $documento->id_documento;
                $documento_cuenta_contable_treceavomes->id_moneda= 1;
                $documento_cuenta_contable_treceavomes->concepto = $documento->concepto;
                $documento_cuenta_contable_treceavomes->debe = $debe_treceavomes_administrativo;
                $documento_cuenta_contable_treceavomes->haber = 0;
                $documento_cuenta_contable_treceavomes->debe_org = $debe_treceavomes_administrativo;
                $documento_cuenta_contable_treceavomes->haber_org = 0;
                $documento_cuenta_contable_treceavomes->id_centro =  null;
                $documento_cuenta_contable_treceavomes->id_cuenta_contable = 197;
                $documento_cuenta_contable_treceavomes->cta_contable = '6121-01-005';
                $documento_cuenta_contable_treceavomes->cta_contable_padre = '6121-00-000';
                $documento_cuenta_contable_treceavomes->save();

                //Documento cuenta contable inss patronal ~ Area administrativa

                //Obtener totalizado de treceavo mes administrativos
                $total_inss_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_inss"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',1)->first(); /*id_cat_ingreso_deduccion = 5 ~ horas extras*/

                $debe_inss_administrativo = $total_inss_administrativo['total_inss']?$total_inss_administrativo['total_inss']:0;

                $documento_cuenta_contable_inss = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_inss->id_documento = $documento->id_documento;
                $documento_cuenta_contable_inss->id_moneda= 1;
                $documento_cuenta_contable_inss->concepto = $documento->concepto;
                $documento_cuenta_contable_inss->debe = $debe_inss_administrativo;
                $documento_cuenta_contable_inss->haber = 0;
                $documento_cuenta_contable_inss->debe_org = $debe_inss_administrativo;
                $documento_cuenta_contable_inss->haber_org = 0;
                $documento_cuenta_contable_inss->id_centro =  null;
                $documento_cuenta_contable_inss->id_cuenta_contable = 203;
                $documento_cuenta_contable_inss->cta_contable = '6121-02-001';
                $documento_cuenta_contable_inss->cta_contable_padre = '6121-00-000';
                $documento_cuenta_contable_inss->save();

                //Documento cuenta contable inatec ~ Area administrativa

                //Obtener totalizado de inatec administrativos
                $total_inatec_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_inactec"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',3)->first(); /*id_cat_ingreso_deduccion = 5 ~ horas extras*/

                $debe_inatec_administrativo = $total_inatec_administrativo['total_inactec']?$total_inatec_administrativo['total_inactec']:0;

                $documento_cuenta_contable_inatec = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_inatec->id_documento = $documento->id_documento;
                $documento_cuenta_contable_inatec->id_moneda= 1;
                $documento_cuenta_contable_inatec->concepto = $documento->concepto;
                $documento_cuenta_contable_inatec->debe = $debe_inatec_administrativo;
                $documento_cuenta_contable_inatec->haber = 0;
                $documento_cuenta_contable_inatec->debe_org = $debe_inatec_administrativo;
                $documento_cuenta_contable_inatec->haber_org = 0;
                $documento_cuenta_contable_inatec->id_centro =  null;
                $documento_cuenta_contable_inatec->id_cuenta_contable = 199;
                $documento_cuenta_contable_inatec->cta_contable = '6121-01-007';
                $documento_cuenta_contable_inatec->cta_contable_padre = '6121-00-000';
                $documento_cuenta_contable_inatec->save();

                //Documento cuenta contable comisiones ~ Area administrativa

                //Obtener totalizado de comisiones administrativos
                $total_comisiones_administrativo = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_comisiones"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',1)->where('id_cat_ingreso_deduccion_trabajador',12)->first(); /*id_cat_ingreso_deduccion = 12 ~ comisiones*/

                $debe_comisiones_administrativo = $total_comisiones_administrativo['total_comisiones']?$total_comisiones_administrativo['total_comisiones']:0;

                $documento_cuenta_contable_comisiones = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_comisiones->id_documento = $documento->id_documento;
                $documento_cuenta_contable_comisiones->id_moneda= 1;
                $documento_cuenta_contable_comisiones->concepto = $documento->concepto;
                $documento_cuenta_contable_comisiones->debe = $debe_comisiones_administrativo;
                $documento_cuenta_contable_comisiones->haber = 0;
                $documento_cuenta_contable_comisiones->debe_org = $debe_comisiones_administrativo;
                $documento_cuenta_contable_comisiones->haber_org = 0;
                $documento_cuenta_contable_comisiones->id_centro =  null;
                $documento_cuenta_contable_comisiones->id_cuenta_contable = 194;
                $documento_cuenta_contable_comisiones->cta_contable = '6121-01-002';
                $documento_cuenta_contable_comisiones->cta_contable_padre = '6121-00-000';
                $documento_cuenta_contable_comisiones->save();

                /*----------------Documento cuenta contable sueldos y salarios ~ Area comercial-------------------*/

                //Obtener totalizado de salarios comercial
                $total_salarios_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_salarios"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',4)->first(); /*id_cat_ingreso_deduccion = 4 ~ Salario básico*/

                $debe_salario_comercial = $total_salarios_comercial['total_salarios']?$total_salarios_comercial['total_salarios']:0;

                $documento_cuenta_contable_suelos_comercial = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_suelos_comercial->id_documento = $documento->id_documento;
                $documento_cuenta_contable_suelos_comercial->id_moneda= 1;
                $documento_cuenta_contable_suelos_comercial->concepto = $documento->concepto;
                $documento_cuenta_contable_suelos_comercial->debe = $debe_salario_comercial;
                $documento_cuenta_contable_suelos_comercial->haber = 0;
                $documento_cuenta_contable_suelos_comercial->debe_org = $debe_salario_comercial;
                $documento_cuenta_contable_suelos_comercial->haber_org = 0;
                $documento_cuenta_contable_suelos_comercial->id_centro =  null;
                $documento_cuenta_contable_suelos_comercial->id_cuenta_contable = 138;
                $documento_cuenta_contable_suelos_comercial->cta_contable = '6111-01-000';
                $documento_cuenta_contable_suelos_comercial->cta_contable_padre = '6110-00-000';
                $documento_cuenta_contable_suelos_comercial->save();

                //Documento cuenta contable horas extras ~ Area comercial

                //Obtener totalizado de horas extras comercial
                $total_horas_extras_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_horas"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',9)->first(); /*id_cat_ingreso_deduccion = 5 ~ horas extras*/

                $debe_horas_extras_comercial = $total_horas_extras_comercial['total_horas']?$total_horas_extras_comercial['total_horas']:0;

                $documento_cuenta_contable_horas_extras_comercial = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_horas_extras_comercial->id_documento = $documento->id_documento;
                $documento_cuenta_contable_horas_extras_comercial->id_moneda= 1;
                $documento_cuenta_contable_horas_extras_comercial->concepto = $documento->concepto;
                $documento_cuenta_contable_horas_extras_comercial->debe = $debe_horas_extras_comercial;
                $documento_cuenta_contable_horas_extras_comercial->haber = 0;
                $documento_cuenta_contable_horas_extras_comercial->debe_org = $debe_horas_extras_comercial;
                $documento_cuenta_contable_horas_extras_comercial->haber_org = 0;
                $documento_cuenta_contable_horas_extras_comercial->id_centro =  null;
                $documento_cuenta_contable_horas_extras_comercial->id_cuenta_contable = 169;
                $documento_cuenta_contable_horas_extras_comercial->cta_contable = '6111-01-003';
                $documento_cuenta_contable_horas_extras_comercial->cta_contable_padre = '6111-00-000';
                $documento_cuenta_contable_horas_extras_comercial->save();

                //Documento cuenta contable vacaciones ~ Area comercial

                //Obtener totalizado de vacaciones comercial
                $total_vacaciones_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_vacaciones"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',10)->first(); /*id_cat_ingreso_deduccion = 5 ~ horas extras*/

                $debe_vacaciones_comercial = $total_vacaciones_comercial['total_vacaciones']?$total_vacaciones_comercial['total_vacaciones']:0;

                $documento_cuenta_contable_vacaciones_comercial = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_vacaciones_comercial->id_documento = $documento->id_documento;
                $documento_cuenta_contable_vacaciones_comercial->id_moneda= 1;
                $documento_cuenta_contable_vacaciones_comercial->concepto = $documento->concepto;
                $documento_cuenta_contable_vacaciones_comercial->debe = $debe_vacaciones_comercial;
                $documento_cuenta_contable_vacaciones_comercial->haber = 0;
                $documento_cuenta_contable_vacaciones_comercial->debe_org = $debe_vacaciones_comercial;
                $documento_cuenta_contable_vacaciones_comercial->haber_org = 0;
                $documento_cuenta_contable_vacaciones_comercial->id_centro =  null;
                $documento_cuenta_contable_vacaciones_comercial->id_cuenta_contable = 170;
                $documento_cuenta_contable_vacaciones_comercial->cta_contable = '6111-01-004';
                $documento_cuenta_contable_vacaciones_comercial->cta_contable_padre = '6111-00-000';
                $documento_cuenta_contable_vacaciones_comercial->save();

                //Documento cuenta contable treceavo mes ~ Area comercial

                //Obtener totalizado de treceavo mes comercial
                $total_treceavomes_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_treceavomes"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',11)->first(); /*id_cat_ingreso_deduccion = 5 ~ horas extras*/

                $debe_treceavomes_comercial = $total_treceavomes_comercial['total_treceavomes']?$total_treceavomes_comercial['total_treceavomes']:0;

                $documento_cuenta_contable_treceavomes_comercial = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_treceavomes_comercial->id_documento = $documento->id_documento;
                $documento_cuenta_contable_treceavomes_comercial->id_moneda= 1;
                $documento_cuenta_contable_treceavomes_comercial->concepto = $documento->concepto;
                $documento_cuenta_contable_treceavomes_comercial->debe = $debe_treceavomes_comercial;
                $documento_cuenta_contable_treceavomes_comercial->haber = 0;
                $documento_cuenta_contable_treceavomes_comercial->debe_org = $debe_treceavomes_comercial;
                $documento_cuenta_contable_treceavomes_comercial->haber_org = 0;
                $documento_cuenta_contable_treceavomes_comercial->id_centro =  null;
                $documento_cuenta_contable_treceavomes_comercial->id_cuenta_contable = 171;
                $documento_cuenta_contable_treceavomes_comercial->cta_contable = '6111-01-005';
                $documento_cuenta_contable_treceavomes_comercial->cta_contable_padre = '6111-00-000';
                $documento_cuenta_contable_treceavomes_comercial->save();

                //Documento cuenta contable inss patronal ~ Area comercial

                //Obtener totalizado de inss patronal comercial
                $total_inss_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_inss"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',1)->first(); /*id_cat_ingreso_deduccion = 5 ~ horas extras*/

                $debe_inss_comercial = $total_inss_comercial['total_inss']?$total_inss_comercial['total_inss']:0;

                $documento_cuenta_contable_inss_comercial = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_inss_comercial->id_documento = $documento->id_documento;
                $documento_cuenta_contable_inss_comercial->id_moneda= 1;
                $documento_cuenta_contable_inss_comercial->concepto = $documento->concepto;
                $documento_cuenta_contable_inss_comercial->debe = $debe_inss_comercial;
                $documento_cuenta_contable_inss_comercial->haber = 0;
                $documento_cuenta_contable_inss_comercial->debe_org = $debe_inss_comercial;
                $documento_cuenta_contable_inss_comercial->haber_org = 0;
                $documento_cuenta_contable_inss_comercial->id_centro =  null;
                $documento_cuenta_contable_inss_comercial->id_cuenta_contable = 177;
                $documento_cuenta_contable_inss_comercial->cta_contable = '6111-02-001';
                $documento_cuenta_contable_inss_comercial->cta_contable_padre = '6111-00-000';
                $documento_cuenta_contable_inss_comercial->save();

                //Documento cuenta contable inatec ~ Area comercial

                //Obtener totalizado de inatec comercial
                $total_inatec_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_inactec"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',3)->first(); /*id_cat_ingreso_deduccion = 5 ~ horas extras*/

                $debe_inatec_comercial = $total_inatec_comercial['total_inactec']?$total_inatec_comercial['total_inactec']:0;

                $documento_cuenta_contable_inatec_comercial = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_inatec_comercial->id_documento = $documento->id_documento;
                $documento_cuenta_contable_inatec_comercial->id_moneda= 1;
                $documento_cuenta_contable_inatec_comercial->concepto = $documento->concepto;
                $documento_cuenta_contable_inatec_comercial->debe = $debe_inatec_comercial;
                $documento_cuenta_contable_inatec_comercial->haber = 0;
                $documento_cuenta_contable_inatec_comercial->debe_org = $debe_inatec_comercial;
                $documento_cuenta_contable_inatec_comercial->haber_org = 0;
                $documento_cuenta_contable_inatec_comercial->id_centro =  null;
                $documento_cuenta_contable_inatec_comercial->id_cuenta_contable = 173;
                $documento_cuenta_contable_inatec_comercial->cta_contable = '6111-01-007';
                $documento_cuenta_contable_inatec_comercial->cta_contable_padre = '6111-00-000';
                $documento_cuenta_contable_inatec_comercial->save();

                //Documento cuenta contable comisiones ~ Area comercial

                //Obtener totalizado de comisiones comercial
                $total_comisiones_comercial = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_comisiones"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('rrhh.trabajadores.id_nomina',2)->where('id_cat_ingreso_deduccion_trabajador',12)->first(); /*id_cat_ingreso_deduccion = 12 ~ comisiones*/

                $debe_comisiones_comercial = $total_comisiones_comercial['total_comisiones']?$total_comisiones_comercial['total_comisiones']:0;

                $documento_cuenta_contable_comisiones_comercial = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_comisiones_comercial->id_documento = $documento->id_documento;
                $documento_cuenta_contable_comisiones_comercial->id_moneda= 1;
                $documento_cuenta_contable_comisiones_comercial->concepto = $documento->concepto;
                $documento_cuenta_contable_comisiones_comercial->debe = $debe_comisiones_comercial;
                $documento_cuenta_contable_comisiones_comercial->haber = 0;
                $documento_cuenta_contable_comisiones_comercial->debe_org = $debe_comisiones_comercial;
                $documento_cuenta_contable_comisiones_comercial->haber_org = 0;
                $documento_cuenta_contable_comisiones_comercial->id_centro =  null;
                $documento_cuenta_contable_comisiones_comercial->id_cuenta_contable = 168;
                $documento_cuenta_contable_comisiones_comercial->cta_contable = '6111-01-002';
                $documento_cuenta_contable_comisiones_comercial->cta_contable_padre = '6111-00-000';
                $documento_cuenta_contable_comisiones_comercial->save();

                //Documento cuenta contable retenciones por pagar
                /*obtener total de ir de planilla*/
                $total_ir = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_ir"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('id_cat_ingreso_deduccion_trabajador',2)->first(); /*id_cat_ingreso_deduccion = 12 ~ comisiones*/

                $total_ir_neto = $total_ir['total_ir']?$total_ir['total_ir']:0;
                $retenciones_por_pagar = $debe_inss_administrativo + $debe_inss_comercial + $total_ir_neto;

                $documento_cuenta_contable_retenciones_pagar = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_retenciones_pagar->id_documento = $documento->id_documento;
                $documento_cuenta_contable_retenciones_pagar->id_moneda= 1;
                $documento_cuenta_contable_retenciones_pagar->concepto = $documento->concepto;
                $documento_cuenta_contable_retenciones_pagar->debe = 0;
                $documento_cuenta_contable_retenciones_pagar->haber = $retenciones_por_pagar;
                $documento_cuenta_contable_retenciones_pagar->debe_org = 0;
                $documento_cuenta_contable_retenciones_pagar->haber_org = $retenciones_por_pagar;
                $documento_cuenta_contable_retenciones_pagar->id_centro =  null;
                $documento_cuenta_contable_retenciones_pagar->id_cuenta_contable = 33;
                $documento_cuenta_contable_retenciones_pagar->cta_contable = '2130-00-000';
                $documento_cuenta_contable_retenciones_pagar->cta_contable_padre = '2130-00-000';
                $documento_cuenta_contable_retenciones_pagar->save();

                //Documento cuenta contable aportes por pagar

                /*calcular total de inss patronal e inatec*/

                $total_inss_patronal = RRHHGenerarPlanilla::select(DB::raw("SUM(monto) as total_inss_patronal"))
                    ->join('rrhh.trabajadores','rrhh.planillas.id_trabajador','rrhh.trabajadores.id_trabajador')->where('id_planilla_control',$planillax['id_planilla_control'])
                    ->where('id_cat_ingreso_deduccion_trabajador',13)->first(); /*id_cat_ingreso_deduccion = 13 ~ inss patronal*/
                $total_inss_patronal_neto = $total_inss_patronal['total_inss_patronal']?$total_ir['total_inss_patronal']:0;

                $aportes_por_pagar = $debe_inatec_administrativo + $debe_inatec_comercial + $total_inss_patronal_neto;

                //$documento_cuenta_contable_aportes_pagar = new ContabilidadDocumentosMovimientos;
                //$documento_cuenta_contable_aportes_pagar->id_documento = $documento->id_documento;
                //$documento_cuenta_contable_aportes_pagar->id_moneda= 1;
                //$documento_cuenta_contable_aportes_pagar->concepto = $documento->concepto;
                //$documento_cuenta_contable_aportes_pagar->debe = 0;
                //$documento_cuenta_contable_aportes_pagar->haber = $retenciones_por_pagar;
                //$documento_cuenta_contable_aportes_pagar->debe_org = 0;
                //$documento_cuenta_contable_aportes_pagar->haber_org = $retenciones_por_pagar;
                //$documento_cuenta_contable_aportes_pagar->id_centro =  null;
                //$documento_cuenta_contable_aportes_pagar->id_cuenta_contable = 160;
                //$documento_cuenta_contable_aportes_pagar->cta_contable = '2130-00-000';
                //$documento_cuenta_contable_aportes_pagar->cta_contable_padre = '2130-00-000';
                //$documento_cuenta_contable_aportes_pagar->save();*/

                //Documento cuenta contable banco
                $monto_total_general = $aportes_por_pagar + $retenciones_por_pagar + $debe_salario_administrativo+$debe_salario_comercial
                    +$debe_treceavomes_administrativo+$debe_treceavomes_comercial+$debe_vacaciones_administrativo+$debe_vacaciones_comercial
                    +$debe_horas_extras_administrativo+$debe_horas_extras_comercial+$debe_comisiones_administrativo+$debe_comisiones_comercial;

                $documento_cuenta_contable_banco = new ContabilidadDocumentosMovimientos;
                $documento_cuenta_contable_banco->id_documento = $documento->id_documento;
                $documento_cuenta_contable_banco->id_moneda= 1;
                $documento_cuenta_contable_banco->concepto = $documento->concepto;
                $documento_cuenta_contable_banco->debe = 0;
                $documento_cuenta_contable_banco->haber = $monto_total_general;
                $documento_cuenta_contable_banco->debe_org = 0;
                $documento_cuenta_contable_banco->haber_org = $monto_total_general;
                $documento_cuenta_contable_banco->id_centro =  null;
                $documento_cuenta_contable_banco->id_cuenta_contable = 160;
                $documento_cuenta_contable_banco->cta_contable = '1113-01-002';
                $documento_cuenta_contable_banco->cta_contable_padre = '1113-01-002';
                $documento_cuenta_contable_banco->save();

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);
            }catch (Exception $e){

                DB::rollBack();

                return response()->json([
                    'status' => 'error',
                    'result' => 'Error de base de datos',
                    'messages' => null
                ]);
            }
        }else
        {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }

    public function nuevo(Request $request)
    {
        $planillas_controles = RRHHPlanillaControl::select(['id_planilla_control','codigo_planilla','tipo_planilla','descripcion','anio_proceso','mes_proceso','estado'])->whereIn('estado',array(1,2))->get();

        return response()->json([
            'status' => 'success',
            'result' => [
                'planillas_controles' => $planillas_controles,
            ],
            'messages' => null
        ]);

    }

    public function generarReporte($ext)
    {
        // echo $ext;
        // $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteZonas';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteZonas';
             $input = '/var/www/html/resources/reports/ReporteZonas';
             $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteZonas';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteZonas.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

}