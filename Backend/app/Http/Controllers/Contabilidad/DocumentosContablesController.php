<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\CajaBancoFacturas;
use App\Models\CajaBancoMinutasDeposito;
use App\Models\CajaBancoMonedas;
use App\Models\CajaBancoSolicitudesPago;
use App\Models\CajaBancoTasasCambios;
use App\Models\Contabilidad\CentrosCostosIngresos;
use App\Models\Contabilidad\CuentasContablesVista;
use App\Models\Contabilidad\Monedas;
use App\Models\Contabilidad\TasasCambios;
use App\Models\CuentasXCobrarRecibos;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPJasper\Exception\ErrorCommandExecutable;
use PHPJasper\Exception\InvalidCommandExecutable;
use PHPJasper\Exception\InvalidFormat;
use PHPJasper\Exception\InvalidInputFile;
use PHPJasper\Exception\InvalidResourceDirectory;
use PHPJasper\PHPJasper;
use DateTime;
use App\Models\Contabilidad\DocumentosContables;
use App\Models\Contabilidad\DocumentosCuentas;
use App\Models\Contabilidad\TiposDocumentos;
use App\Models\Contabilidad\PeriodosFiscales;
use App\Models\Contabilidad\PeriodosMeses;
use Illuminate\Http\Request;

class DocumentosContablesController extends Controller
{
    /**
     * Obtener una lista de Documentos Contables
     *
     * @access  public
     * @param Request $request
     * @param DocumentosContables $documentos_contables
     * @return JsonResponse
     */

    public function obtener(Request $request, DocumentosContables $documentos_contables)
    {
        $documentos_contables = $documentos_contables->obtener($request);
        foreach($documentos_contables as $documento ){
            $items = collect($documento->movimientosCuentas);
            $documento->total_debe = $items->sum(function($item) {
                return $item['debe'];
            });
            $documento->total_haber = $items->sum(function($item) {
                return $item['haber'];
            });
        }
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $documentos_contables->total(),
                'rows' => $documentos_contables->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de Roles sin ningun filtro
     *
     * @access  public
     * @param Request $request
     * @param DocumentosContables $documentos_contables
     * @return JsonResponse
     */

    public function obtenerTodos(Request $request, DocumentosContables $documentos_contables)
    {
        $documentos_contables = DocumentosContables::all();
        return response()->json([
            'status' => 'success',
            'result' => $documentos_contables,
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

    public function obtenerDocumentoContable(Request $request)
    {
        $rules = [
            'id_documento' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $documento_contable = DocumentosContables::find($request->id_documento);
            return response()->json([
                'status' => 'success',
                'result' => $documento_contable,
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
     * obtener Codigo Documento
     *
     * @access  public
     * @param Request $request
     * @param DocumentosContables $documentos_contables
     * @return JsonResponse
     */

    public function obtenerCodigoDocumento(Request $request, DocumentosContables $documentos_contables)
    {
        $rules = [
            'id_tipo_doc' => 'required|integer',
            'fecha_doc' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $codigo = $documentos_contables->obtenerCodigoDocumento($request);
            $tasa = TasasCambios::select('tasa')->where('fecha',$request->fecha_doc)->first();
            return response()->json([
                'status' => 'success',
                'result' => [
                    'codigo' => $codigo[0],
                    't_cambio' => $tasa,
                ],
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
     * Crear un nuevo tipo de Salida
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $messages = [
            'detalleMovimientos.min' => 'Se requiere agregar dos movimientos por lo menos.',
            'detalleMovimientos.*.cuentaContable.id_cuenta_contable.required' => 'Seleccione una cuenta contable válida',
            'detalleMovimientos.*.debe.min' => 'El precio debe ser mayor que cero',
            'detalleMovimientos.*.haber.min' => 'La cantidad debe ser mayor que cero',
            'detalleMovimientos.*.concepto.required' => 'Se debe definir un concepto',
//            'detalleMovimientos.*.centro_costo_ingreso.id_centro.required_if'=>'Se debe definir un centro de costo para estos movimientos'
        ];

        $rules = [
            'fecha_emision' => 'required|date',
            'num_documento' => 'required|string|max:15',
            'codigo_documento' => 'required|integer|min:1',
            'valor' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0',
            //  'moneda' => 'required|array|min:1',
            'concepto' => 'required|string|max:200',
            'tipoDocumento' => 'required|array|min:1',
            'detalleMovimientos' => 'required|array|min:2',
            'detalleMovimientos.*.cuentaContable.id_cuenta_contable' => 'required|integer|exists:pgsql.contabilidad.cuentas_contables,id_cuenta_contable',
            'detalleMovimientos.*.debe' => 'required|numeric|min:0',
            'detalleMovimientos.*.haber' => 'required|numeric|min:0',

            'detalleMovimientos.*.monedaMov' => 'required|array|min:1',
            'detalleMovimientos.*.monedaMov.id_moneda' => 'required|integer|min:1',

            'detalleMovimientos.*.debeORG' => 'required|numeric|min:0',
            'detalleMovimientos.*.haberORG' => 'required|numeric|min:0',
            'detalleMovimientos.*.concepto' => 'required|string|max:100',
//            'detalleMovimientos.*.centro_costo_ingreso_deshabilitada' => 'required|boolean',
//        'detalleMovimientos.*.centro_costo_ingreso' => 'required_if:centro_costo_ingreso_deshabilitada,false|array',
//            'detalleMovimientos.*.centro_costo_ingreso.id_centro' => 'required_if:detalleMovimientos.*.centro_costo_ingreso_deshabilitada,false|integer|min:0',
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            try{

                DB::beginTransaction();
                $documento = new DocumentosContables();
                $tipo = TiposDocumentos::find($request->tipoDocumento['id_tipo_doc']);

                $codigo = $documento->obtenerCodigoDocumento(array('id_tipo_doc'=>$request->tipoDocumento['id_tipo_doc'],'fecha_doc'=>$request->fecha_emision));

                $nuevo_codigo = json_decode($codigo[0]);
                $currency_id = Ajustes::where('id_ajuste',1)->where('id_empresa',$usuario_empresa->id_empresa)->select('valor')->first(); // obtener id_moneda
                $documento->num_documento = $tipo->prefijo.'-'.$nuevo_codigo->secuencia;
                $documento->fecha_emision = $request->fecha_emision;
                $documento->codigo_documento = $nuevo_codigo->secuencia;
                $documento->id_moneda =$currency_id->valor;

                $date = DateTime::createFromFormat("Y-m-d", $documento->fecha_emision);
                $periodo = PeriodosFiscales::where('periodo',$date->format("Y"))->get();

                if(empty($periodo[0])){
                    return response()->json([
                        'status' => 'error',
                        'result' =>   array('fecha_emision'=>["El periodo ".$date->format("Y")." no se encuentra registrado, por favor consulte al administrador"]),
                        'messages' => null
                    ]);
                    exit;
                }

                if($periodo[0]->estado){
                    return response()->json([
                        'status' => 'error',
                        'result' =>   array('fecha_emision'=>["El periodo ".$date->format("Y")." es inválido, ya que se encuentra en estado COMPLETADO"]),
                        'messages' => null
                    ]);
                    exit;
                }

                $periodo_mes = PeriodosMeses::where('id_periodo_fiscal',$periodo[0]->id_periodo_fiscal)->where('mes',$date->format("n"))->get();

                if(empty($periodo_mes[0])){
                    return response()->json([
                        'status' => 'error',
                        'result' =>   array('fecha_emision'=>["El mes ".$date->format("F")." no se encuentra registrado, por favor consulte al administrador"]),
                        'messages' => null
                    ]);
                    exit;
                }

                if($periodo_mes[0]->estado === 2 ){
                    return response()->json([
                        'status' => 'error',
                        'result' =>   array('fecha_emision'=>["El mes ".config('global.meses')[$periodo_mes[0]->mes-1]." es inválido, ya que se encuentra en estado COMPLETADO"]),
                        'messages' => null
                    ]);
                    exit;
                }

                $documento->id_periodo_fiscal = $periodo[0]->id_periodo_fiscal;

                $documento->id_tipo_doc = $request->tipoDocumento['id_tipo_doc'];
                $documento->valor = $request->valor; //monto en cordobas
                $documento->valor_me = $request->valor_me; //monto en dolares
                $documento->concepto = $request->concepto;
                $documento->id_empresa = $usuario_empresa->id_empresa;
                $documento->u_creacion = Auth::user()->name;
                $documento->estado = 1;

/*                 date_default_timezone_set('America/Managua');
                 $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
                 $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                 $log['fecha_log'] = $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') .' a las '.date('h:i:s A');
                 $log['registro'] = 'Registro de salida en sistema por '.$documento->u_creacion;
                 $documento->log_salida = '['.json_encode($log).']';*/

                $documento->save();
                TiposDocumentos::find($documento->id_tipo_doc)->increment('secuencia');

                //print_r($request->detalleMovimientos);
                foreach ($request->detalleMovimientos as $movimientoCuenta) {

                    if($movimientoCuenta['debeORG']>0  || $movimientoCuenta['haberORG'] >0){
                        $documento_cuenta_contable = new DocumentosCuentas();

                        $documento_cuenta_contable->id_documento = $documento->id_documento;
                        $documento_cuenta_contable->concepto = $movimientoCuenta['concepto'];
                        $documento_cuenta_contable->debe = $movimientoCuenta['debe'];
                        $documento_cuenta_contable->haber =  $movimientoCuenta['haber'];
                        $documento_cuenta_contable->debe_org = $movimientoCuenta['debeORG'];
                        $documento_cuenta_contable->haber_org =  $movimientoCuenta['haberORG'];
                        $documento_cuenta_contable->id_moneda =  $movimientoCuenta['monedaMov']['id_moneda'];


//                        $documento_cuenta_contable->id_centro =  (!$movimientoCuenta['centro_costo_ingreso_deshabilitada'])?$movimientoCuenta['centro_costo_ingreso']['id_centro']:null;
                        //print_r($documento_cuenta_contable->id_centro );
                        $documento_cuenta_contable->id_cuenta_contable = $movimientoCuenta['cuentaContable']['id_cuenta_contable'];
                        $documento_cuenta_contable->cta_contable = $movimientoCuenta['cuentaContable']['cta_contable'];
                        $documento_cuenta_contable->cta_contable_padre = $movimientoCuenta['cuentaContable']['cta_contable'];
                        $documento_cuenta_contable->save();
                    }
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


    public function actualizar(Request $request)
    {

    }

    public function nuevo(Request $request)
    {
        $tasa = TasasCambios::select('tasa')->where('fecha',date("Y-m-d"))->first();
        $tipos_documentos = TiposDocumentos::select(['id_tipo_doc','prefijo','descripcion'])->where('estado',true)->where('permite_registro_manual',true)->orderBy('id_tipo_doc')->get();
        $monedas = Monedas::where('estado',1)->orderBy('id_moneda')->get();
        $cuentas_contables = CuentasContablesVista::orderBy('cta_contable')->where('estado',1)->select('id_cuenta_contable','cta_contable','nombre_cuenta','id_tipo_cuenta','nombre_cuenta_completo')->get();

        $centro_costos_ingresos = CentrosCostosIngresos::select('id_centro','descripcion')->where('estado',true)->get();
        return response()->json([
            'status' => 'success',
            'result' => [
                'tipos_documentos' => $tipos_documentos,
                'centro_costos_ingresos'=>$centro_costos_ingresos,
                'cuentas_contables'=>$cuentas_contables,
                'monedas' => $monedas,
                't_cambio' => $tasa,
            ],
            'messages' => null
        ]);

    }

    /*
     *

            obtenerSucursalesTodas() {
                var self = this;
                centro_costo_ingreso.obtenerTodos(
                    data => {
                        self.centros_costos_ingresos = data;
                    },
                    err => {
                        console.log(err);
                    }
                );
     * */

    /**
     * Cambiar Estado
     *
     * @access 	public
     * @param
     * @return JsonResponse
     */

    public function cambiarEstado(Request $request)
    {

        $rules = [
            'id_documento' => 'required',
            'estado' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $documento = DocumentosContables::find($request->id_documento);

            if($documento->es_editable && $request->estado >= 0 && $request->estado <= 2 && $documento->estado <> $request->estado){

                $estado_org = $documento->estado;
                $documento->estado = $request->estado;

                if($request->estado==0 || $request->estado==2){
                    $documento->es_editable = 0 ;
                }

                $estados[0] = 'Cancelado';
                $estados[1] = 'Emitido';
                $estados[2] = 'Aprobado';

                /*date_default_timezone_set('America/Managua');
                $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
                $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                $log['fecha_log'] = $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') .' a las '.date('h:i:s A');
                $log['registro'] = 'Cambiado el estado de la salida de '. $estados[$estado_org].' a estado '.$estados[$request->estado].' por usuario '.Auth::user()->usuario;
                $log_actual = Array(json_decode($salida->log_salida));
               // print_r($log);
               // print_r($log_actual[0]);
                array_push($log_actual[0],$log);
                $salida->log_salida = json_encode($log_actual[0]);
               // echo $entrada->log_entrada;
                */
                $documento->save();

                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);

            }else{
                return response()->json([
                    'status' => 'error',
                    'result' => 'Error al cambiar el estado del documento, revise si el documento esta bloqueada',
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
     * Generar Reporte Jasper
     *
     * @access  public
     * @param $ext
     * @param $id_documento
     * @throws ErrorCommandExecutable
     * @throws InvalidCommandExecutable
     * @throws InvalidFormat
     * @throws InvalidInputFile
     * @throws InvalidResourceDirectory
     */
    public function generarReporte($ext, $id_documento)
    {
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time() ;

            //if(env('AMBIENTE') == 'MACOS'){
            //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/DocumentoContablePDF';
            //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/' .$hora_actual . 'DocumentoContablePDF';
            //}elseif(env('AMBIENTE') == 'WINDOWS'){

            //}elseif(env('AMBIENTE') == 'UBUNTU'){
            $input = '/var/www/html/resources/reports/DocumentoContablePDF';
            $output = '/var/www/html/resources/reports/'.time().'DocumentoContablePDF';
            //}

            if($ext === 'xls'){
                $input .= 'XLS.jasper';
            }

            $options = [
                'format' => [$ext],
                'locale' => 'en',
                'params' => [ 'id_documento' => $id_documento],
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
            header('Content-Description: File Transfer');
            header('Content-Type: multipart/form-data;boundary="boundary"');
            header('Content-Disposition: form-data; filename=' . $hora_actual. 'DocumentoContablePDF.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

    public function generarReporteNuevo($ext, Request $request)
    {
        // echo $ext;
        // echo $ext;

        $os = array("xls", "pdf");
        //echo gethostname();
        if (in_array($ext, $os)) {
            /*$input = 'C:/xampp7/htdocs/resources/reports/Blank_A4.jrxml';
              echo $input;
              $jasper = new PHPJasper;
              $jasper->compile($input)->execute();
            */
            $hora_actual = time();
            $input = env('APP_URL_REPORTES') . 'DocumentosContables';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'DocumentosContables';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'CatalogoCuentasContables';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'CatalogoCuentasContables';

            if ($ext == 'xls') {
                $input = $input . 'XLS.jasper';
            }

            //Ajustes generales del sistema
            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'logo_empresa' => env('APP_URL_IMAGES') . $logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,
                    'id_empresa' => $usuario_empresa->id_empresa,
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
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $hora_actual. 'CuentasContables.' . $ext);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Length: ' . filesize($output . '.' . $ext));
            flush();
            readfile($output . '.' . $ext);
            unlink($output . '.' . $ext);*/

            /*print_r( env('APP_URL_REPORTS').$logo_empresa->valor);*/
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext, $hora_actual . 'DocumentosContables.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }



    public function anular(Request $request)
    {

        $messages = [
        ];


        $rules = [
            'id_documento' => 'required|integer',
            'causa_anulacion' => 'required|string|max:100',
        ];

        $validator = Validator::make($request->all(), $rules,$messages);
        if (!$validator->fails()) {

            try{

                DB::beginTransaction();
                $documento = DocumentosContables::find($request->id_documento);
                $documento->estado = 0;
                //$documento->observacion = $factura['observacion'] . ' **Factura cancelada por '.Auth::user()->usuario.' a las '.date("Y-m-d H:i:s").' Causa: '.$request->causa_anulacion;
                $documento->u_modificacion = Auth::user()->name;
                $documento->save();

                if($documento->id_tipo_doc==3 || $documento->id_tipo_doc==4){
                    //cheques
                    $cheque = CajaBancoSolicitudesPago::where('id_documento_contable',$documento->id_documento)->first();
                    if(!empty($cheque)){
                        if($cheque->estado == 3){
                            $cheque->estado=0;
                            $cheque->save();
                        }else{
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'result' => 'No se puede actualizar la solicitud de pago, ya fue recibida',
                                'messages' => null
                            ]);
                        }
                    }
                }

                if($documento->id_tipo_doc==5){
                    //minutas de deposito
                    $minuta = CajaBancoMinutasDeposito::where('id_documento_contable',$documento->id_documento)->first();
                    if(!empty($minuta)){
                        if($minuta->estado == 1){
                            $minuta->estado=0;
                            $minuta->save();
                        }else{
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'result' => 'No se puede actualizar la minuta, ya fue recibida',
                                'messages' => null
                            ]);
                        }
                    }
                }

                if($documento->id_tipo_doc==6){
                    //recibos (falta unir tabla)
                    $recibos = CuentasXCobrarRecibos::where('id_documento_contable',$documento->id_documento)->first();
                    if(!empty($minuta)){
                        if($recibos->estado == 1){
                            $recibos->estado=0;
                            $recibos->save();
                        }else{
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'result' => 'No se puede actualizar la minuta, ya fue recibida',
                                'messages' => null
                            ]);
                        }
                    }
                }

                if($documento->id_tipo_doc==7){
                    //facturas
                    $factura = CajaBancoFacturas::where('id_documento_contable',$documento->id_documento)->first();
                    if(!empty($factura)){
                        if($factura->estado == 1){
                            $factura->estado=0;
                            $factura->save();
                        }else{
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'result' => 'No se puede actualizar la minuta, ya fue recibida',
                                'messages' => null
                            ]);
                        }
                    }
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
