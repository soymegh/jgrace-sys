<?php 

namespace App\Http\Controllers;

use App\Models\AdmonAjustes;
use App\Models\AdmonMenus;
use App\Models\CajaChicaCaja;
use App\Models\CajaChicaComprobante;
use App\Models\CajaChicaSolicitudViatico;
use App\Models\ContabilidadPeriodoFiscal;
use App\Models\RRHHFeriados;
use App\Models\RRHHSaldosVacaciones;
use App\Models\RRHHSolicitudVacaciones;
use App\Models\RRHHSolicitudVacacionesDetalle;
use App\Models\RRHHTrabajadores;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;
use Validator,Auth;
use App\Models\RRHHNivelesEstudios;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class RRHHSolicitudVacacionesController extends Controller
{
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param Request $request
     * @param RRHHSolicitudVacaciones $solicitud
     * @return  json(array)
     */

    public function obtener(Request $request, RRHHSolicitudVacaciones $solicitud)
    {
        $solicitud = $solicitud->obtenerSolicitudVacaciones($request);
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
     * @param RRHHFeriados $solicitud
     * @return  json(array)
     */

    public function obtenerTodas(Request $request, RRHHSolicitudVacaciones $solicitud)
    {
        $solicitud = RRHHSolicitudVacaciones::orderby('id_vacacion_solicitud')->get();
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

    public function obtenersolicitud(Request $request)
    {
        $rules = [
            'id_vacacion_solicitud'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $solicitud = RRHHSolicitudVacaciones::where('id_vacacion_solicitud',$request->id_vacacion_solicitud)->with('solicitudDetalle','solicitudTrabajador','trabajadorDetalles','trabajadorArea','trabajadorCargo','trabajadorSaldoVacacion')->first();
            $periodos = ContabilidadPeriodoFiscal::select('id_periodo_fiscal','periodo')->with('mesesPeriodo')->get();

            if(!empty($solicitud)){	
            return response()->json([
                'status' => 'success',
                'result' => [
                    'solicitud' => $solicitud,
                    'periodos' => $periodos
                ],
                'messages' => null
            ]);

        }
		else{
		  return response()->json([
				'status' => 'error',
				'result' => array('id_vacacion_solicitud'=>["Datos no encontrados"]),
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
        $trabajadores = RRHHTrabajadores::select('id_trabajador','primer_apellido','primer_nombre','segundo_apellido','segundo_nombre',
            'id_area','id_cargo','codigo',
            DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS nombre_completo"),
            DB::raw("coalesce((select sv.saldo_actual from rrhh.vacaciones_saldos sv where sv.id_trabajador = rrhh.trabajadores.id_trabajador),0) AS saldo_actual")
            )->with('trabajadorCargo','trabajadorArea','trabajadorDetalles')->get();

        $periodos = ContabilidadPeriodoFiscal::select('id_periodo_fiscal','periodo')->with('mesesPeriodo')->get();

        $listado_reportes = AdmonMenus::select('admon.menus.id_menu')->join('admon.roles_menus','admon.menus.id_menu','admon.roles_menus.id_menu')
            ->join('admon.roles','admon.roles.id_rol','admon.roles_menus.id_rol')
            ->where('admon.roles.id_rol',Auth::user()->id_rol)
            ->where('admon.menus.activo',1)
            ->where('admon.menus.tipo_menu',4)
            ->orderBy('admon.menus.secuencia')
            ->get();

            return response()->json([
                'status' => 'success',
                'result' => [
                    'trabajadores' => $trabajadores,
                    'periodos' => $periodos,
                    'lista_reportes'=>$listado_reportes
                ],
                'messages' => null
            ]);

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
            'f_solicitud' => 'required|date',
            'trabajador.id_trabajador' => 'required|integer',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date',
            'total_dias' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'costo_vacaciones' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'saldo_dias' => 'required|numeric|',
            'justificacion' => 'required|string|max:100',

            'detalleSolicitud.*.fecha_desdex' => 'required|date',
            'detalleSolicitud.*.fecha_hastax' => 'required|date',
            'detalleSolicitud.*.cantidad_dias' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'detalleSolicitud.*.anio' => 'required|integer',
            'detalleSolicitud.*.mes' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try {
                DB::beginTransaction();

                $secuencia = RRHHSolicitudVacaciones::max('id_vacacion_solicitud') + 1;
                $solicitud = new RRHHSolicitudVacaciones();
                $solicitud->id_tipo = $request->tipo_solicitud;
                $solicitud->num_solicitud = 'SV-' . $secuencia;
                $solicitud->f_solicitud = $request->f_solicitud;
                $solicitud->estado = 1;
                $solicitud->id_trabajador = $request['trabajador']['id_trabajador'];
                $solicitud->fecha_desde = $request->fecha_desde;
                $solicitud->fecha_hasta = $request->fecha_hasta;
                $solicitud->total_dias = $request->total_dias;
                $solicitud->costo_vacaciones = $request->costo_vacaciones;
                $solicitud->saldo_dias = $request->saldo_dias;
                $solicitud->justificacion = $request->justificacion;
                $solicitud->u_grabacion = Auth::user()->usuario;
                $solicitud->save();

                $i = 1;
                foreach ($request->detalleSolicitud as $comprobante_detalle) {
                    $detalles = new RRHHSolicitudVacacionesDetalle();
                    $detalles->id_vacacion_solicitud = $solicitud->id_vacacion_solicitud;
                    $detalles->fecha_desde = $comprobante_detalle['fecha_desdex'];
                    $detalles->fecha_hasta = $comprobante_detalle['fecha_hastax'];
                    $detalles->cantidad_dias = $comprobante_detalle['cantidad_dias'];
                    $detalles->anio = $comprobante_detalle['anio'];
                    $detalles->mes = $comprobante_detalle['mes'];
                    $detalles->save();
                    $i++;
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
            'saldo_dias' => 'required|numeric|',
            'justificacion' => 'required|string|max:100',
            //'observacion' => 'required|string|max:100',

            'solicitud_detalle.*.fecha_desde' => 'required|date',
            'solicitud_detalle.*.fecha_hasta' => 'required|date',
            'solicitud_detalle.*.cantidad_dias' => 'required|numeric|',
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


            $solicitud = RRHHSolicitudVacaciones::where('id_vacacion_solicitud',$request->id_vacacion_solicitud)->with('solicitudTrabajador','solicitudDetalle')->first();

            if($solicitud->estado ==1 && $request->estado == 0){

                $solicitud->estado = $request->estado;
                $solicitud->u_anulacion = Auth::user()->usuario;
                $solicitud->f_anulacion = Carbon::now()->toDateTimeString();
                $solicitud->observacion = $request->observacion;
                $solicitud->save();

            }elseif($solicitud->estado==1 && $request-> estado == 2){

                $solicitud->estado = $request->estado;

                $factor= -1;

                if($solicitud->id_tipo == 2){ $factor= 1;}

                $solicitud->u_autoriza = Auth::user()->usuario;
                $solicitud->f_autoriza = date('Y-m-d H:i:s');
                $solicitud->dias_autorizados = $solicitud->total_dias;
                $solicitud->observacion = $request->observacion;
                $solicitud->save();

                $saldo = RRHHSaldosVacaciones::where('id_trabajador', $solicitud->id_trabajador)->first();

                if(!empty($saldo)) {
                    $saldo->dias_solicitados = $saldo->dias_solicitados - ($solicitud->dias_autorizados*$factor);
                    $saldo->saldo_actual = $saldo->saldo_actual + ($solicitud->dias_autorizados*$factor);
                    $saldo->u_grabacion = Auth::user()->usuario;
                    $saldo->f_grabacion =date('Y-m-d H:i:s');
                    $saldo->save();

                }else {
                   //print_r( $solicitud['solicitudTrabajador']);
                    $saldo = new RRHHSaldosVacaciones();
                    $saldo->id_area = $solicitud['solicitudTrabajador']['id_area'];
                    $saldo->id_trabajador = $solicitud['solicitudTrabajador']['id_trabajador'];
                    $saldo->id_cargo = $solicitud['solicitudTrabajador']['id_cargo'];
                    $saldo->anio = $solicitud['solicitudDetalle'][0]['anio'];
                    $saldo->mes = $solicitud['solicitudDetalle'][0]['mes'];
                    $saldo->saldo_inicial_dias = 0;
                    $saldo->acumulados_mes = 0;
                    $saldo->dias_solicitados = $saldo->dias_solicitados - ($solicitud->dias_autorizados*$factor);
                    $saldo->saldo_actual = $saldo->saldo_actual + ($solicitud->dias_autorizados*$factor);
                    $saldo->u_grabacion = Auth::user()->usuario;
                    $saldo->f_grabacion = date('Y-m-d H:i:s');
                    $saldo->fecha_proceso = date('Y-m-d H:i:s');
                    $saldo->save();
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