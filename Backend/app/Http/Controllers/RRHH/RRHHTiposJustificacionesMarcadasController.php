<?php 

namespace App\Http\Controllers;

use App\Models\AdmonAjustes;
use App\Models\CajaChicaCaja;
use App\Models\CajaChicaComprobante;
use App\Models\CajaChicaSolicitudViatico;
use App\Models\RRHHIngresosDeducciones;
use App\Models\RRHHIngresosDeduccionesTrabajadores;
use App\Models\RRHHMarcadas;
use App\Models\RRHHPlanillaControl;
use App\Models\RRHHSaldosVacaciones;
use App\Models\RRHHSolicitudVacaciones;
use App\Models\RRHHSolicitudVacacionesDetalle;
use App\Models\RRHHTiposJustificacionesMarcadas;
use App\Models\RRHHTrabajadores;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;
use Validator,Auth;
use App\Models\RRHHNivelesEstudios;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class RRHHTiposJustificacionesMarcadasController extends Controller
{
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param Request $request
     * @param RRHHTiposJustificacionesMarcadas $solicitud
     * @return  json(array)
     */

    public function obtener(Request $request, RRHHTiposJustificacionesMarcadas $solicitud)
    {
        $solicitud = $solicitud->obtenerJustificacion($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $solicitud->total(), 
                'rows' => $solicitud->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de zonas sin paginacion
     *
     * @access  public
     * @param Request $request
     * @param RRHHMarcadas $solicitud
     * @return  json(array)
     */

    public function obtenerTodas(Request $request, RRHHTiposJustificacionesMarcadas $solicitud)
    {
        $solicitud = RRHHTiposJustificacionesMarcadas::orderby('id_tipo_marcada_justificacion')->get();

        return response()->json([
            'status' => 'success',
            'result' => $solicitud,
            'messages' => null
        ]);
    }

    /**
     * obtener Rol Especifico
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function obtenerJustificacion(Request $request)
    {
        $rules = [
            'id_tipo_marcada_justificacion'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $solicitud = RRHHTiposJustificacionesMarcadas::where('id_tipo_marcada_justificacion',$request->id_tipo_marcada_justificacion)->first();

            if(!empty($solicitud)){	
            return response()->json([
                'status' => 'success',
                'result' => [
                    'solicitud' => $solicitud,
                ],
                'messages' => null
            ]);

        }
		else{
		  return response()->json([
				'status' => 'error',
				'result' => array('id_marcada'=>["Datos no encontrados"]),
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
    public function nuevo(Request $request)
    {
        $trabajadores = RRHHTrabajadores::select('id_trabajador','primer_apellido','primer_nombre','segundo_apellido','segundo_nombre','codigo',
            'id_area','id_cargo','codigo',
            DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido,'(',codigo,')') AS nombre_completo"))->with('trabajadorCargo','trabajadorArea','trabajadorDetalles')->get();

        $planillas = RRHHPlanillaControl::select('id_planilla_control','id_sucursal','codigo_planilla','tipo_planilla','descripcion',
            DB::raw("CONCAT(descripcion,'(',codigo_planilla,')' )AS planilla"))->get();

        $ingresos = RRHHIngresosDeducciones::select('id_cat_ingreso_deduccion','cve_ingreso_deduccion','descripcion','codigo',
            DB::raw("CONCAT(descripcion,'(',cve_ingreso_deduccion,')' ) AS Ingreso"))->get();

        if(!empty($request->id_planilla_control))
        {
            $planilla = RRHHPlanillaControl::select('id_planilla_control','id_sucursal','codigo_planilla','tipo_planilla','descripcion',
                DB::raw("CONCAT(descripcion,'(',codigo_planilla,')' )AS planilla"))->where('id_planilla_control',$request->id_planilla_control)->get();
            return response()->json([
                'status' => 'success',
                'result' => [
                    'trabajadores' => $trabajadores,
                    'planillas' => $planillas,
                    'ingresos' => $ingresos,
                    'planilla' => $planilla[0]

                ],
                'messages' => null
            ]);
        }else {

            return response()->json([
                'status' => 'success',
                'result' => [
                    'trabajadores' => $trabajadores,
                    'planillas' => $planillas,
                    'ingresos' => $ingresos,

                ],
                'messages' => null
            ]);
        }

    }


    public function obtenerPlanilla(Request $request)
    {
        $rules = [
            'id_planilla_control' => 'required|integer|min:1',
            'id_trabajador' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $ingresos = RRHHIngresosDeduccionesTrabajadores::where('id_trabajador',$request->id_trabajador)->where('id_planilla_control',$request->id_planilla_control)->where('estado',1)->with('asignacionIngreso')->get();

            if(!empty($ingresos)){

                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'ingresos' => $ingresos,
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

    /**
     * Crear un nuevo rol
     *
     * @access  public
     * @param   
     * @return  json(string)
     */

    public function registrar(Request $request)
    {
        $rules = [
            //'detalleSolicitud.*.codigo' => 'required|integer',
            'detalleSolicitud.*.id_trabajador' => 'required|integer',
            'detalleSolicitud.*.id_planilla_control' => 'required|integer',
            'detalleSolicitud.*.id_sucursal' => 'required|integer',
            'detalleSolicitud.*.id_cat_ingreso_deduccion' => 'required|integer',
            'detalleSolicitud.*.monto' => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try {
                DB::beginTransaction();

              /*  $solicitud = new RRHHIngresosDeduccionesTrabajadores;
               $solicitud->id_planilla_control = $request['planilla']['id_planilla_control'];
                $solicitud->id_sucursal = $request['planilla']['id_sucursal'];
                $solicitud->id_trabajador = $request['trabajador']['id_trabajador'];
                $solicitud->save();*/

                RRHHIngresosDeduccionesTrabajadores::where('id_cat_ingreso_deduccion_trabajador',$request->id_cat_ingreso_deduccion_trabajador)->delete();

                $i = 1;
                foreach ($request->detalleSolicitud as $comprobante_detalle) {

                    if($comprobante_detalle['id_cat_ingreso_deduccion_trabajador'] == 0 && $comprobante_detalle['estado'] == 1) {
                        $detalles = new RRHHIngresosDeduccionesTrabajadores;
                        $detalles->id_planilla_control = $comprobante_detalle['id_planilla_control'];
                        $detalles->id_sucursal = $comprobante_detalle['id_sucursal'];
                        $detalles->id_trabajador = $comprobante_detalle['id_trabajador'];
                        $detalles->id_cat_ingreso_deduccion = $comprobante_detalle['id_cat_ingreso_deduccion'];
                        $detalles->monto = $comprobante_detalle['monto'];
                        $detalles->estado = 1;
                        $detalles->u_grabacion = Auth::user()->usuario;
                        $detalles->save();
                        $i++;
                    }
                    if($comprobante_detalle['id_cat_ingreso_deduccion_trabajador'] > 0 && $comprobante_detalle['estado'] == 0) {
                        $detalles = RRHHIngresosDeduccionesTrabajadores::find($comprobante_detalle['id_cat_ingreso_deduccion_trabajador']);
                        $detalles->estado = $comprobante_detalle['estado'];
                        $detalles->u_modificacion = Auth::user()->usuario;
                        $detalles->save();
                        $i++;
                    }
                }
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
            'id_vacacion_solicitud' => 'required|integer|min:1',

            'f_solicitud' => 'required|date',
            'solicitud_trabajador.id_trabajador' => 'required|integer',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date',
            'total_dias' => 'required|numeric',
            'id_tipo' => 'required|integer',
            'costo_vacaciones' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'saldo_dias' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'justificacion' => 'required|string|max:100',
            'observacion' => 'required|string|max:100',

            'solicitud_detalle.*.fecha_desde' => 'required|date',
            'solicitud_detalle.*.fecha_hasta' => 'required|date',
            'solicitud_detalle.*.cantidad_dias' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'solicitud_detalle.*.anio' => 'required|integer',
            'solicitud_detalle.*.mes' => 'required|integer',

        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try{
                DB::beginTransaction();


                $solicitud = RRHHSolicitudVacaciones::findOrFail($request->id_vacacion_solicitud);
                if($solicitud->estado == 1 || $solicitud->estado ==2){
                    $solicitud->id_tipo = $request->id_tipo;
                    $solicitud->num_solicitud = $request->num_solicitud;
                    $solicitud->f_solicitud = $request->f_solicitud;
                    $solicitud->estado = $request->estado;
                    $solicitud->id_trabajador = $request['solicitud_trabajador']['id_trabajador'];
                    $solicitud->fecha_desde = $request->fecha_desde;
                    $solicitud->fecha_hasta = $request->fecha_hasta;
                    $solicitud->total_dias = $request->total_dias;
                    $solicitud->costo_vacaciones = $request->costo_vacaciones;
                    $solicitud->saldo_dias = $request->saldo_dias;
                    $solicitud->justificacion = $request->justificacion;
                    $solicitud->observacion = $request->observacion;
                    $solicitud->u_modificacion = Auth::user()->usuario;
                    $solicitud->save();


                    RRHHSolicitudVacacionesDetalle::where('id_vacacion_solicitud', $solicitud->id_vacacion_solicitud)->delete();

                    $i = 1;
                    foreach ($request->solicitud_detalle as $comprobante_detalle) {
                        $detalles = new RRHHSolicitudVacacionesDetalle();
                        $detalles->id_vacacion_solicitud = $solicitud->id_vacacion_solicitud;
                        $detalles->fecha_desde = $comprobante_detalle['fecha_desde'];
                        $detalles->fecha_hasta = $comprobante_detalle['fecha_hasta'];
                        $detalles->cantidad_dias = $comprobante_detalle['cantidad_dias'];
                        $detalles->anio = $comprobante_detalle['anio'];
                        $detalles->mes = $comprobante_detalle['mes'];
                        $detalles->save();
                        $i++;
                    }
                }else
                {
                    return response()->json([
                        'status' => 'error',
                        'result' => array('id_vacacion_solicitud'=>['El estado de la solicitud a cambiado externamente']),
                        'messages' => null
                    ]);
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

    public function cambiarEstadoSolicitud(Request $request)
    {
        $rules = [
            'id_vacacion_solicitud' => 'required|integer|min:1',
            'estado' => 'required|integer|min:0',
            'observacion' => 'required|string|max:200',
        ];

        $validator = Validator::make($request->all(), $rules);

        if (!$validator->fails()) {

            try{
                DB::beginTransaction();


            $solicitud = RRHHSolicitudVacaciones::find($request->id_vacacion_solicitud);

            if($solicitud->estado==1 && $request-> estado == 0){

                $solicitud->estado = $request->estado;
                $solicitud->u_anulacion = Auth::user()->usuario;
                $solicitud->f_anulacion = Carbon::now()->toDateTimeString();
                $solicitud->observacion = $request->observacion;

            }elseif($solicitud->estado==1 && $request-> estado == 2){
                $solicitud->estado = $request->estado;

                $factor= -1;

                if($solicitud->id_tipo == 2){ $factor= 1;}

                $solicitud->u_autoriza = Auth::user()->usuario;
                $solicitud->f_autoriza = Carbon::now()->toDateTimeString();
                $solicitud->dias_autorizados = $solicitud->total_dias;
                $solicitud->save();

                $saldo = RRHHSaldosVacaciones::where('id_trabajador', $solicitud->id_trabajador)->first();

                if(!empty($saldo)) {
                    $saldo->id_area = $request->solicitud_trabajador['id_area'];
                    $saldo->id_cargo = $request->solicitud_trabajador['id_cargo'];
                    $saldo->anio = $request->solicitud_detalle['anio'];
                    $saldo->mes = $request->solicitud_detalle['mes'];
                    $saldo->dias_solicitados = $saldo->dias_solicitados - ($solicitud->dias_autorizados*$factor);
                    $saldo->saldo_actual = $saldo->saldo_actual + ($solicitud->dias_autorizados*$factor);
                    $saldo->u_grabacion = Auth::user()->usuario;
                    $saldo->f_grabacion = Carbon::now()->toDateTimeString();
                    $saldo->save();

                }else {}
            }
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
            'id_feriado' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $solicitud = RRHHFeriados::find($request->id_feriado);
            $solicitud->estado = 0;
            $solicitud->save();

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
     * Activa rol
     *
     * @access  public
     * @param   
     * @return  json(string)
     */

    public function activar(Request $request)
    {
        $rules = [
            'id_feriado' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $rol = RRHHFeriados::find($request->id_feriado);
            $rol->estado = 1;
            $rol->save();

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

    public function generarReporte($id_trabajador)
    {
        // echo $ext;
         $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteSolicitudVacaciones';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteSolicitudVacaciones';
             $input = '/var/www/html/resources/reports/ReporteSolicitudVacaciones';
             $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteSolicitudVacaciones';
            $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
            $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
            $url = '/var/www/html/resources/reports/';

            $options = [
                'format' => [$ext],
                'locale' => 'en',
                'params' => [
                    'id_trabajador' => $id_trabajador,
                    'empresa_nombre' => $nombre_empresa->valor,
                    'empresa_logo' =>  env('APP_URL_REPORTS').$logo_empresa->file_thumbnail
                ],
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

            return response()->download($output . '.' . $ext ,$hora_actual. 'FormatoSolicitudVacaciones.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

}