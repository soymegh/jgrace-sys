<?php

namespace App\Http\Controllers\CuentasXCobrar;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\CajaBanco\Bancos;
use App\Models\CajaBanco\Facturas;
use App\Models\CajaBanco\ViaPagos;
use App\Models\Contabilidad\CentrosCostosIngresos;
use App\Models\Contabilidad\ConfiguracionComprobantes;
use App\Models\Contabilidad\CuentasContables;
use App\Models\Contabilidad\CuentasContablesVista;
use App\Models\Contabilidad\DocumentosContables;
use App\Models\Contabilidad\DocumentosCuentas;
use App\Models\Contabilidad\Monedas;
use App\Models\Contabilidad\PeriodosFiscales;
use App\Models\Contabilidad\PeriodosMeses;
use App\Models\Contabilidad\TasasCambios;
use App\Models\Contabilidad\TiposDocumentos;
use App\Models\CuentasXCobrar\CatAuxiliar;
use App\Models\CuentasXCobrar\CuentasXCobrar;
use App\Models\CuentasXCobrar\Proyectos;
use App\Models\CuentasXCobrar\Recibos;
use App\Models\CuentasXCobrar\RecibosDetalles;
use App\Models\CuentasXCobrar\ReciboViaPagos;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;

class RecibosController extends Controller
{
    /**
     * Obtener una lista de Documentos Contables
     *
     * @access  public
     * @param Request $request
     * @param Recibos $recibos
     * @return JsonResponse
     */

    public function obtener(Request $request, Recibos $recibos)
    {
        $recibos = $recibos->obtener($request);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first(); // 1-cordobas 2-dolares
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $recibos->total(),
                'rows' => $recibos->items(),
                'currency_id' => $currency_id->valor
            ],
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

    public function obtenerRecibo(Request $request)
    {
        $rules = [
            'id_recibo' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $recibo = Recibos::where('id_recibo', $request->id_recibo)->with('reciboDetalles', 'reciboCliente')->first();
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
            $direccion_empresa = Ajustes::where('id_ajuste', 5)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
            $telefono_empresa = Ajustes::where('id_ajuste', 6)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
            return response()->json([
                'status' => 'success',
                'result' => [
                    'recibo' => $recibo,
                    'nombre_empresa' => $nombre_empresa,
                    'direccion_empresa' => $direccion_empresa,
                    'telefono_empresa' => $telefono_empresa,
                ],
                'messages' => null
            ]);
        }

        return response()->json([
            'status' => 'error',
            'result' => $validator->messages(),
            'messages' => null
        ]);
    }

    /**
     * Obtener recibos de clientes por proyecto
     * @param Request $request
     * @param Recibos $recibos
     * @return JsonResponse
     */
    public function obtenerRecibosCliente(Request $request, Recibos $recibos)
    {
        $recibos = $recibos->obtenerRecibosCliente($request);

        return response()->json([
            'status' => 'success',
            'result' => $recibos,
            'messages' => null
        ]);
    }

    public function nuevo(Request $request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $tasa = TasasCambios::select('tasa')->where('fecha', date("Y-m-d"))->first();
        $vias_pago = ViaPagos::select(['id_via_pago', 'descripcion'])->where('estado', true)->orderBy('id_via_pago')->get();
        $monedas = Monedas::where('estado', 1)->orderBy('id_moneda')->get();
        $bancos = Bancos::select(['id_banco', 'descripcion'])->where('estado', 1)->get();
        $proyectos = Proyectos::select(['*'])->where('id_empresa', '=', $usuario_empresa->id_empresa)->where('estado', '=', 1)->get();
        $no_documento = Recibos::max('no_documento') + 1;

        // update
        $cuentas_contables = CuentasContablesVista::orderBy('cta_contable')->where('id_empresa', $usuario_empresa->id_empresa)->where('estado', 1)->select('id_cuenta_contable', 'cta_contable', 'nombre_cuenta', 'nombre_cuenta_completo', 'requiere_aux', 'id_centro_costo', 'codigo_centro_costo', 'codigo_auxiliar')->get();
        $centro_ingreso = CentrosCostosIngresos::where('tipo_centro', 1)->where('id_empresa', $usuario_empresa->id_empresa)->where('estado', 1)->get();
        $centro_costo = CentrosCostosIngresos::where('tipo_centro', 2)->where('id_empresa', $usuario_empresa->id_empresa)->where('estado', 1)->get();
//        $auxiliares = CuentasXCobrarCatAuxiliar::where('estado',1)->get();}

        return response()->json([
            'status' => 'success',
            'result' => [
                'vias_pago' => $vias_pago,
                'monedas' => $monedas,
                'bancos' => $bancos,
                'proyectos' => $proyectos,
                't_cambio' => $tasa,
                'cuentas_contables' => $cuentas_contables,
                'centro_ingreso' => $centro_ingreso,
                'centro_costo' => $centro_costo,
                'no_documento' => $no_documento
//                'auxiliares' => $auxiliares,
            ],
            'messages' => null
        ]);

    }


    /**
     * Crear un nuevo recibo oficial de caja Cliente
     *
     * @access  public
     * @param Request $request
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $messages = [
            'detalleCuentasxCobrar.min' => 'Se requiere agregar por lo menos una deuda.',
            'detalleCuentasxCobrar.doc_exoneracion_ir.required_if' => 'El campo documento exoneracion es requerido cuando el recibo aplica Retención IR',
            'detalleCuentasxCobrar.doc_exoneracion_imi.required_if' => 'El campo documento exoneracion es requerido cuando el recibo aplica Retención IMI',
            'recibo_cliente.required' => 'El campo cliente es requerido'
        ];

        $rules = [
            'fecha_emision' => 'required|date',
            'recibo_cliente' => 'required|array|min:1',
            'recibo_cliente.id_cliente' => 'required|integer|min:1',
            'nombre_persona' => 'required|string|max:100',
            'concepto' => 'required|string|max:300',
            'proyecto' => 'nullable|array|min:0',
            'proyecto.id_proyecto' => 'nullable|integer|min:0',

            'monto_total' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/|min:0',
            'monto_total_me' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/|min:0',
            /*            'pago_vuelto' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
                        'pago_vuelto_mn' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',*/
            't_cambio' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',

            'detallePago' => 'required|array|min:1',
            'detallePago.*.via_pagox.id_via_pago' => 'required|integer|min:1',
            'detallePago.*.moneda_x.id_moneda' => 'required|integer|min:1',
            'detallePago.*.monto_me' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detallePago.*.monto' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detallePago.*.banco_x.id_banco' => 'required_if:detallePago.*.via_pagox.id_via_pago,5|required_if:detallePago.*.via_pagox.id_via_pago,6|integer|min:1|nullable',
            'detallePago.*.numero_minuta' => 'required_if:detallePago.*.via_pagox.id_via_pago,1|string|max:30|nullable',
            'detallePago.*.numero_nota_credito' => 'required_if:detallePago.*.via_pagox.id_via_pago,4|string|max:30|nullable',
            'detallePago.*.numero_cheque' => 'required_if:detallePago.*.via_pagox.id_via_pago,5|string|max:30|nullable',
            'detallePago.*.numero_transferencia' => 'required_if:detallePago.*.via_pagox.id_via_pago,6|string|max:30|nullable',
            'detallePago.*.numero_recibo_pago' => 'required_if:detallePago.*.via_pagox.id_via_pago,7|string|max:30|nullable',

            'detalleCuentasxCobrar' => 'required_if:tipo_recibo,1|array|min:1',
            'detalleCuentasxCobrar.*.cuentax.id_cuentaxcobrar' => 'required_if:tipo_recibo,1|integer|exists:pgsql.cuentasxcobrar.cuentasxcobrar,id_cuentaxcobrar',
            'detalleCuentasxCobrar.*.descripcion_pago' => 'required|string|max:300',
            'detalleCuentasxCobrar.*.monto' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleCuentasxCobrar.*.monto_me' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',

            'detalleCuentasxCobrar.*.aplicaIR' => 'required|boolean',
            'detalleCuentasxCobrar.*.aplicaIMI' => 'required|boolean',

            'detalleCuentasxCobrar.*.monto_retencion_ir' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleCuentasxCobrar.*.monto_retencion_imi' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',

            'detalleCuentasxCobrar.*.doc_exoneracion_ir' => 'required_if:aplicaIR,true|string|max:20|nullable',
            'detalleCuentasxCobrar.*.doc_exoneracion_imi' => 'required_if:aplicaIMI,true|string|max:20|nullable',
            'monto_total_letras' => 'required|string',
            'monto_total_letras_me' => 'required|string',


        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            try {

                $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();

                DB::beginTransaction();
                $recibo = new Recibos;
                $recibo->fecha_emision = $request->fecha_emision;
                $recibo->estado = 1;
                $recibo->id_cliente = $request->recibo_cliente['id_cliente'];
                $recibo->nombre_persona = $request->nombre_persona;
                $recibo->tipo_recibo = $request->tipo_recibo;
                $recibo->total_vuelto = $request->pago_vuelto_mn;
                $recibo->total_pendiente = $request->pago_pendiente;
                $recibo->concepto = $request->concepto;
                $recibo->monto_total = $request->monto_total;
                $recibo->monto_total_me = $request->monto_total_me;
                $recibo->monto_total_letra = $request->monto_total_letras;
                $recibo->monto_total_letra_me = $request->monto_total_letras_me;
                $recibo->t_cambio = $request->t_cambio;
//                $recibo->no_documento = Recibos::max('no_documento') + 1;
                $recibo->no_documento = $request->no_documento;
                $recibo->u_creacion = Auth::user()->name;
                $recibo->id_empresa = $usuario_empresa->id_empresa;
                if ($request->tipo_recibo === 2 || $request->tipo_recibo === "2") {
                    $recibo->id_proyecto = $request->proyecto['id_proyecto'];
                }
                $recibo->save();

                if ($recibo->monto_total > 0) {
                    $cuentas_x_cobrar = new CuentasXCobrar();
                    $cuentas_x_cobrar->id_cliente = $recibo->id_cliente;
                    $cuentas_x_cobrar->id_tipo_documento = 2; //Cuenta por cobrar tipo "RECIBO"
                    $cuentas_x_cobrar->no_documento_origen = $recibo->no_documento;
                    $cuentas_x_cobrar->es_registro_importado = false;
                    $cuentas_x_cobrar->identificador_origen = $recibo->id_recibo;
                    $cuentas_x_cobrar->fecha_movimiento = $recibo->fecha_emision; //date("Y-m-d H:i:s");
                    $cuentas_x_cobrar->monto_movimiento = $recibo->monto_total * -1;
                    $cuentas_x_cobrar->monto_movimiento_me = $recibo->monto_total_me * -1;
                    $cuentas_x_cobrar->saldo_actual = 0;
                    $cuentas_x_cobrar->saldo_actual_me = 0;
                    $cuentas_x_cobrar->fecha_vencimiento = $recibo->fecha_emision;
                    $cuentas_x_cobrar->descripcion_movimiento = 'Registro de Abono del recibo No.  ' . $recibo->no_documento;
                    $cuentas_x_cobrar->usuario_registra = $recibo->u_creacion;
                    $cuentas_x_cobrar->id_empresa = $usuario_empresa->id_empresa;
                    $cuentas_x_cobrar->estado = 2;
                    $cuentas_x_cobrar->save();
                }

                $total_pago_cordobas = 0;
                $total_pago_dolares = 0;

                foreach ($request->detallePago as $pago) {
                    $recibo_pago = new ReciboViaPagos();
                    $recibo_pago->id_recibo = $recibo->id_recibo;
                    $recibo_pago->id_via_pago = $pago['via_pagox']['id_via_pago'];
                    $recibo_pago->id_moneda = $pago['moneda_x']['id_moneda'];
                    $recibo_pago->monto_me = $pago['monto_me'];
                    $recibo_pago->monto = $pago['monto'];
                    if ($recibo_pago->id_via_pago === 1 || $recibo_pago->id_via_pago === 3 || $recibo_pago->id_via_pago === 5 || $recibo_pago->id_via_pago === 6) {
                        $recibo_pago->id_banco = $pago['banco_x']['id_banco'];
                    }
                    $recibo_pago->numero_minuta = $pago['numero_minuta'];
                    $recibo_pago->numero_nota_credito = $pago['numero_nota_credito'];
                    $recibo_pago->numero_cheque = $pago['numero_cheque'];
                    $recibo_pago->numero_transferencia = $pago['numero_transferencia'];
                    $recibo_pago->numero_recibo_pago = $pago['numero_recibo_pago'];

                    //if($recibo_pago->id_moneda==1){
                    $total_pago_cordobas += $recibo_pago->monto;
                    //}else{
                    //  $total_pago_dolares = $total_pago_dolares + $recibo_pago->monto_me;
                    //}
                    $recibo_pago->id_empresa = $usuario_empresa->id_empresa;

                    $recibo_pago->save();
                }

                $total_ir = 0;
                $total_imi = 0;

                foreach ($request->detalleCuentasxCobrar as $recibosDetalles) {
                    $recibos_detalles = new RecibosDetalles;
                    $recibos_detalles->id_recibo = $recibo->id_recibo;
                    $recibos_detalles->descripcion_pago = $recibosDetalles['descripcion_pago'];
                    $recibos_detalles->monto = $recibosDetalles['monto'];
                    $recibos_detalles->monto_me = $recibosDetalles['monto_me'];
                    if ($recibo->tipo_recibo === 1 || $recibo->tipo_recibo === "1") {
                        $recibos_detalles->id_cuentaxcobrar = $recibosDetalles['cuentax']['id_cuentaxcobrar'];
                    }
                    if ($recibo->tipo_recibo === 3 || $recibo->tipo_recibo === '3') {
                        $recibos_detalles->id_factura = $recibosDetalles['cuentax']['id_factura'];
                    }
                    $recibos_detalles->fecha_pago = $recibo->fecha_emision;

                    if ($recibosDetalles['aplicaIR']) {
                        $recibos_detalles->retencion_ir = $recibosDetalles['monto_retencion_ir'];
                        $recibos_detalles->doc_exoneracion_ir = $recibosDetalles['doc_exoneracion_ir'];
                    } else {
                        $recibos_detalles->retencion_ir = 0;
                        $recibos_detalles->doc_exoneracion_ir = '';
                    }

                    if ($recibosDetalles['aplicaIMI']) {
                        $recibos_detalles->retencion_imi = $recibosDetalles['monto_retencion_imi'];
                        $recibos_detalles->doc_exoneracion_imi = $recibosDetalles['doc_exoneracion_imi'];
                    } else {
                        $recibos_detalles->retencion_imi = 0;
                        $recibos_detalles->doc_exoneracion_imi = '';
                    }

                    $total_imi += $recibos_detalles->retencion_imi;
                    $total_ir += $recibos_detalles->retencion_ir;

                    $recibos_detalles->id_empresa = $usuario_empresa->id_empresa;
                    $recibos_detalles->save();

                    if ($recibo->tipo_recibo === 1 || $recibo->tipo_recibo === "1") {
                        $cuentas_x_cobrarUpdate = CuentasXCobrar::findOrFail($recibos_detalles->id_cuentaxcobrar);
                        $facturaUpdate = Facturas::findOrFail($cuentas_x_cobrarUpdate->identificador_origen);
                        $saldoActual = round($cuentas_x_cobrarUpdate->saldo_actual - $recibos_detalles->monto, 2);
                        $saldoActualMe = $cuentas_x_cobrarUpdate->saldo_actual_me - $recibos_detalles->monto_me;
                        /* 0.0018 */
                        /*revisar condiciones */
//                        print_r('saldo actua:'.abs($saldoActual));
//                        print_r('saldo actua me:'.abs($saldoActualMe));
                        if ($saldoActualMe === 0) {
                            $cuentas_x_cobrarUpdate->saldo_actual = 0;
                            $cuentas_x_cobrarUpdate->saldo_actual_me = 0;
                            $cuentas_x_cobrarUpdate->estado = 2;
                            $cuentas_x_cobrarUpdate->save();
                            $facturaUpdate->estado = 2; // Cancelada
                            $facturaUpdate->save();
                        } else {
                            $cuentas_x_cobrarUpdate->saldo_actual = $saldoActual;
                            $cuentas_x_cobrarUpdate->saldo_actual_me = $saldoActualMe;
                            $cuentas_x_cobrarUpdate->save();
                        }


                        if ($cuentas_x_cobrarUpdate->id_tipo_documento === 1 && !$recibosDetalles['cuentax']['es_registro_importado']) {
                            $facturaActualizar = Facturas::where('id_factura', $cuentas_x_cobrarUpdate->identificador_origen)->first();
                            $facturaActualizar->saldo_factura = round($facturaActualizar->saldo_factura - $recibos_detalles->monto, 4);
                            $facturaActualizar->saldo_factura_me = round($facturaActualizar->saldo_factura_me - $recibos_detalles->monto_me, 4);
                            $facturaActualizar->save();
                        }
                    }

                    if ($recibo->tipo_recibo === 3 || $recibo->tipo_recibo === "3") {
                        $facturaUpdate = Facturas::findOrFail($recibos_detalles->id_factura);
                        $saldoActualMe = round($facturaUpdate->saldo_factura_me - $recibos_detalles->monto_me, 2);
                        $saldoActual = round($facturaUpdate->saldo_factura - $recibos_detalles->monto, 2);


                        if (abs($saldoActual) === 0 || abs($saldoActualMe) === 0) {
                            $facturaUpdate->saldo_factura = 0;
                            $facturaUpdate->saldo_factura_me = 0;
                            $facturaUpdate->estado = 2; // Cancelada
                        } else {
                            $facturaUpdate->saldo_factura = $saldoActual;
                            $facturaUpdate->saldo_factura_me = $saldoActualMe;

                        }
                        $facturaUpdate->save();
                    }
                }


                #region /*INICIA movimiento contable - recibo*/

                $documento = new DocumentosContables();
                $tipo = TiposDocumentos::find(6); // Tipo recibo oficial de caja
                $fecha = date("Y-m-d H:i:s");
                $codigo = $documento->obtenerCodigoDocumento(array('id_tipo_doc' => 6, 'fecha_doc' => $fecha));

                $nuevo_codigo = json_decode($codigo[0]);

                date_default_timezone_set('America/Managua');

                $documento->num_documento = $tipo->prefijo . '-' . $nuevo_codigo->secuencia;
                $documento->fecha_emision = $recibo->fecha_emision; //date('Y-m-d');
                $documento->codigo_documento = $nuevo_codigo->secuencia;


                $date = DateTime::createFromFormat("Y-m-d", $documento->fecha_emision);

                $periodo = PeriodosFiscales::where('periodo', $date->format("Y"))->get();

                if (empty($periodo[0])) {
                    return response()->json([
                        'status' => 'error',
                        'result' => array('fecha_emision' => ["El periodo " . $date->format("Y") . " no se encuentra registrado, por favor consulte al administrador"]),
                        'messages' => null
                    ]);
                    exit;
                }

                if ($periodo[0]->estado) {
                    return response()->json([
                        'status' => 'error',
                        'result' => array('fecha_emision' => ["El periodo " . $date->format("Y") . " es inválido, ya que se encuentra en estado COMPLETADO"]),
                        'messages' => null
                    ]);
                    exit;
                }

                $periodo_mes = PeriodosMeses::where('id_periodo_fiscal', $periodo[0]->id_periodo_fiscal)->where('mes', $date->format("n"))->get();

                if (empty($periodo_mes[0])) {
                    return response()->json([
                        'status' => 'error',
                        'result' => array('fecha_emision' => ["El mes " . $date->format("F") . " no se encuentra registrado, por favor consulte al administrador"]),
                        'messages' => null
                    ]);
                    exit;
                }

                if ($periodo_mes[0]->estado === 2) {
                    return response()->json([
                        'status' => 'error',
                        'result' => array('fecha_emision' => ["El mes " . config('global.meses')[$periodo_mes[0]->mes - 1] . " es inválido, ya que se encuentra en estado COMPLETADO"]),
                        'messages' => null
                    ]);
                    exit;
                }
                $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
                $documento->id_periodo_fiscal = $periodo[0]->id_periodo_fiscal;

                $documento->id_tipo_doc = 6;
                $documento->valor = $recibo->monto_total;
                $documento->valor_me = $recibo->monto_total_me;
                $documento->concepto = 'Registramos pago de Recibo No. ' . $recibo->no_documento;
                $documento->id_moneda = $currency_id->valor;
                $documento->u_creacion = Auth::user()->name;
                $documento->id_empresa = $usuario_empresa->id_empresa;
                $documento->estado = 1;
                $documento->save();

                $recibo->id_documento_contable = $documento->id_documento;
                $recibo->save();
                TiposDocumentos::find($documento->id_tipo_doc)->increment('secuencia');


                #region ROC-cobro-a-clientes
                // ---------------------------------------contabilziación tipo recibo 1 - ROC cobro a cliente---------------------------------------------
                if ($request->tipo_recibo === 1 || $request->tipo_recibo === '1') {
                    // INGRESO MONEDA NACIONAL

                    if ($recibo->monto_total > 0) {
                        $total_pago_tarjeta=0; //Acumulador de pago de tarjeta cordobas
                        $total_pago_tarjeta_me=0; //Acumulador de pago de tarjeta dolares

                        foreach($request->detallePago as $pago){
                            $id_tipo_configuracion = 1; // Recibo oficial de caja por anticipo de clientes - Tarjeta
                            $cancelacion_contado = 1; // Recibo oficial de caja por anticipo de clientes - Banco
                            #region Pago por Tarjeta - BAC
                            if ($pago['via_pagox']['id_via_pago'] === 3 && $pago['banco_x']['id_banco'] === 1) {

                                $total_pago_tarjeta += $pago['monto'];
                                $total_pago_tarjeta_me += $pago['monto_me'];
                                #region pago BAC CORDOBAS
                                if ($pago['moneda_x']['id_moneda'] === 1 && $pago['banco_x']['id_banco'] === 1) {
                                    $nombre_seccion_MonNacional = 'BacC$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;

                                    //Calculos de retencion por uso de tarjeta de crédito
                                    //Dinamizar estos factores de cálculos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    //Validar naturaleza de la cuenta y guardar montos
                                    // Primero se realiza la comision, y de este resultado se aplica retencion
                                    //comision tasa 3.50
                                    //retencion tasa 1.50

                                    $comision_tarjeta = $pago['monto'] * 0.035; // resultado con monto 500 * 0.035 = 17.5
                                    $monto_despues_comision = $pago['monto'] - $comision_tarjeta; // 500 - 17.5 = resultado = 482.5
                                    $retencion_tarjeta = $monto_despues_comision * 0.015; // resultado = 7.2375

                                    $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta; // resultado = 24.7375

                                    $total_recibo = $pago['monto'] - $total_retencion_tarjeta; // resultado = 475.2625

                                    $monto_total_bruto = $total_recibo;

                                    $comision_tarjeta_me = $pago['monto_me'] * 0.035;
                                    $monto_despues_comision_me = $pago['monto_me'] - $comision_tarjeta_me;
                                    $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                    $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                    $total_recibo_me = $pago['monto_me'] - $total_retencion_tarjeta_me;

                                    $monto_total_bruto_me = $total_recibo_me;


                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        $documento_cuenta_contableS1->debe = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber = 0;
                                        $documento_cuenta_contableS1->debe_org = $monto_total_bruto - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber_org = 0;

                                    } else {
                                        $documento_cuenta_contableS1->debe = 0;
                                        $documento_cuenta_contableS1->haber = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->debe_org = 0;
                                        $documento_cuenta_contableS1->haber_org = $monto_total_bruto - $total_imi - $total_ir;
                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                    #endregion
                                #region Pago BAC Dolares
                                }else if ($pago['moneda_x']['id_moneda'] === 2 && $pago['banco_x']['id_banco'] === 1){
                                    $nombre_seccion_MonNacional = 'Bac$';
                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;

                                    //Calculos de retencion por uso de tarjeta de crédito
                                    //Dinamizar estos factores de cálculos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    //Validar naturaleza de la cuenta y guardar montos
                                    // Primero se realiza la comision, y de este resultado se aplica retencion
                                    //comision tasa 3.50
                                    //retencion tasa 1.50

                                    $comision_tarjeta = $pago['monto'] * 0.035; // resultado con monto 500 * 0.035 = 17.5
                                    $monto_despues_comision = $pago['monto'] - $comision_tarjeta; // 500 - 17.5 = resultado = 482.5
                                    $retencion_tarjeta = $monto_despues_comision * 0.015; // resultado = 7.2375

                                    $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta; // resultado = 24.7375

                                    $total_recibo = $pago['monto'] - $total_retencion_tarjeta; // resultado = 475.2625

                                    $monto_total_bruto = $total_recibo;

                                    $comision_tarjeta_me = $pago['monto_me'] * 0.035;
                                    $monto_despues_comision_me = $pago['monto_me'] - $comision_tarjeta_me;
                                    $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                    $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                    $total_recibo_me = $pago['monto_me'] - $total_retencion_tarjeta_me;

                                    $monto_total_bruto_me = $total_recibo_me;


                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        $documento_cuenta_contableS1->debe = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber = 0;
                                        $documento_cuenta_contableS1->debe_org = $monto_total_bruto - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber_org = 0;

                                    } else {
                                        $documento_cuenta_contableS1->debe = 0;
                                        $documento_cuenta_contableS1->haber = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->debe_org = 0;
                                        $documento_cuenta_contableS1->haber_org = $monto_total_bruto - $total_imi - $total_ir;
                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                }
                                #endregion

                                #endregion
                                #region Via pago banco (transferencias o minutas de deposito) -> Banco
                            } else if (($pago['via_pagox']['id_via_pago'] === 1 || $pago['via_pagox']['id_via_pago'] === 6)) {
                                #region Bac moneda cordobas

                                if ($pago['moneda_x']['id_moneda'] === 1 && $pago['banco_x']['id_banco'] === 1) {
                                    $nombre_seccion_MonNacional = 'BacC$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();

                                    #endregion

                                    #region Bac Dolares
                                } else if ($pago['moneda_x']['id_moneda'] === 2 && $pago['banco_x']['id_banco'] === 1) {
                                    $nombre_seccion_MonNacional = 'Bac$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();

                                    #endregion
                                    #region Lafise Cordobas
                                } else if ($pago['moneda_x']['id_moneda'] === 1 && $pago['banco_x']['id_banco'] === 2) {
                                    $nombre_seccion_MonNacional = 'lafiseC$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                    #endregion

                                    #region Lafise Dolares
                                } else if ($pago['moneda_x']['id_moneda'] === 2 && $pago['banco_x']['id_banco'] === 2) {
                                    $nombre_seccion_MonNacional = 'lafise$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                }
                                #endregion
                            }
                                #endregion
                        }
                        #region IR y COMISIONES de Tarjeta
                        /*======================================================Comisiones tarjeta credito==================================*/

                        $id_tipo_configuracion = 1; // Recibo oficial de caja por anticipo de clientes

                        $nombre_seccion_Comision = 'ComisionTarjeta';

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionComision = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_Comision)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_comision = CuentasContables::find($cuentaSeccionComision->id_cuenta_contable);
                        $cuenta_contable_comision_padre = CuentasContables::find($cuenta_contable_comision->id_cuenta_padre);

                        $documento_cuenta_contableS2 = new DocumentosCuentas;
                        $documento_cuenta_contableS2->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS2->concepto = $cuentaSeccionComision->descripcion_movimiento . ' ' . $recibo->no_documento;

                        //Calculos de retencion por uso de tarjeta de crédito

                        $comision_tarjeta = $total_pago_tarjeta * 0.035;
                        $monto_despues_comision = $total_pago_tarjeta - $comision_tarjeta;
                        $retencion_tarjeta = $monto_despues_comision * 0.015;
                        $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                        $total_recibo = $total_pago_tarjeta - $total_retencion_tarjeta;

                        $comision_tarjeta_me = $total_pago_tarjeta_me * 0.035;
                        $monto_despues_comision_me = $total_pago_tarjeta_me - $comision_tarjeta_me;
                        $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                        $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                        $total_recibo_me = $total_pago_tarjeta_me - $total_retencion_tarjeta_me;


                        //Validar naturaleza de la cuenta y guardar montos

                        if ($cuentaSeccionComision->debe_haber === 1) {
                            $documento_cuenta_contableS2->debe = $comision_tarjeta_me;
                            $documento_cuenta_contableS2->haber = 0;
                            $documento_cuenta_contableS2->debe_org = $comision_tarjeta;
                            $documento_cuenta_contableS2->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS2->debe = 0;
                            $documento_cuenta_contableS2->haber = $comision_tarjeta_me;
                            $documento_cuenta_contableS2->debe_org = 0;
                            $documento_cuenta_contableS2->haber_org = $comision_tarjeta;
                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_comision->requiere_aux === 0) {
                            $documento_cuenta_contableS2->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_comision->requiere_aux === 2 || $cuenta_contable_comision->requiere_aux === 3) {
                            $documento_cuenta_contableS2->id_centro = $cuentaSeccionComision->id_centro_costo;
                            $documento_cuenta_contableS2->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_comision->requiere_aux === 1) {
                            $documento_cuenta_contableS2->id_centro = null;
                            $documento_cuenta_contableS2->id_cat_auxiliar_cxc = $cuentaSeccionComision->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS2->id_moneda = 1;
                        $documento_cuenta_contableS2->id_cuenta_contable = $cuenta_contable_comision->id_cuenta_contable;
                        $documento_cuenta_contableS2->cta_contable = $cuenta_contable_comision->cta_contable;
                        $documento_cuenta_contableS2->cta_contable_padre = $cuenta_contable_comision_padre->id_cuenta_contable;
                        $documento_cuenta_contableS2->save();

                        /*======================================================Retencion tarjeta credito==================================*/

                        $id_tipo_configuracion = 1; // Recibo oficial de caja por anticipo de clientes

                        $nombre_seccion_Retencion = 'IrTarjetaCred';

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionRetencion = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_Retencion)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_retencion = CuentasContables::find($cuentaSeccionRetencion->id_cuenta_contable);
                        $cuenta_contable_retencion_padre = CuentasContables::find($cuenta_contable_retencion->id_cuenta_padre);

                        $documento_cuenta_contableS3 = new DocumentosCuentas;
                        $documento_cuenta_contableS3->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS3->concepto = $cuentaSeccionRetencion->descripcion_movimiento . ' ' . $recibo->no_documento;

                        //Calculos de retencion por uso de tarjeta de crédito
                        $comision_tarjeta = $total_pago_tarjeta * 0.035;
                        $monto_despues_comision = $total_pago_tarjeta - $comision_tarjeta;
                        $retencion_tarjeta = $monto_despues_comision * 0.015;
                        $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                        $total_recibo = $total_pago_tarjeta - $total_retencion_tarjeta;

                        $comision_tarjeta_me = $total_pago_tarjeta_me * 0.035;
                        $monto_despues_comision_me = $total_pago_tarjeta_me - $comision_tarjeta_me;
                        $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                        $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                        $total_recibo_me = $total_pago_tarjeta_me - $total_retencion_tarjeta_me;


                        //Validar naturaleza de la cuenta y guardar montos

                        if ($cuentaSeccionRetencion->debe_haber === 1) {
                            $documento_cuenta_contableS3->debe = $retencion_tarjeta_me;
                            $documento_cuenta_contableS3->haber = 0;
                            $documento_cuenta_contableS3->debe_org = $retencion_tarjeta;
                            $documento_cuenta_contableS3->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS3->debe = 0;
                            $documento_cuenta_contableS3->haber = $retencion_tarjeta_me;
                            $documento_cuenta_contableS3->debe_org = 0;
                            $documento_cuenta_contableS3->haber_org = $retencion_tarjeta;
                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_retencion->requiere_aux === 0) {
                            $documento_cuenta_contableS3->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_retencion->requiere_aux === 2 || $cuenta_contable_retencion->requiere_aux === 3) {
                            $documento_cuenta_contableS3->id_centro = $cuentaSeccionRetencion->id_centro_costo;
                            $documento_cuenta_contableS3->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_retencion->requiere_aux === 1) {
                            $documento_cuenta_contableS3->id_centro = null;
                            $documento_cuenta_contableS3->id_cat_auxiliar_cxc = $cuentaSeccionRetencion->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS3->id_moneda = 1;
                        $documento_cuenta_contableS3->id_cuenta_contable = $cuenta_contable_retencion->id_cuenta_contable;
                        $documento_cuenta_contableS3->cta_contable = $cuenta_contable_retencion->cta_contable;
                        $documento_cuenta_contableS3->cta_contable_padre = $cuenta_contable_retencion_padre->id_cuenta_contable;
                        $documento_cuenta_contableS3->save();
                        #endregion
                        #region Anticipo de clientes por compra*/
                        $id_tipo_configuracion = 1; // Recibo oficial de caja por anticipo de clientes

                        $nombre_seccion_cliente = 'AnticipoCliente';

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionCliente = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_cliente)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_cliente = CuentasContables::find($cuentaSeccionCliente->id_cuenta_contable);
                        $cuenta_contable_cliente_padre = CuentasContables::find($cuenta_contable_cliente->id_cuenta_padre);

                        $documento_cuenta_contableS4 = new DocumentosCuentas;
                        $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS4->concepto = $cuentaSeccionCliente->descripcion_movimiento . ' ' . $recibo->no_documento;

                        //Calculos de retencion por uso de tarjeta de crédito
                        $comision_tarjeta = $recibo->monto_total * 0.035;
                        $monto_despues_comision = $recibo->monto_total - $comision_tarjeta;
                        $retencion_tarjeta = $monto_despues_comision * 0.015;
                        $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                        $total_recibo = $recibo->monto_total - $total_retencion_tarjeta;

                        //Validar naturaleza de la cuenta y guardar montos
                        $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
                        if ($cuentaSeccionCliente->debe_haber === 1) {
                            if ($currency_id->valor === 1) {
                                $comision_tarjeta = $recibo->monto_total * 0.035;
                                $monto_despues_comision = $recibo->monto_total - $comision_tarjeta;
                                $retencion_tarjeta = $monto_despues_comision * 0.015;
                                $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                                $total_recibo = $recibo->monto_total - $total_retencion_tarjeta;

                                $documento_cuenta_contableS4->debe = $recibo->monto_total + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber = 0;
                                $documento_cuenta_contableS4->debe_org = $recibo->monto_total + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber_org = 0;
                            } else {
                                $comision_tarjeta_me = $recibo->monto_total_me * 0.035;
                                $monto_despues_comision_me = $recibo->monto_total_me - $comision_tarjeta_me;
                                $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                $total_recibo_me = $recibo->monto_total_me - $total_retencion_tarjeta_me;

                                $documento_cuenta_contableS4->debe = $recibo->monto_total_me;//+ $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber = 0;
                                $documento_cuenta_contableS4->debe_org = $recibo->monto_total_me;// + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber_org = 0;
                            }

                        } else {
                            if ($currency_id->valor === 1) {
                                $comision_tarjeta = $recibo->monto_total * 0.035;
                                $monto_despues_comision = $recibo->monto_total - $comision_tarjeta;
                                $retencion_tarjeta = $monto_despues_comision * 0.015;
                                $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;

                                $documento_cuenta_contableS4->debe = 0;
                                $documento_cuenta_contableS4->haber = $recibo->monto_total;// + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->debe_org = 0;
                                $documento_cuenta_contableS4->haber_org = $recibo->monto_total;// + $total_retencion_tarjeta;
                            } else {
                                $comision_tarjeta_me = $recibo->monto_total_me * 0.035;
                                $monto_despues_comision_me = $recibo->monto_total_me - $comision_tarjeta_me;
                                $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                $total_recibo_me = $recibo->monto_total_me - $total_retencion_tarjeta_me;

                                $documento_cuenta_contableS4->debe = 0;
                                $documento_cuenta_contableS4->haber = $recibo->monto_total_me;// + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->debe_org = 0;
                                $documento_cuenta_contableS4->haber_org = $recibo->monto_total;// + $total_retencion_tarjeta;
                            }

                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_cliente->requiere_aux === 0) {
                            $documento_cuenta_contableS4->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_cliente->requiere_aux === 2 || $cuenta_contable_cliente->requiere_aux === 3) {
                            $documento_cuenta_contableS4->id_centro = $cuentaSeccionCliente->id_centro_costo;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_cliente->requiere_aux === 1) {
                            $documento_cuenta_contableS4->id_centro = null;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = $cuentaSeccionCliente->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS4->id_moneda = 1;
                        $documento_cuenta_contableS4->id_cuenta_contable = $cuenta_contable_cliente->id_cuenta_contable;
                        $documento_cuenta_contableS4->cta_contable = $cuenta_contable_cliente->cta_contable;
                        $documento_cuenta_contableS4->cta_contable_padre = $cuenta_contable_cliente_padre->id_cuenta_contable;
                        $documento_cuenta_contableS4->save();
                        #endregion

                    }
                    // RETENCION IR
                    if ($total_ir > 0) {


                        $id_tipo_configuracion = 1; // Recibo oficial de caja por cobro a cliente
                        $nombre_seccion_IR = 'Retencion1';
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionIR = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_IR)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_ir = CuentasContables::find($cuentaSeccionIR->id_cuenta_contable);
                        $cuenta_contable_ir_padre = CuentasContables::find($cuenta_contable_ir->id_cuenta_padre);

                        $documento_cuenta_contableS3 = new DocumentosCuentas;
                        $documento_cuenta_contableS3->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS3->concepto = $cuentaSeccionIR->descripcion_movimiento . ' ' . $recibo->no_documento;

                        if ($cuentaSeccionIR->debe_haber === 1) {
                            $documento_cuenta_contableS3->debe = $total_ir;
                            $documento_cuenta_contableS3->haber = 0;
                            $documento_cuenta_contableS3->debe_org = $total_ir;
                            $documento_cuenta_contableS3->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS3->debe = 0;
                            $documento_cuenta_contableS3->haber = $total_ir;
                            $documento_cuenta_contableS3->debe_org = 0;
                            $documento_cuenta_contableS3->haber_org = $total_ir;
                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_ir->requiere_aux === 0) {
                            $documento_cuenta_contableS3->id_centro = null;
                            $documento_cuenta_contableS3->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_ir->requiere_aux === 2 || $cuenta_contable_ir->requiere_aux === 3) {
                            $documento_cuenta_contableS3->id_centro = $cuentaSeccionIR->id_centro_costo;
                            $documento_cuenta_contableS3->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_ir->requiere_aux === 1) {
                            $documento_cuenta_contableS3->id_centro = null;
                            $documento_cuenta_contableS3->id_cat_auxiliar_cxc = $cuentaSeccionIR->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS3->id_moneda = 1;
                        $documento_cuenta_contableS3->id_cuenta_contable = $cuenta_contable_ir->id_cuenta_contable;
                        $documento_cuenta_contableS3->cta_contable = $cuenta_contable_ir->cta_contable;
                        $documento_cuenta_contableS3->cta_contable_padre = $cuenta_contable_ir_padre->cta_contable;
                        $documento_cuenta_contableS3->save();
                    }
                    if ($total_imi > 0) {

                        //Retencion IMI
                        $id_tipo_configuracion = 1; // Recibo oficial de caja por cobro a cliente
                        $nombre_seccion_IMI = 'Retencion2';
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionIMI = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_IMI)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_imi = CuentasContables::find($cuentaSeccionIMI->id_cuenta_contable);
                        $cuenta_contable_imi_padre = CuentasContables::find($cuenta_contable_imi->id_cuenta_padre);

                        $documento_cuenta_contableS4 = new DocumentosCuentas;
                        $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS4->concepto = $cuentaSeccionIMI->descripcion_movimiento . ' ' . $recibo->no_documento;

                        if ($cuentaSeccionIMI->debe_haber === 1) {
                            $documento_cuenta_contableS4->debe = $total_imi;
                            $documento_cuenta_contableS4->haber = 0;
                            $documento_cuenta_contableS4->debe_org = $total_imi;
                            $documento_cuenta_contableS4->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS4->debe = 0;
                            $documento_cuenta_contableS4->haber = $total_imi;;
                            $documento_cuenta_contableS4->debe_org = 0;
                            $documento_cuenta_contableS4->haber_org = $total_imi;;
                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_imi->requiere_aux === 0) {
                            $documento_cuenta_contableS4->id_centro = null;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_imi->requiere_aux === 2 || $cuenta_contable_imi->requiere_aux === 3) {
                            $documento_cuenta_contableS4->id_centro = $cuentaSeccionIMI->id_centro_costo;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_imi->requiere_aux === 1) {
                            $documento_cuenta_contableS4->id_centro = null;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = $cuentaSeccionIMI->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS4->id_moneda = 1;
                        $documento_cuenta_contableS4->id_cuenta_contable = $cuenta_contable_imi->id_cuenta_contable;
                        $documento_cuenta_contableS4->cta_contable = $cuenta_contable_imi->cta_contable;
                        $documento_cuenta_contableS4->cta_contable_padre = $cuenta_contable_imi_padre->cta_contable;
                        $documento_cuenta_contableS4->save();
                    }

                    #endregion ROC-cobro-a-clientes

                    #region AnticipoClientes
                    // ---------------------------------------contabilziación tipo recibo 2 - Anticipos---------------------------------------------
                } else if ($request->tipo_recibo === '2' || $request->tipo_recibo === 2) {  // Tipo de recibo "Anticipo de clientes""

                    if ($recibo->monto_total > 0) {
                        $total_pago_tarjeta=0; //Acumulador de pago de tarjeta cordobas
                        $total_pago_tarjeta_me=0; //Acumulador de pago de tarjeta dolares

                        foreach($request->detallePago as $pago){
                            $id_tipo_configuracion = 2; // Recibo oficial de caja por anticipo de clientes - Tarjeta
                            $cancelacion_contado = 3; // Recibo oficial de caja por anticipo de clientes - Banco

                            #region Pago por Tarjeta - BAC
                            if ($pago['via_pagox']['id_via_pago'] === 3 && $pago['banco_x']['id_banco'] === 1) {
                                $total_pago_tarjeta += $pago['monto'];
                                $total_pago_tarjeta_me += $pago['monto_me'];
                                #region pago BAC CORDOBAS
                                if ($pago['moneda_x']['id_moneda'] === 1 && $pago['banco_x']['id_banco'] === 1) {
                                    $nombre_seccion_MonNacional = 'BacC$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;

                                    //Calculos de retencion por uso de tarjeta de crédito
                                    //Dinamizar estos factores de cálculos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    //Validar naturaleza de la cuenta y guardar montos
                                    // Primero se realiza la comision, y de este resultado se aplica retencion
                                    //comision tasa 3.50
                                    //retencion tasa 1.50

                                    $comision_tarjeta = $pago['monto'] * 0.035; // resultado con monto 500 * 0.035 = 17.5
                                    $monto_despues_comision = $pago['monto'] - $comision_tarjeta; // 500 - 17.5 = resultado = 482.5
                                    $retencion_tarjeta = $monto_despues_comision * 0.015; // resultado = 7.2375

                                    $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta; // resultado = 24.7375

                                    $total_recibo = $pago['monto'] - $total_retencion_tarjeta; // resultado = 475.2625

                                    $monto_total_bruto = $total_recibo;

                                    $comision_tarjeta_me = $pago['monto_me'] * 0.035;
                                    $monto_despues_comision_me = $pago['monto_me'] - $comision_tarjeta_me;
                                    $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                    $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                    $total_recibo_me = $pago['monto_me'] - $total_retencion_tarjeta_me;

                                    $monto_total_bruto_me = $total_recibo_me;


                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        $documento_cuenta_contableS1->debe = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber = 0;
                                        $documento_cuenta_contableS1->debe_org = $monto_total_bruto - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber_org = 0;

                                    } else {
                                        $documento_cuenta_contableS1->debe = 0;
                                        $documento_cuenta_contableS1->haber = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->debe_org = 0;
                                        $documento_cuenta_contableS1->haber_org = $monto_total_bruto - $total_imi - $total_ir;
                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                    #endregion
                                #region Pago BAC Dolares
                                }else if ($pago['moneda_x']['id_moneda'] === 2 && $pago['banco_x']['id_banco'] === 1){
                                    $nombre_seccion_MonNacional = 'Bac$';
                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;

                                    //Calculos de retencion por uso de tarjeta de crédito
                                    //Dinamizar estos factores de cálculos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    //Validar naturaleza de la cuenta y guardar montos
                                    // Primero se realiza la comision, y de este resultado se aplica retencion
                                    //comision tasa 3.50
                                    //retencion tasa 1.50

                                    $comision_tarjeta = $pago['monto'] * 0.035; // resultado con monto 500 * 0.035 = 17.5
                                    $monto_despues_comision = $pago['monto'] - $comision_tarjeta; // 500 - 17.5 = resultado = 482.5
                                    $retencion_tarjeta = $monto_despues_comision * 0.015; // resultado = 7.2375

                                    $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta; // resultado = 24.7375

                                    $total_recibo = $pago['monto'] - $total_retencion_tarjeta; // resultado = 475.2625

                                    $monto_total_bruto = $total_recibo;

                                    $comision_tarjeta_me = $pago['monto_me'] * 0.035;
                                    $monto_despues_comision_me = $pago['monto_me'] - $comision_tarjeta_me;
                                    $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                    $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                    $total_recibo_me = $pago['monto_me'] - $total_retencion_tarjeta_me;

                                    $monto_total_bruto_me = $total_recibo_me;


                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        $documento_cuenta_contableS1->debe = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber = 0;
                                        $documento_cuenta_contableS1->debe_org = $monto_total_bruto - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber_org = 0;

                                    } else {
                                        $documento_cuenta_contableS1->debe = 0;
                                        $documento_cuenta_contableS1->haber = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->debe_org = 0;
                                        $documento_cuenta_contableS1->haber_org = $monto_total_bruto - $total_imi - $total_ir;
                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                }
                                #endregion

                            #endregion

                            #region Via pago banco (transferencias o minutas de deposito) -> Banco
                            } else if (($pago['via_pagox']['id_via_pago'] === 1 || $pago['via_pagox']['id_via_pago'] === 6)) {
                                #region Bac moneda cordobas

                                if ($pago['moneda_x']['id_moneda'] === 1 && $pago['banco_x']['id_banco'] === 1) {
                                    $nombre_seccion_MonNacional = 'BacC$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();

                                    #endregion

                                    #region Bac Dolares
                                } else if ($pago['moneda_x']['id_moneda'] === 2 && $pago['banco_x']['id_banco'] === 1) {
                                    $nombre_seccion_MonNacional = 'Bac$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();

                                    #endregion
                                    #region Lafise Cordobas
                                } else if ($pago['moneda_x']['id_moneda'] === 1 && $pago['banco_x']['id_banco'] === 2) {
                                    $nombre_seccion_MonNacional = 'lafiseC$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                    #endregion

                                    #region Lafise Dolares
                                } else if ($pago['moneda_x']['id_moneda'] === 2 && $pago['banco_x']['id_banco'] === 2) {
                                    $nombre_seccion_MonNacional = 'lafise$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                }
                                #endregion
                            }
                        }
                        #region IR y COMISIONES de Tarjeta
                        /*======================================================Comisiones tarjeta credito==================================*/

                        $id_tipo_configuracion = 2; // Recibo oficial de caja por anticipo de clientes

                        $nombre_seccion_Comision = 'ComisionTarjeta';

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionComision = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_Comision)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_comision = CuentasContables::find($cuentaSeccionComision->id_cuenta_contable);
                        $cuenta_contable_comision_padre = CuentasContables::find($cuenta_contable_comision->id_cuenta_padre);

                        $documento_cuenta_contableS2 = new DocumentosCuentas;
                        $documento_cuenta_contableS2->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS2->concepto = $cuentaSeccionComision->descripcion_movimiento . ' ' . $recibo->no_documento;

                        //Calculos de retencion por uso de tarjeta de crédito

                        $comision_tarjeta = $total_pago_tarjeta * 0.035;
                        $monto_despues_comision = $total_pago_tarjeta - $comision_tarjeta;
                        $retencion_tarjeta = $monto_despues_comision * 0.015;
                        $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                        $total_recibo = $total_pago_tarjeta - $total_retencion_tarjeta;

                        $comision_tarjeta_me = $total_pago_tarjeta_me * 0.035;
                        $monto_despues_comision_me = $total_pago_tarjeta_me - $comision_tarjeta_me;
                        $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                        $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                        $total_recibo_me = $total_pago_tarjeta_me - $total_retencion_tarjeta_me;


                        //Validar naturaleza de la cuenta y guardar montos

                        if ($cuentaSeccionComision->debe_haber === 1) {
                            $documento_cuenta_contableS2->debe = $comision_tarjeta_me;
                            $documento_cuenta_contableS2->haber = 0;
                            $documento_cuenta_contableS2->debe_org = $comision_tarjeta;
                            $documento_cuenta_contableS2->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS2->debe = 0;
                            $documento_cuenta_contableS2->haber = $comision_tarjeta_me;
                            $documento_cuenta_contableS2->debe_org = 0;
                            $documento_cuenta_contableS2->haber_org = $comision_tarjeta;
                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_comision->requiere_aux === 0) {
                            $documento_cuenta_contableS2->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_comision->requiere_aux === 2 || $cuenta_contable_comision->requiere_aux === 3) {
                            $documento_cuenta_contableS2->id_centro = $cuentaSeccionComision->id_centro_costo;
                            $documento_cuenta_contableS2->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_comision->requiere_aux === 1) {
                            $documento_cuenta_contableS2->id_centro = null;
                            $documento_cuenta_contableS2->id_cat_auxiliar_cxc = $cuentaSeccionComision->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS2->id_moneda = 1;
                        $documento_cuenta_contableS2->id_cuenta_contable = $cuenta_contable_comision->id_cuenta_contable;
                        $documento_cuenta_contableS2->cta_contable = $cuenta_contable_comision->cta_contable;
                        $documento_cuenta_contableS2->cta_contable_padre = $cuenta_contable_comision_padre->id_cuenta_contable;
                        $documento_cuenta_contableS2->save();

                        /*======================================================Retencion tarjeta credito==================================*/

                        $id_tipo_configuracion = 2; // Recibo oficial de caja por anticipo de clientes

                        $nombre_seccion_Retencion = 'IrTarjetaCred';

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionRetencion = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_Retencion)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_retencion = CuentasContables::find($cuentaSeccionRetencion->id_cuenta_contable);
                        $cuenta_contable_retencion_padre = CuentasContables::find($cuenta_contable_retencion->id_cuenta_padre);

                        $documento_cuenta_contableS3 = new DocumentosCuentas;
                        $documento_cuenta_contableS3->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS3->concepto = $cuentaSeccionRetencion->descripcion_movimiento . ' ' . $recibo->no_documento;

                        //Calculos de retencion por uso de tarjeta de crédito
                        $comision_tarjeta = $total_pago_tarjeta * 0.035;
                        $monto_despues_comision = $total_pago_tarjeta - $comision_tarjeta;
                        $retencion_tarjeta = $monto_despues_comision * 0.015;
                        $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                        $total_recibo = $total_pago_tarjeta - $total_retencion_tarjeta;

                        $comision_tarjeta_me = $total_pago_tarjeta_me * 0.035;
                        $monto_despues_comision_me = $total_pago_tarjeta_me - $comision_tarjeta_me;
                        $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                        $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                        $total_recibo_me = $total_pago_tarjeta_me - $total_retencion_tarjeta_me;


                        //Validar naturaleza de la cuenta y guardar montos

                        if ($cuentaSeccionRetencion->debe_haber === 1) {
                            $documento_cuenta_contableS3->debe = $retencion_tarjeta_me;
                            $documento_cuenta_contableS3->haber = 0;
                            $documento_cuenta_contableS3->debe_org = $retencion_tarjeta;
                            $documento_cuenta_contableS3->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS3->debe = 0;
                            $documento_cuenta_contableS3->haber = $retencion_tarjeta_me;
                            $documento_cuenta_contableS3->debe_org = 0;
                            $documento_cuenta_contableS3->haber_org = $retencion_tarjeta;
                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_retencion->requiere_aux === 0) {
                            $documento_cuenta_contableS3->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_retencion->requiere_aux === 2 || $cuenta_contable_retencion->requiere_aux === 3) {
                            $documento_cuenta_contableS3->id_centro = $cuentaSeccionRetencion->id_centro_costo;
                            $documento_cuenta_contableS3->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_retencion->requiere_aux === 1) {
                            $documento_cuenta_contableS3->id_centro = null;
                            $documento_cuenta_contableS3->id_cat_auxiliar_cxc = $cuentaSeccionRetencion->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS3->id_moneda = 1;
                        $documento_cuenta_contableS3->id_cuenta_contable = $cuenta_contable_retencion->id_cuenta_contable;
                        $documento_cuenta_contableS3->cta_contable = $cuenta_contable_retencion->cta_contable;
                        $documento_cuenta_contableS3->cta_contable_padre = $cuenta_contable_retencion_padre->id_cuenta_contable;
                        $documento_cuenta_contableS3->save();
                        #endregion
                        #region Anticipo de clientes por compra*/
                        $id_tipo_configuracion = 2; // Recibo oficial de caja por anticipo de clientes

                        $nombre_seccion_cliente = 'AnticipoCliente';

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionCliente = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_cliente)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_cliente = CuentasContables::find($cuentaSeccionCliente->id_cuenta_contable);
                        $cuenta_contable_cliente_padre = CuentasContables::find($cuenta_contable_cliente->id_cuenta_padre);

                        $documento_cuenta_contableS4 = new DocumentosCuentas;
                        $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS4->concepto = $cuentaSeccionCliente->descripcion_movimiento . ' ' . $recibo->no_documento;

                        //Calculos de retencion por uso de tarjeta de crédito
                        $comision_tarjeta = $recibo->monto_total * 0.035;
                        $monto_despues_comision = $recibo->monto_total - $comision_tarjeta;
                        $retencion_tarjeta = $monto_despues_comision * 0.015;
                        $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                        $total_recibo = $recibo->monto_total - $total_retencion_tarjeta;

                        //Validar naturaleza de la cuenta y guardar montos
                        $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
                        if ($cuentaSeccionCliente->debe_haber === 1) {
                            if ($currency_id->valor === 1) {
                                $comision_tarjeta = $recibo->monto_total * 0.035;
                                $monto_despues_comision = $recibo->monto_total - $comision_tarjeta;
                                $retencion_tarjeta = $monto_despues_comision * 0.015;
                                $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                                $total_recibo = $recibo->monto_total - $total_retencion_tarjeta;

                                $documento_cuenta_contableS4->debe = $recibo->monto_total + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber = 0;
                                $documento_cuenta_contableS4->debe_org = $recibo->monto_total + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber_org = 0;
                            } else {
                                $comision_tarjeta_me = $recibo->monto_total_me * 0.035;
                                $monto_despues_comision_me = $recibo->monto_total_me - $comision_tarjeta_me;
                                $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                $total_recibo_me = $recibo->monto_total_me - $total_retencion_tarjeta_me;

                                $documento_cuenta_contableS4->debe = $recibo->monto_total_me;//+ $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber = 0;
                                $documento_cuenta_contableS4->debe_org = $recibo->monto_total_me;// + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber_org = 0;
                            }

                        } else {
                            if ($currency_id->valor === 1) {
                                $comision_tarjeta = $recibo->monto_total * 0.035;
                                $monto_despues_comision = $recibo->monto_total - $comision_tarjeta;
                                $retencion_tarjeta = $monto_despues_comision * 0.015;
                                $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;

                                $documento_cuenta_contableS4->debe = 0;
                                $documento_cuenta_contableS4->haber = $recibo->monto_total;// + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->debe_org = 0;
                                $documento_cuenta_contableS4->haber_org = $recibo->monto_total;// + $total_retencion_tarjeta;
                            } else {
                                $comision_tarjeta_me = $recibo->monto_total_me * 0.035;
                                $monto_despues_comision_me = $recibo->monto_total_me - $comision_tarjeta_me;
                                $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                $total_recibo_me = $recibo->monto_total_me - $total_retencion_tarjeta_me;

                                $documento_cuenta_contableS4->debe = 0;
                                $documento_cuenta_contableS4->haber = $recibo->monto_total_me;// + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->debe_org = 0;
                                $documento_cuenta_contableS4->haber_org = $recibo->monto_total;// + $total_retencion_tarjeta;
                            }

                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_cliente->requiere_aux === 0) {
                            $documento_cuenta_contableS4->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_cliente->requiere_aux === 2 || $cuenta_contable_cliente->requiere_aux === 3) {
                            $documento_cuenta_contableS4->id_centro = $cuentaSeccionCliente->id_centro_costo;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_cliente->requiere_aux === 1) {
                            $documento_cuenta_contableS4->id_centro = null;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = $cuentaSeccionCliente->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS4->id_moneda = 1;
                        $documento_cuenta_contableS4->id_cuenta_contable = $cuenta_contable_cliente->id_cuenta_contable;
                        $documento_cuenta_contableS4->cta_contable = $cuenta_contable_cliente->cta_contable;
                        $documento_cuenta_contableS4->cta_contable_padre = $cuenta_contable_cliente_padre->id_cuenta_contable;
                        $documento_cuenta_contableS4->save();
                        #endregion

                    }
                    // RETENCION IR
                    if ($total_ir > 0) {


                        $id_tipo_configuracion = 1; // Recibo oficial de caja por cobro a cliente
                        $nombre_seccion_IR = 'Retencion1';
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionIR = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_IR)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_ir = CuentasContables::find($cuentaSeccionIR->id_cuenta_contable);
                        $cuenta_contable_ir_padre = CuentasContables::find($cuenta_contable_ir->id_cuenta_padre);

                        $documento_cuenta_contableS5 = new DocumentosCuentas;
                        $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS5->concepto = $cuentaSeccionIR->descripcion_movimiento . ' ' . $recibo->no_documento;

                        if ($cuentaSeccionIR->debe_haber === 1) {
                            $documento_cuenta_contableS5->debe = $total_ir;
                            $documento_cuenta_contableS5->haber = 0;
                            $documento_cuenta_contableS5->debe_org = $total_ir;
                            $documento_cuenta_contableS5->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS5->debe = 0;
                            $documento_cuenta_contableS5->haber = $total_ir;
                            $documento_cuenta_contableS5->debe_org = 0;
                            $documento_cuenta_contableS5->haber_org = $total_ir;
                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_ir->requiere_aux === 0) {
                            $documento_cuenta_contableS5->id_centro = null;
                            $documento_cuenta_contableS5->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_ir->requiere_aux === 2 || $cuenta_contable_ir->requiere_aux === 3) {
                            $documento_cuenta_contableS5->id_centro = $cuentaSeccionIR->id_centro_costo;
                            $documento_cuenta_contableS5->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_ir->requiere_aux === 1) {
                            $documento_cuenta_contableS5->id_centro = null;
                            $documento_cuenta_contableS5->id_cat_auxiliar_cxc = $cuentaSeccionIR->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS5->id_moneda = 1;
                        $documento_cuenta_contableS5->id_cuenta_contable = $cuenta_contable_ir->id_cuenta_contable;
                        $documento_cuenta_contableS5->cta_contable = $cuenta_contable_ir->cta_contable;
                        $documento_cuenta_contableS5->cta_contable_padre = $cuenta_contable_ir_padre->cta_contable;
                        $documento_cuenta_contableS5->save();
                    }


                    if ($total_imi > 0) {

                        //Retencion IMI
                        $id_tipo_configuracion = 1; // Recibo oficial de caja por cobro a cliente
                        $nombre_seccion_IMI = 'Retencion2';
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionIMI = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_IMI)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_imi = CuentasContables::find($cuentaSeccionIMI->id_cuenta_contable);
                        $cuenta_contable_imi_padre = CuentasContables::find($cuenta_contable_imi->id_cuenta_padre);

                        $documento_cuenta_contableS4 = new DocumentosCuentas;
                        $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS4->concepto = $cuentaSeccionIMI->descripcion_movimiento . ' ' . $recibo->no_documento;

                        if ($cuentaSeccionIMI->debe_haber === 1) {
                            $documento_cuenta_contableS4->debe = $total_imi;
                            $documento_cuenta_contableS4->haber = 0;
                            $documento_cuenta_contableS4->debe_org = $total_imi;
                            $documento_cuenta_contableS4->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS4->debe = 0;
                            $documento_cuenta_contableS4->haber = $total_imi;
                            $documento_cuenta_contableS4->debe_org = 0;
                            $documento_cuenta_contableS4->haber_org = $total_imi;
                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_imi->requiere_aux === 0) {
                            $documento_cuenta_contableS4->id_centro = null;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_imi->requiere_aux === 2 || $cuenta_contable_imi->requiere_aux === 3) {
                            $documento_cuenta_contableS4->id_centro = $cuentaSeccionIMI->id_centro_costo;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_imi->requiere_aux === 1) {
                            $documento_cuenta_contableS4->id_centro = null;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = $cuentaSeccionIMI->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS4->id_moneda = 1;
                        $documento_cuenta_contableS4->id_cuenta_contable = $cuenta_contable_imi->id_cuenta_contable;
                        $documento_cuenta_contableS4->cta_contable = $cuenta_contable_imi->cta_contable;
                        $documento_cuenta_contableS4->cta_contable_padre = $cuenta_contable_imi_padre->cta_contable;
                        $documento_cuenta_contableS4->save();
                    }

                    #endregion AnticipoClientes

                    #endregion
                    #region ReciboFacturaContado
                } else if ($request->tipo_recibo === '3' || $request->tipo_recibo === 3) {
                    if ($recibo->monto_total > 0) {
                        $total_pago_tarjeta=0; //Acumulador de pago de tarjeta cordobas
                        $total_pago_tarjeta_me=0; //Acumulador de pago de tarjeta dolares

                        foreach($request->detallePago as $pago) {
                            $id_tipo_configuracion = 3; // Recibo oficial de caja por anticipo de clientes - Tarjeta
                            $cancelacion_contado = 3; // Recibo oficial de caja por anticipo de clientes - Banco

                            #region Pago por Tarjeta - BAC
                            if ($pago['via_pagox']['id_via_pago'] === 3 && $pago['banco_x']['id_banco'] === 1) {
                                $total_pago_tarjeta += $pago['monto'];
                                $total_pago_tarjeta_me += $pago['monto_me'];
                                #region pago BAC CORDOBAS
                                if ($pago['moneda_x']['id_moneda'] === 1 && $pago['banco_x']['id_banco'] === 1) {
                                    $nombre_seccion_MonNacional = 'BacC$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;

                                    //Calculos de retencion por uso de tarjeta de crédito
                                    //Dinamizar estos factores de cálculos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    //Validar naturaleza de la cuenta y guardar montos
                                    // Primero se realiza la comision, y de este resultado se aplica retencion
                                    //comision tasa 3.50
                                    //retencion tasa 1.50

                                    $comision_tarjeta = $pago['monto'] * 0.035; // resultado con monto 500 * 0.035 = 17.5
                                    $monto_despues_comision = $pago['monto'] - $comision_tarjeta; // 500 - 17.5 = resultado = 482.5
                                    $retencion_tarjeta = $monto_despues_comision * 0.015; // resultado = 7.2375

                                    $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta; // resultado = 24.7375

                                    $total_recibo = $pago['monto'] - $total_retencion_tarjeta; // resultado = 475.2625

                                    $monto_total_bruto = $total_recibo;

                                    $comision_tarjeta_me = $pago['monto_me'] * 0.035;
                                    $monto_despues_comision_me = $pago['monto_me'] - $comision_tarjeta_me;
                                    $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                    $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                    $total_recibo_me = $pago['monto_me'] - $total_retencion_tarjeta_me;

                                    $monto_total_bruto_me = $total_recibo_me;


                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        $documento_cuenta_contableS1->debe = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber = 0;
                                        $documento_cuenta_contableS1->debe_org = $monto_total_bruto - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber_org = 0;

                                    } else {
                                        $documento_cuenta_contableS1->debe = 0;
                                        $documento_cuenta_contableS1->haber = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->debe_org = 0;
                                        $documento_cuenta_contableS1->haber_org = $monto_total_bruto - $total_imi - $total_ir;
                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                    #endregion
                                    #region Pago BAC Dolares
                                } else if ($pago['moneda_x']['id_moneda'] === 2 && $pago['banco_x']['id_banco'] === 1) {
                                    $nombre_seccion_MonNacional = 'Bac$';
                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;

                                    //Calculos de retencion por uso de tarjeta de crédito
                                    //Dinamizar estos factores de cálculos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    //Validar naturaleza de la cuenta y guardar montos
                                    // Primero se realiza la comision, y de este resultado se aplica retencion
                                    //comision tasa 3.50
                                    //retencion tasa 1.50

                                    $comision_tarjeta = $pago['monto'] * 0.035; // resultado con monto 500 * 0.035 = 17.5
                                    $monto_despues_comision = $pago['monto'] - $comision_tarjeta; // 500 - 17.5 = resultado = 482.5
                                    $retencion_tarjeta = $monto_despues_comision * 0.015; // resultado = 7.2375

                                    $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta; // resultado = 24.7375

                                    $total_recibo = $pago['monto'] - $total_retencion_tarjeta; // resultado = 475.2625

                                    $monto_total_bruto = $total_recibo;

                                    $comision_tarjeta_me = $pago['monto_me'] * 0.035;
                                    $monto_despues_comision_me = $pago['monto_me'] - $comision_tarjeta_me;
                                    $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                    $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                    $total_recibo_me = $pago['monto_me'] - $total_retencion_tarjeta_me;

                                    $monto_total_bruto_me = $total_recibo_me;


                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        $documento_cuenta_contableS1->debe = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber = 0;
                                        $documento_cuenta_contableS1->debe_org = $monto_total_bruto - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->haber_org = 0;

                                    } else {
                                        $documento_cuenta_contableS1->debe = 0;
                                        $documento_cuenta_contableS1->haber = $monto_total_bruto_me - $total_imi - $total_ir;
                                        $documento_cuenta_contableS1->debe_org = 0;
                                        $documento_cuenta_contableS1->haber_org = $monto_total_bruto - $total_imi - $total_ir;
                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                }
                                #endregion

                                #endregion
                                #region Via pago banco (transferencias o minutas de deposito) -> Banco
                            } else if (($pago['via_pagox']['id_via_pago'] === 1 || $pago['via_pagox']['id_via_pago'] === 6)) {
                                #region Bac moneda cordobas

                                if ($pago['moneda_x']['id_moneda'] === 1 && $pago['banco_x']['id_banco'] === 1) {
                                    $nombre_seccion_MonNacional = 'BacC$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();

                                    #endregion

                                    #region Bac Dolares
                                } else if ($pago['moneda_x']['id_moneda'] === 2 && $pago['banco_x']['id_banco'] === 1) {
                                    $nombre_seccion_MonNacional = 'Bac$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();

                                    #endregion
                                    #region Lafise Cordobas
                                } else if ($pago['moneda_x']['id_moneda'] === 1 && $pago['banco_x']['id_banco'] === 2) {
                                    $nombre_seccion_MonNacional = 'lafiseC$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                    #endregion

                                    #region Lafise Dolares
                                } else if ($pago['moneda_x']['id_moneda'] === 2 && $pago['banco_x']['id_banco'] === 2) {
                                    $nombre_seccion_MonNacional = 'lafise$';

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                                    //obtener datos de BD con estos paremetros
                                    $cuentaSeccionMonNacional = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $cancelacion_contado)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();

                                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $recibo->no_documento;


                                    //Validar naturaleza de la cuenta y guardar montos

                                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        } else {
                                            $documento_cuenta_contableS1->debe = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber = 0;
                                            $documento_cuenta_contableS1->debe_org = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->haber_org = 0;
                                        }
                                    } else {
                                        if ($currency_id->valor === 1) {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto_me'] - $total_imi - $total_ir;
                                        } else {
                                            $documento_cuenta_contableS1->debe = 0;
                                            $documento_cuenta_contableS1->haber = $pago['monto_me'] - $total_imi - $total_ir;
                                            $documento_cuenta_contableS1->debe_org = 0;
                                            $documento_cuenta_contableS1->haber_org = $pago['monto'] - $total_imi - $total_ir;
                                        }

                                    }

                                    //Verificación de centros de costo

                                    if ($cuenta_contable_mon_nacional->requiere_aux === 0) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 2 || $cuenta_contable_mon_nacional->requiere_aux === 3) {
                                        $documento_cuenta_contableS1->id_centro = $cuentaSeccionMonNacional->id_centro_costo;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                                    } else if ($cuenta_contable_mon_nacional->requiere_aux === 1) {
                                        $documento_cuenta_contableS1->id_centro = null;
                                        $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionMonNacional->id_cat_auxiliar_cxc;
                                    }

                                    $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                                    $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_mon_nacional->id_cuenta_contable;
                                    $documento_cuenta_contableS1->cta_contable = $cuenta_contable_mon_nacional->cta_contable;
                                    $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_mon_nacional_padre->id_cuenta_contable;
                                    $documento_cuenta_contableS1->save();
                                }
                                #endregion
                            }
                                #endregion
                        }

                        #region IR y COMISIONES de Tarjeta
                        /*======================================================Comisiones tarjeta credito==================================*/

                        $id_tipo_configuracion = 3; // Recibo oficial de caja por anticipo de clientes

                        $nombre_seccion_Comision = 'ComisionTarjeta';

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionComision = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_Comision)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_comision = CuentasContables::find($cuentaSeccionComision->id_cuenta_contable);
                        $cuenta_contable_comision_padre = CuentasContables::find($cuenta_contable_comision->id_cuenta_padre);

                        $documento_cuenta_contableS2 = new DocumentosCuentas;
                        $documento_cuenta_contableS2->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS2->concepto = $cuentaSeccionComision->descripcion_movimiento . ' ' . $recibo->no_documento;

                        //Calculos de retencion por uso de tarjeta de crédito

                        $comision_tarjeta = $total_pago_tarjeta * 0.035;
                        $monto_despues_comision = $total_pago_tarjeta - $comision_tarjeta;
                        $retencion_tarjeta = $monto_despues_comision * 0.015;
                        $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                        $total_recibo = $total_pago_tarjeta - $total_retencion_tarjeta;

                        $comision_tarjeta_me = $total_pago_tarjeta_me * 0.035;
                        $monto_despues_comision_me = $total_pago_tarjeta_me - $comision_tarjeta_me;
                        $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                        $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                        $total_recibo_me = $total_pago_tarjeta_me - $total_retencion_tarjeta_me;


                        //Validar naturaleza de la cuenta y guardar montos

                        if ($cuentaSeccionComision->debe_haber === 1) {
                            $documento_cuenta_contableS2->debe = $comision_tarjeta_me;
                            $documento_cuenta_contableS2->haber = 0;
                            $documento_cuenta_contableS2->debe_org = $comision_tarjeta;
                            $documento_cuenta_contableS2->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS2->debe = 0;
                            $documento_cuenta_contableS2->haber = $comision_tarjeta_me;
                            $documento_cuenta_contableS2->debe_org = 0;
                            $documento_cuenta_contableS2->haber_org = $comision_tarjeta;
                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_comision->requiere_aux === 0) {
                            $documento_cuenta_contableS2->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_comision->requiere_aux === 2 || $cuenta_contable_comision->requiere_aux === 3) {
                            $documento_cuenta_contableS2->id_centro = $cuentaSeccionComision->id_centro_costo;
                            $documento_cuenta_contableS2->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_comision->requiere_aux === 1) {
                            $documento_cuenta_contableS2->id_centro = null;
                            $documento_cuenta_contableS2->id_cat_auxiliar_cxc = $cuentaSeccionComision->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS2->id_moneda = 1;
                        $documento_cuenta_contableS2->id_cuenta_contable = $cuenta_contable_comision->id_cuenta_contable;
                        $documento_cuenta_contableS2->cta_contable = $cuenta_contable_comision->cta_contable;
                        $documento_cuenta_contableS2->cta_contable_padre = $cuenta_contable_comision_padre->id_cuenta_contable;
                        $documento_cuenta_contableS2->save();

                        /*======================================================Retencion tarjeta credito==================================*/

                        $id_tipo_configuracion = 3; // Recibo oficial de caja por anticipo de clientes

                        $nombre_seccion_Retencion = 'IrTarjetaCred';

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionRetencion = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_Retencion)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_retencion = CuentasContables::find($cuentaSeccionRetencion->id_cuenta_contable);
                        $cuenta_contable_retencion_padre = CuentasContables::find($cuenta_contable_retencion->id_cuenta_padre);

                        $documento_cuenta_contableS3 = new DocumentosCuentas;
                        $documento_cuenta_contableS3->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS3->concepto = $cuentaSeccionRetencion->descripcion_movimiento . ' ' . $recibo->no_documento;

                        //Calculos de retencion por uso de tarjeta de crédito
                        $comision_tarjeta = $total_pago_tarjeta * 0.035;
                        $monto_despues_comision = $total_pago_tarjeta - $comision_tarjeta;
                        $retencion_tarjeta = $monto_despues_comision * 0.015;
                        $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                        $total_recibo = $total_pago_tarjeta - $total_retencion_tarjeta;

                        $comision_tarjeta_me = $total_pago_tarjeta_me * 0.035;
                        $monto_despues_comision_me = $total_pago_tarjeta_me - $comision_tarjeta_me;
                        $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                        $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                        $total_recibo_me = $total_pago_tarjeta_me - $total_retencion_tarjeta_me;


                        //Validar naturaleza de la cuenta y guardar montos

                        if ($cuentaSeccionRetencion->debe_haber === 1) {
                            $documento_cuenta_contableS3->debe = $retencion_tarjeta_me;
                            $documento_cuenta_contableS3->haber = 0;
                            $documento_cuenta_contableS3->debe_org = $retencion_tarjeta;
                            $documento_cuenta_contableS3->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS3->debe = 0;
                            $documento_cuenta_contableS3->haber = $retencion_tarjeta_me;
                            $documento_cuenta_contableS3->debe_org = 0;
                            $documento_cuenta_contableS3->haber_org = $retencion_tarjeta;
                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_retencion->requiere_aux === 0) {
                            $documento_cuenta_contableS3->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_retencion->requiere_aux === 2 || $cuenta_contable_retencion->requiere_aux === 3) {
                            $documento_cuenta_contableS3->id_centro = $cuentaSeccionRetencion->id_centro_costo;
                            $documento_cuenta_contableS3->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_retencion->requiere_aux === 1) {
                            $documento_cuenta_contableS3->id_centro = null;
                            $documento_cuenta_contableS3->id_cat_auxiliar_cxc = $cuentaSeccionRetencion->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS3->id_moneda = 1;
                        $documento_cuenta_contableS3->id_cuenta_contable = $cuenta_contable_retencion->id_cuenta_contable;
                        $documento_cuenta_contableS3->cta_contable = $cuenta_contable_retencion->cta_contable;
                        $documento_cuenta_contableS3->cta_contable_padre = $cuenta_contable_retencion_padre->id_cuenta_contable;
                        $documento_cuenta_contableS3->save();
                        #endregion
                        #region Anticipo de clientes por compra*/
                        $id_tipo_configuracion = 3; // Recibo oficial de caja por anticipo de clientes

                        $nombre_seccion_cliente = 'AnticipoCliente';

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', \Illuminate\Support\Facades\Auth::user()->id)->first();
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionCliente = ConfiguracionComprobantes::where('nombre_seccion', $nombre_seccion_cliente)->where('id_tipo_configuracion', $id_tipo_configuracion)->where('id_empresa', $usuario_empresa->id_empresa)->with('configuracionCuentaContable')->first();
                        $cuenta_contable_cliente = CuentasContables::find($cuentaSeccionCliente->id_cuenta_contable);
                        $cuenta_contable_cliente_padre = CuentasContables::find($cuenta_contable_cliente->id_cuenta_padre);

                        $documento_cuenta_contableS4 = new DocumentosCuentas;
                        $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS4->concepto = $cuentaSeccionCliente->descripcion_movimiento . ' ' . $recibo->no_documento;

                        //Calculos de retencion por uso de tarjeta de crédito
                        $comision_tarjeta = $recibo->monto_total * 0.035;
                        $monto_despues_comision = $recibo->monto_total - $comision_tarjeta;
                        $retencion_tarjeta = $monto_despues_comision * 0.015;
                        $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                        $total_recibo = $recibo->monto_total - $total_retencion_tarjeta;

                        //Validar naturaleza de la cuenta y guardar montos
                        $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
                        if ($cuentaSeccionCliente->debe_haber === 1) {
                            if ($currency_id->valor === 1) {
                                $comision_tarjeta = $recibo->monto_total * 0.035;
                                $monto_despues_comision = $recibo->monto_total - $comision_tarjeta;
                                $retencion_tarjeta = $monto_despues_comision * 0.015;
                                $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;
                                $total_recibo = $recibo->monto_total - $total_retencion_tarjeta;

                                $documento_cuenta_contableS4->debe = $recibo->monto_total + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber = 0;
                                $documento_cuenta_contableS4->debe_org = $recibo->monto_total + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber_org = 0;
                            } else {
                                $comision_tarjeta_me = $recibo->monto_total_me * 0.035;
                                $monto_despues_comision_me = $recibo->monto_total_me - $comision_tarjeta_me;
                                $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                $total_recibo_me = $recibo->monto_total_me - $total_retencion_tarjeta_me;

                                $documento_cuenta_contableS4->debe = $recibo->monto_total_me;//+ $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber = 0;
                                $documento_cuenta_contableS4->debe_org = $recibo->monto_total_me;// + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->haber_org = 0;
                            }

                        } else {
                            if ($currency_id->valor === 1) {
                                $comision_tarjeta = $recibo->monto_total * 0.035;
                                $monto_despues_comision = $recibo->monto_total - $comision_tarjeta;
                                $retencion_tarjeta = $monto_despues_comision * 0.015;
                                $total_retencion_tarjeta = $comision_tarjeta + $retencion_tarjeta;

                                $documento_cuenta_contableS4->debe = 0;
                                $documento_cuenta_contableS4->haber = $recibo->monto_total;// + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->debe_org = 0;
                                $documento_cuenta_contableS4->haber_org = $recibo->monto_total;// + $total_retencion_tarjeta;
                            } else {
                                $comision_tarjeta_me = $recibo->monto_total_me * 0.035;
                                $monto_despues_comision_me = $recibo->monto_total_me - $comision_tarjeta_me;
                                $retencion_tarjeta_me = $monto_despues_comision_me * 0.015;

                                $total_retencion_tarjeta_me = $comision_tarjeta_me + $retencion_tarjeta_me;

                                $total_recibo_me = $recibo->monto_total_me - $total_retencion_tarjeta_me;

                                $documento_cuenta_contableS4->debe = 0;
                                $documento_cuenta_contableS4->haber = $recibo->monto_total_me;// + $total_retencion_tarjeta;
                                $documento_cuenta_contableS4->debe_org = 0;
                                $documento_cuenta_contableS4->haber_org = $recibo->monto_total;// + $total_retencion_tarjeta;
                            }

                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_cliente->requiere_aux === 0) {
                            $documento_cuenta_contableS4->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_cliente->requiere_aux === 2 || $cuenta_contable_cliente->requiere_aux === 3) {
                            $documento_cuenta_contableS4->id_centro = $cuentaSeccionCliente->id_centro_costo;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_cliente->requiere_aux === 1) {
                            $documento_cuenta_contableS4->id_centro = null;
                            $documento_cuenta_contableS4->id_cat_auxiliar_cxc = $cuentaSeccionCliente->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS4->id_moneda = 1;
                        $documento_cuenta_contableS4->id_cuenta_contable = $cuenta_contable_cliente->id_cuenta_contable;
                        $documento_cuenta_contableS4->cta_contable = $cuenta_contable_cliente->cta_contable;
                        $documento_cuenta_contableS4->cta_contable_padre = $cuenta_contable_cliente_padre->id_cuenta_contable;
                        $documento_cuenta_contableS4->save();
                        #endregion
                    }
                }
                #endregion ReciboFacturaContado


#endregion /*INICIA movimiento contable - recibo*/


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

    /**
     * Método de anulación
     * @param Request $request
     * @return JsonResponse
     * @author octaviom
     * @access public
     */
    public function anular(Request $request)
    {
        $messages = [
        ];
        $rules = [
            'id_recibo' => 'required|integer',
            'causa_anulacion' => 'required|string|max:100',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();
                $recibo = Recibos::where('id_recibo', $request->id_recibo)->first();
                $recibo->estado = 0;
                $recibo->observacion = $recibo['observacion'] . ' **Recibo o por ' . Auth::user()->usuario . ' a las ' . date("Y-m-d H:i:s") . ' Causa: ' . $request->causa_anulacion;
                $recibo->save();


                if (!empty($recibo->id_documento_contable)) {//facturas con doc contable
                    $documento = DocumentosContables::find($recibo->id_documento_contable);
                    $documento->estado = 0;
                    $documento->save();
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

    /**
     * Crear un nuevo recibo oficial de caja Trabajador
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function registrarROCTrabajador(Request $request)
    {
        $messages = [
            'detalleCuentasxCobrar.min' => 'Se requiere agregar por lo menos una deuda.',
            'detalleCuentasxCobrar.doc_exoneracion_ir.required_if' => 'El campo documento exoneracion es requerido cuando el recibo aplica Retención IR',
            'detalleCuentasxCobrar.doc_exoneracion_imi.required_if' => 'El campo documento exoneracion es requerido cuando el recibo aplica Retención IMI',
        ];

        $rules = [
            'fecha_emision' => 'required|date',
            'recibo_trabajador' => 'required|array|min:1',
            'recibo_trabajador.id_trabajador' => 'required|integer|min:1',
            'nombre_persona' => 'required|string|max:100',
            //'concepto' => 'required|string|max:300',

            'monto_total' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/|min:0',
            'monto_total_me' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/|min:0',
            //'moneda' => 'required|array|min:1',
            't_cambio' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',

            'detallePago' => 'required|array|min:1',
            'detallePago.*.via_pagox.id_via_pago' => 'required|integer|min:1',
            'detallePago.*.moneda_x.id_moneda' => 'required|integer|min:1',
            'detallePago.*.monto_me' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detallePago.*.monto' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detallePago.*.banco_x.id_banco' => 'required_if:detallePago.*.via_pagox.id_via_pago,5|required_if:detallePago.*.via_pagox.id_via_pago,6|integer|min:1|nullable',
            'detallePago.*.numero_minuta' => 'required_if:detallePago.*.via_pagox.id_via_pago,1|string|max:30|nullable',
            'detallePago.*.numero_nota_credito' => 'required_if:detallePago.*.via_pagox.id_via_pago,4|string|max:30|nullable',
            'detallePago.*.numero_cheque' => 'required_if:detallePago.*.via_pagox.id_via_pago,5|string|max:30|nullable',
            'detallePago.*.numero_transferencia' => 'required_if:detallePago.*.via_pagox.id_via_pago,6|string|max:30|nullable',
            'detallePago.*.numero_recibo_pago' => 'required_if:detallePago.*.via_pagox.id_via_pago,7|string|max:30|nullable',

            'detalleCuentasxCobrar' => 'required|array|min:1',
            'detalleCuentasxCobrar.*.cuentax.id_cuentaxcobrar' => 'required|integer|exists:pgsql.cuentasxcobrar.cuentasxcobrar,id_cuentaxcobrar',
            'detalleCuentasxCobrar.*.descripcion_pago' => 'required|string|max:300',
            'detalleCuentasxCobrar.*.monto' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleCuentasxCobrar.*.monto_me' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',

            'detalleCuentasxCobrar.*.aplicaIR' => 'required|boolean',
            'detalleCuentasxCobrar.*.aplicaIMI' => 'required|boolean',

            'detalleCuentasxCobrar.*.monto_retencion_ir' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleCuentasxCobrar.*.monto_retencion_imi' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',

            'detalleCuentasxCobrar.*.doc_exoneracion_ir' => 'required_if:aplicaIR,true|string|max:20|nullable',
            'detalleCuentasxCobrar.*.doc_exoneracion_imi' => 'required_if:aplicaIMI,true|string|max:20|nullable',


        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            try {

                DB::beginTransaction();
                $recibo = new Recibos;
                $recibo->fecha_emision = $request->fecha_emision;
                $recibo->estado = 1;
                $recibo->id_empleado = $request->recibo_trabajador['id_trabajador'];
                $recibo->nombre_persona = $request->nombre_persona;
                $recibo->tipo_recibo = 2;
                $recibo->concepto = $request->concepto;
                $recibo->monto_total = $request->monto_total;
                $recibo->monto_total_me = $request->monto_total_me;
                $recibo->t_cambio = $request->t_cambio;
                $recibo->no_documento = Recibos::max('no_documento') + 1;
                $recibo->u_creacion = Auth::user()->name;
                $recibo->save();

                if ($recibo->monto_total > 0) {
                    $cuentas_x_cobrar = new CuentasXCobrarCuentasXCobrar;
                    $cuentas_x_cobrar->id_trabajador = $recibo->id_empleado;
                    $cuentas_x_cobrar->id_tipo_documento = 6;///Recibo empleado
                    $cuentas_x_cobrar->no_documento_origen = $recibo->no_documento;
                    $cuentas_x_cobrar->es_registro_importado = false;
                    $cuentas_x_cobrar->identificador_origen = $recibo->id_recibo;
                    $cuentas_x_cobrar->fecha_movimiento = date("Y-m-d H:i:s");//$recibo->fecha_emision;
                    $cuentas_x_cobrar->monto_movimiento = $recibo->monto_total * -1;
                    $cuentas_x_cobrar->saldo_actual = 0;
                    $cuentas_x_cobrar->fecha_vencimiento = $recibo->fecha_emision;
                    $cuentas_x_cobrar->descripcion_movimiento = 'Registro de Abono del recibo No.  ' . $recibo->no_documento;
                    $cuentas_x_cobrar->usuario_registra = $recibo->u_creacion;
                    $cuentas_x_cobrar->estado = 2;
                    $cuentas_x_cobrar->save();
                }

                $total_pago_cordobas = 0;
                $total_pago_dolares = 0;

                foreach ($request->detallePago as $pago) {
                    $recibo_pago = new CuentasXCobrarReciboViaPagos();
                    $recibo_pago->id_recibo = $recibo->id_recibo;
                    $recibo_pago->id_via_pago = $pago['via_pagox']['id_via_pago'];
                    $recibo_pago->id_moneda = $pago['moneda_x']['id_moneda'];
                    $recibo_pago->monto_me = $pago['monto_me'];
                    $recibo_pago->monto = $pago['monto'];
                    if ($recibo_pago->id_via_pago === 1 || $recibo_pago->id_via_pago === 3 || $recibo_pago->id_via_pago === 5 || $recibo_pago->id_via_pago === 6) {
                        $recibo_pago->id_banco = $pago['banco_x']['id_banco'];
                    }
                    $recibo_pago->numero_minuta = $pago['numero_minuta'];
                    $recibo_pago->numero_nota_credito = $pago['numero_nota_credito'];
                    $recibo_pago->numero_cheque = $pago['numero_cheque'];
                    $recibo_pago->numero_transferencia = $pago['numero_transferencia'];
                    $recibo_pago->numero_recibo_pago = $pago['numero_recibo_pago'];

                    //if($recibo_pago->id_moneda==1){
                    $total_pago_cordobas += $recibo_pago->monto;
                    //}else{
                    //  $total_pago_dolares = $total_pago_dolares + $recibo_pago->monto_me;
                    //}

                    $recibo_pago->save();
                }

                $total_ir = 0;
                $total_imi = 0;

                foreach ($request->detalleCuentasxCobrar as $recibosDetalles) {
                    $recibos_detalles = new RecibosDetalles;
                    $recibos_detalles->id_recibo = $recibo->id_recibo;
                    $recibos_detalles->descripcion_pago = $recibosDetalles['descripcion_pago'];
                    $recibos_detalles->monto = $recibosDetalles['monto'];
                    $recibos_detalles->monto_me = $recibosDetalles['monto_me'];
                    $recibos_detalles->id_cuentaxcobrar = $recibosDetalles['cuentax']['id_cuentaxcobrar'];
                    $recibos_detalles->fecha_pago = $recibo->fecha_emision;

                    if ($recibosDetalles['aplicaIR']) {
                        $recibos_detalles->retencion_ir = $recibosDetalles['monto_retencion_ir'];
                        $recibos_detalles->doc_exoneracion_ir = $recibosDetalles['doc_exoneracion_ir'];
                    } else {
                        $recibos_detalles->retencion_ir = 0;
                        $recibos_detalles->doc_exoneracion_ir = '';
                    }

                    if ($recibosDetalles['aplicaIMI']) {
                        $recibos_detalles->retencion_imi = $recibosDetalles['monto_retencion_imi'];
                        $recibos_detalles->doc_exoneracion_imi = $recibosDetalles['doc_exoneracion_imi'];
                    } else {
                        $recibos_detalles->retencion_imi = 0;
                        $recibos_detalles->doc_exoneracion_imi = '';
                    }

                    $total_imi = $total_imi + $recibos_detalles->retencion_imi;
                    $total_ir = $total_ir + $recibos_detalles->retencion_ir;

                    $recibos_detalles->save();

                    $cuentas_x_cobrarUpdate = CuentasXCobrar::findOrFail($recibos_detalles->id_cuentaxcobrar);

                    $saldoActual = round($cuentas_x_cobrarUpdate->saldo_actual - $recibos_detalles->monto, 2);
                    /* 0.0018 */
                    /*revisar condiciones */
                    if (abs($saldoActual) === 0) {
                        $cuentas_x_cobrarUpdate->saldo_actual = 0;
                        $cuentas_x_cobrarUpdate->estado = 2;
                    } else {
                        $cuentas_x_cobrarUpdate->saldo_actual = $saldoActual;
                    }

                    $cuentas_x_cobrarUpdate->save();

                    if ($cuentas_x_cobrarUpdate->id_tipo_documento === 1) {
                        $facturaActualizar = Facturas::where('id_factura', $cuentas_x_cobrarUpdate->identificador_origen)->first();
                        $facturaActualizar->saldo_factura = round($facturaActualizar->saldo_factura - $recibos_detalles->monto, 4);
                        $facturaActualizar->save();
                    }
                }


                /*INICIA movimiento contable - recibo*/

                $documento = new DocumentosContables();
                $tipo = TiposDocumentos::find(6);
                $fecha = date("Y-m-d H:i:s");
                $codigo = $documento->obtenerCodigoDocumento(array('id_tipo_doc' => 6, 'fecha_doc' => $fecha));

                $nuevo_codigo = json_decode($codigo[0]);

                date_default_timezone_set('America/Managua');

                $documento->num_documento = $tipo->prefijo . '-' . $nuevo_codigo->secuencia;
                $documento->fecha_emision = date('Y-m-d');
                $documento->codigo_documento = $nuevo_codigo->secuencia;


                $date = DateTime::createFromFormat("Y-m-d", $documento->fecha_emision);

                $periodo = PeriodosFiscales::where('periodo', $date->format("Y"))->get();

                if (empty($periodo[0])) {
                    return response()->json([
                        'status' => 'error',
                        'result' => array('fecha_emision' => ["El periodo " . $date->format("Y") . " no se encuentra registrado, por favor consulte al administrador"]),
                        'messages' => null
                    ]);
                    exit;
                }

                if ($periodo[0]->estado) {
                    return response()->json([
                        'status' => 'error',
                        'result' => array('fecha_emision' => ["El periodo " . $date->format("Y") . " es inválido, ya que se encuentra en estado COMPLETADO"]),
                        'messages' => null
                    ]);
                    exit;
                }

                $periodo_mes = PeriodosMeses::where('id_periodo_fiscal', $periodo[0]->id_periodo_fiscal)->where('mes', $date->format("n"))->get();

                if (empty($periodo_mes[0])) {
                    return response()->json([
                        'status' => 'error',
                        'result' => array('fecha_emision' => ["El mes " . $date->format("F") . " no se encuentra registrado, por favor consulte al administrador"]),
                        'messages' => null
                    ]);
                    exit;
                }

                if ($periodo_mes[0]->estado === 2) {
                    return response()->json([
                        'status' => 'error',
                        'result' => array('fecha_emision' => ["El mes " . config('global.meses')[$periodo_mes[0]->mes - 1] . " es inválido, ya que se encuentra en estado COMPLETADO"]),
                        'messages' => null
                    ]);
                    exit;
                }

                $documento->id_periodo_fiscal = $periodo[0]->id_periodo_fiscal;

                $documento->id_tipo_doc = 6;
                $documento->valor = $recibo->monto_total;
                $documento->concepto = 'Registramos pago de Recibo No. ' . $recibo->no_documento;
                $documento->id_moneda = 1;
                $documento->u_creacion = Auth::user()->name;
                $documento->estado = 1;

                $documento->save();
                TiposDocumentos::find($documento->id_tipo_doc)->increment('secuencia');

                if ($recibo->monto_total > 0) {
                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS1->concepto = 'Registramos ingreso de moneda nacional por recibo No. ' . $recibo->no_documento;
                    $documento_cuenta_contableS1->debe = $recibo->monto_total - $total_imi - $total_ir;
                    $documento_cuenta_contableS1->haber = 0;
                    $documento_cuenta_contableS1->debe_org = $recibo->monto_total - $total_imi - $total_ir;
                    $documento_cuenta_contableS1->haber_org = 0;
                    $documento_cuenta_contableS1->id_moneda = 1;
                    $documento_cuenta_contableS1->id_centro = null;
                    $documento_cuenta_contableS1->id_cuenta_contable = 112;
                    $documento_cuenta_contableS1->cta_contable = '1111-01-000';
                    $documento_cuenta_contableS1->cta_contable_padre = '1111-00-000';
                    $documento_cuenta_contableS1->save();
                }

                if ($total_ir > 0) {
                    $documento_cuenta_contableS6 = new DocumentosCuentas;
                    $documento_cuenta_contableS6->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS6->concepto = 'Registramos retencion 2% ventas por Recibo No. ' . $recibo->no_documento;
                    $documento_cuenta_contableS6->debe = $total_ir;
                    $documento_cuenta_contableS6->haber = 0;
                    $documento_cuenta_contableS6->debe_org = $total_ir;
                    $documento_cuenta_contableS6->haber_org = 0;
                    $documento_cuenta_contableS6->id_moneda = 1;
                    $documento_cuenta_contableS6->id_centro = null;
                    $documento_cuenta_contableS6->id_cuenta_contable = 129;
                    $documento_cuenta_contableS6->cta_contable = '1142-02-000';
                    $documento_cuenta_contableS6->cta_contable_padre = '1142-00-000';
                    $documento_cuenta_contableS6->save();
                }

                if ($total_imi > 0) {
                    $documento_cuenta_contableS4 = new DocumentosCuentas;
                    $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS4->concepto = 'Registramos retencion alcaldia 1% ventas por Recibo No. ' . $recibo->no_documento;
                    $documento_cuenta_contableS4->debe = $total_imi;
                    $documento_cuenta_contableS4->haber = 0;
                    $documento_cuenta_contableS4->debe_org = $total_imi;
                    $documento_cuenta_contableS4->haber_org = 0;
                    $documento_cuenta_contableS4->id_moneda = 1;
                    $documento_cuenta_contableS4->id_centro = null;
                    $documento_cuenta_contableS4->id_cuenta_contable = 130;
                    $documento_cuenta_contableS4->cta_contable = '1142-03-000';
                    $documento_cuenta_contableS4->cta_contable_padre = '1142-00-000';
                    $documento_cuenta_contableS4->save();
                }

                $documento_cuenta_contableCliente = new DocumentosCuentas;
                $documento_cuenta_contableCliente->id_documento = $documento->id_documento;
                $documento_cuenta_contableCliente->id_moneda = 1;
                $documento_cuenta_contableCliente->concepto = $documento->concepto;
                $documento_cuenta_contableCliente->debe_org = 0;
                $documento_cuenta_contableCliente->haber_org = $recibo->monto_total;
                $documento_cuenta_contableCliente->debe = 0;
                $documento_cuenta_contableCliente->haber = $recibo->monto_total;
                $documento_cuenta_contableCliente->id_centro = null;
                $documento_cuenta_contableCliente->id_cuenta_contable = 60;
                $documento_cuenta_contableCliente->cta_contable = '1123-00-000';
                $documento_cuenta_contableCliente->cta_contable_padre = '1120-00-000';
                $documento_cuenta_contableCliente->save();

                /* TERMINA MOVIMIENTO CONTABLE*/


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

    public function reporteRecibos($ext, $id_recibo)
    {
        // echo $ext;
        //$ext = 'pdf';
        $os = array("pdf");
        if (in_array($ext, $os, true)) {
            $hora_actual = time();
            // Rutas para descarga Reportes local
            $input = env('APP_URL_REPORTES') . 'ReciboCaja';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'ReciboCaja';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'InventarioFacturasProducto';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'InventarioFacturasProducto';

            //Ajustes generales del sistema

            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'id_recibo' => $id_recibo,
                    /*'logo_empresa' => env('APP_URL_IMAGES') . $logo_empresa->valor,
                    'nombre_empresa' => $nombre_empresa->valor,*/
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


            //print_r( env('APP_URL_REPORTS').$logo_empresa->valor);
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext, $hora_actual . 'ReporteRecibo.' . $ext, $headers);

            /*            exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                        print_r($output);*/
        } else {
            return '';
        }
    }


}
