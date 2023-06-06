<?php

namespace App\Http\Controllers\CajaBanco;


use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\Departamentos;
use App\Models\Admon\Sucursales;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Admon\Zonas;
use App\Models\CajaBanco\Afectaciones;
use App\Models\CajaBanco\Bancos;
use App\Models\CajaBanco\FacturacionConfiguracion;
use App\Models\CajaBanco\FacturaDetalle;
use App\Models\CajaBanco\Facturas;
use App\Models\CajaBanco\FacturaViaPagos;
use App\Models\CajaBanco\Proformas;
use App\Models\CajaBanco\ViaPagos;
use App\Models\Contabilidad\CuentasContables;
use App\Models\Contabilidad\DocumentosContables;
use App\Models\Contabilidad\DocumentosCuentas;
use App\Models\Contabilidad\Monedas;
use App\Models\Contabilidad\PeriodosFiscales;
use App\Models\Contabilidad\PeriodosMeses;
use App\Models\Contabilidad\TasasCambios;
use App\Models\Contabilidad\TiposDocumentos;
use App\Models\CuentasXCobrar\CuentasXCobrar;
use App\Models\CuentasXCobrar\Proyectos;
use App\Models\CuentasXCobrar\Recibos;
use App\Models\Inventario\BodegaProductos;
use App\Models\Inventario\Bodegas;
use App\Models\Inventario\ConfiguracionInventario;
use App\Models\Inventario\EntradaProductos;
use App\Models\Inventario\Entradas;
use App\Models\Inventario\SalidaProductos;
use App\Models\Inventario\Salidas;
use App\Models\Ventas\Clientes;
use App\Models\Ventas\Vendedores;
use Clarkeash\Doorman\Exceptions\DoormanException;
use Clarkeash\Doorman\Facades\Doorman;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;

class FacturasController extends Controller
{
    /**
     * Get List of Facturas
     *
     * @access  public
     * @param Request $request
     * @param Facturas $facturas
     * @return JsonResponse
     */

    public function obtener(Request $request, Facturas $facturas)
    {
//        DB::connection()->enableQueryLog();
        $facturas = $facturas->obtenerFacturas($request);
//        $queries = DB::getQueryLog();
        foreach ($facturas as $factura) {
            //   print_r($entrada);
            $items = collect($factura->facturaProductos);

            $factura->tot_unidades = $items->sum(function ($item) {
                return $item['cantidad'];
            });
        }
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first(); // 1-cordobas 2-dolares
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $facturas->total(),
                'rows' => $facturas->items(),
                'currency_id' => $currency_id->valor
            ],
            'messages' => null,
//            'queries' => $queries
        ]);
    }

    /**
     * Get List of Entrada
     *
     * @access  public
     * @param Request $request
     * @param Facturas $factura
     * @return JsonResponse
     */

    public function obtenerFactura(Request $request, Facturas $factura)
    {
        $rules = [
            'id_factura' => 'required|integer|min:1',
            'cargar_dependencias' => 'required|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            // $productos = $factura->obtenerProductosSalida($request);
            $factura = $factura->obtenerFactura($request);

            if (!empty($factura)) {


                if ($request->cargar_dependencias) {
                    $ajustes_basicos = Ajustes::whereIn('id_ajuste', array(4, 5, 26))->orderBy('id_ajuste')->select('id_ajuste', 'valor')->get();

                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'factura' => $factura,
                            'ajustes_basicos' => $ajustes_basicos,
                        ],
                        'messages' => null
                    ]);
                } else {
                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'factura' => $factura,
                        ],
                        'messages' => null
                    ]);
                }


            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_factura' => ["Datos no encontrados"]),
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

    public function registrarFacturaConsignacion(Request $request)
    {
        $messages = [
            'detalleProductos.min' => 'Se requiere agregar un producto por lo menos.',
            'detalleProductos.*.productox.id_producto.required' => 'Seleccione un producto válido',
            'detalleProductos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'detalleProductos.*.cantidad.min' => 'La cantidad debe ser mayor que cero',
            'doc_exoneracion.required_if' => 'El campo documento exoneracion es requerido cuando la factura no aplica IVA',
            'doc_exoneracion_ir.required_if' => 'El campo documento exoneracion es requerido cuando la factura aplica Retención IR',
            'doc_exoneracion_imi.required_if' => 'El campo documento exoneracion es requerido cuando la factura aplica Retención IMI',
            'factura_cliente.required' => 'El campo cliente es requerido.',
            'factura_vendedor.required' => 'El campo vendedor es requerido',
        ];

        $rules = [
            'f_factura' => 'required|date',
            'id_tipo' => 'required|integer|min:1|max:2',
            'tipo_identificacion' => 'required|integer|min:1|max:2',
            'identificacion' => 'required|string|max:18',
            'observacion' => 'nullable|string|max:100',

            'aplicaIVA' => 'required|boolean',
            'aplicaIR' => 'required|boolean',
            'aplicaIMI' => 'required|boolean',

            'factura_cliente' => 'required|array|min:1',
            'factura_cliente.id_cliente' => 'required|integer|min:1',

            'factura_vendedor' => 'required|array|min:1',
            'factura_vendedor.id_vendedor' => 'required|integer|min:1',
            't_cambio' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',

            'doc_exoneracion' => 'required_if:aplicaIVA,false|string|max:20|nullable',
            'doc_exoneracion_ir' => 'required_if:aplicaIR,true|string|max:20|nullable',
            'doc_exoneracion_imi' => 'required_if:aplicaIMI,true|string|max:20|nullable',

            'mt_retencion' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_retencion_imi' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_impuesto' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_descuento' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_ajuste' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',

            'pago_vuelto_mn' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'pago_vuelto' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',

            'detallePago' => 'required_if:id_tipo,1|array|nullable',
            'detallePago.*.via_pagox.id_via_pago' => 'required|integer|min:1',
            'detallePago.*.moneda_x.id_moneda' => 'required|integer|min:1',
            'detallePago.*.monto_me' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'detallePago.*.monto' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'detallePago.*.banco_x.id_banco' => 'required_if:detallePago.*.via_pagox.id_via_pago,3|required_if:detallePago.*.via_pagox.id_via_pago,5|required_if:detallePago.*.via_pagox.id_via_pago,6|integer|min:1|nullable',
            'detallePago.*.numero_minuta' => 'required_if:detallePago.*.via_pagox.id_via_pago,1required_if:detallePago.*.via_pagox.id_via_pago,3||string|max:30|nullable',
            'detallePago.*.numero_nota_credito' => 'required_if:detallePago.*.via_pagox.id_via_pago,4|string|max:30|nullable',
            'detallePago.*.numero_cheque' => 'required_if:detallePago.*.via_pagox.id_via_pago,5|string|max:30|nullable',
            'detallePago.*.numero_transferencia' => 'required_if:detallePago.*.via_pagox.id_via_pago,6|string|max:30|nullable',
            'detallePago.*.numero_recibo_pago' => 'required_if:detallePago.*.via_pagox.id_via_pago,7|string|max:30|nullable',

            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.productox.id_bodega_producto' => 'required|integer|min:0',
            'detalleProductos.*.precio_costo' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.precio_lista' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.precio' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.cantidad' => 'required|integer|min:1',
            'detalleProductos.*.productox.codigo_sistema' => 'required|string|max:50',
            'detalleProductos.*.productox.text' => 'required|string|max:100',
            'detalleProductos.*.productox.unidad_medida' => 'required|string|max:100',
            'detalleProductos.*.afectacionx.id_afectacion' => 'required|integer|exists:pgsql.cjabnco.facturas_afectaciones,id_afectacion',
            'detalleProductos.*.afectacionx.valor' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();
                $factura = new Facturas;

                $bodegaConsignacion = Bodegas::find(17);
                $sucursalConsignacion = Sucursales::find($bodegaConsignacion->id_sucursal);

                /*$nuevo_num = CajaBancoFacturas::select([DB::raw("COALESCE(max(no_factura),0)+1 as no_factura")])->where('serie',$sucursalConsignacion->serie)->first();
                $factura->no_factura = $nuevo_num['no_factura'];*/

                $sucursalx = PublicSucursales::find($sucursalConsignacion->id_sucursal);
                //$nuevo_num = CajaBancoFacturas::select([DB::raw("COALESCE(max(no_factura),0)+1 as no_factura")])->where('serie',$request->serie)->first();
                $factura->no_factura = $sucursalx['numeracion_facturacion'] + 1;

                $str_length = 8;
                $str = substr("0000000" . $factura->no_factura, -$str_length);
                //$factura->no_factura=$str;

                $factura->no_documento = $sucursalConsignacion->serie . '-' . $str;
                $factura->f_factura = date("Y-m-d H:i:s");
                $factura->serie = $sucursalConsignacion->serie;
                $factura->id_moneda = 1;
                $factura->observacion = $request->observacion;
                $factura->id_tipo = $request->id_tipo;
                $factura->id_sucursal = $sucursalConsignacion->id_sucursal;
                $factura->id_bodega = $bodegaConsignacion->id_bodega;
                $factura->id_cliente = $request->factura_cliente['id_cliente'];
                $factura->tipo_identificacion = $request->tipo_identificacion;
                $factura->identificacion = $request->identificacion;
                $factura->id_vendedor = $request->factura_vendedor['id_vendedor'];
                $factura->t_cambio = $request->t_cambio;
                $factura->doc_exoneracion = $request->doc_exoneracion;
                $factura->doc_exoneracion_ir = $request->doc_exoneracion_ir;
                $factura->doc_exoneracion_imi = $request->doc_exoneracion_imi;
                $factura->impuesto_exonerado = !$request->aplicaIVA;

                $factura->mt_retencion = round($request->mt_retencion, 2);
                $factura->mt_retencion_imi = round($request->mt_retencion_imi, 2);
                $factura->mt_impuesto = round($request->mt_impuesto, 2);
                $factura->mt_descuento = round($request->mt_descuento, 2);
                $factura->mt_ajuste = round($request->mt_ajuste, 2);
                $factura->mt_total = $request->total_final_cordobas;

                $factura->mt_deuda = $request->pago_pendiente_mn;
                $factura->pago_vuelto = $request->pago_vuelto_mn;
                $factura->pago_vuelto_me = $request->pago_vuelto;

                $factura->saldo_factura = $request->pago_pendiente_mn;
                $factura->dias_credito = $request->dias_credito;
                $factura->f_vencimiento = date('Y-m-d', strtotime($factura->f_factura . ' + ' . $request->dias_credito . ' days'));
                $factura->u_creacion = Auth::user()->usuario;
                $factura->estado = 1;

                $salida = new InventarioSalidas;
                $salida->codigo_salida = InventarioSalidas::max('id_salida') + 1;
                $salida->id_tipo_salida = 1;
                $salida->id_cliente = $factura->id_cliente;
                $salida->fecha_salida = $factura->f_factura;
                $salida->id_bodega = $factura->id_bodega;
                $salida->descripcion_salida = $request->factura_cliente['nombre_comercial'] . ' Fact. No.' . $factura->no_documento;
                $salida->u_creacion = $factura->u_creacion;
                $salida->estado = 1;
                $salida->save();

                $factura->id_salida = $salida->id_salida;
                $factura->save();

                PublicSucursales::find($factura->id_sucursal)->increment('numeracion_facturacion');

                if ($factura->id_tipo == 2 && round($factura->mt_deuda, 2) > 0) {
                    $cuentas_x_cobrar = new CuentasXCobrarCuentasXCobrar;
                    $cuentas_x_cobrar->id_cliente = $factura->id_cliente;
                    $cuentas_x_cobrar->id_tipo_documento = 1;
                    $cuentas_x_cobrar->no_documento_origen = $factura->no_documento;
                    $cuentas_x_cobrar->es_registro_importado = false;

                    $cuentas_x_cobrar->identificador_origen = $factura->id_factura;
                    $cuentas_x_cobrar->fecha_movimiento = date("Y-m-d H:i:s");//$factura->f_factura;
                    $cuentas_x_cobrar->monto_movimiento = $factura->mt_deuda;
                    $cuentas_x_cobrar->saldo_actual = $factura->mt_deuda;
                    $cuentas_x_cobrar->fecha_vencimiento = $factura->f_vencimiento;
                    $cuentas_x_cobrar->descripcion_movimiento = 'Registro del Monto de la Factura ' . $factura->no_documento;
                    $cuentas_x_cobrar->usuario_registra = $factura->u_creacion;
                    $cuentas_x_cobrar->estado = 1;
                    $cuentas_x_cobrar->save();
                }

                $monto_cordobas = 0;
                $monto_dolares = 0;

                foreach ($request->detallePago as $pago) {
                    $factura_pago = new CajaBancoFacturaViaPagos;
                    $factura_pago->id_factura = $factura->id_factura;
                    $factura_pago->id_via_pago = $pago['via_pagox']['id_via_pago'];
                    $factura_pago->id_moneda = $pago['moneda_x']['id_moneda'];
                    $factura_pago->monto_me = $pago['monto_me'];
                    $factura_pago->monto = $pago['monto'];
                    if ($factura_pago->id_via_pago == 1 || $factura_pago->id_via_pago == 3 || $factura_pago->id_via_pago == 5 || $factura_pago->id_via_pago == 6) {
                        $factura_pago->id_banco = $pago['banco_x']['id_banco'];
                    }
                    $factura_pago->numero_minuta = $pago['numero_minuta'];
                    $factura_pago->numero_nota_credito = $pago['numero_nota_credito'];
                    $factura_pago->numero_cheque = $pago['numero_cheque'];
                    $factura_pago->numero_transferencia = $pago['numero_transferencia'];
                    $factura_pago->numero_recibo_pago = $pago['numero_recibo_pago'];

                    //if($factura_pago->id_moneda==1){
                    $monto_cordobas = $monto_cordobas + $factura_pago->monto;
                    //}else{
                    //    $monto_dolares = $monto_dolares + $factura_pago->monto_me;
                    //}

                    $factura_pago->save();
                }

                $total_venta_producto = 0; //35.8072
                $total_venta_servicios = 0;
                $total_costo_producto = 0;
                $total_costo_servicios = 0;
                $i = 0;
                foreach ($request->detalleProductos as $producto) {

                    $factura_producto = new CajaBancoFacturaProductos;

                    if ($producto['productox']['id_bodega_producto'] > 0 && $producto['productox']['id_tipo_producto'] != 2) {

                        $bodega_sub = InventarioBodegaProductos::where('id_bodega_producto', $producto['productox']['id_bodega_producto'])->first();
                        if (($bodega_sub->cantidad - $producto['cantidad']) >= 0) {
                            $bodega_sub->cantidad = $bodega_sub->cantidad - $producto['cantidad'];
                        } else {
                            //$producto['cantidad'] = $bodega_sub->cantidad;
                            //$bodega_sub->cantidad = 0;
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'result' => array('detalleProductos.' . $i . '.cantidad' => ['La cantidad solicitada para este producto no esta disponible']),
                                'messages' => null
                            ]);
                        }
                        $bodega_sub->save();
                        $factura_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                        $factura_producto->id_producto = $bodega_sub->id_producto;

                    } else {
                        $factura_producto->id_bodega_producto = null;
                        $factura_producto->id_producto = $producto['productox']['id_producto'];
                    }

                    $factura_producto->id_factura = $factura->id_factura;
                    $factura_producto->descripcion_producto = $producto['productox']['text'];
                    $factura_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                    $factura_producto->unidad_medida = $producto['productox']['unidad_medida'];

                    $factura_producto->cantidad = $producto['cantidad'];
                    $factura_producto->precio_costo = $producto['precio_costo'];
                    $factura_producto->precio_lista = round($producto['precio_lista'], 2);
                    $factura_producto->precio = round($producto['p_unitario'], 2);

                    $factura_producto->p_descuento = $producto['p_descuento'];
                    $factura_producto->p_ajuste = $producto['p_ajuste'];
                    $factura_producto->p_impuesto = $producto['p_impuesto'];

                    $factura_producto->m_impuesto = round($producto['mt_impuesto'], 2);
                    $factura_producto->m_descuento = round($producto['mt_descuento'], 2);
                    $factura_producto->m_ajuste = round($producto['mt_ajuste'], 2);

                    $factura_producto->id_afectacion = $producto['afectacionx']['id_afectacion'];

                    $factura_producto->f_creacion = date("Y-m-d H:i:s");
                    $factura_producto->save();


                    $consignacion_producto = new InventarioConsignacionProductos();
                    $consignacion_producto->id_bodega_producto = $factura_producto->id_bodega_producto;
                    $consignacion_producto->id_producto = $factura_producto->id_producto;
                    $consignacion_producto->id_cliente = $factura->id_cliente;
                    $consignacion_producto->fecha_movimiento = date("Y-m-d H:i:s");//$factura->f_factura;// date("Y-m-d H:i:s");
                    $consignacion_producto->descripcion_movimiento = $salida->descripcion_salida;
                    $consignacion_producto->identificador_origen_movimiento = $salida->id_salida;
                    $consignacion_producto->tipo_movimiento = 2;//1 consignacion 2 venta 3 devolucion
                    $consignacion_producto->cantidad_movimiento = $factura_producto->cantidad * -1;
                    $consignacion_producto->usuario_registra = Auth::user()->usuario;
                    $consignacion_producto->save();


                    if ($producto['productox']['id_tipo_producto'] != 2) {
                        $productoExiste = InventarioSalidaProductos::where('id_bodega_producto', $bodega_sub->id_bodega_producto)
                            ->where('id_salida', $salida->id_salida)->first();
                        if (!$productoExiste) {
                            $salida_producto = new InventarioSalidaProductos;
                            $salida_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                            $salida_producto->id_salida = $salida->id_salida;
                            $salida_producto->descripcion_producto = $producto['productox']['text'];
                            $salida_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                            $salida_producto->unidad_medida = $producto['productox']['unidad_medida'];
                            $salida_producto->precio_unitario = $producto['precio_costo'];
                            $salida_producto->cantidad_saliente = $producto['cantidad'];
                            $salida_producto->cantidad_despachada = 0;
                            $salida_producto->cantidad_faltante = 0;
                            $salida_producto->u_creacion = $salida->u_creacion;
                            $salida_producto->save();
                        } else {
                            $productoExiste->cantidad_saliente = $productoExiste->cantidad_saliente + $producto['cantidad'];
                            $productoExiste->save();
                        }

                        $total_venta_producto = $total_venta_producto + round($factura_producto->precio_lista * $factura_producto->cantidad, 2);
                        $total_costo_producto = $total_costo_producto + round($factura_producto->precio_costo * $factura_producto->cantidad, 6);
                    } else {
                        $total_venta_servicios = $total_venta_servicios + round($factura_producto->precio_lista * $factura_producto->cantidad, 2);
                        $total_costo_servicios = $total_costo_servicios + round($factura_producto->precio_costo * $factura_producto->cantidad, 6);
                    }


                    $i++;

                }


                /*INICIA movimiento contable - Factura*/

                $clientex = VentaClientes::select('id_cliente', 'id_zona')->find($factura->id_cliente);
                $zonax = PublicZonas::select('id_zona', 'id_centro_costo', 'id_centro_ingreso')->find($clientex->id_zona);
                $tipos_cuentas = array(4, 5, 6);

                $documento = new ContabilidadDocumentosContables;
                $tipo = ContabilidadTiposDocumentos::find(7);
                $fecha = date("Y-m-d H:i:s");
                $codigo = $documento->obtenerCodigoDocumento(array('id_tipo_doc' => 7, 'fecha_doc' => $fecha));

                $nuevo_codigo = json_decode($codigo[0]);

                date_default_timezone_set('America/Managua');

                $documento->num_documento = $tipo->prefijo . '-' . $nuevo_codigo->secuencia;
                $documento->fecha_emision = date('Y-m-d');
                $documento->codigo_documento = $nuevo_codigo->secuencia;


                $date = DateTime::createFromFormat("Y-m-d", $documento->fecha_emision);

                $periodo = ContabilidadPeriodoFiscal::where('periodo', $date->format("Y"))->get();

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

                $periodo_mes = ContabilidadPeriodoMeses::where('id_periodo_fiscal', $periodo[0]->id_periodo_fiscal)->where('mes', $date->format("n"))->get();

                if (empty($periodo_mes[0])) {
                    return response()->json([
                        'status' => 'error',
                        'result' => array('fecha_emision' => ["El mes " . $date->format("F") . " no se encuentra registrado, por favor consulte al administrador"]),
                        'messages' => null
                    ]);
                    exit;
                }

                if ($periodo_mes[0]->estado == 2) {
                    return response()->json([
                        'status' => 'error',
                        'result' => array('fecha_emision' => ["El mes " . config('global.meses')[$periodo_mes[0]->mes - 1] . " es inválido, ya que se encuentra en estado COMPLETADO"]),
                        'messages' => null
                    ]);
                    exit;
                }

                $documento->id_periodo_fiscal = $periodo[0]->id_periodo_fiscal;

                $documento->id_tipo_doc = 7;
                $documento->valor = $factura->mt_total;
                $documento->concepto = 'Registramos venta por factura No. ' . $factura->no_documento . '. Monto total C$ ' . $factura->mt_total;
                $documento->id_moneda = 1;
                $documento->u_creacion = Auth::user()->usuario;
                $documento->estado = 1;

                $documento->save();
                $factura->id_documento_contable = $documento->id_documento;
                $factura->save();

                ContabilidadTiposDocumentos::find($documento->id_tipo_doc)->increment('secuencia');


                /*INICIA MOVIMIENTO CONTABLE*/
                /*///////////////////////////VENTA CONSIGNACION ////////////////////////////////////////*/
                /*///////////////////////////ID_TIPO_CONFIGURACION = 9 ////////////////////////////////////////*/
                /*///////////////////////////SECCION 1 - CUENTAS POR COBRAR////////////////////////////////////////*/

                //Definición de parametros
                $nombre_seccion_cuentas_cobrar = 'CuentasCobrar';
                $id_tipo_configuracion = 9;
                //Obtener datos de BD con parametros
                $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_cuentas_cobrar)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                $documento_cuenta_contableS2a = new DocumentosCuentas;
                $documento_cuenta_contableS2a->id_documento = $documento->id_documento;
                $documento_cuenta_contableS2a->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                $documento_cuenta_contableS2a->debe = $request->mt_subtotal_sin_iva + $request->mt_impuesto; /*-$factura->mt_descuento*/
//                $documento_cuenta_contableS2a->debe = $factura->mt_deuda - $factura->mt_retencion - $factura->mt_retencion_imi; /*-$factura->mt_descuento*/

                $documento_cuenta_contableS2a->haber = 0;
                $documento_cuenta_contableS2a->debe_org = $request->mt_subtotal_sin_iva + $request->mt_impuesto; /*-$factura->mt_descuento*/
//                $documento_cuenta_contableS2a->debe_org = $factura->mt_deuda - $factura->mt_retencion - $factura->mt_retencion_imi; /*-$factura->mt_descuento*/
                $documento_cuenta_contableS2a->haber_org = 0;
                $documento_cuenta_contableS2a->id_moneda = 1;
                if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                    if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                        if ($documento_cuenta_contableS2a->haber > 0) {
                            $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_ingreso;
                        } else {
                            $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_costo;
                        }
                    } else {
                        if ($documento_cuenta_contableS2a->debe > 0) {
                            $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_costo;
                        } else {
                            $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_ingreso;
                        }
                    }
                } else {
                    $documento_cuenta_contableS2a->id_centro = null;
                }

                $documento_cuenta_contableS2a->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                $documento_cuenta_contableS2a->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                $documento_cuenta_contableS2a->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                $documento_cuenta_contableS2a->save();


                /*///////////////////////////FIN SECCION 1 - CUENTAS POR COBRAR////////////////////////////////////////*/

                /*///////////////////////////SECCION 2 - COMERCIALIZACION////////////////////////////////////////*/
                if ($factura->mt_descuento > 0) { //Si el monto de descuento en factura es mayor a 0

                    $nombre_seccion_descuentos = 'PorComercializacion';
                    $id_tipo_configuracion = 9;

                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                    $documento_cuenta_contableS2 = new DocumentosCuentas;
                    $documento_cuenta_contableS2->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS2->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS2->debe = $factura->mt_descuento;
                    $documento_cuenta_contableS2->haber = 0;
                    $documento_cuenta_contableS2->debe_org = $factura->mt_descuento;
                    $documento_cuenta_contableS2->haber_org = 0;
                    $documento_cuenta_contableS2->id_moneda = 1;
                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS2->haber > 0) {
                                $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS2->debe > 0) {
                                $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS2->id_centro = null;
                    }

                    $documento_cuenta_contableS2->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS2->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS2->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS2->save();
                }
                /*///////////////////////////FIN SECCION 2 - COMERCIALIZACION ////////////////////////////////////////*/


                /*///////////////////////////SECCION 4 - IR////////////////////////////////////////*/


                if ($request->aplicaIR && $factura->mt_retencion > 0) { //Si factura aplica IR y monto retención mayor a 0

                    $nombre_seccion_retencion = 'ImpuestoRentaAnual';
                    $id_tipo_configuracion = 9;

                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                    $documento_cuenta_contableS4 = new DocumentosCuentas;
                    $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS4->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS4->debe = $factura->mt_retencion;
                    $documento_cuenta_contableS4->haber = 0;
                    $documento_cuenta_contableS4->debe_org = $factura->mt_retencion;
                    $documento_cuenta_contableS4->haber_org = 0;
                    $documento_cuenta_contableS4->id_moneda = 1;
                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS4->haber > 0) {
                                $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS4->debe > 0) {
                                $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS4->id_centro = null;
                    }
                    $documento_cuenta_contableS4->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;

                    $documento_cuenta_contableS4->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS4->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS4->save();
                }

                /*///////////////////////////FIN SECCION 4 - IR ////////////////////////////////////////*/

                /*///////////////////////////SECCION 5 - COSTO VENTA////////////////////////////////////////*/


                if ($total_costo_producto > 0) { //Si el total de costo de productos es mayor a 0

                    $nombre_seccion_costo_prod = 'CostoVentaArti';
                    $id_tipo_configuracion = 9;

                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_costo_prod)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                    $documento_cuenta_contableS5 = new DocumentosCuentas;
                    $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS5->debe = $total_costo_producto;
                    $documento_cuenta_contableS5->haber = 0;
                    $documento_cuenta_contableS5->debe_org = $total_costo_producto;
                    $documento_cuenta_contableS5->haber_org = 0;
                    $documento_cuenta_contableS5->id_moneda = 1;

                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS5->haber > 0) {
                                $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS5->debe > 0) {
                                $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS5->id_centro = null;
                    }

                    $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                    $documento_cuenta_contableS5->save();

                }

                /*///////////////////////////FIN SECCION 5 - COSTO VENTA ////////////////////////////////////////*/

                /*///////////////////////////SECCION 6 - IMPUESTO SOBREVENTA//////////////////////////////////////////////*/

                if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) {
                    $total_ventax = $total_venta_producto + $total_venta_servicios;

                    $nombre_seccion_imp_venta = 'ImpuestoSobreVenta';
                    $id_tipo_configuracion = 9;

                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_imp_venta)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                    $documento_cuenta_contableS6 = new DocumentosCuentas;
                    $documento_cuenta_contableS6->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS6->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS6->debe = $factura->mt_retencion_imi;
                    $documento_cuenta_contableS6->haber = 0;
                    $documento_cuenta_contableS6->debe_org = $factura->mt_retencion_imi;
                    $documento_cuenta_contableS6->haber_org = 0;
                    $documento_cuenta_contableS6->id_moneda = 1;

                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS6->haber > 0) {
                                $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS6->debe > 0) {
                                $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS6->id_centro = null;
                    }

                    $documento_cuenta_contableS6->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS6->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS6->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                    $documento_cuenta_contableS6->save();
                }

                /*///////////////////////////FIN SECCION 6 - IMPUESTO SOBRE VENTA ////////////////////////////////////////*/

                /*///////////////////////////SSECCION 7 - D. GENERAL INGRESOS/////////////////////////////////////////////*/

                if ($request->aplicaIR && $factura->mt_retencion > 0) {
                    $nombre_seccion_pago_minimo_dgi = 'DireccionGeneralIngreso';
                    $id_tipo_configuracion = 9;

                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_pago_minimo_dgi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                    $documento_cuenta_contableS7 = new DocumentosCuentas;
                    $documento_cuenta_contableS7->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS7->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS7->debe = 0;
                    $documento_cuenta_contableS7->haber = $factura->mt_retencion;
                    $documento_cuenta_contableS7->debe_org = 0;
                    $documento_cuenta_contableS7->haber_org = $factura->mt_retencion;
                    $documento_cuenta_contableS7->id_moneda = 1;

                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS7->haber > 0) {
                                $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS7->debe > 0) {
                                $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS7->id_centro = null;
                    }

                    $documento_cuenta_contableS7->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS7->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS7->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                    $documento_cuenta_contableS7->save();
                }

                /*///////////////////////////FIN SECCION 7 - D. GENERAL INGRESOS//////////////////////////////////////////*/


                /*///////////////////////////SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/
                if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) { //Si factura aplica IMI y monto retención imi mayor a 0

                    $nombre_seccion_retencion_imi = 'AlcaldiasMunicipales';
                    $id_tipo_configuracion = 9;

                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion_imi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                    $documento_cuenta_contableS8 = new DocumentosCuentas;
                    $documento_cuenta_contableS8->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS8->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS8->debe = 0;
                    $documento_cuenta_contableS8->haber = $factura->mt_retencion_imi;
                    $documento_cuenta_contableS8->debe_org = 0;
                    $documento_cuenta_contableS8->haber_org = $factura->mt_retencion_imi;;
                    $documento_cuenta_contableS8->id_moneda = 1;
                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS8->haber > 0) {
                                $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS8->debe > 0) {
                                $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS8->id_centro = null;
                    }
                    $documento_cuenta_contableS8->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS8->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS8->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS8->save();
                }
                /*///////////////////////////FIN SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/

                /*///////////////////////////SECCION 9 - IVA//////////////////////////////////////////*/
                if (!$factura->impuesto_exonerado && $factura->mt_impuesto > 0) { // si aplica a impuesto exonerado y monto impuesto mayor a 0

                    $nombre_seccion_iva = 'DireccionGeneralIngresoH';
                    $id_tipo_configuracion = 9;

                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_iva)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                    $documento_cuenta_contableS9 = new DocumentosCuentas;
                    $documento_cuenta_contableS9->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS9->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS9->debe = 0;
                    $documento_cuenta_contableS9->haber = $factura->mt_impuesto;
                    $documento_cuenta_contableS9->debe_org = 0;
                    $documento_cuenta_contableS9->haber_org = $factura->mt_impuesto;
                    $documento_cuenta_contableS9->id_moneda = 1;

                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS9->haber > 0) {
                                $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS9->debe > 0) {
                                $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS9->id_centro = null;
                    }

                    $documento_cuenta_contableS9->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS9->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS9->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS9->save();
                }
                /*///////////////////////////FIN SECCION 9 - IVA//////////////////////////////////////////*/

                /*///////////////////////////SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/
                if ($total_costo_producto > 0) { //So el total de costo de productos es mayor a 0

                    $nombre_seccion_inventario = 'ArticuloBodegaCosto';
                    $id_tipo_configuracion = 9;


                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_inventario)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                    $documento_cuenta_contableS10 = new DocumentosCuentas;
                    $documento_cuenta_contableS10->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS10->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS10->debe = 0;
                    $documento_cuenta_contableS10->haber = $total_costo_producto;
                    $documento_cuenta_contableS10->debe_org = 0;
                    $documento_cuenta_contableS10->haber_org = $total_costo_producto;
                    $documento_cuenta_contableS10->id_moneda = 1;
                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS10->haber > 0) {
                                $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS10->debe > 0) {
                                $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS10->id_centro = null;
                    }

                    $documento_cuenta_contableS10->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS10->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS10->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                    $documento_cuenta_contableS10->save();
                }
                /*///////////////////////////FIN SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/


                /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/
                if ($total_venta_producto > 0) { //Si total venta productos es mayor a 0

                    $nombre_seccion_ingreso_productos = 'Nacionales';
                    $id_tipo_configuracion = 9;

                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_ingreso_productos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                    $documento_cuenta_contableS11 = new DocumentosCuentas;
                    $documento_cuenta_contableS11->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS11->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS11->debe = 0;
                    $documento_cuenta_contableS11->haber = $total_venta_producto;
                    $documento_cuenta_contableS11->debe_org = 0;
                    $documento_cuenta_contableS11->haber_org = $total_venta_producto;
                    $documento_cuenta_contableS11->id_moneda = 1;

                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS11->haber > 0) {
                                $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS11->debe > 0) {
                                $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS11->id_centro = null;
                    }
                    $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS11->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS11->save();

                    /*///////////////////////////SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
                    $nombre_seccion_descuentos = 'PorBonificacion';
                    $id_tipo_configuracion = 9;

                    $cuentaSeccionAjuste = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                    foreach ($request->detalleProductos as $producto) {
                        if ($producto['afectacionx']['id_afectacion'] > 1 && $producto['mt_ajuste'] > 0) { // Si el producto tiene algún tipo de afectación y el monto de ajuste es mayor a 0

                            $documento_cuenta_contableS11 = new DocumentosCuentas;
                            $documento_cuenta_contableS11->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS11->concepto = $cuentaSeccionAjuste->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS11->debe = $producto['mt_ajuste'];
                            $documento_cuenta_contableS11->haber = 0;
                            $documento_cuenta_contableS11->debe_org = $producto['mt_ajuste'];
                            $documento_cuenta_contableS11->haber_org = 0;
                            $documento_cuenta_contableS11->id_moneda = 1;
                            $documento_cuenta_contableS11->id_centro = 1;///cambiar centro de costo ingreso

                            /* revisar */
                            if (in_array($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                                if ($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS11->haber > 0) {
                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS11->debe > 0) {
                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS11->id_centro = null;
                            }

                            $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccionAjuste->id_cuenta_contable;
                            $documento_cuenta_contableS11->cta_contable = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS11->save();
                            /*///////////////////////////FIN SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
                        }
                    }

                }
                /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/

                /*///////////////////////////FIN VENTA CONSIGNACION ////////////////////////////////////////*/
                /*///////////////////////////ID_TIPO_CONFIGURACION = 9 ////////////////////////////////////////*/
                /* TERMINA MOVIMIENTO CONTABLE*/

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'result' => '',
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


    /*
     * Method: register invoice
     * Auth: omorales
     * date modified: 15/03/22
     * */
    public function registrar(Request $request)
    {
        $messages = [
            'detalleProductos.min' => 'Se requiere agregar un producto por lo menos.',
            'detalleProductos.*.productox.id_producto.required' => 'Seleccione un producto válido',
            'detalleProductos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'detalleProductos.*.cantidad.min' => 'La cantidad debe ser mayor que cero',
            'doc_exoneracion.required_if' => 'El campo documento exoneracion es requerido cuando la factura no aplica IVA',
            'doc_exoneracion_ir.required_if' => 'El campo documento exoneracion es requerido cuando la factura aplica Retención IR',
            'doc_exoneracion_imi.required_if' => 'El campo documento exoneracion es requerido cuando la factura aplica Retención IMI',
            'factura_cliente.required' => 'El campo cliente es requerido.',
            'factura_sucursal.required' => 'El campo sucursal es requerido.',
            'factura_bodega.required' => 'El campo bodega es requerido.',
            'factura_vendedor.required' => 'El campo vendedor es requerido',
            'proformasBusqueda.required_if' => 'El campo es requerido',

        ];

        $rules = [
            // 'no_documento' => 'required|string|max:8',
            'f_factura' => 'required|date',
            'serie' => 'required|string|max:2',
            // 'factura_moneda' => 'required|array|min:1',
            // 'factura_moneda.id_moneda' => 'required|integer|min:1',
            'id_tipo' => 'required|integer|min:1|max:4',
            'tipo_venta' => 'required|integer|min:1|max:4',
            'factura_sucursal' => 'required_if:es_nuevo,true|array|min:1',
            'factura_sucursal.id_sucursal' => 'required_if:es_nuevo,true|integer|min:1',
            'factura_bodega' => 'required_if:es_nuevo,true|array|min:1',
            'factura_bodega.id_bodega' => 'required_if:es_nuevo,true|integer|min:1',

            /*'proforma_sucursal' => 'required_if:es_nuevo,false|array|min:1',
            'proforma_sucursal.id_sucursal' => 'required_if:es_nuevo,false|integer|min:1',
            'proforma_bodega' => 'required_if:es_nuevo,false|array|min:1',
            'proforma_bodega.id_bodega' => 'required_if:es_nuevo,false|integer|min:1',*/

            'tipo_identificacion' => 'required|integer|min:1|max:2',
            'identificacion' => 'required|string|max:18',
            'observacion' => 'nullable|string|max:100',

            'aplicaIVA' => 'required|boolean',
            'aplicaIR' => 'required|boolean',
            'aplicaIMI' => 'required|boolean',

            'factura_cliente' => 'required_if:es_nuevo,==,true|array|min:1',
            'factura_cliente.id_cliente' => 'required_if:es_nuevo,==,true|integer|min:1',

            /*'proforma_cliente' => 'required_if:es_nuevo,false|array|min:1',
            'proforma_cliente.id_cliente' => 'required_if:es_nuevo,false|integer|min:1',*/


            'proforma_especifica' => 'required|boolean',
            'proformasBusqueda' => 'required_if:proforma_especifica,true|array|min:0|nullable',
            'proformasBusqueda.id_proforma' => 'required_if:proforma_especifica:true,false|integer|min:1|nullable',

            'factura_vendedor' => 'required_if:es_nuevo,!=,false|array|min:1',
            'factura_vendedor.id_vendedor' => 'required_if:es_nuevo,!=,false|integer|min:1',

            /*'proforma_vendedor' => 'required_if:es_nuevo,false|array|min:1',
            'proforma_vendedor.id_vendedor' => 'required_if:es_nuevo,false|integer|min:1',*/

            't_cambio' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',

            'doc_exoneracion' => 'required_if:aplicaIVA,false|string|max:20|nullable',
            'doc_exoneracion_ir' => 'required_if:aplicaIR,true|string|max:20|nullable',
            'doc_exoneracion_imi' => 'required_if:aplicaIMI,true|string|max:20|nullable',

            'mt_retencion' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_retencion_imi' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_impuesto' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_descuento' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_ajuste' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',

            'pago_vuelto_mn' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'pago_vuelto' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',

            'detallePago' => 'required_if:currrency_id,1|array|nullable',
            'detallePago.*.via_pagox.id_via_pago' => 'required|integer|min:1',
            'detallePago.*.moneda_x.id_moneda' => 'required|integer|min:1',
            'detallePago.*.monto_me' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'detallePago.*.monto' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'detallePago.*.banco_x.id_banco' => 'required_if:detallePago.*.via_pagox.id_via_pago,3|required_if:detallePago.*.via_pagox.id_via_pago,5|required_if:detallePago.*.via_pagox.id_via_pago,6|integer|min:1|nullable',
            'detallePago.*.numero_minuta' => 'required_if:detallePago.*.via_pagox.id_via_pago,1|required_if:detallePago.*.via_pagox.id_via_pago,3|string|max:30|nullable',
            'detallePago.*.numero_nota_credito' => 'required_if:detallePago.*.via_pagox.id_via_pago,4|string|max:30|nullable',
            'detallePago.*.numero_cheque' => 'required_if:detallePago.*.via_pagox.id_via_pago,5|string|max:30|nullable',
            'detallePago.*.numero_transferencia' => 'required_if:detallePago.*.via_pagox.id_via_pago,6|string|max:30|nullable',
            'detallePago.*.numero_recibo_pago' => 'required_if:detallePago.*.via_pagox.id_via_pago,7|string|max:30|nullable',

            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.productox.id_bodega_producto' => 'required|integer|min:0',
            'detalleProductos.*.precio_costo' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.precio_lista' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.precio' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.cantidad' => 'required|numeric|min:0.01',
            'detalleProductos.*.productox.codigo_sistema' => 'required|string|max:50',
            'detalleProductos.*.productox.descripcion' => 'required|string|max:100',
            'detalleProductos.*.productox.unidad_medida' => 'required|string|max:100',
            'detalleProductos.*.afectacionx.id_afectacion' => 'required|integer|exists:pgsql.cjabnco.facturas_afectaciones,id_afectacion',
            'detalleProductos.*.afectacionx.valor' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();

            try {

                DB::beginTransaction();

                if (!empty($request->codigo_autorizacion)) {
                    try {
                        Doorman::redeem($request->codigo_autorizacion);
                    } catch (DoormanException $e) {
//                        return back()->withErrors(['codigo_autorizacion' => $e->getMessage()]);
                        return response()->json([
                            'status' => 'error',
                            'result' => 'invalid_code',
                            'messages' => 'El código de autorización es incorrecto',
                            'code' => $e->getMessage()
                        ]);

                    }
                }
                $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first(); //
                $factura = new Facturas;
                if (!empty($request->proformasBusqueda)) { //Si la sucursal proviene de una factura nueva o una proforma
                    $sucursalx = Sucursales::find($request->proforma_sucursal['id_sucursal']);
                    $factura->id_proforma = $request->proformasBusqueda['id_proforma'];
                    //$nuevo_num = CajaBancoFacturas::select([DB::raw("COALESCE(max(no_factura),0)+1 as no_factura")])->where('serie',$request->serie)->first();
                } else {
                    $sucursalx = Sucursales::find($request->factura_sucursal['id_sucursal']);
                    //$nuevo_num = CajaBancoFacturas::select([DB::raw("COALESCE(max(no_factura),0)+1 as no_factura")])->where('serie',$request->serie)->first();
                }


//                $factura->no_factura = $sucursalx['numero_facturacion'] + 1;
                $factura->no_factura = $request->no_factura;
                $str_length = 8;
                $str = substr("0000000" . $factura->no_factura, -$str_length);
                $factura->no_documento = $request->serie . '-' . $str;

                $fact_rev = Facturas::where('no_documento', $request->serie . '-' . $str)->first();

                if (!empty($fact_rev)) {
                    if ($request->es_nuevo === true) {
                        $sucursalx2 = Sucursales::find($request->factura_sucursal['id_sucursal']);
                    } else {
                        $sucursalx2 = Sucursales::find($request->proforma_sucursal['id_sucursal']);
                    }
//                    $factura->no_factura = $sucursalx2['numero_facturacion'] + 1;
                    $factura->no_factura = $request->no_factura;
                    $str_length = 8;
                    $str = substr("0000000" . $factura->no_factura, -$str_length);
                    $factura->no_documento = $request->serie . '-' . $str;
                }

                $factura->f_factura = $request->f_factura; //date("Y-m-d H:i:s");
                $factura->serie = $request->serie;
                $factura->tipo_venta = $request->tipo_venta;
                $factura->id_moneda = $currency_id->valor;
                $factura->observacion = $request->observacion;
                if ($request->id_tipo === 3) {
                    $factura->id_tipo = 1;
                } else {
                    $factura->id_tipo = $request->id_tipo;
                }
                if ($request->es_nuevo === true) {
                    $factura->id_sucursal = $request->factura_sucursal['id_sucursal'];
                    $factura->id_bodega = $request->factura_bodega['id_bodega'];
                    $factura->id_cliente = $request->factura_cliente['id_cliente'];
                    $factura->id_vendedor = $request->factura_vendedor['id_vendedor'];
                } else {
                    $factura->id_sucursal = $request->proforma_sucursal['id_sucursal'];
                    $factura->id_bodega = $request->proforma_bodega['id_bodega'];
                    $factura->id_cliente = $request->proforma_cliente['id_cliente'];
                    $factura->id_vendedor = $request->proforma_vendedor['id_vendedor'];
                }

                $factura->tipo_identificacion = $request->tipo_identificacion;
                $factura->identificacion = $request->identificacion;
                $factura->t_cambio = $request->t_cambio;
                $factura->doc_exoneracion = $request->doc_exoneracion;
                $factura->doc_exoneracion_ir = $request->doc_exoneracion_ir;
                $factura->doc_exoneracion_imi = $request->doc_exoneracion_imi;
                $factura->impuesto_exonerado = !$request->aplicaIVA;

                $factura->mt_retencion = round($request->mt_retencion, 2);
                $factura->mt_retencion_imi = round($request->mt_retencion_imi, 2);
                $factura->mt_impuesto = round($request->mt_impuesto, 2);
                $factura->mt_descuento = round($request->mt_descuento, 2);
                $factura->mt_ajuste = round($request->mt_ajuste, 2);
                $factura->mt_total = $request->total_final_cordobas;
                $factura->mt_total_me = $request->total_final;

                $factura->mt_deuda = $request->pago_pendiente_mn;
                $factura->mt_deuda_me = $request->pago_pendiente;
                $factura->pago_vuelto = $request->pago_vuelto_mn;
                $factura->pago_vuelto_me = $request->pago_vuelto;
                if ($factura->id_tipo === 2) {
                    $factura->saldo_factura = $request->total_final_cordobas;
                    $factura->saldo_factura_me = $request->total_final;
                    /*                    $factura->saldo_factura = $request->pago_pendiente_mn; metodo de pago dentro de facturación
                                        $factura->saldo_factura_me = $request->pago_pendiente;*/
                } else {
                    $factura->saldo_factura = $request->total_final_cordobas;
                    $factura->saldo_factura_me = $request->total_final;
                }

                $factura->dias_credito = $request->dias_credito;
                if ($request->id_tipo === 3) {
                    $factura->f_vencimiento = date('Y-m-d', strtotime($factura->f_factura . ' + 8 days'));
                } else {
                    $factura->f_vencimiento = date('Y-m-d', strtotime($factura->f_factura . ' + ' . $request->dias_credito . ' days'));
                }

                $factura->u_creacion = Auth::user()->name;
                $factura->id_empresa = $usuario_empresa->id_empresa;
                $factura->estado = 1;

                $salida = new Salidas;
                $salida->codigo_salida = Salidas::max('id_salida') + 1;
                if ($factura->tipo_venta === 1) {
                    $salida->id_tipo_salida = 1;
                } elseif ($factura->tipo_venta === 3) {
                    $salida->id_tipo_salida = 13;//salida venta recuperado
                    $salida->condicion_productos = 8;//salida venta recuperado
                } elseif ($factura->tipo_venta === 4) {
                    $salida->id_tipo_salida = 14;//salida venta obsoleto
                    $salida->condicion_productos = 6;//salida venta recuperado
                }

                $salida->id_cliente = $factura->id_cliente;
                $salida->fecha_salida = $factura->f_factura;
                $salida->numero_documento = $factura->no_documento;
                $salida->id_bodega = $factura->id_bodega;
                $salida->descripcion_salida = $request->factura_cliente['nombre_comercial'] . ' Fact. No.' . $factura->no_documento;
                $salida->u_creacion = $factura->u_creacion;
                $salida->estado = 1;
                $salida->id_empresa = $usuario_empresa->id_empresa;
                $salida->save();

                $factura->id_salida = $salida->id_salida;
                $factura->save();

//                Sucursales::find($factura->id_sucursal)->increment('numero_facturacion');
                $sucursalx->numero_facturacion = $request->no_factura;
                $sucursalx->save();

                if ($request->proforma_especifica) {
                    $proforma = Proformas::find($request->proformasBusqueda['id_proforma']);

                    if (!empty($proforma)) {
                        $proforma->id_factura = $factura->id_factura;
                        $proforma->estado = 2;///proforma convertida en factura
                        $proforma->save();
                    }
                }

                if ($request->id_tipo === 2 && round($request->total_final, 2) > 0) {
                    $cuentas_x_cobrar = new CuentasXCobrar();
                    $cuentas_x_cobrar->id_cliente = $factura->id_cliente;
                    $cuentas_x_cobrar->id_tipo_documento = 1;
                    $cuentas_x_cobrar->no_documento_origen = $factura->no_documento;
                    $cuentas_x_cobrar->es_registro_importado = false;

                    $cuentas_x_cobrar->identificador_origen = $factura->id_factura;
                    $cuentas_x_cobrar->fecha_movimiento = date("Y-m-d H:i:s");//$factura->f_factura;
                    $cuentas_x_cobrar->monto_movimiento = $request->total_final_cordobas;
                    $cuentas_x_cobrar->monto_movimiento_me = $request->total_final;
                    $cuentas_x_cobrar->saldo_actual = $request->total_final_cordobas;
                    $cuentas_x_cobrar->saldo_actual_me = $request->total_final;
                    /*$cuentas_x_cobrar->monto_movimiento = $factura->mt_deuda;
                    $cuentas_x_cobrar->monto_movimiento_me = $factura->mt_deuda_me;
                    $cuentas_x_cobrar->saldo_actual = $factura->mt_deuda;
                    $cuentas_x_cobrar->saldo_actual_me = $factura->mt_deuda_me;*/
                    $cuentas_x_cobrar->fecha_vencimiento = $factura->f_vencimiento;
                    $cuentas_x_cobrar->descripcion_movimiento = 'Registro del Monto de la Factura ' . $factura->no_documento;
                    $cuentas_x_cobrar->usuario_registra = $factura->u_creacion;
                    $cuentas_x_cobrar->id_empresa = $usuario_empresa->id_empresa;
                    $cuentas_x_cobrar->estado = 1;
                    $cuentas_x_cobrar->save();
                }

                if ($request->id_tipo === 3 && round($factura->mt_deuda, 2) > 0) {//contado ficticio
                    $cuentas_x_cobrar = new CuentasXCobrar();
                    $cuentas_x_cobrar->id_cliente = $factura->id_cliente;
                    $cuentas_x_cobrar->id_tipo_documento = 1;
                    $cuentas_x_cobrar->no_documento_origen = $factura->no_documento;
                    $cuentas_x_cobrar->es_registro_importado = false;

                    $cuentas_x_cobrar->identificador_origen = $factura->id_factura;
                    $cuentas_x_cobrar->fecha_movimiento = date("Y-m-d H:i:s");//$factura->f_factura;
                    $cuentas_x_cobrar->monto_movimiento = $factura->mt_deuda;
                    $cuentas_x_cobrar->saldo_actual = $factura->mt_deuda;
                    $cuentas_x_cobrar->fecha_vencimiento = $factura->f_vencimiento;
                    $cuentas_x_cobrar->descripcion_movimiento = 'Registro del Monto de la Factura ' . $factura->no_documento;
                    $cuentas_x_cobrar->usuario_registra = $factura->u_creacion;
                    $cuentas_x_cobrar->id_empresa = $usuario_empresa->id_empresa;
                    $cuentas_x_cobrar->estado = 1;
                    $cuentas_x_cobrar->save();
                }

                if ($request->id_tipo === 4 || $request->id_tipo === '4') {
                    foreach ($request->recibos as $recibo) {
                        $recibo_update = Recibos::findOrFail($recibo['id_recibo']);
                        $recibo_update->estado = 3; //Cancelado por factura anticipo
                        $recibo_update->save();
                    }
                }

                $monto_cordobas = 0;
                $monto_dolares = 0;

                foreach ($request->detallePago as $pago) {
                    $factura_pago = new FacturaViaPagos();
                    $factura_pago->id_factura = $factura->id_factura;
                    $factura_pago->id_via_pago = $pago['via_pagox']['id_via_pago'];
                    $factura_pago->id_moneda = $pago['moneda_x']['id_moneda'];
                    $factura_pago->monto_me = $pago['monto_me'];
                    $factura_pago->monto = $pago['monto'];
                    if ($factura_pago->id_via_pago === 1 || $factura_pago->id_via_pago === 3 || $factura_pago->id_via_pago === 5 || $factura_pago->id_via_pago === 6) {
                        $factura_pago->id_banco = $pago['banco_x']['id_banco'];
                    }
                    $factura_pago->numero_minuta = $pago['numero_minuta'];
                    $factura_pago->numero_nota_credito = $pago['numero_nota_credito'];
                    $factura_pago->numero_cheque = $pago['numero_cheque'];
                    $factura_pago->numero_transferencia = $pago['numero_transferencia'];
                    $factura_pago->numero_recibo_pago = $pago['numero_recibo_pago'];

                    if ($factura_pago->id_moneda == 1) {
                        $monto_cordobas = $monto_cordobas + $factura_pago->monto;
                    } else {
                        $monto_dolares = $monto_dolares + $factura_pago->monto_me;
                    }

                    $factura_pago->id_empresa = $usuario_empresa->id_empresa;
                    $factura_pago->save();
                }

                $total_venta_producto = 0;
                $total_venta_producto_me = 0;
                $total_venta_servicios_procesamiento = 0;
                $total_venta_servicios_procesamiento_me = 0;
                $total_venta_servicios_asesoria = 0;
                $total_venta_servicios_asesoria_me = 0;
                $total_costo_producto = 0;
                $total_costo_producto_me = 0;
                $total_costo_servicios_procesamiento = 0;
                $total_costo_servicios_procesamiento_me = 0;
                $total_costo_servicios_asesoria = 0;
                $total_costo_servicios_asesoria_me = 0;
                $i = 0;
                $contador_productos = 0;
                $contador_servicios = 0;
                $servicioProcesamiento = 0;
                $servicioAsesoria = 0;
                foreach ($request->detalleProductos as $producto) {


                    /**
                     * Obtenemos los costos de origen de entrada de los productos, según IdProducto y NoLote
                     *
                     */

                    $costoORG =
                    $factura_producto = new FacturaDetalle();

                    if ($producto['productox']['id_bodega_producto'] > 0 && $producto['productox']['id_tipo_producto'] !== 2) {

                        $bodega_sub = BodegaProductos::where('id_bodega_producto', $producto['productox']['id_bodega_producto'])->first();

                        $cantidad_disponible = 0;

                        if ($factura->tipo_venta === 1) {
                            $cantidad_disponible = $bodega_sub->cantidad;
                        } elseif ($factura->tipo_venta === 3) {
                            $cantidad_disponible = $bodega_sub->cantidad_recuperadas;//salida venta recuperado
                        } elseif ($factura->tipo_venta === 4) {
                            $cantidad_disponible = $bodega_sub->cantidad_obsoletas;//salida venta obsoleto
                        }

                        /*                        if (($cantidad_disponible - $producto['cantidad']) >= 0) {

                                                    if ($factura->tipo_venta === 1) {
                                                        $bodega_sub->cantidad = $bodega_sub->cantidad - $producto['cantidad'];
                                                    } elseif ($factura->tipo_venta === 3) {
                                                        $bodega_sub->cantidad_recuperadas = $bodega_sub->cantidad_recuperadas - $producto['cantidad'];//salida venta recuperado
                                                    } elseif ($factura->tipo_venta === 4) {
                                                        $bodega_sub->cantidad_obsoletas = $bodega_sub->cantidad_obsoletas - $producto['cantidad'];//salida venta obsoleto
                                                    }

                                                } else {
                                                    //$producto['cantidad'] = $bodega_sub->cantidad;
                                                    //$bodega_sub->cantidad = 0;
                                                    DB::rollBack();
                                                    return response()->json([
                                                        'status' => 'error',
                                                        'result' => array('detalleProductos.' . $i . '.cantidad' => ['La cantidad solicitada para este producto no esta disponible']),
                                                        'messages' => null
                                                    ]);
                                                }*/
                        $bodega_sub->id_empresa = $usuario_empresa->id_empresa;
                        $bodega_sub->save();
                        $factura_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                        $factura_producto->id_producto = $bodega_sub->id_producto;

                    } else {
                        $factura_producto->id_bodega_producto = null;
                        $factura_producto->id_producto = $producto['productox']['id_producto'];
                    }

                    $factura_producto->id_factura = $factura->id_factura;
                    $factura_producto->descripcion_producto = $producto['productox']['descripcion'];
                    $factura_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                    $factura_producto->unidad_medida = $producto['productox']['unidad_medida'];

                    $factura_producto->cantidad = $producto['cantidad'];
                    $factura_producto->precio_costo = round($producto['precio_costo'], 2);
                    $factura_producto->precio_lista = $producto['precio_lista'];
                    $factura_producto->precio_costo_me = round($producto['precio_costo_me'], 2);
                    $factura_producto->precio_lista_me = round($producto['precio_lista_me'], 2);
                    $factura_producto->precio = round($producto['p_unitario'], 2);

                    $factura_producto->p_descuento = $producto['p_descuento'];
                    $factura_producto->p_ajuste = $producto['p_ajuste'];
                    $factura_producto->p_impuesto = $producto['p_impuesto'];

                    $factura_producto->m_impuesto = round($producto['mt_impuesto'], 2);
                    $factura_producto->m_descuento = round($producto['mt_descuento'], 2);
                    $factura_producto->m_ajuste = round($producto['mt_ajuste'], 2);

                    $factura_producto->id_afectacion = $producto['afectacionx']['id_afectacion'];

                    $factura_producto->f_creacion = date("Y-m-d H:i:s");
                    $factura_producto->id_empresa = $usuario_empresa->id_empresa;
                    $factura_producto->save();

                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                    $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                    if ($producto['productox']['id_tipo_producto'] !== 2) {
                        $contador_productos++;
                        $productoExiste = SalidaProductos::where('id_bodega_producto', $bodega_sub->id_bodega_producto)
                            ->where('id_salida', $salida->id_salida)->first();
                        if (!$productoExiste) {
                            $salida_producto = new SalidaProductos();
                            $salida_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                            $salida_producto->id_salida = $salida->id_salida;
                            $salida_producto->descripcion_producto = $producto['productox']['text'];
                            $salida_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                            $salida_producto->unidad_medida = $producto['productox']['unidad_medida'];
                            $salida_producto->precio_unitario = $producto['precio_costo']; // costo promedio
                            $salida_producto->precio_unitario_me = $producto['precio_costo_me']; // costo promedio
                            $salida_producto->cantidad_saliente = $producto['cantidad'];
                            $salida_producto->cantidad_despachada = 0;
                            $salida_producto->cantidad_faltante = 0;
                            $salida_producto->u_creacion = $salida->u_creacion;
                            $salida_producto->id_empresa = $usuario_empresa->id_empresa;
                            $salida_producto->save();
                        } else {
                            $productoExiste->cantidad_saliente = $productoExiste->cantidad_saliente + $producto['cantidad'];
                            $productoExiste->id_empresa = $usuario_empresa->id_empresa;
                            $productoExiste->save();
                        }

                        /**
                         * Obtenemos el total de ventas y costos de productos para moneda nacional y extrajnera
                         * nomenclatura al final de cada variable para moneda extrajera *_me*
                         */
                        $total_venta_producto += round($factura_producto->precio_lista * $factura_producto->cantidad, 4);
                        $total_costo_producto += round($factura_producto->precio_costo * $factura_producto->cantidad, 4);

                        $total_venta_producto_me += round($factura_producto->precio_lista_me * $factura_producto->cantidad, 4);
                        $total_costo_producto_me += round($factura_producto->precio_costo_me * $factura_producto->cantidad, 4);

                    } else {

                        $contador_servicios++;
                        /**
                         * Obteniendo total de precio y costo de servicios para ambas monedas
                         * acronmino para moneda extranjera al final de cada variable *_me*
                         * @author octaviom
                         */

                        if ($producto['productox']['tipo_servicio'] === 1) {
                            $servicioProcesamiento++;
                            $total_venta_servicios_procesamiento += round($factura_producto->precio_lista * $factura_producto->cantidad, 4);
                            $total_costo_servicios_procesamiento += round(($factura_producto->precio_costo * $request->t_cambio) * $factura_producto->cantidad, 4);

                            $total_venta_servicios_procesamiento_me += round($factura_producto->precio_lista_me * $factura_producto->cantidad, 4);
                            $total_costo_servicios_procesamiento_me += round($factura_producto->precio_costo_me * $factura_producto->cantidad, 4);

                        } else if ($producto['productox']['tipo_servicio'] === 2) {
                            $servicioAsesoria++;
                            $total_venta_servicios_asesoria += round($factura_producto->precio_lista * $factura_producto->cantidad, 4);
                            $total_costo_servicios_asesoria += round(($factura_producto->precio_costo * $request->t_cambio) * $factura_producto->cantidad, 4);

                            $total_venta_servicios_asesoria_me += round($factura_producto->precio_lista_me * $factura_producto->cantidad, 4);
                            $total_costo_servicios_asesoria_me += round($factura_producto->precio_costo_me * $factura_producto->cantidad, 4);
                        }
                    }
                    $i++;

                }

                //Revertir salida si no contiene productos
                /**
                 * Modificado
                 * Validación no contemplaba la existencia de servicios en la factura, solamente productos
                 */
                if ($contador_productos === 0) { //&& $contador_servicios === 0
                    $salidaDel = Salidas::find($salida->id_salida)->delete();
                }
                #region [Contabilización factura]
                /*INICIA movimiento contable - Factura*/

                $clientex = Clientes::select('id_cliente', 'id_zona')->find($factura->id_cliente);
                $zonax = Zonas::select('id_zona', 'id_centro_costo', 'id_centro_ingreso')->find($clientex->id_zona);
                $tipos_cuentas = array(4, 5, 6);

                #region contabilizacion factura
                $documento = new DocumentosContables();
                $tipo = TiposDocumentos::find(2);  //Tipo comprobante factura
                $fecha = date("Y-m-d H:i:s");
                $codigo = $documento->obtenerCodigoDocumento(array('id_tipo_doc' => $tipo->id_tipo_doc, 'fecha_doc' => $factura->f_factura));

                $nuevo_codigo = json_decode($codigo[0]);

                date_default_timezone_set('America/Managua');

                $documento->num_documento = $tipo->prefijo . '-' . $nuevo_codigo->secuencia;
                $documento->fecha_emision = $factura->f_factura; //date('Y-m-d');
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

                $documento->id_tipo_doc = 2; // Comprobante de factura
                $documento->valor = $factura->mt_total;
                $documento->valor_me = $factura->mt_total_me;
                if ($currency_id->valor === 1) {
                    $documento->concepto = 'Registramos venta por factura No. ' . $factura->no_documento . '. Monto total C$ ' . $factura->mt_total;
                } else {
                    $documento->concepto = 'Registramos venta por factura No. ' . $factura->no_documento . '. Monto total $ ' . $factura->mt_total_me;
                }

                $documento->id_moneda = $currency_id->valor;
                $documento->u_creacion = Auth::user()->name;
                $documento->estado = 1;
                $documento->id_empresa = $usuario_empresa->id_empresa;
                $documento->save();
                $factura->id_documento_contable = $documento->id_documento;
                $factura->save();

                TiposDocumentos::find($documento->id_tipo_doc)->increment('secuencia');

                //definición de tipo de configuración de comprobantes
                $factura_contado_bonificacion = 3;
                $factura_contado_us_desc = 4;
                $factura_credito_desc = 5;
                $factura_credito_bonif = 6;
                $factura_contado_cred_tarj = 1;
                $factura_anticipo = 7;

                //  INICIO DETALLE MOVIMIENTO CONTABLE

                /*/////////////////////////////FACTURA CONTADO C$ + BONIFICACION/////////////////////////////////////////
                  /////////////////////////////id_tipo_configuracion = 3////////////////////////////////////////////////*/

                if ($monto_cordobas > 0 && $request->id_tipo === 1 && $request->total_unidades_con_bonificacion >= 1) {

                    /*///////////////////////////SECCION 1 - FONDO POR DEPOSITAR////////////////////////////////////////*/
                    //Definición de parametros
                    $nombre_seccion_ingreso_nacional = 'FondoDepositar';
                    $id_tipo_configuracion = $factura_contado_bonificacion;
                    //Obtener datos de BD con parametros
                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_ingreso_nacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
                    //Inicio de registro de movimientos
                    $documento_cuenta_contableS1 = new DocumentosCuentas();
                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS1->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS1->debe = $request->mt_subtotal_sin_iva + $factura->mt_impuesto;//$request->mt_subtotal - $factura->mt_deuda - $factura->mt_retencion - $factura->mt_retencion_imi - $factura->mt_descuento - $factura->mt_ajuste + $factura->mt_impuesto;
                    $documento_cuenta_contableS1->haber = 0;
                    $documento_cuenta_contableS1->debe_org = ($request->mt_subtotal_sin_iva + $factura->mt_impuesto) *$factura->t_cambio;//$request->mt_subtotal - $factura->mt_deuda - $factura->mt_retencion - $factura->mt_retencion_imi - $factura->mt_descuento - $factura->mt_ajuste + $factura->mt_impuesto;;
                    $documento_cuenta_contableS1->haber_org = 0;
                    $documento_cuenta_contableS1->id_moneda = 1;

                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS1->haber > 0) {
                                $documento_cuenta_contableS1->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS1->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS1->debe > 0) {
                                $documento_cuenta_contableS1->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS1->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS1->id_centro = null;
                    }

                    $documento_cuenta_contableS1->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS1->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS1->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS1->save();

                    /*///////////////////////////FIN SECCION 1 - FONDO POR DEPOSITAR////////////////////////////////////////*/

                    /*///////////////////////////SECCION 2 - COMERCIALIZACION////////////////////////////////////////*/
                    if ($factura->mt_descuento > 0) { //Si el monto de descuento en factura es mayor a 0

                        $nombre_seccion_descuentos = 'PorComercializacion';
                        $id_tipo_configuracion = $factura_contado_bonificacion;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS2 = new DocumentosCuentas;
                        $documento_cuenta_contableS2->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS2->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS2->debe = $factura->mt_descuento;
                        $documento_cuenta_contableS2->haber = 0;
                        $documento_cuenta_contableS2->debe_org = $factura->mt_descuento;
                        $documento_cuenta_contableS2->haber_org = 0;
                        $documento_cuenta_contableS2->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS2->haber > 0) {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS2->debe > 0) {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS2->id_centro = null;
                        }

                        $documento_cuenta_contableS2->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS2->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS2->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS2->save();
                    }
                    /*///////////////////////////FIN SECCION 2 - COMERCIALIZACION ////////////////////////////////////////*/


                    /*///////////////////////////SECCION 4 - IR////////////////////////////////////////*/


                    if ($request->aplicaIR && $factura->mt_retencion > 0) { //Si factura aplica IR y monto retención mayor a 0

                        $nombre_seccion_retencion = 'ImpuestoRentaAnual';
                        $id_tipo_configuracion = $factura_contado_bonificacion;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS4 = new DocumentosCuentas;
                        $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS4->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS4->debe = $factura->mt_retencion;
                        $documento_cuenta_contableS4->haber = 0;
                        $documento_cuenta_contableS4->debe_org = $factura->mt_retencion;
                        $documento_cuenta_contableS4->haber_org = 0;
                        $documento_cuenta_contableS4->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS4->haber > 0) {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS4->debe > 0) {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS4->id_centro = null;
                        }
                        $documento_cuenta_contableS4->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;

                        $documento_cuenta_contableS4->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS4->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS4->save();
                    }

                    /*///////////////////////////FIN SECCION 4 - IR ////////////////////////////////////////*/

                    /*///////////////////////////SECCION 5 - COSTO VENTA////////////////////////////////////////*/


                    if ($total_costo_producto > 0) { //Si el total de costo de productos es mayor a 0

                        $nombre_seccion_costo_prod = 'CostoVentaArti';
                        $id_tipo_configuracion = $factura_contado_bonificacion;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_costo_prod)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS5 = new DocumentosCuentas;
                        $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS5->debe = $total_costo_producto_me;
                        $documento_cuenta_contableS5->haber = 0;
                        $documento_cuenta_contableS5->debe_org = $total_costo_producto;
                        $documento_cuenta_contableS5->haber_org = 0;
                        $documento_cuenta_contableS5->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS5->haber > 0) {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS5->debe > 0) {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS5->id_centro = null;
                        }

                        $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS5->save();

                    }

                    /*///////////////////////////FIN SECCION 5 - COSTO VENTA ////////////////////////////////////////*/

                    /*///////////////////////////SECCION 6 - IMPUESTO SOBREVENTA//////////////////////////////////////////////*/

                    if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) {
                        $total_ventax = $total_venta_producto + $total_venta_servicios;

                        $nombre_seccion_imp_venta = 'ImpuestoSobreVenta';
                        $id_tipo_configuracion = $factura_contado_bonificacion;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_imp_venta)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS6 = new DocumentosCuentas;
                        $documento_cuenta_contableS6->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS6->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS6->debe = $factura->mt_retencion_imi;
                        $documento_cuenta_contableS6->haber = 0;
                        $documento_cuenta_contableS6->debe_org = $factura->mt_retencion_imi;
                        $documento_cuenta_contableS6->haber_org = 0;
                        $documento_cuenta_contableS6->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS6->haber > 0) {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS6->debe > 0) {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS6->id_centro = null;
                        }

                        $documento_cuenta_contableS6->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS6->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS6->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS6->save();
                    }

                    /*///////////////////////////FIN SECCION 6 - IMPUESTO SOBRE VENTA ////////////////////////////////////////*/

                    /*///////////////////////////SSECCION 7 - D. GENERAL INGRESOS/////////////////////////////////////////////*/

                    if ($request->aplicaIR && $factura->mt_retencion > 0) {
                        $nombre_seccion_pago_minimo_dgi = 'DireccionGeneralIngreso';
                        $id_tipo_configuracion = $factura_contado_bonificacion;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_pago_minimo_dgi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS7 = new DocumentosCuentas;
                        $documento_cuenta_contableS7->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS7->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS7->debe = 0;
                        $documento_cuenta_contableS7->haber = $factura->mt_retencion;
                        $documento_cuenta_contableS7->debe_org = 0;
                        $documento_cuenta_contableS7->haber_org = $factura->mt_retencion;
                        $documento_cuenta_contableS7->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS7->haber > 0) {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS7->debe > 0) {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS7->id_centro = null;
                        }

                        $documento_cuenta_contableS7->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS7->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS7->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS7->save();
                    }

                    /*///////////////////////////FIN SECCION 7 - D. GENERAL INGRESOS//////////////////////////////////////////*/


                    /*///////////////////////////SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/
                    if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) { //Si factura aplica IMI y monto retención imi mayor a 0

                        $nombre_seccion_retencion_imi = 'AlcaldiasMunicipales';
                        $id_tipo_configuracion = $factura_contado_bonificacion;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion_imi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS8 = new DocumentosCuentas;
                        $documento_cuenta_contableS8->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS8->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS8->debe = 0;
                        $documento_cuenta_contableS8->haber = $factura->mt_retencion_imi;
                        $documento_cuenta_contableS8->debe_org = 0;
                        $documento_cuenta_contableS8->haber_org = $factura->mt_retencion_imi;;
                        $documento_cuenta_contableS8->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS8->haber > 0) {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS8->debe > 0) {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS8->id_centro = null;
                        }
                        $documento_cuenta_contableS8->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS8->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS8->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS8->save();
                    }
                    /*///////////////////////////FIN SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/

                    /*///////////////////////////SECCION 9 - IVA//////////////////////////////////////////*/
                    if (!$factura->impuesto_exonerado && $factura->mt_impuesto > 0) { // si aplica a impuesto exonerado y monto impuesto mayor a 0

                        $nombre_seccion_iva = 'DireccionGeneralIngresoH';
                        $id_tipo_configuracion = $factura_contado_bonificacion;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_iva)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS9 = new DocumentosCuentas;
                        $documento_cuenta_contableS9->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS9->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS9->debe = 0;
                        $documento_cuenta_contableS9->haber = $factura->mt_impuesto;
                        $documento_cuenta_contableS9->debe_org = 0;
                        $documento_cuenta_contableS9->haber_org = $factura->mt_impuesto;
                        $documento_cuenta_contableS9->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS9->haber > 0) {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS9->debe > 0) {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS9->id_centro = null;
                        }

                        $documento_cuenta_contableS9->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS9->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS9->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS9->save();
                    }
                    /*///////////////////////////FIN SECCION 9 - IVA//////////////////////////////////////////*/

                    /*///////////////////////////SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/
                    if ($total_costo_producto > 0) { //So el total de costo de productos es mayor a 0

                        $nombre_seccion_inventario = 'ArticuloBodegaCosto';


                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_inventario)->where('id_tipo_configuracion', $factura_contado_bonificacion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS10 = new DocumentosCuentas;
                        $documento_cuenta_contableS10->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS10->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS10->debe = 0;
                        $documento_cuenta_contableS10->haber = $total_costo_producto_me;
                        $documento_cuenta_contableS10->debe_org = 0;
                        $documento_cuenta_contableS10->haber_org = $total_costo_producto;
                        $documento_cuenta_contableS10->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS10->haber > 0) {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS10->debe > 0) {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS10->id_centro = null;
                        }

                        $documento_cuenta_contableS10->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS10->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS10->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS10->save();
                    }
                    /*///////////////////////////FIN SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/


                    /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/
                    if ($total_venta_producto > 0) { //Si total venta productos es mayor a 0

                        $nombre_seccion_ingreso_productos = 'Nacionales';

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_ingreso_productos)->where('id_tipo_configuracion', $factura_contado_bonificacion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS11 = new DocumentosCuentas;
                        $documento_cuenta_contableS11->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS11->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS11->debe = 0;
                        $documento_cuenta_contableS11->haber = $total_venta_producto_me;
                        $documento_cuenta_contableS11->debe_org = 0;
                        $documento_cuenta_contableS11->haber_org = $total_venta_producto_me * $factura->t_cambio;
                        $documento_cuenta_contableS11->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS11->haber > 0) {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS11->debe > 0) {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS11->id_centro = null;
                        }
                        $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS11->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS11->save();

                        /*///////////////////////////SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
                        $nombre_seccion_descuentos = 'PorBonificacion';
                        $id_tipo_configuracion = $factura_contado_bonificacion;

                        $cuentaSeccionAjuste = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        foreach ($request->detalleProductos as $producto) {
                            if ($producto['afectacionx']['id_afectacion'] > 1 && $producto['mt_ajuste'] > 0) { // Si el producto tiene algún tipo de afectación y el monto de ajuste es mayor a 0

                                $documento_cuenta_contableS11 = new DocumentosCuentas;
                                $documento_cuenta_contableS11->id_documento = $documento->id_documento;
                                $documento_cuenta_contableS11->concepto = $cuentaSeccionAjuste->descripcion_movimiento . ' ' . $factura->no_documento;
                                $documento_cuenta_contableS11->debe = $producto['mt_ajuste'];
                                $documento_cuenta_contableS11->haber = 0;
                                $documento_cuenta_contableS11->debe_org = $producto['mt_ajuste'];
                                $documento_cuenta_contableS11->haber_org = 0;
                                $documento_cuenta_contableS11->id_moneda = 1;
                                $documento_cuenta_contableS11->id_centro = 1;///cambiar centro de costo ingreso

                                /* revisar */
                                if (in_array($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                                    if ($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                        if ($documento_cuenta_contableS11->haber > 0) {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                        } else {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                        }
                                    } else {
                                        if ($documento_cuenta_contableS11->debe > 0) {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                        } else {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                        }
                                    }
                                } else {
                                    $documento_cuenta_contableS11->id_centro = null;
                                }

                                $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccionAjuste->id_cuenta_contable;
                                $documento_cuenta_contableS11->cta_contable = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
                                $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
                                $documento_cuenta_contableS11->save();
                                /*///////////////////////////FIN SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
                            }
                        }

                    }
                    /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/
                }

                /*/////////////////////////////FIN FACTURA CONTADO C$ + BONIFICACION/////////////////////////////////////////
                  /////////////////////////////id_tipo_configuracion = 3////////////////////////////////////////////////*/

                /*/////////////////////////////FACTURA CONTADO U$ + DESC/////////////////////////////////////////
                 /////////////////////////////id_tipo_configuracion = 4////////////////////////////////////////////////*/

                elseif ($monto_dolares > 0 && $request->id_tipo === 1 && $request->mt_descuento > 0) {

                    /*///////////////////////////SECCION 1 - FONDO POR DEPOSITAR////////////////////////////////////////*/
                    //Definición de parametros
                    $nombre_seccion_ingreso_nacional = 'FondoDepositar';
                    $id_tipo_configuracion = $factura_contado_us_desc;
                    //Obtener datos de BD con parametros
                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_ingreso_nacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
                    //Inicio de registro de movimientos
                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS1->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS1->debe = ($request->mt_subtotal_sin_iva + $factura->mt_impuesto) - $factura->mt_descuento;//$request->mt_subtotal - $factura->mt_deuda - $factura->mt_retencion - $factura->mt_retencion_imi - $factura->mt_descuento - $factura->mt_ajuste + $factura->mt_impuesto;
                    $documento_cuenta_contableS1->haber = 0;
                    $documento_cuenta_contableS1->debe_org = ( ($request->mt_subtotal_sin_iva + $factura->mt_impuesto) - $factura->mt_descuento ) * $factura->t_cambio;//$request->mt_subtotal - $factura->mt_deuda - $factura->mt_retencion - $factura->mt_retencion_imi - $factura->mt_descuento - $factura->mt_ajuste + $factura->mt_impuesto;;
                    $documento_cuenta_contableS1->haber_org = 0;
                    $documento_cuenta_contableS1->id_moneda = 1;

                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS1->haber > 0) {
                                $documento_cuenta_contableS1->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS1->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS1->debe > 0) {
                                $documento_cuenta_contableS1->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS1->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS1->id_centro = null;
                    }

                    $documento_cuenta_contableS1->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS1->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS1->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS1->save();

                    /*///////////////////////////FIN SECCION 1 - FONDO POR DEPOSITAR////////////////////////////////////////*/

                    /*///////////////////////////SECCION 2 - COMERCIALIZACION////////////////////////////////////////*/
                    if ($factura->mt_descuento > 0) { //Si el monto de descuento en factura es mayor a 0

                        $nombre_seccion_descuentos = 'PorComercializacion';
                        $id_tipo_configuracion = $factura_contado_us_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS2 = new DocumentosCuentas;
                        $documento_cuenta_contableS2->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS2->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS2->debe = $factura->mt_descuento;
                        $documento_cuenta_contableS2->haber = 0;
                        $documento_cuenta_contableS2->debe_org = $factura->mt_descuento * $factura->t_cambio;
                        $documento_cuenta_contableS2->haber_org = 0;
                        $documento_cuenta_contableS2->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS2->haber > 0) {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS2->debe > 0) {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS2->id_centro = null;
                        }

                        $documento_cuenta_contableS2->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS2->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS2->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS2->save();
                    }
                    /*///////////////////////////FIN SECCION 2 - COMERCIALIZACION ////////////////////////////////////////*/


                    /*///////////////////////////SECCION 4 - IR////////////////////////////////////////*/


                    if ($request->aplicaIR && $factura->mt_retencion > 0) { //Si factura aplica IR y monto retención mayor a 0

                        $nombre_seccion_retencion = 'ImpuestoRentaAnual';
                        $id_tipo_configuracion = $factura_contado_us_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS4 = new DocumentosCuentas;
                        $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS4->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS4->debe = $factura->mt_retencion;
                        $documento_cuenta_contableS4->haber = 0;
                        $documento_cuenta_contableS4->debe_org = $factura->mt_retencion;
                        $documento_cuenta_contableS4->haber_org = 0;
                        $documento_cuenta_contableS4->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS4->haber > 0) {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS4->debe > 0) {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS4->id_centro = null;
                        }
                        $documento_cuenta_contableS4->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;

                        $documento_cuenta_contableS4->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS4->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS4->save();
                    }

                    /*///////////////////////////FIN SECCION 4 - IR ////////////////////////////////////////*/

                    /*///////////////////////////SECCION 5 - COSTO VENTA////////////////////////////////////////*/


                    if ($total_costo_producto > 0) { //Si el total de costo de productos es mayor a 0

                        $nombre_seccion_costo_prod = 'CostoVentaArti';
                        $id_tipo_configuracion = $factura_contado_us_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_costo_prod)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS5 = new DocumentosCuentas;
                        $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS5->debe = $total_costo_producto_me;
                        $documento_cuenta_contableS5->haber = 0;
                        $documento_cuenta_contableS5->debe_org = $total_costo_producto;
                        $documento_cuenta_contableS5->haber_org = 0;
                        $documento_cuenta_contableS5->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS5->haber > 0) {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS5->debe > 0) {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS5->id_centro = null;
                        }

                        $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS5->save();

                    }

                    /*///////////////////////////FIN SECCION 5 - COSTO VENTA ////////////////////////////////////////*/

                    /*///////////////////////////SECCION 6 - IMPUESTO SOBREVENTA//////////////////////////////////////////////*/

                    if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) {
                        $total_ventax = $total_venta_producto + $total_venta_servicios;

                        $nombre_seccion_imp_venta = 'ImpuestoSobreVenta';
                        $id_tipo_configuracion = $factura_contado_us_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_imp_venta)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS6 = new DocumentosCuentas;
                        $documento_cuenta_contableS6->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS6->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS6->debe = $factura->mt_retencion_imi;
                        $documento_cuenta_contableS6->haber = 0;
                        $documento_cuenta_contableS6->debe_org = $factura->mt_retencion_imi;
                        $documento_cuenta_contableS6->haber_org = 0;
                        $documento_cuenta_contableS6->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS6->haber > 0) {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS6->debe > 0) {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS6->id_centro = null;
                        }

                        $documento_cuenta_contableS6->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS6->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS6->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS6->save();
                    }

                    /*///////////////////////////FIN SECCION 6 - IMPUESTO SOBRE VENTA ////////////////////////////////////////*/

                    /*///////////////////////////SSECCION 7 - D. GENERAL INGRESOS/////////////////////////////////////////////*/

                    if ($request->aplicaIR && $factura->mt_retencion > 0) {
                        $nombre_seccion_pago_minimo_dgi = 'DireccionGeneralIngreso';
                        $id_tipo_configuracion = $factura_contado_us_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_pago_minimo_dgi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS7 = new DocumentosCuentas;
                        $documento_cuenta_contableS7->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS7->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS7->debe = 0;
                        $documento_cuenta_contableS7->haber = $factura->mt_retencion;
                        $documento_cuenta_contableS7->debe_org = 0;
                        $documento_cuenta_contableS7->haber_org = $factura->mt_retencion;
                        $documento_cuenta_contableS7->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS7->haber > 0) {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS7->debe > 0) {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS7->id_centro = null;
                        }

                        $documento_cuenta_contableS7->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS7->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS7->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS7->save();
                    }

                    /*///////////////////////////FIN SECCION 7 - D. GENERAL INGRESOS//////////////////////////////////////////*/


                    /*///////////////////////////SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/
                    if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) { //Si factura aplica IMI y monto retención imi mayor a 0

                        $nombre_seccion_retencion_imi = 'AlcaldiasMunicipales';
                        $id_tipo_configuracion = $factura_contado_us_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion_imi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS8 = new DocumentosCuentas;
                        $documento_cuenta_contableS8->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS8->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS8->debe = 0;
                        $documento_cuenta_contableS8->haber = $factura->mt_retencion_imi;
                        $documento_cuenta_contableS8->debe_org = 0;
                        $documento_cuenta_contableS8->haber_org = $factura->mt_retencion_imi;;
                        $documento_cuenta_contableS8->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS8->haber > 0) {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS8->debe > 0) {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS8->id_centro = null;
                        }
                        $documento_cuenta_contableS8->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS8->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS8->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS8->save();
                    }
                    /*///////////////////////////FIN SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/

                    /*///////////////////////////SECCION 9 - IVA//////////////////////////////////////////*/
                    if (!$factura->impuesto_exonerado && $factura->mt_impuesto > 0) { // si aplica a impuesto exonerado y monto impuesto mayor a 0

                        $nombre_seccion_iva = 'DireccionGeneralIngresoH';
                        $id_tipo_configuracion = $factura_contado_us_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_iva)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS9 = new DocumentosCuentas;
                        $documento_cuenta_contableS9->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS9->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS9->debe = 0;
                        $documento_cuenta_contableS9->haber = $factura->mt_impuesto;
                        $documento_cuenta_contableS9->debe_org = 0;
                        $documento_cuenta_contableS9->haber_org = $factura->mt_impuesto;
                        $documento_cuenta_contableS9->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS9->haber > 0) {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS9->debe > 0) {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS9->id_centro = null;
                        }

                        $documento_cuenta_contableS9->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS9->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS9->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS9->save();
                    }
                    /*///////////////////////////FIN SECCION 9 - IVA//////////////////////////////////////////*/

                    /*///////////////////////////SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/
                    if ($total_costo_producto > 0) { //So el total de costo de productos es mayor a 0

                        $nombre_seccion_inventario = 'ArticuloBodegaCosto';
                        $id_tipo_configuracion = $factura_contado_us_desc;


                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_inventario)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS10 = new DocumentosCuentas;
                        $documento_cuenta_contableS10->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS10->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS10->debe = 0;
                        $documento_cuenta_contableS10->haber = $total_costo_producto_me;
                        $documento_cuenta_contableS10->debe_org = 0;
                        $documento_cuenta_contableS10->haber_org = $total_costo_producto;
                        $documento_cuenta_contableS10->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS10->haber > 0) {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS10->debe > 0) {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS10->id_centro = null;
                        }

                        $documento_cuenta_contableS10->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS10->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS10->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS10->save();
                    }
                    /*///////////////////////////FIN SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/


                    /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/
                    if ($total_venta_producto > 0) { //Si total venta productos es mayor a 0

                        $nombre_seccion_ingreso_productos = 'Nacionales';
                        $id_tipo_configuracion = $factura_contado_us_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_ingreso_productos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS11 = new DocumentosCuentas;
                        $documento_cuenta_contableS11->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS11->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS11->debe = 0;
                        $documento_cuenta_contableS11->haber = $total_venta_producto_me;
                        $documento_cuenta_contableS11->debe_org = 0;
                        $documento_cuenta_contableS11->haber_org = $total_venta_producto_me * $factura->t_cambio;
                        $documento_cuenta_contableS11->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS11->haber > 0) {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS11->debe > 0) {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS11->id_centro = null;
                        }
                        $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS11->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS11->save();

                        /*///////////////////////////SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
                        $nombre_seccion_descuentos = 'PorBonificacion';
                        $id_tipo_configuracion = $factura_contado_us_desc;

                        $cuentaSeccionAjuste = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        foreach ($request->detalleProductos as $producto) {
                            if ($producto['afectacionx']['id_afectacion'] > 1 && $producto['mt_ajuste'] > 0) { // Si el producto tiene algún tipo de afectación y el monto de ajuste es mayor a 0

                                $documento_cuenta_contableS11 = new DocumentosCuentas;
                                $documento_cuenta_contableS11->id_documento = $documento->id_documento;
                                $documento_cuenta_contableS11->concepto = $cuentaSeccionAjuste->descripcion_movimiento . ' ' . $factura->no_documento;
                                $documento_cuenta_contableS11->debe = $producto['mt_ajuste'];
                                $documento_cuenta_contableS11->haber = 0;
                                $documento_cuenta_contableS11->debe_org = $producto['mt_ajuste'];
                                $documento_cuenta_contableS11->haber_org = 0;
                                $documento_cuenta_contableS11->id_moneda = 1;
                                $documento_cuenta_contableS11->id_centro = 1;///cambiar centro de costo ingreso

                                /* revisar */
                                if (in_array($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                                    if ($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                        if ($documento_cuenta_contableS11->haber > 0) {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                        } else {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                        }
                                    } else {
                                        if ($documento_cuenta_contableS11->debe > 0) {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                        } else {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                        }
                                    }
                                } else {
                                    $documento_cuenta_contableS11->id_centro = null;
                                }

                                $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccionAjuste->id_cuenta_contable;
                                $documento_cuenta_contableS11->cta_contable = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
                                $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
                                $documento_cuenta_contableS11->save();
                                /*///////////////////////////FIN SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
                            }
                        }

                    }
                    /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/
                }

                /*/////////////////////////////FIN FACTURA CONTADO U$ + DESC/////////////////////////////////////////
                  /////////////////////////////id_tipo_configuracion = 4////////////////////////////////////////////////*/

                /*/////////////////////////////FACTURA ANTICIPO/////////////////////////////////////////
                /////////////////////////////id_tipo_configuracion = 7////////////////////////////////////////////////*/
                elseif (($factura->id_tipo === 4 || $factura->id_tipo === '4')) {

                    $id_tipo_configuracion = $factura_anticipo; // Contabilización factura anticipo
                    $nombre_seccion_MonNacional = 'AnticipoClienteProyecto';
                    //obtener datos de BD con estos paremetros
                    $cuentaSeccionMonNacional = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $factura->no_documento;

                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                        if ($currency_id->valor === 1) {
                            $documento_cuenta_contableS1->debe = $request->mt_subtotal_sin_iva + $factura->mt_impuesto;//- $factura->mt_descuento;
                            $documento_cuenta_contableS1->haber = 0;
                            $documento_cuenta_contableS1->debe_org = ($request->mt_subtotal_sin_iva + $factura->mt_impuesto) * $factura->t_cambio;//- $factura->mt_descuento;
                            $documento_cuenta_contableS1->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS1->debe = $request->mt_subtotal_sin_iva + $factura->mt_impuesto;//- $factura->mt_descuento;
                            $documento_cuenta_contableS1->haber = 0;
                            $documento_cuenta_contableS1->debe_org = ($request->mt_subtotal_sin_iva + $factura->mt_impuesto) * $factura->t_cambio;//- $factura->mt_descuento;
                            $documento_cuenta_contableS1->haber_org = 0;
                        }

                    } else {
                        if ($currency_id->valor === 1) {
                            $documento_cuenta_contableS1->debe = 0;
                            $documento_cuenta_contableS1->haber = $request->mt_subtotal_sin_iva + $factura->mt_impuesto - $factura->mt_descuento;
                            $documento_cuenta_contableS1->debe_org = 0;
                            $documento_cuenta_contableS1->haber_org = ($request->mt_subtotal_sin_iva + $factura->mt_impuesto - $factura->mt_descuento) * $factura->t_cambio;
                        } else {
                            $documento_cuenta_contableS1->debe = 0;
                            $documento_cuenta_contableS1->haber = $request->mt_subtotal_sin_iva + $factura->mt_impuesto - $factura->mt_descuento;
                            $documento_cuenta_contableS1->debe_org = 0;
                            $documento_cuenta_contableS1->haber_org = ($request->mt_subtotal_sin_iva + $factura->mt_impuesto - $factura->mt_descuento) * $factura->t_cambio;
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

                    if ($total_venta_producto > 0) {
                        $id_tipo_configuracion = $factura_anticipo; // Facturación por anticipo - contabilizar total venta productos
                        $nombre_seccion_MonNacional = 'TotalVentaproductos';
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionMonNacional = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
                        $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                        $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                        $documento_cuenta_contableS1 = new DocumentosCuentas;
                        $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $factura->no_documento;

                        if ($cuentaSeccionMonNacional->debe_haber === 1) {
                            if ($currency_id->valor === 1) {
                                $documento_cuenta_contableS1->debe = $total_venta_producto_me - $factura->mt_descuento;
                                $documento_cuenta_contableS1->haber = 0;
                                $documento_cuenta_contableS1->debe_org = ($total_venta_producto_me - $factura->mt_descuento) * $factura->t_cambio;
                                $documento_cuenta_contableS1->haber_org = 0;
                            } else {
                                $documento_cuenta_contableS1->debe = $total_venta_producto_me - $factura->mt_descuento;
                                $documento_cuenta_contableS1->haber = 0;
                                $documento_cuenta_contableS1->debe_org = ($total_venta_producto_me - $factura->mt_descuento) * $factura->t_cambio;
                                $documento_cuenta_contableS1->haber_org = 0;
                            }

                        } else {
                            if ($currency_id->valor === 1) {
                                $documento_cuenta_contableS1->debe = 0;
                                $documento_cuenta_contableS1->haber = $total_venta_producto_me - $factura->mt_descuento;
                                $documento_cuenta_contableS1->debe_org = 0;
                                $documento_cuenta_contableS1->haber_org = ($total_venta_producto_me - $factura->mt_descuento) * $factura->t_cambio;
                            } else {
                                $documento_cuenta_contableS1->debe = 0;
                                $documento_cuenta_contableS1->haber = $total_venta_producto_me - $factura->mt_descuento;
                                $documento_cuenta_contableS1->debe_org = 0;
                                $documento_cuenta_contableS1->haber_org = ($total_venta_producto_me - $factura->mt_descuento) * $factura->t_cambio;
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

                    if ($total_venta_servicios_asesoria > 0 || $total_venta_servicios_procesamiento > 0) {

                        $nombre_seccion_venta_admon = 'ServAdmon';
                        $id_tipo_configuracion = $factura_credito_bonif;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_venta_admon)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1 && $total_venta_servicios_asesoria > 0) {
                            $documento_cuenta_contableS5 = new DocumentosCuentas;
                            $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS5->debe = 0;
                            $documento_cuenta_contableS5->haber = $total_venta_servicios_asesoria_me - $factura->mt_descuento;
                            $documento_cuenta_contableS5->debe_org = 0;
                            $documento_cuenta_contableS5->haber_org = ($total_venta_servicios_asesoria - $factura->mt_descuento);
                            $documento_cuenta_contableS5->id_moneda = $currency_id->valor;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS5->haber > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS5->debe > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS5->id_centro = null;
                            }

                            $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS5->save();
                        }

                        $nombre_seccion_costo_prod = 'ServProcesamiento';
                        $id_tipo_configuracion = $factura_credito_bonif;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_costo_prod)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1 && $total_venta_servicios_procesamiento > 0) {
                            $documento_cuenta_contableS5 = new DocumentosCuentas;
                            $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS5->debe = 0;
                            $documento_cuenta_contableS5->haber = $total_venta_servicios_procesamiento_me - $factura->mt_descuento;
                            $documento_cuenta_contableS5->debe_org = 0;
                            $documento_cuenta_contableS5->haber_org = ($total_venta_servicios_procesamiento - $factura->mt_descuento);
                            $documento_cuenta_contableS5->id_moneda = $currency_id->valor;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS5->haber > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS5->debe > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS5->id_centro = null;
                            }

                            $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS5->save();
                        }
                    }
                    $id_tipo_configuracion = $factura_credito_bonif; // Contabilizando iva de factura
                    $nombre_seccion_MonNacional = 'IvaRet';
                    //obtener datos de BD con estos paremetros
                    $cuentaSeccionMonNacional = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_MonNacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
                    $cuenta_contable_mon_nacional = CuentasContables::find($cuentaSeccionMonNacional->id_cuenta_contable);
                    $cuenta_contable_mon_nacional_padre = CuentasContables::find($cuenta_contable_mon_nacional->id_cuenta_padre);

                    $documento_cuenta_contableS1 = new DocumentosCuentas;
                    $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS1->concepto = $cuentaSeccionMonNacional->descripcion_movimiento . ' ' . $factura->no_documento;

                    if ($cuentaSeccionMonNacional->debe_haber === 1) {
                        if ($currency_id->valor === 1) {
                            $documento_cuenta_contableS1->debe = $factura->mt_impuesto;
                            $documento_cuenta_contableS1->haber = 0;
                            $documento_cuenta_contableS1->debe_org = ($factura->mt_impuesto) * $factura->t_cambio;
                            $documento_cuenta_contableS1->haber_org = 0;
                        } else {
                            $documento_cuenta_contableS1->debe = $factura->mt_impuesto;
                            $documento_cuenta_contableS1->haber = 0;
                            $documento_cuenta_contableS1->debe_org = ($factura->mt_impuesto) * $factura->t_cambio;
                            $documento_cuenta_contableS1->haber_org = 0;
                        }
                    } else {
                        if ($currency_id->valor === 1) {
                            $documento_cuenta_contableS1->debe = 0;
                            $documento_cuenta_contableS1->haber = ($factura->mt_impuesto);
                            $documento_cuenta_contableS1->debe_org = 0;
                            $documento_cuenta_contableS1->haber_org = ($factura->mt_impuesto) * $factura->t_cambio;
                        } else {
                            $documento_cuenta_contableS1->debe = 0;
                            $documento_cuenta_contableS1->haber = $factura->mt_impuesto;
                            $documento_cuenta_contableS1->debe_org = 0;
                            $documento_cuenta_contableS1->haber_org = ($factura->mt_impuesto) * $factura->t_cambio;
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
                /*/////////////////////////////FIN FACTURA ANTICIPO/////////////////////////////////////////
                /////////////////////////////id_tipo_configuracion = 7////////////////////////////////////////////////*/

                /*/////////////////////////////FACTURA CREDITO + DESCUENTO/////////////////////////////////////////
                 /////////////////////////////id_tipo_configuracion = 5////////////////////////////////////////////////*/

                elseif (($request->id_tipo === 2 || $request->id_tipo === 3) && $request->total_unidades_con_bonificacion > 0 && round($factura->mt_deuda, 2) > 0) {

                    /*///////////////////////////SECCION 1 - CUENTAS POR COBRAR////////////////////////////////////////*/

                    //Definición de parametros
                    $nombre_seccion_cuentas_cobrar = 'CuentasCobrar';
                    $id_tipo_configuracion = $factura_credito_desc;
                    //Obtener datos de BD con parametros
                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_cuentas_cobrar)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                    $documento_cuenta_contableS2a = new DocumentosCuentas;
                    $documento_cuenta_contableS2a->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS2a->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    $documento_cuenta_contableS2a->debe = $factura->mt_deuda_me - $factura->mt_retencion - $factura->mt_retencion_imi /*-$factura->mt_descuento*/
                    ;
                    $documento_cuenta_contableS2a->haber = 0;
                    $documento_cuenta_contableS2a->debe_org = $factura->mt_deuda - $factura->mt_retencion - $factura->mt_retencion_imi /*-$factura->mt_descuento*/
                    ;
                    $documento_cuenta_contableS2a->haber_org = 0;
                    $documento_cuenta_contableS2a->id_moneda = 1;
                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS2a->haber > 0) {
                                $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS2a->debe > 0) {
                                $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS2a->id_centro = null;
                    }

                    $documento_cuenta_contableS2a->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS2a->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS2a->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS2a->save();


                    /*///////////////////////////FIN SECCION 1 - CUENTAS POR COBRAR////////////////////////////////////////*/

                    /*///////////////////////////SECCION 2 - COMERCIALIZACION////////////////////////////////////////*/
                    if ($factura->mt_descuento > 0) { //Si el monto de descuento en factura es mayor a 0

                        $nombre_seccion_descuentos = 'PorComercializacion';
                        $id_tipo_configuracion = $factura_credito_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS2 = new DocumentosCuentas;
                        $documento_cuenta_contableS2->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS2->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS2->debe = $factura->mt_descuento;
                        $documento_cuenta_contableS2->haber = 0;
                        $documento_cuenta_contableS2->debe_org = $factura->mt_descuento * $factura->t_cambio;
                        $documento_cuenta_contableS2->haber_org = 0;
                        $documento_cuenta_contableS2->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS2->haber > 0) {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS2->debe > 0) {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS2->id_centro = null;
                        }

                        $documento_cuenta_contableS2->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS2->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS2->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS2->save();
                    }
                    /*///////////////////////////FIN SECCION 2 - COMERCIALIZACION ////////////////////////////////////////*/


                    /*///////////////////////////SECCION 4 - IR////////////////////////////////////////*/


                    if ($request->aplicaIR && $factura->mt_retencion > 0) { //Si factura aplica IR y monto retención mayor a 0

                        $nombre_seccion_retencion = 'ImpuestoRentaAnual';
                        $id_tipo_configuracion = $factura_credito_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS4 = new DocumentosCuentas;
                        $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS4->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS4->debe = $factura->mt_retencion;
                        $documento_cuenta_contableS4->haber = 0;
                        $documento_cuenta_contableS4->debe_org = $factura->mt_retencion * $factura->t_cambio;
                        $documento_cuenta_contableS4->haber_org = 0;
                        $documento_cuenta_contableS4->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS4->haber > 0) {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS4->debe > 0) {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS4->id_centro = null;
                        }
                        $documento_cuenta_contableS4->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;

                        $documento_cuenta_contableS4->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS4->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS4->save();
                    }

                    /*///////////////////////////FIN SECCION 4 - IR ////////////////////////////////////////*/

                    /*///////////////////////////SECCION 5 - COSTO VENTA////////////////////////////////////////*/


                    if ($total_costo_producto > 0) { //Si el total de costo de productos es mayor a 0

                        $nombre_seccion_costo_prod = 'CostoVentaArti';
                        $id_tipo_configuracion = $factura_credito_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_costo_prod)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS5 = new DocumentosCuentas;
                        $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS5->debe = $total_costo_producto_me;
                        $documento_cuenta_contableS5->haber = 0;
                        $documento_cuenta_contableS5->debe_org = $total_costo_producto;
                        $documento_cuenta_contableS5->haber_org = 0;
                        $documento_cuenta_contableS5->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS5->haber > 0) {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS5->debe > 0) {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS5->id_centro = null;
                        }

                        $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS5->save();

                    }

                    /*///////////////////////////FIN SECCION 5 - COSTO VENTA ////////////////////////////////////////*/

                    /*///////////////////////////SECCION 6 - IMPUESTO SOBREVENTA//////////////////////////////////////////////*/

                    if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) {
                        $total_ventax = $total_venta_producto + $total_venta_servicios;

                        $nombre_seccion_imp_venta = 'ImpuestoSobreVenta';
                        $id_tipo_configuracion = $factura_credito_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_imp_venta)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS6 = new DocumentosCuentas;
                        $documento_cuenta_contableS6->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS6->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS6->debe = $factura->mt_retencion_imi;
                        $documento_cuenta_contableS6->haber = 0;
                        $documento_cuenta_contableS6->debe_org = $factura->mt_retencion_imi;
                        $documento_cuenta_contableS6->haber_org = 0;
                        $documento_cuenta_contableS6->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS6->haber > 0) {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS6->debe > 0) {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS6->id_centro = null;
                        }

                        $documento_cuenta_contableS6->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS6->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS6->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS6->save();
                    }

                    /*///////////////////////////FIN SECCION 6 - IMPUESTO SOBRE VENTA ////////////////////////////////////////*/

                    /*///////////////////////////SSECCION 7 - D. GENERAL INGRESOS/////////////////////////////////////////////*/

                    if ($request->aplicaIR && $factura->mt_retencion > 0) {
                        $nombre_seccion_pago_minimo_dgi = 'DireccionGeneralIngreso';
                        $id_tipo_configuracion = $factura_credito_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_pago_minimo_dgi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS7 = new DocumentosCuentas;
                        $documento_cuenta_contableS7->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS7->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS7->debe = 0;
                        $documento_cuenta_contableS7->haber = $factura->mt_retencion;
                        $documento_cuenta_contableS7->debe_org = 0;
                        $documento_cuenta_contableS7->haber_org = $factura->mt_retencion;
                        $documento_cuenta_contableS7->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS7->haber > 0) {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS7->debe > 0) {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS7->id_centro = null;
                        }

                        $documento_cuenta_contableS7->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS7->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS7->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS7->save();
                    }

                    /*///////////////////////////FIN SECCION 7 - D. GENERAL INGRESOS//////////////////////////////////////////*/


                    /*///////////////////////////SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/
                    if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) { //Si factura aplica IMI y monto retención imi mayor a 0

                        $nombre_seccion_retencion_imi = 'AlcaldiasMunicipales';
                        $id_tipo_configuracion = $factura_credito_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion_imi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS8 = new DocumentosCuentas;
                        $documento_cuenta_contableS8->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS8->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS8->debe = 0;
                        $documento_cuenta_contableS8->haber = $factura->mt_retencion_imi;
                        $documento_cuenta_contableS8->debe_org = 0;
                        $documento_cuenta_contableS8->haber_org = $factura->mt_retencion_imi;;
                        $documento_cuenta_contableS8->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS8->haber > 0) {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS8->debe > 0) {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS8->id_centro = null;
                        }
                        $documento_cuenta_contableS8->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS8->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS8->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS8->save();
                    }
                    /*///////////////////////////FIN SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/

                    /*///////////////////////////SECCION 9 - IVA//////////////////////////////////////////*/
                    if (!$factura->impuesto_exonerado && $factura->mt_impuesto > 0) { // si aplica a impuesto exonerado y monto impuesto mayor a 0

                        $nombre_seccion_iva = 'DireccionGeneralIngresoH';
                        $id_tipo_configuracion = $factura_credito_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_iva)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS9 = new DocumentosCuentas;
                        $documento_cuenta_contableS9->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS9->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS9->debe = 0;
                        $documento_cuenta_contableS9->haber = $factura->mt_impuesto;
                        $documento_cuenta_contableS9->debe_org = 0;
                        $documento_cuenta_contableS9->haber_org = $factura->mt_impuesto * $factura->t_cambio;
                        $documento_cuenta_contableS9->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS9->haber > 0) {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS9->debe > 0) {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS9->id_centro = null;
                        }

                        $documento_cuenta_contableS9->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS9->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS9->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS9->save();
                    }
                    /*///////////////////////////FIN SECCION 9 - IVA//////////////////////////////////////////*/

                    /*///////////////////////////SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/
                    if ($total_costo_producto > 0) { //So el total de costo de productos es mayor a 0

                        $nombre_seccion_inventario = 'ArticuloBodegaCosto';
                        $id_tipo_configuracion = $factura_credito_desc;


                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_inventario)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS10 = new DocumentosCuentas;
                        $documento_cuenta_contableS10->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS10->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS10->debe = 0;
                        $documento_cuenta_contableS10->haber = $total_costo_producto_me;
                        $documento_cuenta_contableS10->debe_org = 0;
                        $documento_cuenta_contableS10->haber_org = $total_costo_producto;
                        $documento_cuenta_contableS10->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS10->haber > 0) {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS10->debe > 0) {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS10->id_centro = null;
                        }

                        $documento_cuenta_contableS10->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS10->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS10->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                        $documento_cuenta_contableS10->save();
                    }
                    /*///////////////////////////FIN SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/


                    /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/
                    if ($total_venta_producto > 0) { //Si total venta productos es mayor a 0

                        $nombre_seccion_ingreso_productos = 'Nacionales';
                        $id_tipo_configuracion = $factura_credito_desc;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_ingreso_productos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();


                        $documento_cuenta_contableS11 = new DocumentosCuentas;
                        $documento_cuenta_contableS11->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS11->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS11->debe = 0;
                        $documento_cuenta_contableS11->haber = $total_venta_producto_me;
                        $documento_cuenta_contableS11->debe_org = 0;
                        $documento_cuenta_contableS11->haber_org = $total_venta_producto_me  * $factura->t_cambio;
                        $documento_cuenta_contableS11->id_moneda = 1;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS11->haber > 0) {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS11->debe > 0) {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS11->id_centro = null;
                        }
                        $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS11->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS11->save();

                        /*///////////////////////////SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
                        $nombre_seccion_descuentos = 'PorBonificacion';
                        $id_tipo_configuracion = $factura_credito_desc;

                        $cuentaSeccionAjuste = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        foreach ($request->detalleProductos as $producto) {
                            if ($producto['afectacionx']['id_afectacion'] > 1 && $producto['mt_ajuste'] > 0) { // Si el producto tiene algún tipo de afectación y el monto de ajuste es mayor a 0

                                $documento_cuenta_contableS11 = new DocumentosCuentas;
                                $documento_cuenta_contableS11->id_documento = $documento->id_documento;
                                $documento_cuenta_contableS11->concepto = $cuentaSeccionAjuste->descripcion_movimiento . ' ' . $factura->no_documento;
                                $documento_cuenta_contableS11->debe = $producto['mt_ajuste'];
                                $documento_cuenta_contableS11->haber = 0;
                                $documento_cuenta_contableS11->debe_org = $producto['mt_ajuste'];
                                $documento_cuenta_contableS11->haber_org = 0;
                                $documento_cuenta_contableS11->id_moneda = 1;
                                $documento_cuenta_contableS11->id_centro = 1;///cambiar centro de costo ingreso

                                /* revisar */
                                if (in_array($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                                    if ($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                        if ($documento_cuenta_contableS11->haber > 0) {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                        } else {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                        }
                                    } else {
                                        if ($documento_cuenta_contableS11->debe > 0) {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                        } else {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                        }
                                    }
                                } else {
                                    $documento_cuenta_contableS11->id_centro = null;
                                }

                                $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccionAjuste->id_cuenta_contable;
                                $documento_cuenta_contableS11->cta_contable = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
                                $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
                                $documento_cuenta_contableS11->save();
                                /*///////////////////////////FIN SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
                            }
                        }

                    }
                    /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/
                }

                /*/////////////////////////////FIN FACTURA CREDITO + DESCUENTO/////////////////////////////////////////
                  /////////////////////////////id_tipo_configuracion = 5////////////////////////////////////////////////*/


                /*/////////////////////////////FACTURA CREDITO + BONIFICACION/////////////////////////////////////////
                /////////////////////////////id_tipo_configuracion = 6////////////////////////////////////////////////*/

                // en uso
                elseif (($request->id_tipo === 2 || $request->id_tipo === 3)) {

                    /*///////////////////////////SECCION 1 - CUENTAS POR COBRAR////////////////////////////////////////*/

                    //Definición de parametros
                    $nombre_seccion_cuentas_cobrar = 'ClienteVenta';
                    $id_tipo_configuracion = $factura_credito_bonif;
                    //Obtener datos de BD con parametros
                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_cuentas_cobrar)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                    $documento_cuenta_contableS2a = new DocumentosCuentas;
                    $documento_cuenta_contableS2a->id_documento = $documento->id_documento;
                    $documento_cuenta_contableS2a->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                    // Validar cálculos - metodo para grabar facturas de crédito
                    if ($currency_id->valor === 1) {
                        $documento_cuenta_contableS2a->debe = $request->mt_subtotal_sin_iva + $factura->mt_impuesto;//$factura->mt_deuda - $factura->mt_retencion - $factura->mt_retencion_imi - $factura->mt_descuento; //-$factura->mt_descuento
                        $documento_cuenta_contableS2a->haber = 0;
                        $documento_cuenta_contableS2a->debe_org = ($request->mt_subtotal_sin_iva + $factura->mt_impuesto) * $factura->t_cambio;//$factura->mt_deuda - $factura->mt_retencion - $factura->mt_retencion_imi - $factura->mt_descuento; //-$factura->mt_descuento
                        $documento_cuenta_contableS2a->haber_org = 0;
                    } else {
                        $documento_cuenta_contableS2a->debe = $request->mt_subtotal_sin_iva + $factura->mt_impuesto;//$factura->mt_deuda_me - $factura->mt_retencion - $factura->mt_retencion_imi - $factura->mt_descuento; //-$factura->mt_descuento
                        $documento_cuenta_contableS2a->haber = 0;
                        $documento_cuenta_contableS2a->debe_org = ($request->mt_subtotal_sin_iva + $factura->mt_impuesto) * $factura->t_cambio;//$factura->mt_deuda_me - $factura->mt_retencion - $factura->mt_retencion_imi - $factura->mt_descuento; //-$factura->mt_descuento
                        $documento_cuenta_contableS2a->haber_org = 0;
                    }

                    $documento_cuenta_contableS2a->id_moneda = $currency_id->valor;
                    if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                        if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                            if ($documento_cuenta_contableS2a->haber > 0) {
                                $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_ingreso;
                            } else {
                                $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_costo;
                            }
                        } else {
                            if ($documento_cuenta_contableS2a->debe > 0) {
                                $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_costo;
                            } else {
                                $documento_cuenta_contableS2a->id_centro = $zonax->id_centro_ingreso;
                            }
                        }
                    } else {
                        $documento_cuenta_contableS2a->id_centro = null;
                    }

                    $documento_cuenta_contableS2a->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                    $documento_cuenta_contableS2a->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS2a->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                    $documento_cuenta_contableS2a->save();


                    /*///////////////////////////FIN SECCION 1 - CUENTAS POR COBRAR////////////////////////////////////////*/

                    /*///////////////////////////SECCION 2 - COMERCIALIZACION////////////////////////////////////////*/
                    if ($factura->mt_descuento > 0) { //Si el monto de descuento en factura es mayor a 0

                        $nombre_seccion_descuentos = 'PorComercializacion';
                        $id_tipo_configuracion = $factura_credito_bonif;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS2 = new DocumentosCuentas;
                            $documento_cuenta_contableS2->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS2->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS2->debe = $factura->mt_descuento;
                            $documento_cuenta_contableS2->haber = 0;
                            $documento_cuenta_contableS2->debe_org = $factura->mt_descuento;
                            $documento_cuenta_contableS2->haber_org = 0;
                            $documento_cuenta_contableS2->id_moneda = 1;
                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS2->haber > 0) {
                                        $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS2->debe > 0) {
                                        $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS2->id_centro = null;
                            }

                            $documento_cuenta_contableS2->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS2->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS2->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS2->save();
                        }

                    }
                    /*///////////////////////////FIN SECCION 2 - COMERCIALIZACION ////////////////////////////////////////*/


                    /*///////////////////////////SECCION 4 - IR////////////////////////////////////////*/


                    if ($request->aplicaIR && $factura->mt_retencion > 0) { //Si factura aplica IR y monto retención mayor a 0

                        $nombre_seccion_retencion = 'ImpuestoRentaAnual';
                        $id_tipo_configuracion = $factura_credito_bonif;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS4 = new DocumentosCuentas;
                            $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS4->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS4->debe = $factura->mt_retencion;
                            $documento_cuenta_contableS4->haber = 0;
                            $documento_cuenta_contableS4->debe_org = $factura->mt_retencion;
                            $documento_cuenta_contableS4->haber_org = 0;
                            $documento_cuenta_contableS4->id_moneda = 1;
                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS4->haber > 0) {
                                        $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS4->debe > 0) {
                                        $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS4->id_centro = null;
                            }
                            $documento_cuenta_contableS4->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;

                            $documento_cuenta_contableS4->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS4->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS4->save();
                        }

                    }

                    /*///////////////////////////FIN SECCION 4 - IR ////////////////////////////////////////*/

                    /*///////////////////////////SECCION 5 - COSTO VENTA////////////////////////////////////////*/


                    if ($total_costo_producto > 0 || $total_venta_producto > 0 || $total_venta_servicios_procesamiento > 0 || $total_venta_servicios_asesoria > 0) { //Si el total de costo de productos es mayor a 0

                        $nombre_seccion_costo_prod = 'VentaProducto';
                        $id_tipo_configuracion = $factura_credito_bonif;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_costo_prod)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1 && $total_venta_producto > 0) {
                            $documento_cuenta_contableS5 = new DocumentosCuentas;
                            $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS5->debe = 0;
                            $documento_cuenta_contableS5->haber = $total_venta_producto_me - $factura->mt_descuento;
                            $documento_cuenta_contableS5->debe_org = 0;
                            $documento_cuenta_contableS5->haber_org = ($total_venta_producto_me - $factura->mt_descuento) * $factura->t_cambio;
                            $documento_cuenta_contableS5->id_moneda = $currency_id->valor;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS5->haber > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS5->debe > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS5->id_centro = null;
                            }

                            $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS5->save();
                        }

                        $nombre_seccion_venta_admon = 'ServAdmon';
                        $id_tipo_configuracion = $factura_credito_bonif;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_venta_admon)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1 && $total_venta_servicios_asesoria > 0) {
                            $documento_cuenta_contableS5 = new DocumentosCuentas;
                            $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS5->debe = 0;
                            $documento_cuenta_contableS5->haber = $total_venta_servicios_asesoria_me - $factura->mt_descuento;
                            $documento_cuenta_contableS5->debe_org = 0;
                            $documento_cuenta_contableS5->haber_org = $total_venta_servicios_asesoria - $factura->mt_descuento;
                            $documento_cuenta_contableS5->id_moneda = $currency_id->valor;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS5->haber > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS5->debe > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS5->id_centro = null;
                            }

                            $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS5->save();
                        }

                        $nombre_seccion_costo_prod = 'ServProcesamiento';
                        $id_tipo_configuracion = $factura_credito_bonif;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_costo_prod)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1 && $total_venta_servicios_procesamiento > 0) {
                            $documento_cuenta_contableS5 = new DocumentosCuentas;
                            $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS5->debe = 0;
                            $documento_cuenta_contableS5->haber = $total_venta_servicios_procesamiento_me - $factura->mt_descuento;
                            $documento_cuenta_contableS5->debe_org = 0;
                            $documento_cuenta_contableS5->haber_org = $total_venta_servicios_procesamiento - $factura->mt_descuento;
                            $documento_cuenta_contableS5->id_moneda = $currency_id->valor;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS5->haber > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS5->debe > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS5->id_centro = null;
                            }

                            $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS5->save();
                        }

                    }

                    /*///////////////////////////FIN SECCION 5 - COSTO VENTA ////////////////////////////////////////*/

                    /*///////////////////////////SECCION 6 - IMPUESTO SOBREVENTA//////////////////////////////////////////////*/

                    if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) {
                        $total_ventax = $total_venta_producto + $total_costo_servicios_asesoria + $total_venta_servicios_procesamiento;

                        $nombre_seccion_imp_venta = 'ImpuestoSobreVenta';
                        $id_tipo_configuracion = $factura_credito_bonif;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_imp_venta)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS6 = new DocumentosCuentas;
                            $documento_cuenta_contableS6->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS6->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS6->debe = $factura->mt_retencion_imi;
                            $documento_cuenta_contableS6->haber = 0;
                            $documento_cuenta_contableS6->debe_org = $factura->mt_retencion_imi;
                            $documento_cuenta_contableS6->haber_org = 0;
                            $documento_cuenta_contableS6->id_moneda = 1;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS6->haber > 0) {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS6->debe > 0) {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS6->id_centro = null;
                            }

                            $documento_cuenta_contableS6->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS6->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS6->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS6->save();
                        }

                    }

                    /*///////////////////////////FIN SECCION 6 - IMPUESTO SOBRE VENTA ////////////////////////////////////////*/

                    /*///////////////////////////SSECCION 7 - D. GENERAL INGRESOS/////////////////////////////////////////////*/

                    if ($request->aplicaIR && $factura->mt_retencion > 0) {
                        $nombre_seccion_pago_minimo_dgi = 'DireccionGeneralIngreso';
                        $id_tipo_configuracion = $factura_credito_bonif;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_pago_minimo_dgi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS7 = new DocumentosCuentas;
                            $documento_cuenta_contableS7->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS7->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS7->debe = 0;
                            $documento_cuenta_contableS7->haber = $factura->mt_retencion;
                            $documento_cuenta_contableS7->debe_org = 0;
                            $documento_cuenta_contableS7->haber_org = $factura->mt_retencion * $factura->t_cambio;
                            $documento_cuenta_contableS7->id_moneda = 1;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS7->haber > 0) {
                                        $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS7->debe > 0) {
                                        $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS7->id_centro = null;
                            }

                            $documento_cuenta_contableS7->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS7->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS7->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS7->save();
                        }

                    }

                    /*///////////////////////////FIN SECCION 7 - D. GENERAL INGRESOS//////////////////////////////////////////*/


                    /*///////////////////////////SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/
                    if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) { //Si factura aplica IMI y monto retención imi mayor a 0

                        $nombre_seccion_retencion_imi = 'AlcaldiasMunicipales';
                        $id_tipo_configuracion = $factura_credito_bonif;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion_imi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        $documento_cuenta_contableS8 = new DocumentosCuentas;
                        $documento_cuenta_contableS8->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS8->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS8->debe = 0;
                        $documento_cuenta_contableS8->haber = $factura->mt_retencion_imi;
                        $documento_cuenta_contableS8->debe_org = 0;
                        $documento_cuenta_contableS8->haber_org = $factura->mt_retencion_imi;;
                        $documento_cuenta_contableS8->id_moneda = 1;
                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS8->haber > 0) {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS8->debe > 0) {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS8->id_centro = null;
                        }
                        $documento_cuenta_contableS8->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS8->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS8->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS8->save();
                    }
                    /*///////////////////////////FIN SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/

                    /*///////////////////////////SECCION 9 - IVA//////////////////////////////////////////*/
                    if (!$factura->impuesto_exonerado && $factura->mt_impuesto > 0) { // si no aplica a impuesto exonerado y monto impuesto mayor a 0

                        $nombre_seccion_iva = 'IvaRet';
                        $id_tipo_configuracion = $factura_credito_bonif;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_iva)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS9 = new DocumentosCuentas;
                            $documento_cuenta_contableS9->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS9->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS9->debe = 0;
                            $documento_cuenta_contableS9->haber = $factura->mt_impuesto;
                            $documento_cuenta_contableS9->debe_org = 0;
                            $documento_cuenta_contableS9->haber_org = $factura->mt_impuesto * $factura->t_cambio;
                            $documento_cuenta_contableS9->id_moneda = $currency_id->valor;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS9->haber > 0) {
                                        $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS9->debe > 0) {
                                        $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS9->id_centro = null;
                            }

                            $documento_cuenta_contableS9->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS9->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS9->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS9->save();
                        }

                    }
                    /*///////////////////////////FIN SECCION 9 - IVA//////////////////////////////////////////*/

                    /*///////////////////////////SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/
                    if ($total_costo_producto > 0) { //So el total de costo de productos es mayor a 0

                        $nombre_seccion_inventario = 'ArticuloBodegaCosto';
                        $id_tipo_configuracion = $factura_credito_bonif;


                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_inventario)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS10 = new DocumentosCuentas;
                            $documento_cuenta_contableS10->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS10->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS10->debe = 0;
                            $documento_cuenta_contableS10->haber = $total_costo_producto_me;
                            $documento_cuenta_contableS10->debe_org = 0;
                            $documento_cuenta_contableS10->haber_org = $total_costo_producto;
                            $documento_cuenta_contableS10->id_moneda = 1;
                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS10->haber > 0) {
                                        $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                                    }
                                } else if ($documento_cuenta_contableS10->debe > 0) {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                                }
                            } else {
                                $documento_cuenta_contableS10->id_centro = null;
                            }

                            $documento_cuenta_contableS10->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS10->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS10->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS10->save();
                        }


                    }
                    /*///////////////////////////FIN SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/


                    /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/
//                    if ($total_venta_producto > 0) { //Si total venta productos es mayor a 0
//
//                        $nombre_seccion_ingreso_productos = 'Nacionales';
//                        $id_tipo_configuracion = $factura_credito_bonif;
//
//                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_ingreso_productos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
//
//
//                        if($cuentaSeccion->estado === 1){
//                            $documento_cuenta_contableS11 = new DocumentosCuentas;
//                            $documento_cuenta_contableS11->id_documento = $documento->id_documento;
//                            $documento_cuenta_contableS11->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
//                            $documento_cuenta_contableS11->debe = 0;
//                            $documento_cuenta_contableS11->haber = $total_venta_producto;
//                            $documento_cuenta_contableS11->debe_org = 0;
//                            $documento_cuenta_contableS11->haber_org = $total_venta_producto;
//                            $documento_cuenta_contableS11->id_moneda = 1;
//
//                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
//                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
//                                    if ($documento_cuenta_contableS11->haber > 0) {
//                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
//                                    } else {
//                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
//                                    }
//                                } else {
//                                    if ($documento_cuenta_contableS11->debe > 0) {
//                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
//                                    } else {
//                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
//                                    }
//                                }
//                            } else {
//                                $documento_cuenta_contableS11->id_centro = null;
//                            }
//                            $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
//                            $documento_cuenta_contableS11->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
//                            $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
//                            $documento_cuenta_contableS11->save();
//                        }
//
//
//                        /*///////////////////////////SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
//                        $nombre_seccion_descuentos = 'PorBonificacion';
//                        $id_tipo_configuracion = $factura_credito_bonif;
//
//                        $cuentaSeccionAjuste = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
//
//                        foreach ($request->detalleProductos as $producto) {
//                            if ($producto['afectacionx']['id_afectacion'] > 1 && $producto['mt_ajuste'] > 0) { // Si el producto tiene algún tipo de afectación y el monto de ajuste es mayor a 0
//
//                                $documento_cuenta_contableS11 = new DocumentosCuentas;
//                                $documento_cuenta_contableS11->id_documento = $documento->id_documento;
//                                $documento_cuenta_contableS11->concepto = $cuentaSeccionAjuste->descripcion_movimiento . ' ' . $factura->no_documento;
//                                $documento_cuenta_contableS11->debe = $producto['mt_ajuste'];
//                                $documento_cuenta_contableS11->haber = 0;
//                                $documento_cuenta_contableS11->debe_org = $producto['mt_ajuste'];
//                                $documento_cuenta_contableS11->haber_org = 0;
//                                $documento_cuenta_contableS11->id_moneda = 1;
//                                $documento_cuenta_contableS11->id_centro = 1;///cambiar centro de costo ingreso
//
//                                /* revisar */
//                                if (in_array($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
//                                    if ($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
//                                        if ($documento_cuenta_contableS11->haber > 0) {
//                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
//                                        } else {
//                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
//                                        }
//                                    } else {
//                                        if ($documento_cuenta_contableS11->debe > 0) {
//                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
//                                        } else {
//                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
//                                        }
//                                    }
//                                } else {
//                                    $documento_cuenta_contableS11->id_centro = null;
//                                }
//
//                                $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccionAjuste->id_cuenta_contable;
//                                $documento_cuenta_contableS11->cta_contable = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
//                                $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
//                                $documento_cuenta_contableS11->save();
//                                /*///////////////////////////FIN SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
//                            }
//                        }
//
//                    }
                    /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/
                }

                /*/////////////////////////////FIN FACTURA CREDITO + BONIFICACION/////////////////////////////////////////
                  /////////////////////////////id_tipo_configuracion = 6////////////////////////////////////////////////*/

                /*/////////////////////////////FACTURA CONTADO TARJ CRED/////////////////////////////////////////
                   /////////////////////////////id_tipo_configuracion = 1////////////////////////////////////////////////*/
                else if ($request->id_tipo === 1 || $request->id_tipo === '1') {

                    /*///////////////////////////SECCION 1 - FONDO POR DEPOSITAR////////////////////////////////////////*/
                    //Definición de parametros
                    $nombre_seccion_ingreso_nacional = 'FondoDepositar';
                    $id_tipo_configuracion = $factura_contado_cred_tarj;
                    //Obtener datos de BD con parametros
                    $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_ingreso_nacional)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
                    //Inicio de registro de movimientos
                    if ($cuentaSeccion->estado === 1) {
                        $documento_cuenta_contableS1 = new DocumentosCuentas;
                        $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                        $documento_cuenta_contableS1->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                        $documento_cuenta_contableS1->debe = ($request->mt_subtotal_sin_iva + $request->mt_impuesto);//- $factura->mt_descuento;//$request->mt_subtotal - $factura->mt_deuda - $factura->mt_retencion - $factura->mt_retencion_imi - $factura->mt_descuento - $factura->mt_ajuste + $factura->mt_impuesto;
                        $documento_cuenta_contableS1->haber = 0;
                        $documento_cuenta_contableS1->debe_org = ($request->mt_subtotal_sin_iva + $request->mt_impuesto) * $factura->t_cambio;//- $factura->mt_descuento;//$request->mt_subtotal - $factura->mt_deuda - $factura->mt_retencion - $factura->mt_retencion_imi - $factura->mt_descuento - $factura->mt_ajuste + $factura->mt_impuesto;;
                        $documento_cuenta_contableS1->haber_org = 0;
                        $documento_cuenta_contableS1->id_moneda = $currency_id->valor;

                        if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                            if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                if ($documento_cuenta_contableS1->haber > 0) {
                                    $documento_cuenta_contableS1->id_centro = $zonax->id_centro_ingreso;
                                } else {
                                    $documento_cuenta_contableS1->id_centro = $zonax->id_centro_costo;
                                }
                            } else {
                                if ($documento_cuenta_contableS1->debe > 0) {
                                    $documento_cuenta_contableS1->id_centro = $zonax->id_centro_costo;
                                } else {
                                    $documento_cuenta_contableS1->id_centro = $zonax->id_centro_ingreso;
                                }
                            }
                        } else {
                            $documento_cuenta_contableS1->id_centro = null;
                        }

                        $documento_cuenta_contableS1->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                        $documento_cuenta_contableS1->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS1->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                        $documento_cuenta_contableS1->save();
                    }
                    /*///////////////////////////FIN SECCION 1 - FONDO POR DEPOSITAR////////////////////////////////////////*/

                    /*///////////////////////////SECCION 2 - COMERCIALIZACION////////////////////////////////////////*/
                    if ($factura->mt_descuento > 0) { //Si el monto de descuento en factura es mayor a 0

                        $nombre_seccion_descuentos = 'PorComercializacion';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS2 = new DocumentosCuentas;
                            $documento_cuenta_contableS2->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS2->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS2->debe = $factura->mt_descuento;
                            $documento_cuenta_contableS2->haber = 0;
                            $documento_cuenta_contableS2->debe_org = $factura->mt_descuento;
                            $documento_cuenta_contableS2->haber_org = 0;
                            $documento_cuenta_contableS2->id_moneda = $currency_id->valor;
                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS2->haber > 0) {
                                        $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS2->debe > 0) {
                                        $documento_cuenta_contableS2->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS2->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS2->id_centro = null;
                            }

                            $documento_cuenta_contableS2->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS2->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS2->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS2->save();
                        }
                    }
                    /*///////////////////////////FIN SECCION 2 - COMERCIALIZACION ////////////////////////////////////////*/


                    /*///////////////////////////SECCION 4 - IR////////////////////////////////////////*/


                    if ($request->aplicaIR && $factura->mt_retencion > 0) { //Si factura aplica IR y monto retención mayor a 0

                        $nombre_seccion_retencion = 'ImpuestoRentaAnual';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS4 = new DocumentosCuentas;
                            $documento_cuenta_contableS4->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS4->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS4->debe = $factura->mt_retencion;
                            $documento_cuenta_contableS4->haber = 0;
                            $documento_cuenta_contableS4->debe_org = $factura->mt_retencion;
                            $documento_cuenta_contableS4->haber_org = 0;
                            $documento_cuenta_contableS4->id_moneda = $currency_id->valor;
                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS4->haber > 0) {
                                        $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS4->debe > 0) {
                                        $documento_cuenta_contableS4->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS4->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS4->id_centro = null;
                            }
                            $documento_cuenta_contableS4->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;

                            $documento_cuenta_contableS4->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS4->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS4->save();
                        }
                    }

                    /*///////////////////////////FIN SECCION 4 - IR ////////////////////////////////////////*/

                    /*///////////////////////////SECCION 5 - COSTO VENTA////////////////////////////////////////*/


                    if ($total_costo_producto > 0 || $total_venta_producto > 0 || $total_venta_servicios_procesamiento > 0 || $total_venta_servicios_asesoria > 0) { //Si el total de costo de productos es mayor a 0

                        // Contabilización de costos de productos

                        $nombre_seccion_costo_prod = 'CostoVentaArti';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_costo_prod)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1 && $total_costo_producto > 0) {
                            $documento_cuenta_contableS5 = new DocumentosCuentas;
                            $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS5->debe = $total_costo_producto_me;
                            $documento_cuenta_contableS5->haber = 0;
                            $documento_cuenta_contableS5->debe_org = $total_costo_producto;
                            $documento_cuenta_contableS5->haber_org = 0;
                            $documento_cuenta_contableS5->id_moneda = $currency_id->valor;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS5->haber > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS5->debe > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS5->id_centro = null;
                            }

                            $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS5->save();

                        }

                        // Contabilizacion de precios de productos

                        $nombre_seccion_costo_prod = 'VentaProduct';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_costo_prod)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1 && $total_venta_producto > 0) {
                            $documento_cuenta_contableS5 = new DocumentosCuentas;
                            $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS5->debe = 0;
                            $documento_cuenta_contableS5->haber = ($total_venta_producto_me - $factura->mt_descuento);
                            $documento_cuenta_contableS5->debe_org = 0;
                            $documento_cuenta_contableS5->haber_org = ($total_venta_producto_me - $factura->mt_descuento) * $factura->t_cambio;
                            $documento_cuenta_contableS5->id_moneda = $currency_id->valor;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS5->haber > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS5->debe > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS5->id_centro = null;
                            }

                            $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS5->save();

                        }

                        /**
                         * Contabilización para facturación de servicios
                         */

                        $nombre_seccion_costo_prod = 'CostoVentaServ';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_costo_prod)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1 && $total_venta_servicios_procesamiento > 0) {
                            $documento_cuenta_contableS5 = new DocumentosCuentas;
                            $documento_cuenta_contableS5->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS5->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS5->debe = 0;
                            $documento_cuenta_contableS5->haber = ($total_venta_servicios_procesamiento_me - $factura->mt_descuento);
                            $documento_cuenta_contableS5->debe_org = 0;
                            $documento_cuenta_contableS5->haber_org = $total_venta_servicios_procesamiento - $factura->mt_descuento;
                            $documento_cuenta_contableS5->id_moneda = $currency_id->valor;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS5->haber > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS5->debe > 0) {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS5->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS5->id_centro = null;
                            }

                            $documento_cuenta_contableS5->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS5->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS5->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS5->save();

                        }

                        $nombre_seccion_costo_serv2 = 'CostoVentaServ2';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_costo_serv2)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1 && $total_venta_servicios_asesoria > 0) {
                            $documento_cuenta_contableS6 = new DocumentosCuentas;
                            $documento_cuenta_contableS6->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS6->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS6->debe = 0;
                            $documento_cuenta_contableS6->haber = ($total_venta_servicios_asesoria_me - $factura->mt_descuento);
                            $documento_cuenta_contableS6->debe_org = 0;
                            $documento_cuenta_contableS6->haber_org = ($total_venta_servicios_asesoria - $factura->mt_descuento);
                            $documento_cuenta_contableS6->id_moneda = $currency_id->valor;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS6->haber > 0) {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS6->debe > 0) {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS6->id_centro = null;
                            }

                            $documento_cuenta_contableS6->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS6->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS6->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS6->save();

                        }

                    }

                    /*///////////////////////////FIN SECCION 5 - COSTO VENTA ////////////////////////////////////////*/

                    /*///////////////////////////SECCION 6 - IMPUESTO SOBREVENTA//////////////////////////////////////////////*/

                    if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) {
                        $total_ventax = $total_venta_producto + $total_venta_servicios_procesamiento + $total_costo_servicios_asesoria;

                        $nombre_seccion_imp_venta = 'ImpuestoSobreVenta';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_imp_venta)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS6 = new DocumentosCuentas;
                            $documento_cuenta_contableS6->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS6->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS6->debe = $factura->mt_retencion_imi;
                            $documento_cuenta_contableS6->haber = 0;
                            $documento_cuenta_contableS6->debe_org = $factura->mt_retencion_imi;
                            $documento_cuenta_contableS6->haber_org = 0;
                            $documento_cuenta_contableS6->id_moneda = 1;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS6->haber > 0) {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS6->debe > 0) {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS6->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS6->id_centro = null;
                            }

                            $documento_cuenta_contableS6->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS6->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS6->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS6->save();
                        }
                    }

                    /*///////////////////////////FIN SECCION 6 - IMPUESTO SOBRE VENTA ////////////////////////////////////////*/

                    /*///////////////////////////SSECCION 7 - D. GENERAL INGRESOS/////////////////////////////////////////////*/

                    if ($request->aplicaIR && $factura->mt_retencion > 0) {
                        $nombre_seccion_pago_minimo_dgi = 'DireccionGeneralIngreso';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_pago_minimo_dgi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS7 = new DocumentosCuentas;
                            $documento_cuenta_contableS7->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS7->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS7->debe = 0;
                            $documento_cuenta_contableS7->haber = $factura->mt_retencion;
                            $documento_cuenta_contableS7->debe_org = 0;
                            $documento_cuenta_contableS7->haber_org = $factura->mt_retencion;
                            $documento_cuenta_contableS7->id_moneda = 1;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS7->haber > 0) {
                                        $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS7->debe > 0) {
                                        $documento_cuenta_contableS7->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS7->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS7->id_centro = null;
                            }

                            $documento_cuenta_contableS7->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS7->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS7->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS7->save();
                        }
                    }

                    /*///////////////////////////FIN SECCION 7 - D. GENERAL INGRESOS//////////////////////////////////////////*/


                    /*///////////////////////////SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/
                    if ($request->aplicaIMI && $factura->mt_retencion_imi > 0) { //Si factura aplica IMI y monto retención imi mayor a 0

                        $nombre_seccion_retencion_imi = 'AlcaldiasMunicipales';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_retencion_imi)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();
                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS8 = new DocumentosCuentas;
                            $documento_cuenta_contableS8->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS8->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS8->debe = 0;
                            $documento_cuenta_contableS8->haber = $factura->mt_retencion_imi;
                            $documento_cuenta_contableS8->debe_org = 0;
                            $documento_cuenta_contableS8->haber_org = $factura->mt_retencion_imi;
                            $documento_cuenta_contableS8->id_moneda = 1;
                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS8->haber > 0) {
                                        $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS8->debe > 0) {
                                        $documento_cuenta_contableS8->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS8->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS8->id_centro = null;
                            }
                            $documento_cuenta_contableS8->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS8->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS8->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS8->save();
                        }
                    }
                    /*///////////////////////////FIN SECCION 8 - ALCALDIAS MUNICIPALES//////////////////////////////////////////*/

                    /*///////////////////////////SECCION 9 - IVA//////////////////////////////////////////*/
                    if (!$factura->impuesto_exonerado && $factura->mt_impuesto > 0) { // si aplica a impuesto exonerado y monto impuesto mayor a 0

                        $nombre_seccion_iva = 'DireccionGeneralIngresoH';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_iva)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS9 = new DocumentosCuentas;
                            $documento_cuenta_contableS9->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS9->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS9->debe = 0;
                            $documento_cuenta_contableS9->haber = $factura->mt_impuesto;
                            $documento_cuenta_contableS9->debe_org = 0;
                            $documento_cuenta_contableS9->haber_org = $factura->mt_impuesto * $factura->t_cambio;
                            $documento_cuenta_contableS9->id_moneda = 1;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS9->haber > 0) {
                                        $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS9->debe > 0) {
                                        $documento_cuenta_contableS9->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS9->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS9->id_centro = null;
                            }

                            $documento_cuenta_contableS9->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS9->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS9->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS9->save();
                        }
                    }
                    /*///////////////////////////FIN SECCION 9 - IVA//////////////////////////////////////////*/

                    /*///////////////////////////SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/
                    if ($total_costo_producto > 0) { //So el total de costo de productos es mayor a 0

                        $nombre_seccion_inventario = 'ArticuloBodegaCosto';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;


                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_inventario)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS10 = new DocumentosCuentas;
                            $documento_cuenta_contableS10->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS10->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS10->debe = 0;
                            $documento_cuenta_contableS10->haber = $total_costo_producto_me;
                            $documento_cuenta_contableS10->debe_org = 0;
                            $documento_cuenta_contableS10->haber_org = $total_costo_producto;
                            $documento_cuenta_contableS10->id_moneda = 1;
                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS10->haber > 0) {
                                        $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS10->debe > 0) {
                                        $documento_cuenta_contableS10->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS10->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS10->id_centro = null;
                            }

                            $documento_cuenta_contableS10->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS10->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS10->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];

                            $documento_cuenta_contableS10->save();
                        }
                    }
                    /*///////////////////////////FIN SECCION 10 - ART. BODEGA AL COSTO//////////////////////////////////////////*/


                    /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/
                    if ($total_venta_producto > 0) { //Si total venta productos es mayor a 0

                        $nombre_seccion_ingreso_productos = 'Nacionales';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccion = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_ingreso_productos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        if ($cuentaSeccion->estado === 1) {
                            $documento_cuenta_contableS11 = new DocumentosCuentas;
                            $documento_cuenta_contableS11->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS11->concepto = $cuentaSeccion->descripcion_movimiento . ' ' . $factura->no_documento;
                            $documento_cuenta_contableS11->debe = 0;
                            $documento_cuenta_contableS11->haber = $total_venta_producto_me;
                            $documento_cuenta_contableS11->debe_org = 0;
                            $documento_cuenta_contableS11->haber_org = $total_venta_producto_me * $factura->t_cambio;
                            $documento_cuenta_contableS11->id_moneda = $currency_id->valor;

                            if (in_array($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas, true)) {
                                if ($cuentaSeccion->configuracionFacturacuentaContable['id_tipo_cuenta'] === 4) {//cuenta ingreso
                                    if ($documento_cuenta_contableS11->haber > 0) {
                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                    } else {
                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                    }
                                } else {
                                    if ($documento_cuenta_contableS11->debe > 0) {
                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                    } else {
                                        $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                    }
                                }
                            } else {
                                $documento_cuenta_contableS11->id_centro = null;
                            }
                            $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccion->id_cuenta_contable;
                            $documento_cuenta_contableS11->cta_contable = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccion->configuracionFacturacuentaContable['cta_contable'];
                            $documento_cuenta_contableS11->save();
                        }
                        /*///////////////////////////SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
                        $nombre_seccion_descuentos = 'PorBonificacion';
                        $id_tipo_configuracion = $factura_contado_cred_tarj;

                        $cuentaSeccionAjuste = FacturacionConfiguracion::where('nombre_seccion', $nombre_seccion_descuentos)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionFacturacuentaContable')->first();

                        foreach ($request->detalleProductos as $producto) {
                            if ($producto['afectacionx']['id_afectacion'] > 1 && $producto['mt_ajuste'] > 0) { // Si el producto tiene algún tipo de afectación y el monto de ajuste es mayor a 0

                                $documento_cuenta_contableS11 = new DocumentosCuentas;
                                $documento_cuenta_contableS11->id_documento = $documento->id_documento;
                                $documento_cuenta_contableS11->concepto = $cuentaSeccionAjuste->descripcion_movimiento . ' ' . $factura->no_documento;
                                $documento_cuenta_contableS11->debe = $producto['mt_ajuste'];
                                $documento_cuenta_contableS11->haber = 0;
                                $documento_cuenta_contableS11->debe_org = $producto['mt_ajuste'];
                                $documento_cuenta_contableS11->haber_org = 0;
                                $documento_cuenta_contableS11->id_moneda = 1;
                                $documento_cuenta_contableS11->id_centro = 1;///cambiar centro de costo ingreso

                                /* revisar */
                                if (in_array($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'], $tipos_cuentas)) {
                                    if ($cuentaSeccionAjuste->configuracionFacturacuentaContable['id_tipo_cuenta'] == 4) {//cuenta ingreso
                                        if ($documento_cuenta_contableS11->haber > 0) {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                        } else {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                        }
                                    } else {
                                        if ($documento_cuenta_contableS11->debe > 0) {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_costo;
                                        } else {
                                            $documento_cuenta_contableS11->id_centro = $zonax->id_centro_ingreso;
                                        }
                                    }
                                } else {
                                    $documento_cuenta_contableS11->id_centro = null;
                                }

                                $documento_cuenta_contableS11->id_cuenta_contable = $cuentaSeccionAjuste->id_cuenta_contable;
                                $documento_cuenta_contableS11->cta_contable = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
                                $documento_cuenta_contableS11->cta_contable_padre = $cuentaSeccionAjuste->configuracionFacturacuentaContable['cta_contable'];
                                $documento_cuenta_contableS11->save();
                                /*///////////////////////////FIN SECCION 11.1 - BONIFICACION////////////////////////////////////////*/
                            }
                        }

                    }
                    /*///////////////////////////FIN SECCION 11 - NACIONALES//////////////////////////////////////////*/
                }
                /*/////////////////////////////FIN FACTURA CONTADO TARJ CRED /////////////////////////////////////////
                  /////////////////////////////id_tipo_configuracion = 1////////////////////////////////////////////////*/

                /*/////////////////////////////*/
                /* TERMINA MOVIMIENTO CONTABLE*/
                #endregion contabilizacion factura

                #region [Contabilización inventario]
                /**
                 * INICIA MOVIMIENTO CONTABLE DE INVENTARIO
                 */

                // Si es el tipo de producto es distinto de servicio
                if ($producto['productox']['id_tipo_producto'] !== 2) {
                    $documento_inventario = new DocumentosContables();
                    $tipo = TiposDocumentos::find(10);  //Tipo comprobante de inventario
                    $fecha = date("Y-m-d H:i:s");
                    $codigo = $documento_inventario->obtenerCodigoDocumento(array('id_tipo_doc' => $tipo->id_tipo_doc, 'fecha_doc' => $fecha));

                    $nuevo_codigo = json_decode($codigo[0]);

                    date_default_timezone_set('America/Managua');

                    $documento_inventario->num_documento = $tipo->prefijo . '-' . $nuevo_codigo->secuencia;
                    $documento_inventario->fecha_emision = $factura->f_factura;//date('Y-m-d');
                    $documento_inventario->codigo_documento = $nuevo_codigo->secuencia;


                    $date = DateTime::createFromFormat("Y-m-d", $documento_inventario->fecha_emision);

                    $documento_inventario->id_periodo_fiscal = $periodo[0]->id_periodo_fiscal;

                    $documento_inventario->id_tipo_doc = 10; // Comprobante de inventario
                    $documento_inventario->valor = $total_costo_producto;
                    $documento_inventario->valor_me = $total_costo_producto_me;
                    if ($currency_id->valor === 1) {
                        $documento_inventario->concepto = 'Registramos salida de productos por factura No. ' . $factura->no_documento . '. Monto total C$ ' . $total_costo_producto;
                    } else {
                        $documento_inventario->concepto = 'Registramos salida de productos por factura No. ' . $factura->no_documento . '. Monto total $ ' . $total_costo_producto_me;
                    }
                    $documento_inventario->id_moneda = $currency_id->valor;
                    $documento_inventario->u_creacion = Auth::user()->name;
                    $documento_inventario->estado = 1;
                    $documento_inventario->id_empresa = $usuario_empresa->id_empresa;
                    $documento_inventario->save();
                    $salida->id_documento_contable = $documento_inventario->id_documento;
                    $salida->save();

                    TiposDocumentos::find($documento_inventario->id_tipo_doc)->increment('secuencia');

                    //definición de tipo de configuración de comprobantes
                    $id_tipo_configuracion = 2; // Salida por ventas

                    if ($total_costo_producto > 0) { //|| $total_costo_me > 0

                        // Registramos contabilización de costo de venta de productos
                        $nombre_seccion_ArtCosto = 'ArtCostBod';
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionArtCosto = ConfiguracionInventario::where('nombre_seccion', $nombre_seccion_ArtCosto)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionImportacioncuentaContable')->first();
                        $cuenta_contable_art_costo = CuentasContables::find($cuentaSeccionArtCosto->id_cuenta_contaable);
                        $cuenta_contable_art_costo_padre = CuentasContables::find($cuenta_contable_art_costo->id_cuenta_padre);

                        $documento_cuenta_contableS1 = new DocumentosCuentas;
                        $documento_cuenta_contableS1->id_documento = $documento_inventario->id_documento;
                        $documento_cuenta_contableS1->concepto = $cuentaSeccionArtCosto->descripcion_movimiento . ' ' . $salida->codigo_salida;

                        if ($cuentaSeccionArtCosto->debe_haber === 1) {
                            if ($currency_id->valor === 1) {
                                $documento_cuenta_contableS1->debe = $total_costo_producto_me;
                                $documento_cuenta_contableS1->haber = 0;
                                $documento_cuenta_contableS1->debe_org = $total_costo_producto;
                                $documento_cuenta_contableS1->haber_org = 0;
                            } else {
                                $documento_cuenta_contableS1->debe = $total_costo_producto_me;
                                $documento_cuenta_contableS1->haber = 0;
                                $documento_cuenta_contableS1->debe_org = $total_costo_producto;
                                $documento_cuenta_contableS1->haber_org = 0;
                            }

                        } else {
                            if ($currency_id->valor === 1) {
                                $documento_cuenta_contableS1->debe = 0;
                                $documento_cuenta_contableS1->haber = $total_costo_producto_me;
                                $documento_cuenta_contableS1->debe_org = 0;
                                $documento_cuenta_contableS1->haber_org = $total_costo_producto;
                            } else {
                                $documento_cuenta_contableS1->debe = 0;
                                $documento_cuenta_contableS1->haber = $total_costo_producto_me;
                                $documento_cuenta_contableS1->debe_org = 0;
                                $documento_cuenta_contableS1->haber_org = $total_costo_producto;
                            }

                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_art_costo->requiere_aux === 0) {
                            $documento_cuenta_contableS1->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_art_costo->requiere_aux === 2 || $cuenta_contable_art_costo->requiere_aux === 3) {
                            $documento_cuenta_contableS1->id_centro = $cuentaSeccionArtCosto->id_centro_costo;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_art_costo->requiere_aux === 1) {
                            $documento_cuenta_contableS1->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionArtCosto->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                        $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_art_costo->id_cuenta_contable;
                        $documento_cuenta_contableS1->cta_contable = $cuenta_contable_art_costo->cta_contable;
                        $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_art_costo_padre->id_cuenta_contable;
                        $documento_cuenta_contableS1->save();

                        // Registramos contabilización de costos de salida de inventario por venta

                        $nombre_seccion_CostoVenta = 'CostVentArt';
                        //obtener datos de BD con estos paremetros
                        $cuentaSeccionCostoVentaArt = ConfiguracionInventario::where('nombre_seccion', $nombre_seccion_CostoVenta)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionImportacioncuentaContable')->first();
                        $cuenta_contable_costo_venta_art = CuentasContables::find($cuentaSeccionCostoVentaArt->id_cuenta_contaable);
                        $cuenta_contable_costo_venta_art_padre = CuentasContables::find($cuenta_contable_costo_venta_art->id_cuenta_padre);

                        $documento_cuenta_contableS1 = new DocumentosCuentas;
                        $documento_cuenta_contableS1->id_documento = $documento_inventario->id_documento;
                        $documento_cuenta_contableS1->concepto = $cuentaSeccionCostoVentaArt->descripcion_movimiento . ' ' . $salida->codigo_salida;

                        if ($cuentaSeccionCostoVentaArt->debe_haber === 1) {
                            if ($currency_id->valor === 1) {
                                $documento_cuenta_contableS1->debe = $total_costo_producto_me;
                                $documento_cuenta_contableS1->haber = 0;
                                $documento_cuenta_contableS1->debe_org = $total_costo_producto;
                                $documento_cuenta_contableS1->haber_org = 0;
                            } else {
                                $documento_cuenta_contableS1->debe = $total_costo_producto_me;
                                $documento_cuenta_contableS1->haber = 0;
                                $documento_cuenta_contableS1->debe_org = $total_costo_producto;
                                $documento_cuenta_contableS1->haber_org = 0;
                            }

                        } else {
                            if ($currency_id->valor === 1) {
                                $documento_cuenta_contableS1->debe = 0;
                                $documento_cuenta_contableS1->haber = $total_costo_producto_me;
                                $documento_cuenta_contableS1->debe_org = 0;
                                $documento_cuenta_contableS1->haber_org = $total_costo_producto;
                            } else {
                                $documento_cuenta_contableS1->debe = 0;
                                $documento_cuenta_contableS1->haber = $total_costo_producto_me;
                                $documento_cuenta_contableS1->debe_org = 0;
                                $documento_cuenta_contableS1->haber_org = $total_costo_producto;
                            }

                        }

                        //Verificación de centros de costo

                        if ($cuenta_contable_costo_venta_art->requiere_aux === 0) {
                            $documento_cuenta_contableS1->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_costo_venta_art->requiere_aux === 2 || $cuenta_contable_costo_venta_art->requiere_aux === 3) {
                            $documento_cuenta_contableS1->id_centro = $cuentaSeccionCostoVentaArt->id_centro_costo;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;

                        } else if ($cuenta_contable_costo_venta_art->requiere_aux === 1) {
                            $documento_cuenta_contableS1->id_centro = null;
                            $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionCostoVentaArt->id_cat_auxiliar_cxc;
                        }

                        $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
                        $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_costo_venta_art->id_cuenta_contable;
                        $documento_cuenta_contableS1->cta_contable = $cuenta_contable_costo_venta_art->cta_contable;
                        $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_costo_venta_art_padre->id_cuenta_contable;
                        $documento_cuenta_contableS1->save();
                    }
                }
                /*TERMINA MOVIMIENTO CONTABLE DE INVENTARIO*/
                #endregion

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'result' => '',
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
                'status' => 'fields_empty',
                'result' => $validator->messages(),
                'messages' => null
            ]);
        }
    }


    public function nueva(Request $request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $afectaciones = Afectaciones::where('estado', 1)->whereNotIn('id_afectacion', array(3, 4))->orderby('id_afectacion')->get();
        $tasa = TasasCambios::select('tasa_paralela', 'tasa')->where('fecha', date("Y-m-d"))->first();
        $vendedores = Vendedores::select(['id_vendedor', 'nombre_completo', 'id_zona'])->where('estado', 1)->get();
        $proyectos = Proyectos::select(['*'])->where('id_empresa', '=', $usuario_empresa->id_empresa)->where('estado', '=', 1)->get();
        // $sucursales = PublicSucursales::select(['id_sucursal','serie','descripcion'])->with('sucursalBodegaVentas')->get();

        if (Auth::user()->id_sucursal > 0) {
            $sucursales = Sucursales::select(['id_sucursal', 'serie', 'descripcion'])->with('sucursalBodegaVentas')
                ->where('id_sucursal', Auth::user()->id_sucursal)
                ->orderby('descripcion')->orderby('descripcion')->get();
        } else {
            $sucursales = Sucursales::select(['id_sucursal', 'serie', 'descripcion'])->with('sucursalBodegaVentas')->orderby('descripcion')
                ->get();
        }

//        $trabajador_actual = Usuarios::select('id_empleado')->where('usuario', Auth::user()->name)->first();
        $vendedor_actual = Vendedores::select(['id_vendedor', 'nombre_completo', 'id_zona'])->where('estado', 1)->first();

        $departamentos = Departamentos::with('municipiosDepartamento')->orderby('id_departamento')->get();
        $vias_pago = ViaPagos::select(['id_via_pago', 'descripcion'])->where('estado', true)->orderBy('id_via_pago')->get();
        $monedas = Monedas::where('estado', 1)->orderBy('id_moneda')->get();
        $zonas = Zonas::select(['id_zona', 'descripcion'])->where('estado', 1)->get();
        $bancos = Bancos::select(['id_banco', 'descripcion'])->where('estado', 1)->get();
        // obtener monto máximo de descuento
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $discount_max = Ajustes::where('id_ajuste', 14)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first(); // 1-cordobas 2-dolares


        // $obtener_cierre = DB::select('SELECT * from venta.cargararqueodiario(?,?)', array(14,'2020-06-10'));

        return response()->json([
            'status' => 'success',
            'result' => [
                'vias_pago' => $vias_pago,
                'monedas' => $monedas,
                'bancos' => $bancos,
                'proyectos' => $proyectos,
                'afectaciones' => $afectaciones,
                'vendedores' => $vendedores,
                'vendedor_actual' => $vendedor_actual,
                'departamentos' => $departamentos,
                'zonas' => $zonas,
                't_cambio' => $tasa,
                'sucursales' => $sucursales,
                'discount_max' => $discount_max->valor,
                // 'obtener_cierre'=>$obtener_cierre
            ],
            'messages' => null
        ]);

    }

    public function obtenerConsecutivo(Request $request)
    {
        $sucursalx = Sucursales::find($request->id_sucursal);
        //$nuevo_num = CajaBancoFacturas::select([DB::raw("COALESCE(max(no_factura),0)+1 as no_factura")])->where('serie',$request->serie)->first();


        $no_factura = $sucursalx['numero_facturacion'] + 1;
        $str_length = 8;
        $str = substr("0000000" . $no_factura, -$str_length);
        $no_documento = $request->serie . '-' . $str;

        return response()->json([
            'status' => 'success',
            'result' => [
                'no_documento_siguiente' => $no_documento,
                'no_factura' => $no_factura
            ],
            'messages' => null
        ]);
    }

    public function nuevaFacturaAjuste(Request $request)
    {

        if (Auth::user()->id_sucursal > 0) {
            $sucursales = PublicSucursales::select(['id_sucursal', 'serie', 'descripcion'])->with('sucursalBodegaVentas')->with('sucursalBodegaAjustes')
                ->where('id_sucursal', Auth::user()->id_sucursal)
                ->orderby('descripcion')->get();
        } else {
            $sucursales = PublicSucursales::select(['id_sucursal', 'serie', 'descripcion'])->with('sucursalBodegaVentas')->with('sucursalBodegaAjustes')->get();
        }

        /*$usuario_actual = AdmonUsuarios::select('id_empleado')->where('usuario',Auth::user()->usuario)->first();
        $trabajador_actual = RRHHTrabajadores::select(['id_area'])->where('id_trabajador',$usuario_actual['id_empleado'])->where('activo',true)->first();
        $area_actual = PublicSucursales::select('id_area','descripcion','activo')->where('activo', 1)->where('id_area',$trabajador_actual['id_area'])->first();
        */
        $afectaciones = CajaBancoAfectaciones::where('activo', true)->whereIn('id_afectacion', array(3, 4))->orderby('id_afectacion')->get();
        $tasa = CajaBancoTasasCambios::select('tasa_paralela', 'tasa')->where('fecha', date("Y-m-d"))->first();
        $vendedores = VentaVendedores::select(['id_vendedor', 'nombre_completo', 'id_zona'])->get();
        $departamentos = PublicDepartamentos::with('municipiosDepartamento')->orderby('id_departamento')->get();
        $vias_pago = PublicViaPagos::select(['id_via_pago', 'descripcion'])->where('activo', true)->orderBy('id_via_pago')->get();
        $monedas = CajaBancoMonedas::where('activo', 1)->orderBy('id_moneda')->get();
        $zonas = PublicZonas::select(['id_zona', 'descripcion'])->where('activo', true)->get();
        $bancos = CajaBancoBancos::select(['id_banco', 'descripcion'])->where('activo', 1)->get();
        $bodegas_ajustes = InventarioBodegas::select('id_bodega', 'descripcion')->where('activo', 1)->where('id_tipo_bodega', 4)->get();

        $productos_todos = InventarioProductos::select('id_producto', 'precio_sugerido', DB::raw('coalesce(inventario.calcular_costo_promedio(inventario.productos.id_producto),0) as costo_promedio'), 'codigo_barra', DB::raw("CONCAT(inventario.productos.descripcion,' (',inventario.productos.codigo_barra,')') AS text"))
            ->where('activo', 1)->whereIn('id_tipo_producto', array(3))
            ->whereHas('productoDetallesBaterias', function ($q) {
                $q->whereNotIn('id_submarca', array(7));
            })
            ->orderby('id_producto')->get();

        $productos_motobaterias = InventarioProductos::select('id_producto', 'precio_sugerido', DB::raw('coalesce(inventario.calcular_costo_promedio(inventario.productos.id_producto),0) as costo_promedio'), 'codigo_barra', DB::raw("CONCAT(inventario.productos.descripcion,' (',inventario.productos.codigo_barra,')') AS text"))
            ->where('activo', 1)->whereIn('id_tipo_producto', array(3))->where('condicion', 1)
            ->whereHas('productoDetallesBaterias', function ($q) {
                $q->whereIn('id_submarca', array(7));
            })
            ->orderby('id_producto')->get();

        /*['id_producto','codigo_sistema','tasa_impuesto','inventario.v_productos_venta.codigo_barra','inventario.v_productos_venta.descripcion','inventario.bodegas_productos.id_bodega_producto'
            ,DB::raw("CONCAT(inventario.v_productos_venta.descripcion,' (',inventario.v_productos_venta.codigo_barra,')') AS text"),
            'inventario.bodegas_productos.cantidad as cantidad_disponible',
            'inventario.v_productos_venta.precio_sugerido','inventario.v_productos_venta.costo_promedio','inventario.v_productos_venta.tipo_producto']
        */
        return response()->json([
            'status' => 'success',
            'result' => [
                'vias_pago' => $vias_pago,
                'monedas' => $monedas,
                'bancos' => $bancos,
                'afectaciones' => $afectaciones,
                'vendedores' => $vendedores,
                'departamentos' => $departamentos,
                'zonas' => $zonas,
                't_cambio' => $tasa,
                'sucursales' => $sucursales,
                'bodegas_ajustes' => $bodegas_ajustes,
                'productos_todos' => $productos_todos,
                'productos_motobaterias' => $productos_motobaterias,
            ],
            'messages' => null
        ]);

    }

    public function cancelarFactura(Request $request, Facturas $factura)
    {
        $rules = [
            'id_factura' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $factura = Facturas::find($request->id_factura);

            if (!empty($factura) && $factura->estado == 1) {

                try {
                    DB::beginTransaction();
                    $factura->estado = 0;
                    $factura->observacion = $factura->observacion . ' **Factura cancelada por ' . Auth::user()->usuario . ' a las ' . date("Y-m-d H:i:s");
                    $factura->save();


                    foreach ($request->factura_productos as $producto) {
                        $bodega_sub = InventarioBodegaProductos::where('id_bodega_producto', $producto['id_bodega_producto'])->first();
                        //restaurar unidades dependiendo tipo de factura

                        if ($factura->tipo_venta === 1) {
                            $bodega_sub->cantidad = $bodega_sub->cantidad - $producto['cantidad_solicitada'];
                        } elseif ($factura->tipo_venta === 3) {
                            $bodega_sub->cantidad_recuperadas = $bodega_sub->cantidad_recuperadas - $producto['cantidad_solicitada'];//salida venta recuperado
                        } elseif ($factura->tipo_venta === 4) {
                            $bodega_sub->cantidad_obsoletas = $bodega_sub->cantidad_obsoletas - $producto['cantidad_solicitada'];//salida venta obsoleto
                        }

                        //$bodega_sub->cantidad = $bodega_sub->cantidad+$producto['cantidad_solicitada'];
                        $bodega_sub->save();
                    }

                    DB::commit();

                    return response()->json([
                        'status' => 'success',
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
                    'result' => array('id_factura' => ["Datos no encontrados o la solicitud ya autorizada"]),
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

    public function obtenerFacturasCliente(Request $request, Facturas $facturas)
    {
        $facturas = $facturas->obtenerFacturasCliente($request);

        return response()->json([
            'status' => 'success',
            'result' => $facturas,
            'messages' => null
        ]);
    }


    public function reporte($ext, $id_factura)
    {
        // echo $ext;
        //$ext = 'pdf';
        $os = array("pdf");
        if (in_array($ext, $os, true)) {
            $hora_actual = time();
            // Rutas para descarga Reportes local
            $input = env('APP_URL_REPORTES') . 'InventarioFacturasProducto';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'InventarioFacturasProducto';

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
                    'id_factura' => $id_factura,
//                    'logo_empresa' => env('APP_URL_IMAGES') . $logo_empresa->valor,
//                    'nombre_empresa' => $nombre_empresa->valor,
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

            /*            exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                        print_r($output);*/

//            print_r( env('APP_URL_REPORTS').$logo_empresa->valor);
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext, $hora_actual . 'ReporteFactura.' . $ext, $headers);


        } else {
            return '';
        }
    }


    public function anular(Request $request)
    {

        $messages = [
        ];


        $rules = [
            'id_factura' => 'required|integer',
            'causa_anulacion' => 'required|string|max:100',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();
                $factura = Facturas::where('id_factura', $request->id_factura)->with('facturaProductos')->first();
                $factura->estado = 0;
                $factura->observacion = $factura['observacion'] . ' **Factura cancelada por ' . Auth::user()->usuario . ' a las ' . date("Y-m-d H:i:s") . ' Causa: ' . $request->causa_anulacion;
                $factura->save();

                if (!empty($factura->id_salida)) {
                    $salida = Salidas::find($factura->id_salida);
                    #region Anulación de comprobante de inventario
                    if (!empty($salida->id_documento_contable)) {
                        $documento_inventario = DocumentosContables::find($salida->id_documento_contable);
                        $documento_inventario->estado = 0;
                        $documento_inventario->save();
                    }
                    #endregion Anulación de comprobante de inventario

                    $entrada = Entradas::where('id_salida', $factura->id_salida)->first();
                    //print_r($salida->estado);
                    if ($salida->estado === 1) {
                        $salida->estado = 0;
                        $salida->save();

                        if ($factura->tipo_venta === 2 && !empty($entrada)) {
                            if ($entrada['estado'] === 1 || $entrada['estado'] === 0) {
                                $entrada->estado = 0;
                                $entrada->save();
                            } else {
                                DB::rollBack();
                                return response()->json([
                                    'status' => 'error',
                                    'result' => 'No se puede actualizar la factura de ajuste, la entrada ya fue recibida',
                                    'messages' => null
                                ]);
                            }
                        }

                        foreach ($factura->facturaProductos as $producto) {

                            $bodega_sub = BodegaProductos::where('id_bodega_producto', $producto['id_bodega_producto'])->first();
                            //restaurar unidades dependiendo tipo de factura
                            //$bodega_sub->cantidad = $bodega_sub->cantidad+$producto['cantidad_solicitada'];

                            if (!empty($bodega_sub)) {
                                if ($factura->tipo_venta === 1) {
//                                    $bodega_sub->cantidad += $producto['cantidad'];
                                } elseif ($factura->tipo_venta === 3) {
                                    $bodega_sub->cantidad_recuperadas += $producto['cantidad'];//salida venta recuperado
                                } elseif ($factura->tipo_venta === 4) {
                                    $bodega_sub->cantidad_obsoletas += $producto['cantidad'];//salida venta obsoleto
                                }

                                $bodega_sub->save();
                            }

                        }

                        if ($factura->id_tipo === 2) {//facturas de credito
                            $cuentas_x_cobrar = CuentasXCobrar::where('id_tipo_documento', 1)
                                ->where('identificador_origen', $factura->id_factura)->first();
                            if (!empty($cuentas_x_cobrar)) {
                                $cuentas_x_cobrar->estado = 0;
                                $cuentas_x_cobrar->saldo_actual = 0;
                                $cuentas_x_cobrar->save();
                            }

                        }

                        if (!empty($factura->id_documento_contable)) {//facturas con doc contable
                            $documento = DocumentosContables::find($factura->id_documento_contable);
                            $documento->estado = 0;
                            $documento->save();
                        }



                        if ($factura->tipo_venta === 2) {///venta por ajuste

                            $entrada = Entradas::where('id_salida', $salida->id_salida)->first();
                            $entradas_productos = EntradaProductos::where('id_entrada', $entrada['id_entrada']);

                            foreach ($entradas_productos as $entrada_producto) {
                                $salida_baterias = InventarioEntradaProductoBaterias::where('id_entrada_producto', $entrada_producto['id_entrada_producto'])->get();
                                foreach ($salida_baterias as $baterias) {
                                    $bateria_individual = InventarioBaterias::find($baterias['id_bateria']);
                                    $bateria_individual->reservada = false; //vendida y reservada
                                    $bateria_individual->save();

                                }
                            }

                        }


                        $proforma = Proformas::where('id_factura', $factura->id_factura)->first();

                        if (!empty($proforma)) {
                            $proforma->id_factura = null;
                            $proforma->estado = 1;///proforma convertida en venta
                            $proforma->save();
                        }


                    } else {
                        DB::rollBack();
                        return response()->json([
                            'status' => 'error',
                            'result' => 'No se puede actualizar la entrada, ya fue recibida',
                            'messages' => null
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 'error',
                        'result' => 'Error encontrando factura',
                        'messages' => null
                    ]);
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
