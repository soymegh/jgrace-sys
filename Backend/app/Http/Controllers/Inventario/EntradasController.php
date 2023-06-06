<?php


namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Contabilidad\CuentasContables;
use App\Models\Contabilidad\DocumentosContables;
use App\Models\Contabilidad\DocumentosCuentas;
use App\Models\Contabilidad\PeriodosFiscales;
use App\Models\Contabilidad\PeriodosMeses;
use App\Models\Contabilidad\TasasCambios;
use App\Models\Contabilidad\TiposDocumentos;
use App\Models\Inventario\BodegaProductos;
use App\Models\Inventario\BodegasProductos;
use App\Models\Inventario\ConfiguracionInventario;
use App\Models\Inventario\EntradaProductos;
use App\Models\Inventario\Entradas;
use App\Models\Inventario\MovimientosProductos;
use App\Models\Inventario\Proveedores;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;

class EntradasController extends Controller
{
    public function buscarProductos(Request $request, Entradas $productos_entrada)
    {
        $productos_entrada = $productos_entrada->obtenerProductosEntrada($request);
        return response()->json([
            'results' => $productos_entrada
        ]);
    }

    public function reporteEntrada($ext, $id_entrada)
    {
        // echo $ext;
        //$ext = 'pdf';
        $os = array("pdf");
        if (in_array($ext, $os, true)) {
            $hora_actual = time();
            // Rutas para descarga Reportes local
            $input = env('APP_URL_REPORTES') . 'ReporteEntradaInventario';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'ReporteEntradaInventario';

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
                    'id_entrada' => $id_entrada,
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

            return response()->download($output . '.' . $ext, $hora_actual . 'ReporteEntradaInventario.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
            print_r($output);*/
        } else {
            return '';
        }
    }


    /**
     * Get List of Entradas
     *
     * @access  public
     * @param Request $request
     * @param Entradas $entradas
     * @return JsonResponse
     */

    public function obtener(Request $request, Entradas $entradas)
    {

        $now = now();
        $quotes = Entradas::where('estado', 99)->get();
        foreach ($quotes as $quote) {
            $expiryDate = \Carbon\Carbon::parse($quote->f_modificacion);
            $result = $now->diffInDays($expiryDate, true);
            /*print_r('resultado resta fechas: '.$result);*/
            /*print_r('Fecha now(): '.$now);*/
            if ($result >= 5) {
                $quote->delete();
            }
        }

        $entradas = $entradas->obtenerEntradas($request);

        foreach ($entradas as $entrada) {
            //   print_r($entrada);
            $items = collect($entrada->entradaProductos);

            // Calcular total de unidades por entrada
            $entrada->tot_unidades = $items->sum(function ($item) {
                return $item['cantidad_solicitada'];
            });

            // Calcular total de precio unitario por entrada
            $entrada->tot_precio_unitario = $items->sum(function ($item) {
                return $item['precio_unitario_me'];
            });

            $entrada->subtotal = $items->sum(function ($item) {
                return $item['precio_unitario'] * $item['cantidad_solicitada'];
            });

            $entrada->total = $entrada->subtotal;
        }

        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $entradas->total(),
                'rows' => $entradas->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Get List of Entrada
     *
     * @access  public
     * @param Request $request
     * @param Entradas $entrada
     * @return JsonResponse
     */


    public function obtenerEntrada(Request $request, Entradas $entrada)
    {
        $rules = [
            'id_entrada' => 'required|integer|min:1'
        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();

            $productos = $entrada->obtenerProductosEntrada($request);

//            $traslados = $entrada->obtenerBateriasEntradaTraslado($request);
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();
            $direccion_empresa = Ajustes::where('id_ajuste', 5)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
            $telefono_empresa = Ajustes::where('id_ajuste', 6)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
            $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
            $entrada = $entrada->obtenerEntrada($request);
//            $entrada_proveedor = Proveedores::whereIn('id_proveedor', array($mapProveedor->id_proveedor))->get();
            $tasa = TasasCambios::select('tasa')->where('fecha', $entrada->fecha_entrada)->first();

            if (!empty($entrada)) {

                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'entrada' => $entrada,
                        'productos' => $productos,
//                        'traslados' => $traslados,
                        'nombre_empresa' => $nombre_empresa->valor,
                        'direccion_empresa' => $direccion_empresa->valor,
                        'telefono_empresa' => $telefono_empresa->valor,
                        'currency_id' => $currency_id->valor,
                        't_cambio' => $tasa
                    ],
                    'messages' => null
                ]);
            }

            return response()->json([
                'status' => 'error',
                'result' => array('id_entrada' => ["Datos no encontrados"]),
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

    public function obtenerProveedores(Request $request)
    {
        $rules = [
            'id_entrada' => 'required|integer|min:1'
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            /*$proveedor = Entradas::where('id_entrada', $request->id_entrada)->where('estado', 1)->first();*/
            $mapId = [];
            $entrada_proveedor = Entradas::select('id_proveedor')->where('id_entrada', $request->id_entrada)->first();
            foreach ($entrada_proveedor->toArray() as $entradas) {
                $mapId = $entradas;
            }
            $identificadores_proveedores = array_map('intval', explode(',', (string)$mapId));
            $entrada_proveedores = Proveedores::select('nombre_comercial', 'id_proveedor')->whereIn('id_proveedor', $identificadores_proveedores)->get();

            return response()->json([
                'status' => 'success',
                'result' => [
                    'proveedores' => $entrada_proveedores,
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


    public function obtenerEntradaPorCodigo(Request $request, Entradas $entrada)
    {
        $rules = [
            'codigo_entrada' => 'required|string|max:25'
        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $entrada = $entrada->obtenerEntradaPorCodigo($request);

            if (!$entrada->isEmpty()) {
                //print_r($entrada);
                $items = collect($entrada[0]->entradasProductos);
                $entrada[0]->sub_total = $items->sum(function ($item) {
                    return $item['cantidad'] * $item['precio_unitario'];
                });

                $entrada[0]->total = $items->sum(function ($item) {
                    return $item['cantidad'] * $item['precio_unitario'];
                });

                $entrada[0]->cant_inicial = 0;

                return response()->json([
                    'status' => 'success',
                    'result' => $entrada[0],
                    'messages' => null
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => "No se han encontrado resultados para el código de entrada: " . $request->codigo_entrada,
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

    public function pdf($id, Request $request)
    {
        // $data = Entradas::with(['items.product', 'items.taxes', 'client', 'currency', 'invoiceable'])->findOrFail($id);
        $data = Entradas::with(['entradasProductos', 'entradaProveedor', 'entradaBodega', 'entradaTipo'])->findOrFail($id);

        $items = collect($data->entradasProductos);
        $data->sub_total = $items->sum(function ($item) {
            return $item['cantidad'] * $item['precio_unitario'];
        });

        $data->total = $items->sum(function ($item) {
            return $item['cantidad'] * $item['precio_unitario'];
        });

        $data->log_registro = json_decode($data->log_entrada);

        $doc = 'docs.entrada';
        //print_r($data);
        //return $this->f_pdf($doc, $data);
        return pdf($doc, $data, strtoupper('Entrada ' . $data->codigo_entrada));
    }

    /**
     * Registrar nueva entrada de inventerio
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
        ];


        $rules = [
            //'codigo_entrada' => 'required|string|max:25',
            'descripcion_entrada' => 'string|max:255|nullable',
            'es_borrador' => 'required|boolean',
            'fecha_entrada' => 'required|date',
            'numero_documento' => 'required|string|max:100',
            'tipo_entrada' => 'required|array|min:1',
            'tipo_entrada.id_tipo_entrada' => 'required|integer|min:1',

            'bodega' => 'required|array|min:1',
            'bodega.id_bodega' => 'required|integer|min:1',

            'proveedor' => 'required|array|min:1',

            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.precio_unitario' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.cantidad' => 'required|numeric',
            'detalleProductos.*.productox.codigo_sistema' => 'required|string|max:50',
            'detalleProductos.*.productox.descripcion' => 'required|string|max:100',
            'detalleProductos.*.productox.unidad_medida.id_unidad_medida' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {


                DB::beginTransaction();

                $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                $entrada = Entradas::find($request->id_entrada);
//                    $entrada = new Entradas;
//                    $entrada->codigo_entrada = Entradas::max('id_entrada') + 1;
                //$entrada->codigo_entrada = Entradas::select(DB::raw('coalesce(max(codigo_entrada::integer),0)+1'))->first();

                $entrada->id_tipo_entrada = $request->tipo_entrada['id_tipo_entrada'];
                $entrada->fecha_entrada = $request->fecha_entrada;
                $entrada->id_proveedor = $request->mapProveedor;
//                $entrada->id_proveedor = $request->proveedor['id_proveedor'];
                $entrada->id_bodega = $request->bodega['id_bodega'];
                $entrada->descripcion_entrada = $request->descripcion_entrada;
                $entrada->numero_documento = $request->numero_documento;
                $entrada->u_creacion = Auth::user()->name;
//                    $request->es_borrador == 'true' ? $entrada->estado = 99 : $entrada->estado = 1;
                $entrada->estado = 99;
                $entrada->id_empresa = $usuario_empresa->id_empresa;
                $entrada->save();

                foreach ($request->detalleProductos as $producto) {

                    if ($producto['estado'] === 0) {
                        if (!empty($producto['id_entrada_producto'])) {
                            EntradaProductos::where('id_entrada_producto', $producto['id_entrada_producto'])->delete();
                        }
                    } else {
                        if (!$producto['registrada']) {
                            if (empty($producto['id_entrada_producto'])) {
                                $entrada_producto = new EntradaProductos;
                                $bodega_sub = BodegasProductos::where('id_bodega', $request->bodega['id_bodega'])->where('id_producto', $producto['productox']['id_producto'])->where('no_documento', $entrada->numero_documento)->first();
                                if (!empty($bodega_sub)) {
                                    $entrada_producto->id_bodega_producto = $bodega_sub['id_bodega_producto'];
                                } else {
                                    $nueva_bodega_sub = new BodegasProductos;
                                    $nueva_bodega_sub->id_bodega = $request->bodega['id_bodega'];
                                    $nueva_bodega_sub->id_producto = $producto['productox']['id_producto'];
                                    $nueva_bodega_sub->cantidad = 0;
                                    $nueva_bodega_sub->u_creacion = $entrada->u_creacion;
                                    $nueva_bodega_sub->id_empresa = $entrada->id_empresa;
                                    //Guardadno no_lote en bodega productos
                                    $nueva_bodega_sub->no_documento = $entrada->numero_documento;
                                    $nueva_bodega_sub->u_creacion = Auth::user()->name;
                                    $nueva_bodega_sub->estado = 1;
                                    $nueva_bodega_sub->save();
                                    $entrada_producto->id_bodega_producto = $nueva_bodega_sub->id_bodega_producto;
                                }
                                $entrada_producto->id_entrada = $entrada->id_entrada;
                                $entrada_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                                $entrada_producto->descripcion_producto = $producto['productox']['descripcion'];
                                $entrada_producto->unidad_medida = $producto['productox']['unidad_medida']['descripcion'];
                                $entrada_producto->precio_unitario = $producto['precio_unitario'] * $request->t_cambio;
                                $entrada_producto->precio_unitario_me = $producto['precio_unitario'];
                                $entrada_producto->cantidad_solicitada = $producto['cantidad'];
                                $entrada_producto->cantidad_recibida = 0;
                                $entrada_producto->cantidad_faltante = 0;
                                $entrada_producto->u_creacion = $entrada->u_creacion;
                                $entrada_producto->id_empresa = $entrada->id_empresa;
                                //Adding no_lote field to detail
                                $entrada_producto->no_documento = $entrada->numero_documento;
                                $entrada_producto->estado = 1;
                                $entrada_producto->save();
                            } else {
                                $producto_individual = EntradaProductos::find($producto['id_entrada_producto']);
                                //Adding BodegaProducto Key to new products
                                $producto_individual->codigo_producto = $producto['productox']['codigo_sistema'];
                                $producto_individual->descripcion_producto = $producto['productox']['descripcion'];
                                $producto_individual->unidad_medida = $producto['productox']['unidad_medida']['descripcion'];
                                $producto_individual->precio_unitario = $producto['precio_unitario'] * $request->t_cambio;
                                $producto_individual->precio_unitario_me = $producto['precio_unitario'];
                                $producto_individual->cantidad_solicitada = $producto['cantidad'];
                                $producto_individual->cantidad_recibida = 0;
                                $producto_individual->cantidad_faltante = 0;
                                $producto_individual->estado = 1;
                                //Adding no_lote field to detail
                                $producto_individual->no_documento = $entrada->numero_documento;
                                $producto_individual->u_creacion = Auth::user()->name;
                                $producto_individual->id_empresa = $usuario_empresa->id_empresa;
                                $producto_individual->save();
                            }
                        } else {
                            $producto_individual = EntradaProductos::find($producto['id_entrada_producto']);
                            $producto_individual->codigo_producto = $producto['productox']['codigo_sistema'];
                            $producto_individual->descripcion_producto = $producto['productox']['descripcion'];
                            $producto_individual->unidad_medida = $producto['productox']['unidad_medida']['descripcion'];
                            $producto_individual->precio_unitario = $producto['precio_unitario'] * $request->t_cambio;
                            $producto_individual->precio_unitario_me = $producto['precio_unitario'];
                            $producto_individual->cantidad_solicitada = $producto['cantidad'];
                            $producto_individual->cantidad_recibida = 0;
                            $producto_individual->cantidad_faltante = 0;
                            $producto_individual->estado = 1;
                            $producto_individual->u_creacion = Auth::user()->name;
                            $producto_individual->id_empresa = $usuario_empresa->id_empresa;
                            $producto_individual->no_documento = $entrada->numero_documento;
                            $producto_individual->save();
                        }
                    }

                }


                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'result' => '',
                    'messages' => 'Registro guardado correctamente.'
                ]);
            } catch
            (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'result' => 'Error de base de datos',
                    'messages' => 'Error de base de datos, Contacte a soporte'
                ]);
            }


        } else {
            return response()->json([
                'status' => 'error',
                'result' => $validator->messages(),
                'messages' => 'Revisa bien los datos ingresados.'
            ]);
        }
    }


    public function actualizar(Request $request)
    {
        $messages = [
            'entrada_productos' => 'El detalle de la entrada es obligatorio.',
            'entrada_productos.min' => 'Se requiere agregar un producto por lo menos.',
            'entrada_productos.*.id_producto.required' => 'Seleccione un producto válido',
            'entrada_productos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'entrada_productos.*.cantidad_solicitada.min' => 'La cantidad debe ser mayor que cero',
        ];

        $rules = [
            'id_entrada' => 'required|integer|exists:pgsql.inventario.entradas,id_entrada',
            'es_borrador' => 'required|boolean',
            'fecha_entrada' => 'required|date',
            'descripcion_entrada' => 'string|max:255|nullable',
            'numero_documento' => 'required|string|max:100',
            'entrada_tipo' => 'required|array|min:1',
            'entrada_tipo.id_tipo_entrada' => 'required|integer|min:1',

            'entrada_bodega' => 'required|array|min:1',
            'entrada_bodega.id_bodega' => 'required|integer|min:1',


//            'entrada_proveedor' => 'required|array|min:1',
//            'entrada_proveedor.id_proveedor' => 'required|integer|min:1',

            'entrada_productos' => 'required|array|min:1',
            'entrada_productos.*.id_bodega_producto' => 'required|integer|min:0',
            'entrada_productos.*.id_producto' => 'required_if:id_bodega_producto,0|integer|exists:pgsql.inventario.productos,id_producto',
            'entrada_productos.*.precio_unitario' => 'required|min:0|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'entrada_productos.*.precio_unitario_me' => 'required|min:0|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'entrada_productos.*.cantidad_solicitada' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0',
            'entrada_productos.*.codigo_producto' => 'required|string|max:50',
            'entrada_productos.*.descripcion_producto' => 'required|string|max:100',
            'entrada_productos.*.unidad_medida' => 'required|string|max:50',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {


                DB::beginTransaction();
                $entrada = Entradas::find($request->id_entrada);

                $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first(); //get company_id

                if ($entrada->estado === 99) {

                    $entrada->id_tipo_entrada = $request->entrada_tipo['id_tipo_entrada'];
                    $entrada->fecha_entrada = $request->fecha_entrada;
                    $entrada->descripcion_entrada = $request->descripcion_entrada;
                    $entrada->numero_documento = $request->numero_documento;
                    $entrada->id_proveedor = $request->mapProveedor;
                    $entrada->id_bodega = $request->entrada_bodega['id_bodega'];


                    $request->es_borrador === true ? $entrada->estado = 99 : $entrada->estado = 1;

                    $entrada->save();

//                    EntradaProductos::where('id_entrada', $request->id_entrada)->delete();//update(['activo' => false]);


                    foreach ($request->entrada_productos as $producto) {

                        if ($producto['estado'] === 0) { //Productos eliminados

                            if (!empty($producto['id_entrada_producto'])) {

                                EntradaProductos::where('id_entrada_producto', $producto['id_entrada_producto'])->delete();
                            }

                        } else { //Productos validas

                            if (!$producto['registrada']) {

                                if (empty($producto['id_entrada_producto'])) {

                                    $bodega_sub = BodegasProductos::where('id_bodega', $request->entrada_bodega['id_bodega'])->where('id_producto', $producto['bodega_producto']['id_producto'])->where('no_documento', $entrada->numero_documento)->first();


                                    $entrada_producto = new EntradaProductos;
                                    $entrada_producto->id_entrada = $entrada->id_entrada;
                                    $entrada_producto->codigo_producto = $producto['codigo_producto'];
                                    $entrada_producto->descripcion_producto = $producto['descripcion_producto'];
                                    $entrada_producto->unidad_medida = $producto['unidad_medida'];
                                    $entrada_producto->precio_unitario_me = $producto['precio_unitario_me'];
                                    $entrada_producto->precio_unitario = $producto['precio_unitario_me'] * $request->t_cambio;

                                    $entrada_producto->cantidad_solicitada = $producto['cantidad_solicitada'];
                                    $entrada_producto->cantidad_recibida = 0;
                                    $entrada_producto->cantidad_faltante = 0;
                                    $entrada_producto->u_creacion = $entrada->u_creacion;
                                    $entrada_producto->id_empresa = $entrada->id_empresa;
                                    $entrada_producto->no_documento = $entrada->numero_documento;
                                    $entrada_producto->estado = 1;
                                    if (!empty($bodega_sub)) {
                                        $entrada_producto->id_bodega_producto = $bodega_sub['id_bodega_producto'];
                                    } else {
                                        $nueva_bodega_sub = new BodegaProductos;
                                        $nueva_bodega_sub->id_bodega = $entrada->id_bodega;
                                        $nueva_bodega_sub->id_producto = $producto['id_producto'];
                                        $nueva_bodega_sub->cantidad = 0;
                                        $nueva_bodega_sub->u_creacion = $entrada->u_creacion;
                                        $nueva_bodega_sub->id_empresa = $usuario_empresa->id_empresa;
                                        $nueva_bodega_sub->estado = 1;
                                        $nueva_bodega_sub->no_documento = $entrada->numero_documento;
                                        $nueva_bodega_sub->save();
                                        $entrada_producto->id_bodega_producto = $nueva_bodega_sub->id_bodega_producto;
                                    }

                                    $entrada_producto->save();

                                } else {

                                    $producto_individual = EntradaProductos::find($producto['id_entrada_producto']);
                                    $producto_individual->codigo_producto = $producto['codigo_producto'];
                                    $producto_individual->descripcion_producto = $producto['descripcion_producto'];
                                    $producto_individual->unidad_medida = $producto['unidad_medida'];
                                    $producto_individual->precio_unitario_me = $producto['precio_unitario_me'];
                                    $producto_individual->precio_unitario = $producto['precio_unitario_me'] * $request->t_cambio;

                                    $producto_individual->cantidad_solicitada = $producto['cantidad_solicitada'];
                                    $producto_individual->cantidad_recibida = 0;
                                    $producto_individual->cantidad_faltante = 0;
                                    $producto_individual->no_documento = $entrada->numero_documento;
                                    $producto_individual->save();
                                }
                            } else {
                                $producto_individual = EntradaProductos::find($producto['id_entrada_producto']);
                                $producto_individual->codigo_producto = $producto['codigo_producto'];
                                $producto_individual->descripcion_producto = $producto['descripcion_producto'];
                                $producto_individual->unidad_medida = $producto['unidad_medida'];
                                $producto_individual->precio_unitario_me = $producto['precio_unitario_me'];
                                $producto_individual->precio_unitario = $producto['precio_unitario_me'] * $request->t_cambio;
                                $producto_individual->cantidad_solicitada = $producto['cantidad_solicitada'];
                                $producto_individual->cantidad_recibida = 0;
                                $producto_individual->cantidad_faltante = 0;
                                $producto_individual->no_documento = $entrada->numero_documento;
                                $producto_individual->save();
                            }

                        }
                    }

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'result' => null,
                        'messages' => null
                    ]);

                } else {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'result' => 'La entrada ha sido modificada previamente, no se pueden grabar los cambios',
                        'messages' => null
                    ]);
                }

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
                'messages' => 'Ha ocurrido un error, favor verificar los campos faltantes'
            ]);
        }
    }


    public function recibir(Request $request)
    {
        $messages = [
            'entrada_productos.min' => 'Se requiere agregar un producto por lo menos.',
            'entrada_productos.*.id_producto.required' => 'Seleccione un producto válido',
            'entrada_productos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'entrada_productos.*.cantidad_solicitada.min' => 'La cantidad debe ser mayor que cero',
        ];

        $rules = [
            'id_entrada' => 'required|integer|exists:pgsql.inventario.entradas,id_entrada',
            'entrada_productos' => 'required|array|min:1',
            'entrada_productos.*.id_entrada_producto' => 'required|integer|min:1|exists:pgsql.inventario.entrada_productos,id_entrada_producto',
            'entrada_productos.*.id_bodega_producto' => 'required|integer|min:1|exists:pgsql.inventario.bodega_productos,id_bodega_producto',
            'entrada_productos.*.cantidad_solicitada' => 'required|numeric|min:0',
            'entrada_productos.*.cantidad_faltante' => 'required|numeric|min:0|lte:entrada_productos.*.cantidad_solicitada',
            //'entrada_productos.*.cantidad_recibida' => 'required|integer|min:1|lte:entrada_productos.*.cantidad_solicitada',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();
                $entrada = Entradas::find($request->id_entrada);
                if ($entrada->estado === 1) {
                    $entrada->fecha_entrada = $request->fecha_entrada;
                    $entrada->fecha_recepcion = $request->fecha_recepcion; //date("Y-m-d H:i:s");
                    $entrada->u_recepcion = Auth::user()->name;
                    $entrada->estado = 2;
                    $entrada->save();


                    $i = 0;
                    // Inicializamos variable en 0 para sumar los costos de producto
                    $total_costo = 0;
                    $total_costo_me = 0;
                    foreach ($request->entrada_productos as $producto) {
                        $bodega_sub = BodegasProductos::where('id_bodega_producto', $producto['id_bodega_producto'])->first();
                        if (!empty($bodega_sub)) {
                            $bodega_sub->cantidad += $producto['cantidad_recibida'];

                        }


                        $entrada_producto = EntradaProductos::find($producto['id_entrada_producto']);
                        $entrada_producto->cantidad_recibida = $producto['cantidad_recibida'];
                        $entrada_producto->cantidad_faltante = $producto['cantidad_faltante'];
                        $entrada_producto->save();


                        $bodega_sub->save();

                        $movimiento_producto = new MovimientosProductos();
                        $movimiento_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                        $movimiento_producto->fecha_movimiento = $entrada->fecha_recepcion;//date("Y-m-d H:i:s");
                        $movimiento_producto->descripcion_movimiento = $request->entrada_tipo['descripcion'] . ' No. ' . $entrada->codigo_entrada;
                        $movimiento_producto->identificador_origen_movimiento = $entrada->id_entrada;
                        $movimiento_producto->tipo_movimiento = 1;
                        $movimiento_producto->cantidad_movimiento = $entrada_producto->cantidad_recibida;
                        $movimiento_producto->costo = $entrada_producto->precio_unitario;
                        $movimiento_producto->costo_me = $entrada_producto->precio_unitario_me;
                        $movimiento_producto->usuario_registra = Auth::user()->name;
                        $movimiento_producto->id_empresa = $entrada->id_empresa;
                        $movimiento_producto->save();



                        // Asignamos suamtoria del costo estandar del productor contenido en la salida
                        $total_costo += round($producto['precio_unitario'] * $producto['cantidad_recibida'], 6);
                        $total_costo_me += round($producto['precio_unitario_me'] * $producto['cantidad_recibida'], 6);

                        $i++;
                    }


                    /*Contabilización de entradas de productos*/

                    if ($entrada->id_tipo_entrada === 1) { //Salida por venta
                        /*INICIA movimiento contable - Factura*/

                        $tipos_cuentas = array(4, 5, 6);
                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                        $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

                        $documento = new DocumentosContables();
                        $tipo = TiposDocumentos::find(10);  //Tipo comprobante de inventario
                        $fecha = date("Y-m-d H:i:s");
                        $codigo = $documento->obtenerCodigoDocumento(array('id_tipo_doc' => $tipo->id_tipo_doc, 'fecha_doc' => $fecha));

                        $nuevo_codigo = json_decode($codigo[0]);

                        date_default_timezone_set('America/Managua');

                        $documento->num_documento = $tipo->prefijo . '-' . $nuevo_codigo->secuencia;
                        $documento->fecha_emision = $entrada->fecha_recepcion; //date('Y-m-d');
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

                        $documento->id_tipo_doc = 10; // Comprobante de inventario
                        $documento->valor = $total_costo;
                        $documento->valor_me = $total_costo_me;
                        if ($currency_id->valor === 1) {
                            $documento->concepto = 'Registramos entrada de productos por factura No. ' . $entrada->numero_documento . '. Monto total C$ ' . $total_costo;
                        } else {
                            $documento->concepto = 'Registramos entrada de productos por factura No. ' . $entrada->numero_documento . '. Monto total $ ' . $total_costo_me;
                        }

                        $documento->id_moneda = $currency_id->valor;
                        $documento->u_creacion = Auth::user()->name;
                        $documento->estado = 1;
                        $documento->id_empresa = $usuario_empresa->id_empresa;
                        $documento->save();
                        $entrada->id_documento_contable = $documento->id_documento;
                        $entrada->save();

                        TiposDocumentos::find($documento->id_tipo_doc)->increment('secuencia');

                        //definición de tipo de configuración de comprobantes
                        $id_tipo_configuracion = 6; // Salida por ventas

                        if ($total_costo > 0 || $total_costo_me > 0) {

                            // Registramos contabilización de costo de venta de productos
                            $nombre_seccion_ArtCosto = 'InvMercad';
                            //obtener datos de BD con estos paremetros
                            $cuentaSeccionArtCosto = ConfiguracionInventario::where('nombre_seccion', $nombre_seccion_ArtCosto)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionImportacioncuentaContable')->first();
                            $cuenta_contable_art_costo = CuentasContables::find($cuentaSeccionArtCosto->id_cuenta_contaable);
                            $cuenta_contable_art_costo_padre = CuentasContables::find($cuenta_contable_art_costo->id_cuenta_padre);

                            $documento_cuenta_contableS1 = new DocumentosCuentas;
                            $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS1->concepto = $cuentaSeccionArtCosto->descripcion_movimiento . ' ' . $entrada->codigo_entrada;

                            if ($cuentaSeccionArtCosto->debe_haber === 1) {
                                if ($currency_id->valor === 1) {
                                    $documento_cuenta_contableS1->debe = $total_costo_me;
                                    $documento_cuenta_contableS1->haber = 0;
                                    $documento_cuenta_contableS1->debe_org = $total_costo;
                                    $documento_cuenta_contableS1->haber_org = 0;
                                } else {
                                    $documento_cuenta_contableS1->debe = $total_costo_me;
                                    $documento_cuenta_contableS1->haber = 0;
                                    $documento_cuenta_contableS1->debe_org = $total_costo;
                                    $documento_cuenta_contableS1->haber_org = 0;
                                }

                            } else {
                                if ($currency_id->valor === 1) {
                                    $documento_cuenta_contableS1->debe = 0;
                                    $documento_cuenta_contableS1->haber = $total_costo_me;
                                    $documento_cuenta_contableS1->debe_org = 0;
                                    $documento_cuenta_contableS1->haber_org = $total_costo;
                                } else {
                                    $documento_cuenta_contableS1->debe = 0;
                                    $documento_cuenta_contableS1->haber = $total_costo_me;
                                    $documento_cuenta_contableS1->debe_org = 0;
                                    $documento_cuenta_contableS1->haber_org = $total_costo;
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

                            $nombre_seccion_CostoVenta = 'CompMercadInv';
                            //obtener datos de BD con estos paremetros
                            $cuentaSeccionCostoVentaArt = ConfiguracionInventario::where('nombre_seccion', $nombre_seccion_CostoVenta)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionImportacioncuentaContable')->first();
                            $cuenta_contable_costo_venta_art = CuentasContables::find($cuentaSeccionCostoVentaArt->id_cuenta_contaable);
                            $cuenta_contable_costo_venta_art_padre = CuentasContables::find($cuenta_contable_costo_venta_art->id_cuenta_padre);

                            $documento_cuenta_contableS1 = new DocumentosCuentas;
                            $documento_cuenta_contableS1->id_documento = $documento->id_documento;
                            $documento_cuenta_contableS1->concepto = $cuentaSeccionCostoVentaArt->descripcion_movimiento . ' ' . $entrada->codigo_entrada;

                            if ($cuentaSeccionCostoVentaArt->debe_haber === 1) {
                                if ($currency_id->valor === 1) {
                                    $documento_cuenta_contableS1->debe = $total_costo_me;
                                    $documento_cuenta_contableS1->haber = 0;
                                    $documento_cuenta_contableS1->debe_org = $total_costo;
                                    $documento_cuenta_contableS1->haber_org = 0;
                                } else {
                                    $documento_cuenta_contableS1->debe = $total_costo;
                                    $documento_cuenta_contableS1->haber = 0;
                                    $documento_cuenta_contableS1->debe_org = $total_costo_me;
                                    $documento_cuenta_contableS1->haber_org = 0;
                                }

                            } else {
                                if ($currency_id->valor === 1) {
                                    $documento_cuenta_contableS1->debe = 0;
                                    $documento_cuenta_contableS1->haber = $total_costo_me;
                                    $documento_cuenta_contableS1->debe_org = 0;
                                    $documento_cuenta_contableS1->haber_org = $total_costo;
                                } else {
                                    $documento_cuenta_contableS1->debe = 0;
                                    $documento_cuenta_contableS1->haber = $total_costo_me;
                                    $documento_cuenta_contableS1->debe_org = 0;
                                    $documento_cuenta_contableS1->haber_org = $total_costo;
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

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'result' => null,
                        'messages' => null
                    ]);

                } else {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'result' => 'La entrada ha sido modificada previamente, no se pueden grabar los cambios',
                        'messages' => null
                    ]);
                }


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

    public function obtenerNumeroEntrada()
    {
        $num['Numero'] = counter()->next('entrada');
        // $num['Fecha'] = date('Y-m-d');
        return response()->json([
            'status' => 'success',
            'result' => $num,
            'messages' => null
        ]);
    }

    public function crearEntradaPorDevolucion(Request $request)
    {
        $messages = [
            'entrada_original.entradas_productos.min' => 'Se requiere agregar un producto por lo menos.',
            'entrada_original.entradas_productos.required' => 'Se requiere agregar un producto por lo menos.',
            'entrada_original.entradas_productos.*.id_bodega_producto.required' => 'Seleccione un producto válido',
            'entrada_original.entradas_productos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'entrada_original.entradas_productos.*.cantidad_dev.min' => 'La cantidad debe ser mayor que cero',
            'entrada_original.entradas_productos.*.cantidad_dev.required' => 'La cantidad es requerida',
        ];

        $rules = [
            'entrada_original.codigo_entrada' => 'required',
            'entrada_original.fecha_entrada' => 'required',
            'entrada_original.id_tipo_entrada' => 'required',
            'entrada_original.id_bodega' => 'required',
            'entrada_original.id_proveedor' => 'required',
            'entrada_original.entradas_productos' => 'required|array|min:1',
            'entrada_original.entradas_productos.*.id_bodega_producto' => 'required|integer|exists:pgsql.inventario.bodegas_productos,id_bodega_producto',
            'entrada_original.entradas_productos.*.precio_unitario' => 'required|numeric|min:0.01',
            'entrada_original.entradas_productos.*.cantidad_dev' => 'required|numeric|min:1',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();
                $entradaPorDevolucion = new Entradas;
                $entradaPorDevolucion->codigo_entrada = counter()->next('entrada_devolucion') . '-' . $request->entrada_original['codigo_entrada'];
                $entradaPorDevolucion->id_tipo_entrada = 2;
                $entradaPorDevolucion->fecha_entrada = $request->entrada_original['fecha_entrada'];
                $entradaPorDevolucion->id_proveedor = $request->entrada_original['id_proveedor'];
                $entradaPorDevolucion->id_bodega = $request->entrada_original['id_bodega'];
                $entradaPorDevolucion->usuario_registra = Auth::user()->usuario;
                $entradaPorDevolucion->id_entrada_dev = $request->entrada_original['id_entrada'];
                $entradaPorDevolucion->estado = 1;

                date_default_timezone_set('America/Managua');
                $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
                $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                $log['fecha_log'] = $dias[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y') . ' a las ' . date('h:i:s A');
                $log['registro'] = 'Registro de entrada por devolución en sistema por ' . $entradaPorDevolucion->usuario_registra;
                $entradaPorDevolucion->log_entrada = '[' . json_encode($log) . ']';

                $entradaPorDevolucion->save();
                counter()->increment('entrada_devolucion');

                //print_r($entradaPorDevolucion);
                //print_r($request->entrada_original['entradas_productos']);
                $unidadesDiferencia = 0;
                foreach ($request->entrada_original['entradas_productos'] as $producto) {

                    if ($producto['cantidad_dev'] > 0) {

                        $entrada_productoPorDevolucion = new EntradaProductos;
                        $entrada_productoPorDevolucion->id_bodega_producto = $producto['id_bodega_producto'];
                        $entrada_productoPorDevolucion->id_entrada = $entradaPorDevolucion->id_entrada;
                        $entrada_productoPorDevolucion->descripcion_producto = $producto['descripcion_producto'];
                        $entrada_productoPorDevolucion->codigo_producto = $producto['codigo_producto'];
                        $entrada_productoPorDevolucion->unidad_medida = $producto['unidad_medida'];
                        $entrada_productoPorDevolucion->precio_unitario = $producto['precio_unitario'];
                        $entrada_productoPorDevolucion->cantidad = $producto['cantidad_dev'] * -1;
                        $entrada_productoPorDevolucion->cantidad_faltante = 0;
                        $entrada_productoPorDevolucion->id_entrada_producto_dev = $producto['id_entrada_producto'];
                        $entrada_productoPorDevolucion->save();

                        $entrada_productoOriginal = EntradaProductos::findOrFail($producto['id_entrada_producto']);
                        $entrada_productoOriginal->cantidad_faltante = $entrada_productoOriginal->cantidad_faltante + $producto['cantidad_dev'];
                        $unidadesDiferencia = $unidadesDiferencia + $entrada_productoOriginal->cantidad - $entrada_productoOriginal->cantidad_faltante;
                        $entrada_productoOriginal->save();
                    }
                    //print_r($entrada_producto);
                }

                $entradaOriginal = Entradas::findOrFail($request->entrada_original['id_entrada']);
                $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
                $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                $log['fecha_log'] = $dias[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y') . ' a las ' . date('h:i:s A');
                $log['registro'] = 'Se ha registrado una devolución para esta entrada con código: ' . $entradaPorDevolucion->codigo_entrada . ' por el usuario ' . Auth::user()->usuario;

                $log_actual = Array(json_decode($entradaOriginal->log_entrada));
                array_push($log_actual[0], $log);
                $entradaOriginal->log_entrada = json_encode($log_actual[0]);
                $entradaOriginal->save();

                /* $entradaPorDevolucionChS = Entradas::findOrFail( $entradaPorDevolucion->id_entrada);
                 $entradaPorDevolucionChS->estado = 2;
                 $entradaPorDevolucionChS->save();
                 */
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
     * Crear borrador y auto guardado
     *
     * @access    public
     * @param Request $request
     * @return JsonResponse
     * @author octaviom
     */

    public
    function nuevo(Request $request)
    {

        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();

        $entrada = new Entradas();
        $entrada->codigo_entrada = Entradas::max('id_entrada') + 1;
        $entrada->id_tipo_entrada = 1;
        $entrada->id_proveedor = 1;
        $entrada->id_bodega = 1;
        $entrada->fecha_entrada = date("Y-m-d H:i:s");
        $entrada->estado = 99;
        $entrada->id_empresa = $usuario_empresa->id_empresa;
        $entrada->u_creacion = Auth::user()->name;

        $entrada->save();

        $tasa = TasasCambios::select('tasa')->where('fecha', date("Y-m-d"))->first();

        return response()->json([
            'status' => 'success',
            'result' => [
                'id_entrada' => $entrada->id_entrada,
                't_cambio' => $tasa
            ],
            'messages' => null
        ]);
    }

    /**
     * Metodo de auto guardado por registros en detalle
     * @access public
     * @param Request $request
     * @return JsonResponse
     * @author octaviom
     *
     */

    public function autosaveEntradaProducto(Request $request)
    {
        $rules = [
            'id_entrada' => 'required|integer|min:1',
            'codigo_barra' => 'nullable|string|max:50',
            'id_producto' => 'required|integer',
            'id_entrada_producto' => 'nullable|integer',
            'numero_documento' => 'required|string'

        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try {
                DB::beginTransaction();
                if ($request->estado === 0) {

                    if (!empty($request->id_entrada)) {
                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                        EntradaProductos::where('id_entrada_producto', $request->id_entrada_producto)->where('id_empresa', $usuario_empresa->id_empresa)->delete();
                    }

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'id_entrada_producto' => null,
                        ],
                        'messages' => null
                    ]);

                } else {
                    if (!empty($request->id_entrada_producto)) {

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                        $producto_individual = EntradaProductos::find($request->id_entrada_producto);
                        $producto_individual->id_empresa = $usuario_empresa->id_empresa;
                        $producto_individual->save();

                        DB::commit();
                        return response()->json([
                            'status' => 'success',
                            'result' => [
                                'id_entrada_producto' => $producto_individual->id_entrada_producto,
                            ],
                            'messages' => null
                        ]);
                    } else {

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
                        //$bateriaRegistrada = InventarioEntradaInicialProductos::where('codigo_garantia',$request->codigo_garantiax)->where('id_entrada_inicial',$request->id_entrada_inicial)->first();
                        $producto_registrado = EntradaProductos::where('id_entrada', $request->id_entrada)->where('id_empresa', $usuario_empresa->id_empresa)->first();


                        if (!empty($producto_registrado)) {
//                            EntradaProductos::where('id_entrada_producto', $producto_registrado->id_entrada_producto)->where('id_empresa', $usuario_empresa->id_empresa)->delete();
                        }
                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();


                        $producto_individual = new EntradaProductos();

                        $bodega_sub = BodegasProductos::where('id_bodega', $request->id_bodega)->where('id_producto', $request->id_producto)->where('id_empresa', $usuario_empresa->id_empresa)->where('no_documento', $request->numero_documento)->first();
                        if (!empty($bodega_sub)) {
                            $producto_individual->id_bodega_producto = $bodega_sub['id_bodega_producto'];
                        } else {
                            $nueva_bodega_sub = new BodegasProductos;
                            $nueva_bodega_sub->id_bodega = $request->id_bodega;
                            $nueva_bodega_sub->id_producto = $request->id_producto;
                            $nueva_bodega_sub->cantidad = 0;
                            $nueva_bodega_sub->u_creacion = Auth::user()->name;
                            $nueva_bodega_sub->id_empresa = $usuario_empresa->id_empresa;
                            $nueva_bodega_sub->estado = 1;
                            $nueva_bodega_sub->no_documento = $request->numero_documento;
                            $nueva_bodega_sub->save();
                            $producto_individual->id_bodega_producto = $nueva_bodega_sub->id_bodega_producto;
                        }

                        $producto_individual->id_entrada = $request->id_entrada;
                        $producto_individual->codigo_producto = $request->codigo_sistema;
                        $producto_individual->descripcion_producto = $request->descripcion;
                        $producto_individual->unidad_medida = $request->unidad_medida;
                        $producto_individual->precio_unitario = $request->precio_unitario; // multiplicar por la tasa de cambio
                        $producto_individual->precio_unitario_me = $request->precio_unitario;
                        $producto_individual->cantidad_solicitada = $request->cantidad_solicitada;
                        $producto_individual->cantidad_recibida = 0;
                        $producto_individual->cantidad_faltante = 0;
                        $producto_individual->estado = 1;
                        $producto_individual->u_creacion = Auth::user()->name;
                        $producto_individual->id_empresa = $usuario_empresa->id_empresa;
                        $producto_individual->no_documento = $request->numero_documento;
                        $producto_individual->save();

                        DB::commit();

                        return response()->json([
                            'status' => 'success',
                            'result' => [
                                'id_entrada_producto' => $producto_individual->id_entrada_producto,
                            ],
                            'messages' => null
                        ]);


                    }

                }


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

    public
    function cambiarEstado(Request $request)
    {

        $rules = [
            'id_entrada' => 'required',
            'estado' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $entrada = Entradas::find($request->id_entrada);
            if ($entrada->es_editable && $request->estado >= 0 && $request->estado <= 2 && $entrada->estado <> $request->estado) {

                $estado_org = $entrada->estado;
                $entrada->estado = $request->estado;

                if ($request->estado == 0 || $request->estado == 2) {
                    $entrada->es_editable = 0;
                }

                $estados[0] = 'Cancelada';
                $estados[1] = 'Emitida';
                $estados[2] = 'Aprobada';

                date_default_timezone_set('America/Managua');
                $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
                $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                $log['fecha_log'] = $dias[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y') . ' a las ' . date('h:i:s A');
                $log['registro'] = 'Cambiado el estado de la entrada de ' . $estados[$estado_org] . ' a estado ' . $estados[$request->estado] . ' por usuario ' . Auth::user()->usuario;
                $log_actual = Array(json_decode($entrada->log_entrada));
                // print_r($log);
                // print_r($log_actual[0]);
                array_push($log_actual[0], $log);
                $entrada->log_entrada = json_encode($log_actual[0]);
                // echo $entrada->log_entrada;
                $entrada->save();

                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Error al cambiar el estado de la entrada, revise si la entrada esta bloqueada',
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
