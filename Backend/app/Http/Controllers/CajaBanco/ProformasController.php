<?php

namespace App\Http\Controllers\CajaBanco;


use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\Sucursales;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\CajaBanco\Afectaciones;
use App\Models\CajaBanco\Bancos;
use App\Models\CajaBanco\FacturaViaPagos;
use App\Models\Contabilidad\Monedas;
use App\Models\CajaBanco\ProformasSeguimiento;
use App\Models\Contabilidad\TasasCambios;
use App\Models\Contabilidad\DocumentosContables;
use App\Models\Contabilidad\DocumentosCuentas;
use App\Models\Contabilidad\PeriodosFiscales;
use App\Models\Contabilidad\PeriodosMeses;
use App\Models\Contabilidad\TiposDocumentos;
use App\Models\CuentasXCobrar\CuentasXCobrar;
use App\Models\Inventario\Bodegas;
use App\Models\Inventario\SalidaProductos;
use App\Models\Inventario\Salidas;
use App\Models\Admon\Departamentos;
use App\Models\CajaBanco\ViaPagos;
use App\Models\Admon\Zonas;
use App\Models\Ventas\Vendedores;
use DateTime;
use Hash, Illuminate\Support\Facades\Validator, Illuminate\Support\Facades\Auth;
use App\Models\Inventario\BodegaProductos;
use App\Models\CajaBanco\ProformasDetalles;
use App\Models\CajaBanco\Proformas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;

class ProformasController extends Controller
{
    public function buscar(Request $request, Proformas $proformas)
    {
        $proformas = $proformas->buscar($request);
        return response()->json([
            'results' => $proformas
        ]);
    }


    public function obtener(Request $request, Proformas $proformas)
    {
        /*
         * Agregar funcionalidad de verificación de fechas de vencimiento
         * Si se cumple la condición
         * */

        $now = now();
        $quotes = Proformas::whereNotIn('estado',array(2,4,5))->get();

        foreach ($quotes as $quote)
        {
            $expiryDate = \Carbon\Carbon::parse($quote->f_proforma);
            $result = $now->diffInDays($expiryDate,true);
            /*print_r('resultado resta fechas: '.$result);*/
            /*print_r('Fecha now(): '.$now);*/
            if($result >= 15)
            {
                $quote->estado = 4;
                $quote->save();
            }
        }

        $proformas = $proformas->obtenerProformas($request);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first(); // 1-cordobas 2-dolares

        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $proformas->total(),
                'rows' => $proformas->items(),
                'currency_id' => $currency_id->valor
            ],
            'messages' => null
        ]);
    }

    /**
     * Get List of Entrada
     *
     * @access  public
     * @param Request $request
     * @param Proformas $proforma
     * @return JsonResponse
     */

    public function obtenerProforma(Request $request, Proformas $proforma)
    {
        $rules = [
            'id_proforma' => 'required|integer|min:1',
            'cargar_dependencias' => 'required|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {


            // $productos = $proforma->obtenerProductosSalida($request);
            $proforma = $proforma->obtenerProforma($request);

            if (!empty($proforma)) {


                if ($request->cargar_dependencias) {
                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                    $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();
                    $direccion_empresa = Ajustes::where('id_ajuste', 5)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
                    $telefono_empresa = Ajustes::where('id_ajuste', 6)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'factura' => $proforma,
                            'nombre_empresa' => $nombre_empresa->valor,
                            'direccion_empresa' => $direccion_empresa->valor,
                            'telefono_empresa' => $telefono_empresa->valor,
                        ],
                        'messages' => null
                    ]);
                } else {
                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'factura' => $proforma,
                        ],
                        'messages' => null
                    ]);
                }


            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_proforma' => ["Datos no encontrados"]),
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

    public function obtenerDetalleProforma(Request $request, Proformas $proforma)
    {
        $rules = [
            'id_proforma' => 'required|integer|min:1',

        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            /*Obtener detalle de proforma del id_proforma seleccionado*/

            $proforma = Proformas::where('id_proforma', $request->id_proforma)->with('proformaVendedor', 'proformaCliente','proformaTipoCliente','proformaSucursal','proformaBodega')->first();
            $detalleProforma = ProformasDetalles::where('id_proforma', $request->id_proforma)->with('afectacionProducto', 'bodegaProducto')->get();


            if (!empty($detalleProforma)) {

                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'proforma'=> $proforma,
                        'detalleProforma' => $detalleProforma,
                    ],
                    'messages' => null
                ]);


            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_proforma' => ["Datos no encontrados"]),
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
     * Registra una nueva proforma
     *
     * @access    public
     * @param Request $request
     * @return JsonResponse
     */

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
            'factura_cliente.required_if' => 'El campo cliente es requerido.',
            'nombre_razon.required_if' => 'El campo nombre cliente es requerido.',
            'factura_sucursal.required' => 'El campo sucursal es requerido.',
            'factura_bodega.required' => 'El campo bodega es requerido.',
            'factura_vendedor.required' => 'El campo vendedor es requerido',
        ];

        $rules = [
            // 'no_documento' => 'required|string|max:8',
            'f_factura' => 'required|date',
            //'serie' => 'required|string|max:2',
            // 'factura_moneda' => 'required|array|min:1',
            // 'factura_moneda.id_moneda' => 'required|integer|min:1',
            'id_tipo' => 'required|integer|min:1|max:2',
            'factura_sucursal' => 'required|array|min:1',
            'factura_sucursal.id_sucursal' => 'required|integer|min:1',
            'factura_bodega' => 'required|array|min:1',
            'factura_bodega.id_bodega' => 'required|integer|min:1',
            'tipo_identificacion' => 'required|integer|min:1|max:2',
            'identificacion' => 'required|string|max:18',
            'observacion' => 'nullable|string|max:100',

            'aplicaIVA' => 'required|boolean',
            'aplicaIR' => 'required|boolean',
            'aplicaIMI' => 'required|boolean',
            'es_nuevo' => 'required|boolean',

            'factura_cliente' => 'required_if:es_nuevo,false|array|min:0',
            'factura_cliente.id_cliente' => 'required_if:es_nuevo,false|integer',

            'nombre_razon' => 'required_if:es_nuevo,true|string|max:100|nullable',


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

            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.productox.id_bodega_producto' => 'required|integer|min:0',
            'detalleProductos.*.precio_costo' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.precio_lista' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.precio' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.cantidad' => 'required|integer|min:1',
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
                $proforma = new Proformas;
                $nuevo_num = Proformas::select([DB::raw("COALESCE(max(no_proforma),0)+1 as no_proforma")])->first();
                $proforma->no_proforma = $nuevo_num['no_proforma'];
                $str = substr("000{$nuevo_num['no_proforma']}", -4);
                $proforma->no_documento = 'P-' . $str;
                $proforma->f_proforma = $request->f_factura;
                $proforma->serie = $request->serie;
                $proforma->id_moneda = 1;
                $proforma->observacion = $request->observacion;
                $proforma->id_tipo = $request->id_tipo;
                $proforma->id_sucursal = $request->factura_sucursal['id_sucursal'];
                $proforma->id_bodega = $request->factura_bodega['id_bodega'];
                $proforma->id_cliente = $request->factura_cliente['id_cliente'];
//                $proforma->es_nuevo = $request->es_nuevo;
/*                if (!$request->es_nuevo) {
                    $proforma->id_cliente = $request->factura_cliente['id_cliente'];
                } else {
                    $proforma->nombre_razon = $request->nombre_razon;
                }*/
                $proforma->tipo_identificacion = $request->tipo_identificacion;
                $proforma->identificacion = $request->identificacion;
                $proforma->id_vendedor = $request->factura_vendedor['id_vendedor'];
                $proforma->t_cambio = $request->t_cambio;
                $proforma->doc_exoneracion = $request->doc_exoneracion;
                $proforma->doc_exoneracion_ir = $request->doc_exoneracion_ir;
                $proforma->doc_exoneracion_imi = $request->doc_exoneracion_imi;
                $proforma->impuesto_exonerado = !$request->aplicaIVA;

                $proforma->mt_retencion = round($request->mt_retencion, 2);
                $proforma->mt_retencion_imi = round($request->mt_retencion_imi, 2);
                $proforma->mt_impuesto = round($request->mt_impuesto, 2);
                $proforma->mt_descuento = round($request->mt_descuento, 2);
                $proforma->mt_ajuste = round($request->mt_ajuste, 2);
                $proforma->mt_total = $request->total_final_cordobas;

                $proforma->mt_deuda = $request->pago_pendiente_mn;
                $proforma->pago_vuelto = $request->pago_vuelto_mn;
                $proforma->pago_vuelto_me = $request->pago_vuelto;

                $proforma->saldo_factura = $request->pago_pendiente_mn;
                $proforma->dias_credito = $request->dias_credito;
                $proforma->f_vencimiento = date('Y-m-d', strtotime($proforma->f_factura . ' + ' . $request->dias_credito . ' days'));
                $proforma->u_creacion = Auth::user()->name;
                $proforma->estado = 1;
                $proforma->id_empresa = $usuario_empresa->id_empresa;
                $proforma->save();

                foreach ($request->detalleProductos as $producto) {

                    $proforma_producto = new ProformasDetalles();

                    if ($producto['productox']['id_bodega_producto'] > 0 && $producto['productox']['id_tipo_producto'] !==2) {

                        $bodega_sub = BodegaProductos::where('id_bodega_producto', $producto['productox']['id_bodega_producto'])->first();
                        $proforma_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                        $proforma_producto->id_producto = $bodega_sub->id_producto;

                    } else {
                        $proforma_producto->id_bodega_producto = null;
                        $proforma_producto->id_producto = $producto['productox']['id_producto'];
                    }

                    $proforma_producto->id_proforma = $proforma->id_proforma;
                    $proforma_producto->descripcion_producto = $producto['productox']['descripcion'];
                    $proforma_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                    $proforma_producto->unidad_medida = $producto['productox']['unidad_medida'];
                    $proforma_producto->id_tipo_producto = $producto['productox']['id_tipo_producto'];

                    $proforma_producto->id_afectacion = $producto['afectacionx']['id_afectacion'];

                    $proforma_producto->cantidad = $producto['cantidad'];
                    $proforma_producto->precio_lista = round($producto['precio_lista'], 2);
                    $proforma_producto->precio = round($producto['p_unitario'], 2);

                    $proforma_producto->p_descuento = $producto['p_descuento'];
                    $proforma_producto->p_ajuste = $producto['p_ajuste'];
                    $proforma_producto->p_impuesto = $producto['p_impuesto'];

                    $proforma_producto->m_impuesto = round($producto['mt_impuesto'], 2);
                    $proforma_producto->m_descuento = round($producto['mt_descuento'], 2);
                    $proforma_producto->m_ajuste = round($producto['mt_ajuste'], 2);

                    $proforma_producto->f_creacion = date("Y-m-d H:i:s");
                    $proforma_producto->id_empresa = $usuario_empresa->id_empresa;
                    $proforma_producto->save();


                }

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
     * author: omorales
     * method: update quotes
     * date: 07/09/2021
     * */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_proforma' => 'required|integer',

            'detalleProductos.min' => 'Se requiere agregar un producto por lo menos.',
            'detalleProductos.*.productox.id_producto.required' => 'Seleccione un producto válido',
            'detalleProductos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'detalleProductos.*.cantidad.min' => 'La cantidad debe ser mayor que cero',
            'doc_exoneracion.required_if' => 'El campo documento exoneracion es requerido cuando la factura no aplica IVA',
            'doc_exoneracion_ir.required_if' => 'El campo documento exoneracion es requerido cuando la factura aplica Retención IR',
            'doc_exoneracion_imi.required_if' => 'El campo documento exoneracion es requerido cuando la factura aplica Retención IMI',
            'doc_exoneracion' => 'required_if:aplicaIVA,false|string|max:20|nullable',
            'doc_exoneracion_ir' => 'required_if:aplicaIR,true|string|max:20|nullable',
            'doc_exoneracion_imi' => 'required_if:aplicaIMI,true|string|max:20|nullable',

            'mt_retencion' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_retencion_imi' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_impuesto' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_descuento' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',
            'mt_ajuste' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/',


            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.productox.id_bodega_producto' => 'required|integer|min:0',
            'detalleProductos.*.precio_costo' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.precio_lista' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.precio' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.cantidad' => 'required|integer|min:1',
            'detalleProductos.*.productox.codigo_sistema' => 'required|string|max:50',
            'detalleProductos.*.productox.descripcion' => 'required|string|max:100',
            'detalleProductos.*.productox.unidad_medida' => 'required|string|max:100',
            'detalleProductos.*.afectacionx.id_afectacion' => 'required|integer|exists:pgsql.cjabnco.facturas_afectaciones,id_afectacion',
            'detalleProductos.*.afectacionx.valor' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',

        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            try {
                DB::beginTransaction();

                $proforma = Proformas::find($request->id_proforma);
                $proforma->f_proforma = date("Y-m-d H:i:s");
                $proforma->f_modificacion = date("Y-m-d H:i:s");;
                $proforma->estado = 3; //Cambiar estado a modificado
                $proforma->save();


                ProformasDetalles::where('id_proforma', $request->id_proforma)->delete();

                foreach ($request->detalleProductos as $producto) {

                    $proforma_producto = new ProformasDetalles();

                    if ($producto['productox']['id_bodega_producto'] > 0 && $producto['productox']['id_tipo_producto'] != 2) {

                        $bodega_sub = BodegaProductos::where('id_bodega_producto', $producto['productox']['id_bodega_producto'])->first();
                        $proforma_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                        $proforma_producto->id_producto = $bodega_sub->id_producto;

                    } else {
                        $proforma_producto->id_bodega_producto = null;
                        $proforma_producto->id_producto = $producto['productox']['id_producto'];
                    }

                    $proforma_producto->id_proforma = $proforma->id_proforma;
                    $proforma_producto->descripcion_producto = $producto['productox']['descripcion'];
                    $proforma_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                    $proforma_producto->unidad_medida = $producto['productox']['unidad_medida'];

                    $proforma_producto->id_afectacion = $producto['afectacionx']['id_afectacion'];

                    $proforma_producto->cantidad = $producto['cantidad'];
                    $proforma_producto->precio_lista = round($producto['precio_lista'], 2);
                    $proforma_producto->precio = round($producto['p_unitario'], 2);

                    $proforma_producto->p_descuento = $producto['p_descuento'];
                    $proforma_producto->p_ajuste = $producto['p_ajuste'];
                    $proforma_producto->p_impuesto = $producto['p_impuesto'];

                    $proforma_producto->m_impuesto = round($producto['mt_impuesto'], 2);
                    $proforma_producto->m_descuento = round($producto['mt_descuento'], 2);
                    $proforma_producto->m_ajuste = round($producto['mt_ajuste'], 2);


                    $proforma_producto->save();


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

    /*
     * Auth: omorales
     * Method: Archivar cotización
     * date: 08/09/2021
     * */
    public function archivar(Request $request)
    {
        $rules = [
            'id_proforma' => 'required|integer|min:0',
            'estado' => 'required|integer|min:0'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $proforma = Proformas::find($request->id_proforma);


            $proforma->estado = $request->estado;
            $proforma->observacion = $request->observacion;
            $proforma->u_modificacion = Auth::user()->name;
            $proforma->f_modificacion = date('Y-m-d h:i:s');
            $proforma->save();

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
    /*
     * Auth: omorales
     * Method: anular cotización
     * date: 08/09/2021
     * */
    public function anular(Request $request)
    {
        $rules = [
            'id_proforma' => 'required|integer|min:0',
            'estado' => 'required|integer|min:0'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $proforma = Proformas::find($request->id_proforma);


            $proforma->estado = $request->estado;
            $proforma->observacion = $request->observacion;
            $proforma->u_modificacion = Auth::user()->name;
            $proforma->f_modificacion = date('Y-m-d h:i:s');
            $proforma->save();

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

    public function nueva(Request $request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();

        $afectaciones = Afectaciones::where('estado', 1)->where('id_empresa',$usuario_empresa->id_empresa)->orderby('id_afectacion')->get();
        $tasa = TasasCambios::select('tasa_paralela','tasa')->where('fecha', date("Y-m-d"))->where('id_empresa',$usuario_empresa->id_empresa)->first();
        $vendedores = Vendedores::select(['id_vendedor', 'nombre_completo', 'id_zona'])->where('id_empresa',$usuario_empresa->id_empresa)->get();
        $departamentos = Departamentos::with('municipiosDepartamento')->get()->where('id_empresa',$usuario_empresa->id_empresa);
        $vias_pago = ViaPagos::select(['id_via_pago', 'descripcion'])->where('estado', 1)->where('id_empresa',$usuario_empresa->id_empresa)->orderBy('id_via_pago')->get();
        $monedas = Monedas::where('estado', 1)->orderBy('id_moneda')->get(); //->where('id_empresa',$usuario_empresa->id_empresa)
        $zonas = Zonas::select(['id_zona', 'descripcion'])->where('estado', 1)->where('id_empresa',$usuario_empresa->id_empresa)->get();
        $bancos = Bancos::select(['id_banco', 'descripcion'])->where('estado', 1)->where('id_empresa',$usuario_empresa->id_empresa)->get();

        if (Auth::user()->id_sucursal > 0) {
            $sucursales = Sucursales::select(['id_sucursal', 'serie', 'descripcion'])->with('sucursalBodegaVentas')
                ->where('id_sucursal', Auth::user()->id_sucursal)->where('id_empresa',$usuario_empresa->id_empresa)
                ->orderby('descripcion')->orderby('descripcion')->get();
        } else {
            $sucursales = Sucursales::select(['id_sucursal', 'serie', 'descripcion'])->where('id_empresa',$usuario_empresa->id_empresa)->with('sucursalBodegaVentas')->orderby('descripcion')
                ->get();
        }
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
                'sucursales' => $sucursales
            ],
            'messages' => null
        ]);

    }


    public function cancelarFactura(Request $request, Proformas $proforma)
    {
        $rules = [
            'id_proforma' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $proforma = Proformas::find($request->id_proforma);

            if (!empty($proforma) && $proforma->estado == 1) {

                try {
                    DB::beginTransaction();
                    $proforma->estado = 0;
                    $proforma->observacion = $proforma->observacion . ' **Factura cancelada por ' . Auth::user()->name . ' a las ' . date("Y-m-d H:i:s");
                    $proforma->save();

                    foreach ($request->factura_productos as $producto) {
                        $bodega_sub = BodegaProductos::where('id_bodega_producto', $producto['id_bodega_producto'])->first();
                        $bodega_sub->cantidad = $bodega_sub->cantidad + $producto['cantidad_solicitada'];
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
                    'result' => array('id_proforma' => ["Datos no encontrados o la solicitud ya autorizada"]),
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

    public function obtenerProformasCliente(Request $request, Proformas $proformas)
    {
        $proformas = $proformas->obtenerProformasCliente($request);

        return response()->json([
            'status' => 'success',
            'result' => $proformas,
            'messages' => null
        ]);
    }


    public function reporte($ext,$id_proforma)
    {
        // echo $ext;
        //$ext = 'pdf';
        $os = array("pdf");
        if (in_array($ext, $os, true)) {
            $hora_actual = time();
            // Rutas para descarga Reportes local
            $input = env('APP_URL_REPORTES') . 'Proformas';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'Proformas';

            // Rutas de descarga de Reportes en servidor
//            $input = env('APP_URL_REPORTES') . 'ReporteEntradainventario';
//            $output = env('APP_URL_REPORTES') . $hora_actual .  'ReporteEntradainventario';

            //Ajustes generales del sistema

            $logo_empresa = Ajustes::where('id_ajuste', 3)->select('valor')->first();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $options = [
                'format' => [$ext],
                'locale' => 'es',
                'params' => [
                    'id_proforma' => $id_proforma,
                    /*'logo_empresa' => env('APP_URL_IMAGES') . $logo_empresa->valor,*/
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


            //print_r( env('APP_URL_REPORTS').$logo_empresa->valor);
            $headers = [
                'Content-Type' => 'application/pdf',
            ];

            return response()->download($output . '.' . $ext, $hora_actual . 'Proformas.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
            print_r($output);*/
        } else {
            return '';
        }
    }


}

