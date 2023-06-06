<?php


namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Admon\Zonas;
use App\Models\CajaBanco\Facturas;
use App\Models\CajaBanco\FacturasExportacion;
use App\Models\Contabilidad\CuentasContables;
use App\Models\Contabilidad\DocumentosContables;
use App\Models\Contabilidad\DocumentosCuentas;
use App\Models\Contabilidad\PeriodosFiscales;
use App\Models\Contabilidad\PeriodosMeses;
use App\Models\Contabilidad\TiposDocumentos;
use App\Models\Inventario\BodegaProductos;
use App\Models\Inventario\Bodegas;
use App\Models\Inventario\ConfiguracionInventario;
use App\Models\Inventario\EntradaProductos;
use App\Models\Inventario\Entradas;
use App\Models\Inventario\MovimientosProductos;
use App\Models\Inventario\Productos;
use App\Models\Inventario\ProductosVistaVenta;
use App\Models\Inventario\Proveedores;
use App\Models\Inventario\SalidaProductos;
use App\Models\Inventario\Salidas;
use App\Models\Inventario\TipoSalida;
use App\Models\Inventario\UnidadMedida;
use App\Models\Ventas\Clientes;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;
use const Grpc\CHANNEL_IDLE;

class SalidasController extends Controller
{
    /**
     * Get List of salidas
     *
     * @access  public
     * @param Request $request
     * @param Salidas $salidas
     * @return JsonResponse
     */

    public function obtener(Request $request, Salidas $salidas)
    {
        $salidas = $salidas->obtenerSalidas($request);

        foreach ($salidas as $salida) {
            //   print_r($entrada);
            $items = collect($salida->salidaProductos);

            $salida->tot_unidades = $items->sum(function ($item) {
                return $item['cantidad_saliente'];
            });

            $salida->tot_unidades_despachadas = $items->sum(function ($item) {
                return $item['cantidad_despachada'];
            });

            $salida->subtotal = $items->sum(function ($item) {
                return $item['precio_unitario'] * $item['cantidad_saliente'];
            });

            $salida->total = $salida->subtotal;
        }

        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $salidas->total(),
                'rows' => $salidas->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Get List of Entrada
     *
     * @access  public
     * @param Request $request
     * @param Salidas $salida
     * @return JsonResponse
     */

    public function obtenerSalida(Request $request, Salidas $salida)
    {
        $rules = [
            'id_salida' => 'required|integer|min:1'
        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $productos = $salida->obtenerProductosSalida($request);
            $salida = $salida->obtenerSalida($request);
            $proveedores = Proveedores::all();
            $tipos_salidas = TipoSalida::all();
            $nombre_empresa = Ajustes::where('id_ajuste', 4)->select('valor')->first();
            $direccion_empresa = Ajustes::where('id_ajuste', 5)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
            $telefono_empresa = Ajustes::where('id_ajuste', 6)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();

            $bodegas = Bodegas::where('estado', 1)->with(['productosBodega' => function ($query) {
                $query->with('producto')->where('cantidad', '>', 0);
            }])->get();


            if (Auth::user()->id_sucursal > 0) {
                $bodegas_dev = Bodegas::select('id_bodega', 'descripcion', 'estado', 'id_tipo_bodega')->where('estado', 1)->whereIn('id_tipo_bodega', array(1, 2))
                    ->where('id_sucursal', Auth::user()->id_sucursal)
                    ->orderby('descripcion')->get();
            } else {
                $bodegas_dev = Bodegas::select('id_bodega', 'descripcion', 'estado', 'id_tipo_bodega')->where('estado', 1)->whereIn('id_tipo_bodega', array(1, 2))->orderby('descripcion')->get();
            }

            if (!empty($salida)) {

                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'salida' => $salida,
                        'productos' => $productos,
                        'bodegas' => $bodegas,
                        'tipos_salidas' => $tipos_salidas,
                        'proveedores' => $proveedores,
                        'bodegas_dev' => $bodegas_dev,
                        'nombre_empresa' => $nombre_empresa->valor,
                        'direccion_empresa' => $direccion_empresa->valor,
                        'telefono_empresa' => $telefono_empresa->valor
                    ],
                    'messages' => null
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_salida' => ["Datos no encontrados"]),
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

    public function obtenerSalidaPorCodigo(Request $request, Salidas $salida)
    {
        $rules = [
            'codigo_salida' => 'required'
        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $salida = $salida->obtenerSalidaPorCodigo($request);

            if (!$salida->isEmpty()) {
                //print_r($entrada);
                $items = collect($salida[0]->salidaProductos);
                $salida[0]->sub_total = $items->sum(function ($item) {
                    return $item['cantidad'] * $item['precio_unitario'];
                });

                $salida[0]->total = $items->sum(function ($item) {
                    return $item['cantidad'] * $item['precio_unitario'];
                });

                $salida[0]->cant_inicial = 0;

                return response()->json([
                    'status' => 'success',
                    'result' => $salida[0],
                    'messages' => null
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => "No se han encontrado resultados para el código de entrada: " . $request->codigo_salida,
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
     * Registra una (nueva) salida
     *
     * @access    public
     * @param
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

            //'codigo_salida' => 'required|string|max:25',
            'numero_documento' => 'required|string|max:50',
            'descripcion_salida' => 'string|max:255|nullable',
            'fecha_salida' => 'required|date',
            'tipo_salida' => 'required|array|min:1',
            'tipo_salida.id_tipo_salida' => 'required|integer|min:1',
            'bodega' => 'required|array|min:1',
            'bodega.id_bodega' => 'required|integer|min:1',
            /*'proveedor' => 'required|array|min:1',
            'proveedor.id_proveedor' => 'required|integer|min:1',*/
            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.precio_unitario' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.cantidad' => 'required|numeric|min:0.01',
            'detalleProductos.*.productox.codigo_sistema' => 'required|string|max:50',
            'detalleProductos.*.productox.descripcion' => 'required|string|max:100',
            'detalleProductos.*.productox.unidad_medida' => 'required|string|max:100',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();
                $salida = new Salidas;
                $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();

                $salida->codigo_salida = Salidas::max('id_salida') + 1;
                $salida->id_tipo_salida = $request->tipo_salida['id_tipo_salida'];
                $salida->fecha_salida = $request->fecha_salida;
                $salida->id_bodega = $request->bodega['id_bodega'];
                $salida->descripcion_salida = $request->descripcion_salida;
                $salida->numero_documento = $request->numero_documento;
                $salida->id_proveedor = null;
                $salida->u_creacion = Auth::user()->name;
                $salida->id_empresa = $usuario_empresa->id_empresa;
                $salida->estado = 1;
                $salida->save();

                foreach ($request->detalleProductos as $producto) {
                    $bodega_sub = BodegaProductos::where('id_bodega_producto', $producto['productox']['id_bodega_producto'])->first();
//                    $bodega_sub->cantidad = $bodega_sub->cantidad - $producto['cantidad'];
//                    $bodega_sub->save();

                    $salida_producto = new SalidaProductos;
                    $salida_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                    $salida_producto->id_salida = $salida->id_salida;
                    $salida_producto->descripcion_producto = $producto['productox']['descripcion'];
                    $salida_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                    $salida_producto->unidad_medida = $producto['productox']['unidad_medida'];
                    $salida_producto->precio_unitario = $producto['productox']['costo_promedio'];
                    $salida_producto->precio_unitario_me = $producto['productox']['costo_promedio_me'];
                    $salida_producto->cantidad_saliente = $producto['cantidad'];
                    $salida_producto->cantidad_despachada = 0;
                    $salida_producto->cantidad_faltante = 0;
                    $salida_producto->u_creacion = $salida->u_creacion;
                    $salida_producto->id_empresa = $salida->id_empresa;
                    $salida_producto->no_documento = $producto['productox']['no_documento'];
                    $salida_producto->save();
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


    public function nueva(Request $request)
    {
        //$bodegas =InventarioBodegas::where('activo', 1)->select('id_bodega','descripcion')->get();
        //$proveedores = InventarioProveedores::where('activo', 1)->orderby('id_proveedor')->select('id_proveedor','nombre_comercial','numero_ruc','numero_cedula')->get();
        $tipos_salida = TipoSalida::where('estado', 1)->select('id_tipo_salida', 'descripcion')->where('id_tipo_salida', 7)/*->whereIn('id_tipo_salida', array(2,5,7))*/ ->get();

        if (Auth::user()->id_sucursal > 0) {
            $bodegas = Bodegas::select('id_bodega', 'descripcion', 'estado', 'id_tipo_bodega')->where('estado', 1)->with(['productosBodega' => function ($query) {
                $query->with('producto')->where('cantidad', '>', 0);
            }])->whereNotIn('id_tipo_bodega', array(6, 4))
                ->where('id_sucursal', Auth::user()->id_sucursal)
                ->orderby('descripcion')->get();
        } else {
            $bodegas = Bodegas::select('id_bodega', 'descripcion', 'estado', 'id_tipo_bodega')->where('estado', 1)->with(['productosBodega' => function ($query) {
                $query->with('producto')->where('cantidad', '>', 0);
            }])->whereNotIn('id_tipo_bodega', array(6, 4))->orderby('descripcion')->get();
        }

        return response()->json([
            'status' => 'success',
            'result' => [
                'bodegas' => $bodegas,
                //'proveedores' => $proveedores,
                'tipos_salida' => $tipos_salida,
            ],
            'messages' => null
        ]);
    }

    public function registrarSalidaManual(Request $request)
    {
        $messages = [
            'detalleProductos.min' => 'Se requiere agregar un producto por lo menos.',
            'detalleProductos.*.productox.id_producto.required' => 'Seleccione un producto válido',
            'detalleProductos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'detalleProductos.*.cantidad.min' => 'La cantidad debe ser mayor que cero',
        ];

        $rules = [

            'numero_documento' => 'required|string|max:50',
            'descripcion_salida' => 'string|max:255|nullable',
            'fecha_salida' => 'required|date',
            'tipo_salida' => 'required|array|min:1',
            'tipo_salida.id_tipo_salida' => 'required|integer|min:1',
            'bodega' => 'required|array|min:1',
            'bodega.id_bodega' => 'required|integer|min:1',

            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.precio_unitario' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.cantidad_despachada' => 'required|integer|min:1',

            'detalle_baterias' => 'required|array|min:1',
            'detalle_baterias.*.productox.id_bateria' => 'required|integer|exists:pgsql.inventario.baterias,id_bateria',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();
            try {

                DB::beginTransaction();
                $salida = new Salidas;

                $salida->codigo_salida = Salidas::max('id_salida') + 1;
                $salida->id_tipo_salida = $request->tipo_salida['id_tipo_salida'];
                $salida->numero_documento = $request->numero_documento;
                $salida->fecha_salida = $request->fecha_salida;
                $salida->id_bodega = $request->bodega['id_bodega'];
                $salida->descripcion_salida = $request->descripcion_salida;
                $salida->id_proveedor = $request->proveedor['id_proveedor'];
                $salida->u_creacion = Auth::user()->name;
                $salida->fecha_despacho = $request->fecha_salida;//date("Y-m-d H:i:s");
                $salida->u_despacho = Auth::user()->name;
                $salida->id_empresa = $usuario_empresa->id_empresa;
                $salida->estado = 2;
                $salida->save();

                foreach ($request->detalleProductos as $producto) {
                    $bodega_sub = BodegaProductos::where('id_producto', $producto['id_producto'])->where('id_bodega', $salida->id_bodega)->first();
                    $bodega_sub->cantidad = $bodega_sub->cantidad - $producto['cantidad_despachada'];
                    $bodega_sub->save();

                    $productox = ProductosVistaVenta::find($producto['id_producto']);

                    $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();
                    $salida_producto = new SalidaProductos;
                    $salida_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                    $salida_producto->id_salida = $salida->id_salida;
                    $salida_producto->descripcion_producto = $productox['descripcion'];
                    $salida_producto->codigo_producto = $productox['codigo_sistema'];
                    $salida_producto->unidad_medida = $productox['unidad_medida'];
                    $salida_producto->precio_unitario = $productox['costo_promedio'];
                    $salida_producto->precio_unitario_me = $productox['costo_promedio_me'];
                    $salida_producto->cantidad_saliente = $producto['cantidad_despachada'];
                    $salida_producto->cantidad_despachada = $producto['cantidad_despachada'];
                    $salida_producto->cantidad_faltante = 0;
                    $salida_producto->u_creacion = $salida->u_creacion;
                    $salida_producto->id_empresa = $usuario_empresa->id_empresa;
                    $salida_producto->save();


                    foreach ($request->detalle_baterias as $bateria) {
                        if ($bateria['productox']['id_producto'] == $bodega_sub->id_producto) {
                            //1 Disponible, 2 Rotación, 3 En Garantía, 4 Mal estado, 5 Usadas, 6 Obsoletas 7 vendida
                            $salida_baterias = new SalidaProductoBaterias();
                            $salida_baterias->id_bateria = $bateria['productox']['id_bateria'];
                            $salida_baterias->id_salida_producto = $salida_producto->id_salida_producto;
                            $salida_baterias->save();

                            //venta de nuevas, recuperadas y obsoletas
                            if ($salida->id_tipo_salida == 1 || $salida->id_tipo_salida == 7 || $salida->id_tipo_salida == 13 || $salida->id_tipo_salida == 14) {
                                $bateria_individual = Baterias::find($salida_baterias->id_bateria);
                                if ($bateria_individual->estado != 7) {
                                    //$bateria_individual->id_bodega_actual = null;
                                    $bateria_individual->reservada = false;
                                    $bateria_individual->estado = 7; //Vendida
                                    $bateria_individual->save();
                                } else {
                                    DB::rollBack();
                                    return response()->json([
                                        'status' => 'error',
                                        'result' => ['detalle_baterias' => ['Hay baterias en el listado que ya han sido despachadas']],
                                        'messages' => null
                                    ]);
                                }
                            }

                            //traslado de baterias
                            if ($salida->id_tipo_salida == 4 || $salida->id_tipo_salida == 8) {
                                $bateria_individual = Baterias::find($salida_baterias->id_bateria);
                                //$bateria_individual->id_bodega_actual = null;
                                //$bateria_individual->estado = 2; //En transito
                                $bateria_individual->reservada = true; //Reservada
                                $bateria_individual->save();
                            }

                            if ($salida->id_tipo_salida == 11) {
                                $bateria_individual = Baterias::find($salida_baterias->id_bateria);
                                $bateria_individual->estado = 5; //usado
                                $bateria_individual->reservada = false;
                                $bateria_individual->save();
                            }


                        }
                    }

                    $costo_promediox = Productos::select(
                        DB::raw('coalesce(inventario.calcular_costo_promedio(inventario.productos.id_producto),0) as costo_promedio'))
                        ->where('id_producto', $producto['id_producto'])->first();

                    $movimiento_producto = new MovimientosProductos();
                    $movimiento_producto->id_bodega_producto = $salida_producto->id_bodega_producto;
                    $movimiento_producto->fecha_movimiento = $salida->fecha_despacho;// date("Y-m-d H:i:s");
                    $movimiento_producto->descripcion_movimiento = $request->tipo_salida['descripcion'] . ' No. ' . $salida->numero_documento;
                    $movimiento_producto->identificador_origen_movimiento = $salida->id_salida;
                    $movimiento_producto->tipo_movimiento = 2;
                    $movimiento_producto->cantidad_movimiento = $producto['cantidad_despachada'] * -1;
                    $movimiento_producto->costo = $costo_promediox['costo_promedio'];
                    $movimiento_producto->usuario_registra = Auth::user()->name;
                    $movimiento_producto->save();

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

    public function anular(Request $request)
    {
        $messages = [
            'detalleProductos.min' => 'Se requiere agregar un producto por lo menos.',
            'detalleProductos.*.productox.id_producto.required' => 'Seleccione un producto válido',
            'detalleProductos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'detalleProductos.*.cantidad.min' => 'La cantidad debe ser mayor que cero',
        ];

        $rules = [
            'id_salida' => 'required|integer|exists:pgsql.inventario.salidas,id_salida',
            'causa_anulacion' => 'required|string|max:255',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();
                $salida = Salidas::find($request->id_salida);

                if ($salida->estado === 2) {

                    if ($salida->id_tipo_salida === 7) {
                        $entrada = Entradas::where('id_salida', $salida->id_salida)->where('estado', '<>', 0)->first();
                        if (empty($entrada)) {
                            //$salida->causa_anulacion = $request->causa_anulacion;
                            $salida->estado = 0;
                            $salida->save();
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'result' => 'La salida no se puede anular ya que existe una devolución vinculada.',
                                'messages' => null
                            ]);
                        }
                    } elseif ($salida->id_tipo_salida === 4) {

                        $entrada = Entradas::where('id_salida', $salida->id_salida)->first();
                        if ($entrada->estado === 1) {
                            $salida->estado = 0;
                            $salida->save();

                            $entrada->estado = 0;
                            $entrada->save();
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'status' => 'error',
                                'result' => 'El traslado ya fue recibido en la otra bodega, no se puede anular.',
                                'messages' => null
                            ]);
                        }
                    }
                } else if ($salida->estado === 1) {
                    if ($salida->id_tipo_salida === 7 || $salida->id_tipo_salida === 4 || $salida->id_tipo_salida === 15) {
                        $salida->estado = 0;
                        $salida->save();
                    }
                    /*if($salida->id_tipo_salida=4||$salida->id_tipo_salida=8){
                        $salida->estado=0;
                        $salida->save();
                    }*/
                } else {

                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'result' => 'No se puede anular esta salida',
                        'messages' => null
                    ]);
                }

                $salidas_productos = SalidaProductos::where('id_salida', $salida->id_salida)->get();
                // print_r($salidas_productos);

                foreach ($salidas_productos as $salida_producto) {
                    $bodega_sub = BodegaProductos::find($salida_producto->id_bodega_producto);
                    if ($salida->condicion_productos == 8) {
                        $bodega_sub->cantidad_recuperadas = $bodega_sub->cantidad_recuperadas + $salida_producto['cantidad_saliente'];
                    } elseif ($salida->condicion_productos == 6) {
                        $bodega_sub->cantidad_obsoletas = $bodega_sub->cantidad_obsoletas + $salida_producto['cantidad_saliente'];
                    } else {
//                        $bodega_sub->cantidad = $bodega_sub->cantidad + $salida_producto['cantidad_saliente'];
                    }
                    $bodega_sub->save();

                    if ($salida->estado = 2) {
                        MovimientosProductos::where('identificador_origen_movimiento', $salida->id_salida)
                            ->where('id_bodega_producto', $salida_producto->id_bodega_producto)
                            ->where('tipo_movimiento', 2)
                            ->delete();
                    }
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

    public function despachar(Request $request)
    {

        $messages = [
            'salida_productos.min' => 'Se requiere agregar un producto por lo menos.',
            // 'salida_productos.*.id_producto.required' => 'Seleccione un producto válido',
            'salida_productos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'salida_productos.*.cantidad_solicitada.min' => 'La cantidad debe ser mayor que cero',
        ];

        $rules = [
            'id_salida' => 'required|integer|exists:pgsql.inventario.salidas,id_salida',
            'salida_productos' => 'required|array|min:1',
            // 'salida_productos.*.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'salida_productos.*.id_bodega_producto' => 'required|integer|min:1|exists:pgsql.inventario.bodega_productos,id_bodega_producto',
            'salida_productos.*.cantidad_saliente' => 'required|numeric|min:0.01',
            'salida_productos.*.cantidad_faltante' => 'required|numeric|min:0.0|lte:salida_productos.*.cantidad_saliente',
            'salida_productos.*.cantidad_despachada' => 'required|numeric|min:0.1|lte:salida_productos.*.cantidad_saliente',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            try {

                DB::beginTransaction();
                $salida = Salidas::find($request->id_salida);

                if ($salida->estado === 1 || $salida->estado === 99) {

                    $salida->fecha_despacho = date("Y-m-d H:i:s");// //$salida->fecha_salida
                    $salida->u_despacho = Auth::user()->name;
                    $salida->estado = 2;
                    $salida->save();

                    $entrada = new Entradas;

                    if ($salida->id_tipo_salida === 4 || $salida->id_tipo_salida === 15) { //Traslado de productos

                        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();
                        $entrada->codigo_entrada = $salida->codigo_salida;//InventarioEntradas::max('id_entrada')+1;
                        $entrada->id_tipo_entrada = 7; //traslado de productos
                        $entrada->condicion_productos = $salida->condicion_productos;
                        $entrada->fecha_entrada = $request->fecha_salida;
                        $entrada->numero_documento = $salida->numero_documento;
                        $entrada->id_proveedor = $salida->id_proveedor;
                        $entrada->id_bodega = $salida->id_bodega_entrante;
                        $entrada->id_salida = $salida->id_salida;
                        $entrada->descripcion_entrada = $request->descripcion_salida;
                        $entrada->u_creacion = $salida->u_creacion;
                        $entrada->estado = 1;
                        $entrada->id_empresa = $usuario_empresa->id_empresa;
                        $entrada->save();
                    } elseif ($salida->id_tipo_salida === 1 || $salida->id_tipo_salida === 10 || $salida->id_tipo_salida === 13 || $salida->id_tipo_salida === 14) {
                        $factura = Facturas::where('id_salida', $salida->id_salida)->whereNotNull('id_salida')->first();
                        $factura->estado = 2;
                        $factura->save();
                    } elseif ($salida->id_tipo_salida === 2) {
                        $requisa = Requisas::where('id_salida', $salida->id_salida)->whereNotNull('id_salida')->first();
                        if (!empty($requisa) && $requisa->estado === 2) {
                            $requisa->estado = 3;
                            $requisa->save();
                        }
                    } elseif ($salida->id_tipo_salida === 9) {
                        $factura = CajaBancoFacturasExportacion::where('id_salida', $salida->id_salida)->whereNotNull('id_salida')->first();
                        if (!empty($factura)) {
                            $factura->estado = 2;
                            $factura->save();
                        }
                    } elseif ($salida->id_tipo_salida === 8) {
                        $entrada->codigo_entrada = $salida->codigo_salida;//InventarioEntradas::max('id_entrada')+1;
                        $entrada->id_tipo_entrada = 2;
                        $entrada->condicion_productos = $salida->condicion_productos;
                        $entrada->fecha_entrada = $request->fecha_salida;
                        $entrada->id_proveedor = $salida->id_proveedor;
                        $entrada->numero_documento = $salida->numero_documento;
                        $entrada->id_bodega = $salida->id_bodega_entrante;
                        $entrada->id_salida = $salida->id_salida;
                        $entrada->descripcion_entrada = $request->descripcion_salida;
                        $entrada->u_creacion = $salida->u_creacion;
                        $entrada->id_empresa = $usuario_empresa->id_empresa;
                        $entrada->estado = 1;
                        $entrada->save();
                    }/*elseif($salida->id_tipo_salida == 12){//Traslado interno BATERIAS recuperadas

                    $entrada->codigo_entrada = $salida->codigo_salida;//InventarioEntradas::max('id_entrada')+1;
                    $entrada->id_tipo_entrada = 7;
                    $entrada->fecha_entrada = $request->fecha_salida;
                    $entrada->id_proveedor = $salida->id_proveedor;
                    $entrada->id_bodega = $salida->id_bodega_entrante;
                    $entrada->id_salida = $salida->id_salida;
                    $entrada->descripcion_entrada = $request->descripcion_salida;
                    $entrada->u_creacion = $salida->u_creacion;
                    $entrada->estado = 1;
                    $entrada->save();
                }*/
                    foreach ($request->salida_productos as $producto) {
                        // print_r($producto);
                        if ($salida->id_tipo_salida === 4 || $salida->id_tipo_salida === 8 || $salida->id_tipo_salida === 15) { //traslado y devolucion
                            $entrada_producto = new EntradaProductos;

                            $bodega_sub = BodegaProductos::where('id_bodega', $salida->id_bodega_entrante)->where('id_producto', $producto['bodega_producto']['id_producto'])->where('no_documento',$producto['bodega_producto']['no_documento'])->get();

                            if (!empty($bodega_sub[0])) {
                                $entrada_producto->id_bodega_producto = $bodega_sub[0]['id_bodega_producto'];
                            } else {
                                $nueva_bodega_sub = new BodegaProductos;
                                $nueva_bodega_sub->id_bodega = $salida->id_bodega_entrante;
                                $nueva_bodega_sub->id_producto = $producto['bodega_producto']['id_producto'];
                                $nueva_bodega_sub->cantidad = 0;
                                $nueva_bodega_sub->u_creacion = $salida->u_creacion;
                                $nueva_bodega_sub->id_empresa = $usuario_empresa->id_empresa;
                                $nueva_bodega_sub->no_documento = $producto['bodega_producto']['no_documento'];
                                $nueva_bodega_sub->save();
                                $entrada_producto->id_bodega_producto = $nueva_bodega_sub->id_bodega_producto;
                            }

                            $entrada_producto->id_entrada = $entrada->id_entrada;
                            $entrada_producto->codigo_producto = $producto['codigo_producto'];
                            $entrada_producto->descripcion_producto = $producto['descripcion_producto'];
                            $entrada_producto->unidad_medida = $producto['unidad_medida'];
                            $entrada_producto->precio_unitario = $producto['precio_unitario'];
                            $entrada_producto->cantidad_solicitada = $producto['cantidad_saliente'];
                            $entrada_producto->cantidad_recibida = 0;
                            $entrada_producto->cantidad_faltante = 0;
                            $entrada_producto->u_creacion = $entrada->u_creacion;
                            $entrada_producto->id_empresa = $usuario_empresa->id_empresa;
                            $entrada_producto->no_documento = $producto['bodega_producto']['no_documento'];
                            $entrada_producto->save();
                        }

                        $salida_producto = SalidaProductos::find($producto['id_salida_producto']);
                        $salida_producto->cantidad_despachada = $producto['cantidad_despachada'];
                        $salida_producto->cantidad_faltante = $producto['cantidad_faltante'];
                        $salida_producto->id_empresa = $usuario_empresa->id_empresa;
                        $salida_producto->save();

                        // Descontar cantidad despachada de las existencias

                        $bodega_sub2 = BodegaProductos::where('id_bodega_producto', $producto['id_bodega_producto'])->first();
//                        print_r($bodega_sub2);
                        // Realizando calculos para despacho de productos
                        $bodega_sub2->cantidad -= $producto['cantidad_despachada'];
//                        print_r($bodega_sub2->cantidad -= $producto['cantidad_despachada']);
                        $bodega_sub2->save();

                        // Inicializamos variable en 0 para sumar los costos de producto
                        $total_costo = 0;
                        $total_costo_me = 0;
                        // Asignamos suamtoria del costo estandar del productor contenido en la salida
                        $total_costo += round(( $producto['precio_unitario'] * $producto['cantidad_despachada'] ) ,6);
                        $total_costo_me += round(( $producto['precio_unitario_me'] * $producto['cantidad_despachada'] ) ,6);

                        if ($salida->id_tipo_salida === 2) {
                            $requisa_producto = RequisaProductos::where('id_salida_producto', $salida_producto->id_salida_producto)->first();
                            if (!empty($requisa_producto)) {
                                $requisa_producto->cantidad_recibida = $producto['cantidad_despachada'];
                                $requisa_producto->save();
                            }
                        }

                        // Calcular costo promedio en cordobas
                        $costo_promediox = BodegaProductos::select(
                            DB::raw('coalesce(inventario.calcular_costo_promedio(inventario.bodega_productos.id_producto, inventario.bodega_productos.no_documento),0) as costo_promedio'))
                            ->where('id_producto', $producto['bodega_producto']['id_producto'])->where('no_documento',$producto['bodega_producto']['no_documento'])->first();

                        // Calcular costo promedio en dolares
                        $costo_promediox_me = BodegaProductos::select(
                            DB::raw('coalesce(inventario.calcular_costo_promedio_me(inventario.bodega_productos.id_producto, inventario.bodega_productos.no_documento),0) as costo_promedio'))
                            ->where('id_producto', $producto['bodega_producto']['id_producto'])->where('no_documento',$producto['bodega_producto']['no_documento'])->first();


                        $producto_costo_nuevo = Productos::where('id_producto',$producto['bodega_producto']['id_producto'])->first();
                        $producto_costo_nuevo->costo_estandar = $costo_promediox['costo_promedio'];
                        $producto_costo_nuevo->costo_estandar_me = $costo_promediox_me['costo_promedio'];
                        $producto_costo_nuevo->save();

                        // Registrar movimiento en kardex

//                        print_r($costo_promediox['costo_promedio']);
                        $movimiento_producto = new MovimientosProductos();
                        $movimiento_producto->id_bodega_producto = $producto['id_bodega_producto'];
                        $movimiento_producto->fecha_movimiento = $salida->fecha_despacho;// date("Y-m-d H:i:s");
                        $movimiento_producto->descripcion_movimiento = $request->salida_tipo['descripcion'] . ' No. ' . $salida->numero_documento;
                        $movimiento_producto->identificador_origen_movimiento = $salida->id_salida;
                        $movimiento_producto->tipo_movimiento = 2;
                        $movimiento_producto->cantidad_movimiento = $producto['cantidad_despachada'] * -1;
                        $movimiento_producto->costo = $costo_promediox['costo_promedio'];
                        $movimiento_producto->costo_me = $costo_promediox_me['costo_promedio'];
                        $movimiento_producto->usuario_registra = Auth::user()->name;
                        $movimiento_producto->id_empresa = $usuario_empresa->id_empresa;
                        $movimiento_producto->save();


                        if ($salida->id_tipo_salida == 15 && !empty($salida->id_cliente)) {//nuevo tipo 15 traslado consignacion antes 4
                            $consignacion_producto = new ConsignacionProductos();
                            $consignacion_producto->id_bodega_producto = $entrada_producto->id_bodega_producto;
                            $consignacion_producto->id_producto = $producto['bodega_producto']['id_producto'];
                            $consignacion_producto->id_cliente = $salida->id_cliente;
                            $consignacion_producto->fecha_movimiento = $salida->fecha_despacho;// date("Y-m-d H:i:s");
                            $consignacion_producto->descripcion_movimiento = $request->salida_tipo['descripcion'] . ' No. ' . $salida->codigo_salida;
                            $consignacion_producto->identificador_origen_movimiento = $entrada->id_entrada;
                            $consignacion_producto->tipo_movimiento = 1;//1 consignacion 2 venta 3 devolucion
                            $consignacion_producto->cantidad_movimiento = $producto['cantidad_despachada'];
                            $consignacion_producto->usuario_registra = Auth::user()->usuario;
                            $consignacion_producto->save();
                        }


                        if ($salida->id_tipo_salida === 8 && !empty($salida->id_cliente)) { //Consignación de producto
                            $consignacion_producto = new ConsignacionProductos();
                            $consignacion_producto->id_bodega_producto = $producto['id_bodega_producto'];
                            $consignacion_producto->id_producto = $producto['bodega_producto']['id_producto'];
                            $consignacion_producto->id_cliente = $salida->id_cliente;
                            $consignacion_producto->fecha_movimiento = $salida->fecha_despacho;// date("Y-m-d H:i:s");
                            $consignacion_producto->descripcion_movimiento = $request->salida_tipo['descripcion'] . ' No. ' . $salida->codigo_salida;
                            $consignacion_producto->identificador_origen_movimiento = $salida->id_salida;
                            $consignacion_producto->tipo_movimiento = 3;//1 consignacion 2 venta 3 devolucion
                            $consignacion_producto->cantidad_movimiento = $producto['cantidad_despachada'] * -1;
                            $consignacion_producto->usuario_registra = Auth::user()->usuario;
                            $consignacion_producto->save();
                        }

                        $factura = Facturas::where('id_salida', $salida->id_salida)->whereNotNull('id_salida')->first();

                        // Contabilización de salida movido a facturación
//                        if($salida->id_tipo_salida === 1){ //Salida por venta
//                            /*INICIA movimiento contable - Factura*/
//
//                            $clientex = Clientes::select('id_cliente', 'id_zona')->find($factura->id_cliente);
//                            $zonax = Zonas::select('id_zona', 'id_centro_costo', 'id_centro_ingreso')->find($clientex->id_zona);
//                            $tipos_cuentas = array(4, 5, 6);
//                            $factura = Facturas::where('id_salida', $salida->id_salida)->whereNotNull('id_salida')->first();
//                            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
//                            $currency_id = Ajustes::where('id_ajuste', 1)->where('id_empresa', $usuario_empresa->id_empresa)->select('valor')->first();
//
//                            $documento = new DocumentosContables();
//                            $tipo = TiposDocumentos::find(10);  //Tipo comprobante de inventario
//                            $fecha = date("Y-m-d H:i:s");
//                            $codigo = $documento->obtenerCodigoDocumento(array('id_tipo_doc' => $tipo->id_tipo_doc, 'fecha_doc' => $fecha));
//
//                            $nuevo_codigo = json_decode($codigo[0]);
//
//                            date_default_timezone_set('America/Managua');
//
//                            $documento->num_documento = $tipo->prefijo . '-' . $nuevo_codigo->secuencia;
//                            $documento->fecha_emision = date('Y-m-d');
//                            $documento->codigo_documento = $nuevo_codigo->secuencia;
//
//
//                            $date = DateTime::createFromFormat("Y-m-d", $documento->fecha_emision);
//
//                            $periodo = PeriodosFiscales::where('periodo', $date->format("Y"))->get();
//
//                            if (empty($periodo[0])) {
//                                return response()->json([
//                                    'status' => 'error',
//                                    'result' => array('fecha_emision' => ["El periodo " . $date->format("Y") . " no se encuentra registrado, por favor consulte al administrador"]),
//                                    'messages' => null
//                                ]);
//                                exit;
//                            }
//
//                            if ($periodo[0]->estado) {
//                                return response()->json([
//                                    'status' => 'error',
//                                    'result' => array('fecha_emision' => ["El periodo " . $date->format("Y") . " es inválido, ya que se encuentra en estado COMPLETADO"]),
//                                    'messages' => null
//                                ]);
//                                exit;
//                            }
//
//                            $periodo_mes = PeriodosMeses::where('id_periodo_fiscal', $periodo[0]->id_periodo_fiscal)->where('mes', $date->format("n"))->get();
//
//                            if (empty($periodo_mes[0])) {
//                                return response()->json([
//                                    'status' => 'error',
//                                    'result' => array('fecha_emision' => ["El mes " . $date->format("F") . " no se encuentra registrado, por favor consulte al administrador"]),
//                                    'messages' => null
//                                ]);
//                                exit;
//                            }
//
//                            if ($periodo_mes[0]->estado === 2) {
//                                return response()->json([
//                                    'status' => 'error',
//                                    'result' => array('fecha_emision' => ["El mes " . config('global.meses')[$periodo_mes[0]->mes - 1] . " es inválido, ya que se encuentra en estado COMPLETADO"]),
//                                    'messages' => null
//                                ]);
//                                exit;
//                            }
//
//                            $documento->id_periodo_fiscal = $periodo[0]->id_periodo_fiscal;
//
//                            $documento->id_tipo_doc = 10; // Comprobante de inventario
//                            $documento->valor = $total_costo;
//                            $documento->valor_me = $total_costo_me;
//                            if($currency_id->valor === 1){
//                                $documento->concepto = 'Registramos salida de productos por factura No. ' . $factura->no_documento . '. Monto total C$ ' . $total_costo;
//                            }else {
//                                $documento->concepto = 'Registramos salida de productos por factura No. ' . $factura->no_documento . '. Monto total $ ' . $total_costo_me;
//                            }
//
//                            $documento->id_moneda = $currency_id->valor;
//                            $documento->u_creacion = Auth::user()->name;
//                            $documento->estado = 1;
//                            $documento->id_empresa = $usuario_empresa->id_empresa;
//                            $documento->save();
//                            $salida->id_documento_contable = $documento->id_documento;
//                            $salida->save();
//
//                            TiposDocumentos::find($documento->id_tipo_doc)->increment('secuencia');
//
//                            //definición de tipo de configuración de comprobantes
//                            $id_tipo_configuracion = 2; // Salida por ventas
//
//                            if($total_costo > 0 || $total_costo_me > 0 ){
//
//                                // Registramos contabilización de costo de venta de productos
//                                $nombre_seccion_ArtCosto = 'ArtCostBod';
//                                //obtener datos de BD con estos paremetros
//                                $cuentaSeccionArtCosto = ConfiguracionInventario::where('nombre_seccion', $nombre_seccion_ArtCosto)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionImportacioncuentaContable')->first();
//                                $cuenta_contable_art_costo = CuentasContables::find($cuentaSeccionArtCosto->id_cuenta_contaable);
//                                $cuenta_contable_art_costo_padre = CuentasContables::find($cuenta_contable_art_costo->id_cuenta_padre);
//
//                                $documento_cuenta_contableS1 = new DocumentosCuentas;
//                                $documento_cuenta_contableS1->id_documento = $documento->id_documento;
//                                $documento_cuenta_contableS1->concepto = $cuentaSeccionArtCosto->descripcion_movimiento . ' ' . $salida->codigo_salida;
//
//                                if ($cuentaSeccionArtCosto->debe_haber === 1) {
//                                    if ($currency_id->valor === 1) {
//                                        $documento_cuenta_contableS1->debe = $total_costo;
//                                        $documento_cuenta_contableS1->haber = 0;
//                                        $documento_cuenta_contableS1->debe_org = $total_costo_me;
//                                        $documento_cuenta_contableS1->haber_org = 0;
//                                    } else {
//                                        $documento_cuenta_contableS1->debe = $total_costo_me;
//                                        $documento_cuenta_contableS1->haber = 0;
//                                        $documento_cuenta_contableS1->debe_org = $total_costo;
//                                        $documento_cuenta_contableS1->haber_org = 0;
//                                    }
//
//                                } else {
//                                    if ($currency_id->valor === 1) {
//                                        $documento_cuenta_contableS1->debe = 0;
//                                        $documento_cuenta_contableS1->haber = $total_costo;
//                                        $documento_cuenta_contableS1->debe_org = 0;
//                                        $documento_cuenta_contableS1->haber_org = $total_costo_me;
//                                    } else {
//                                        $documento_cuenta_contableS1->debe = 0;
//                                        $documento_cuenta_contableS1->haber = $total_costo_me;
//                                        $documento_cuenta_contableS1->debe_org = 0;
//                                        $documento_cuenta_contableS1->haber_org = $total_costo;
//                                    }
//
//                                }
//
//                                //Verificación de centros de costo
//
//                                if ($cuenta_contable_art_costo->requiere_aux === 0) {
//                                    $documento_cuenta_contableS1->id_centro = null;
//                                    $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;
//
//                                } else if ($cuenta_contable_art_costo->requiere_aux === 2 || $cuenta_contable_art_costo->requiere_aux === 3) {
//                                    $documento_cuenta_contableS1->id_centro = $cuentaSeccionArtCosto->id_centro_costo;
//                                    $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;
//
//                                } else if ($cuenta_contable_art_costo->requiere_aux === 1) {
//                                    $documento_cuenta_contableS1->id_centro = null;
//                                    $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionArtCosto->id_cat_auxiliar_cxc;
//                                }
//
//                                $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
//                                $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_art_costo->id_cuenta_contable;
//                                $documento_cuenta_contableS1->cta_contable = $cuenta_contable_art_costo->cta_contable;
//                                $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_art_costo_padre->id_cuenta_contable;
//                                $documento_cuenta_contableS1->save();
//
//                                // Registramos contabilización de costos de salida de inventario por venta
//
//                                $nombre_seccion_CostoVenta = 'CostVentArt';
//                                //obtener datos de BD con estos paremetros
//                                $cuentaSeccionCostoVentaArt = ConfiguracionInventario::where('nombre_seccion', $nombre_seccion_CostoVenta)->where('id_tipo_configuracion', $id_tipo_configuracion)->with('configuracionImportacioncuentaContable')->first();
//                                $cuenta_contable_costo_venta_art = CuentasContables::find($cuentaSeccionCostoVentaArt->id_cuenta_contaable);
//                                $cuenta_contable_costo_venta_art_padre = CuentasContables::find($cuenta_contable_costo_venta_art->id_cuenta_padre);
//
//                                $documento_cuenta_contableS1 = new DocumentosCuentas;
//                                $documento_cuenta_contableS1->id_documento = $documento->id_documento;
//                                $documento_cuenta_contableS1->concepto = $cuentaSeccionCostoVentaArt->descripcion_movimiento . ' ' . $salida->codigo_salida;
//
//                                if ($cuentaSeccionCostoVentaArt->debe_haber === 1) {
//                                    if ($currency_id->valor === 1) {
//                                        $documento_cuenta_contableS1->debe = $total_costo;
//                                        $documento_cuenta_contableS1->haber = 0;
//                                        $documento_cuenta_contableS1->debe_org = $total_costo_me;
//                                        $documento_cuenta_contableS1->haber_org = 0;
//                                    } else {
//                                        $documento_cuenta_contableS1->debe = $total_costo_me;
//                                        $documento_cuenta_contableS1->haber = 0;
//                                        $documento_cuenta_contableS1->debe_org = $total_costo;
//                                        $documento_cuenta_contableS1->haber_org = 0;
//                                    }
//
//                                } else {
//                                    if ($currency_id->valor === 1) {
//                                        $documento_cuenta_contableS1->debe = 0;
//                                        $documento_cuenta_contableS1->haber = $total_costo;
//                                        $documento_cuenta_contableS1->debe_org = 0;
//                                        $documento_cuenta_contableS1->haber_org = $total_costo_me;
//                                    } else {
//                                        $documento_cuenta_contableS1->debe = 0;
//                                        $documento_cuenta_contableS1->haber = $total_costo_me;
//                                        $documento_cuenta_contableS1->debe_org = 0;
//                                        $documento_cuenta_contableS1->haber_org = $total_costo;
//                                    }
//
//                                }
//
//                                //Verificación de centros de costo
//
//                                if ($cuenta_contable_costo_venta_art->requiere_aux === 0) {
//                                    $documento_cuenta_contableS1->id_centro = null;
//                                    $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;
//
//                                } else if ($cuenta_contable_costo_venta_art->requiere_aux === 2 || $cuenta_contable_costo_venta_art->requiere_aux === 3) {
//                                    $documento_cuenta_contableS1->id_centro = $cuentaSeccionCostoVentaArt->id_centro_costo;
//                                    $documento_cuenta_contableS1->id_cat_auxiliar_cxc = null;
//
//                                } else if ($cuenta_contable_costo_venta_art->requiere_aux === 1) {
//                                    $documento_cuenta_contableS1->id_centro = null;
//                                    $documento_cuenta_contableS1->id_cat_auxiliar_cxc = $cuentaSeccionCostoVentaArt->id_cat_auxiliar_cxc;
//                                }
//
//                                $documento_cuenta_contableS1->id_moneda = $currency_id->valor;
//                                $documento_cuenta_contableS1->id_cuenta_contable = $cuenta_contable_costo_venta_art->id_cuenta_contable;
//                                $documento_cuenta_contableS1->cta_contable = $cuenta_contable_costo_venta_art->cta_contable;
//                                $documento_cuenta_contableS1->cta_contable_padre = $cuenta_contable_costo_venta_art_padre->id_cuenta_contable;
//                                $documento_cuenta_contableS1->save();
//                            }
//                        }

                    }

                    DB::commit();
                    // DB::rollBack();
                    return response()->json([
                        'status' => 'success',
                        'result' => null,
                        'messages' => null
                    ]);

                } else {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'result' => 'La salida ha sido modificada previamente, no se pueden grabar los cambios',
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


    public function guardarSalida(Request $request)
    {

        $messages = [
            'salida_productos.min' => 'Se requiere agregar un producto por lo menos.',
            // 'salida_productos.*.id_producto.required' => 'Seleccione un producto válido',
            'salida_productos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'salida_productos.*.cantidad_solicitada.min' => 'La cantidad debe ser mayor que cero',
        ];


        $rules = [
            'id_salida' => 'required|integer|exists:pgsql.inventario.salidas,id_salida',
            'salida_productos' => 'required|array|min:1',
            // 'salida_productos.*.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'salida_productos.*.id_bodega_producto' => 'required|integer|min:1|exists:pgsql.inventario.bodega_productos,id_bodega_producto',
            'salida_productos.*.cantidad_saliente' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/|min:1',
//            'salida_productos.*.cantidad_faltante' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0|lte:salida_productos.*.cantidad_saliente',
//            'salida_productos.*.cantidad_despachada' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/|min:0|lte:salida_productos.*.cantidad_saliente',

            'detalle_baterias' => 'nullable|required_if:contiene_baterias,true|array|min:0',
            'detalle_baterias.*.id_bateria' => 'nullable|required_if:contiene_baterias,true|integer|exists:pgsql.inventario.baterias,id_bateria',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();
                $salida = Salidas::find($request->id_salida);

                if ($salida->estado === 1 || $salida->estado === 99) {//despacho en progreso

                    $salida->id_tipo_salida = $request->salida_tipo['id_tipo_salida'];
                    $salida->fecha_salida = $request->fecha_salida;
                    $salida->id_bodega = $request->salida_bodega['id_bodega'];
                    $salida->descripcion_salida = $request->descripcion_salida;
                    $salida->numero_documento = $request->numero_documento;
                    $salida->save();

                    SalidaProductos::where('id_salida', $salida->id_salida)->delete();

                    foreach ($request->salida_productos as $producto) {

                        /*                        if(!empty($producto['id_entrada_producto'])){

                                                    SalidaProductos::where('id_salida_producto', $producto['id_salida_producto'])->delete();
                                                }*/


//                        $bodega_sub2 = BodegaProductos::where('id_bodega_producto',$producto['bodega_producto']['id_bodega_producto'])->first();


//                        print_r($producto['id_salida_producto']);
                        $salida_producto = new SalidaProductos;
                        $salida_producto->id_salida = $salida->id_salida;
                        $salida_producto->id_bodega_producto = $producto['id_bodega_producto'];
                        $salida_producto->descripcion_producto = $producto['descripcion_producto'];
                        $salida_producto->codigo_producto = $producto['codigo_producto'];
                        $salida_producto->unidad_medida = $producto['unidad_medida'];
                        $salida_producto->precio_unitario = $producto['precio_unitario'];
                        $salida_producto->cantidad_saliente = $producto['cantidad_saliente'];
                        $salida_producto->cantidad_despachada = 0;
                        $salida_producto->cantidad_faltante = 0;
                        $salida_producto->u_creacion = $salida->u_creacion;
                        $salida_producto->id_empresa = $salida->id_empresa;
                        $salida_producto->no_documento = $producto['no_documento'];
                        $salida_producto->save();

                    }

                    DB::commit();
                    // DB::rollBack();
                    return response()->json([
                        'status' => 'success',
                        'result' => null,
                        'messages' => null
                    ]);

                } else {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'result' => 'La salida ha sido modificada previamente, no se pueden grabar los cambios',
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


    public function crearSalidaPorDevolucion(Request $request)
    {
        //print_r($request->entrada_original);

        $messages = [
            'salida_original.salida_productos.min' => 'Se requiere agregar un producto por lo menos.',
            'salida_original.salida_productos.required' => 'Se requiere agregar un producto por lo menos.',
            'salida_original.salida_productos.*.id_bodega_producto.required' => 'Seleccione un producto válido',
            'salida_original.salida_productos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'salida_original.salida_productos.*.cantidad_dev.min' => 'La cantidad debe ser mayor que cero',
            'salida_original.salida_productos.*.cantidad_dev.required' => 'La cantidad es requerida',
        ];

        $rules = [
            'salida_original.codigo_salida' => 'required',
            'salida_original.fecha_salida' => 'required',
            'salida_original.id_tipo_salida' => 'required',
            'salida_original.id_bodega_salida' => 'required',
            'salida_original.descripcion_salida' => 'required',
            'salida_original.salida_productos' => 'required|array|min:1',
            'salida_original.salida_productos.*.id_bodega_producto' => 'required|integer|exists:pgsql.inventario.bodegas_productos,id_bodega_producto',
            'salida_original.salida_productos.*.precio_unitario' => 'required|numeric|min:0.01',
            'salida_original.salida_productos.*.cantidad_dev' => 'required|numeric|min:1',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();

            try {

                DB::beginTransaction();
                date_default_timezone_set('America/Managua');
                $salidaPorDevolucion = new Salidas;
                $salidaPorDevolucion->codigo_salida = counter()->next('salida_devolucion') . '-' . $request->salida_original['codigo_salida'];
                $salidaPorDevolucion->id_tipo_salida = 2;
                $salidaPorDevolucion->fecha_salida = $request->fecha_salida;//date('Y-m-d');
                $salidaPorDevolucion->descripcion_salida = 'Registro de devolución de productos de la Salida: ' . $request->salida_original['codigo_salida'];
                $salidaPorDevolucion->id_bodega_salida = $request->salida_original['id_bodega_salida'];
                $salidaPorDevolucion->usuario_registra = Auth::user()->usuario;
                $salidaPorDevolucion->id_salida_dev = $request->salida_original['id_salida'];
                $salidaPorDevolucion->estado = 1;

                $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
                $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                $log['fecha_log'] = $dias[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y') . ' a las ' . date('h:i:s A');
                $log['registro'] = 'Registro de salida por devolución en sistema por ' . $salidaPorDevolucion->usuario_registra;
                $salidaPorDevolucion->nto = '[' . json_encode($log) . ']';

                $salidaPorDevolucion->save();
                counter()->increment('salida_devolucion');

                //print_r($salidaPorDevolucion);
                //print_r($request->entrada_original['entradas_productos']);
                $unidadesDiferencia = 0;
                foreach ($request->salida_original['salida_productos'] as $producto) {

                    if ($producto['cantidad_dev'] > 0) {

                        $salida_productoPorDevolucion = new SalidaProductos;
                        $salida_productoPorDevolucion->id_bodega_producto = $producto['id_bodega_producto'];
                        $salida_productoPorDevolucion->id_salida = $salidaPorDevolucion->id_salida;
                        $salida_productoPorDevolucion->descripcion_producto = $producto['descripcion_producto'];
                        $salida_productoPorDevolucion->codigo_producto = $producto['codigo_producto'];
                        $salida_productoPorDevolucion->unidad_medida = $producto['unidad_medida'];
                        $salida_productoPorDevolucion->precio_unitario = $producto['precio_unitario'];
                        $salida_productoPorDevolucion->cantidad = $producto['cantidad_dev'] * -1;
                        $salida_productoPorDevolucion->cantidad_faltante = 0;
                        $salida_productoPorDevolucion->id_salida_producto_dev = $producto['id_salida_producto'];
                        $salida_productoPorDevolucion->id_empresa = $usuario_empresa->id_empresa;
                        $salida_productoPorDevolucion->save();

                        $salida_productoOriginal = SalidaProductos::findOrFail($producto['id_salida_producto']);
                        $salida_productoOriginal->cantidad_faltante = $salida_productoOriginal->cantidad_faltante + $producto['cantidad_dev'];
                        $unidadesDiferencia = $unidadesDiferencia + $salida_productoOriginal->cantidad - $salida_productoOriginal->cantidad_faltante;
                        $salida_productoOriginal->save();
                    }
                    //print_r($entrada_producto);
                }

                $salidaOriginal = Salidas::findOrFail($request->salida_original['id_salida']);
                $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
                $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                $log['fecha_log'] = $dias[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y') . ' a las ' . date('h:i:s A');
                $log['registro'] = 'Se ha registrado una devolución para esta salida con código: ' . $salidaPorDevolucion->codigo_salida . ' por el usuario ' . Auth::user()->usuario;

                $log_actual = Array(json_decode($salidaOriginal->log_salida));
                array_push($log_actual[0], $log);
                $salidaOriginal->log_salida = json_encode($log_actual[0]);
                $salidaOriginal->save();

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
     * Registra una nueva salida
     *
     * @access    public
     * @param
     * @return JsonResponse
     */

    public function registrarTraslado(Request $request)
    {
        $messages = [
            'detalleProductos.min' => 'Se requiere agregar un producto por lo menos.',
            'detalleProductos.*.productox.id_producto.required' => 'Seleccione un producto válido',
            'detalleProductos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'detalleProductos.*.cantidad.min' => 'La cantidad debe ser mayor que cero',
        ];

        $rules = [
            //'codigo_salida' => 'required|string|max:25',
            'numero_documento' => 'required|string|max:50',
            'descripcion_salida' => 'string|max:255|nullable',
            'fecha_salida' => 'required|date',
            'condicion' => 'required|integer|min:1|max:8',
            'bodega_saliente' => 'required|array|min:1',
            'bodega_saliente.id_bodega' => 'required|integer|min:1',
            'bodega_entrante' => 'required|array|min:1',
            'bodega_entrante.id_bodega' => 'required|integer|min:1',
            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.precio_unitario' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.cantidad' => 'required|integer|min:1',
            'detalleProductos.*.productox.codigo_sistema' => 'required|string|max:50',
            'detalleProductos.*.productox.descripcion' => 'required|string|max:100',
            'detalleProductos.*.productox.unidad_medida' => 'required|string|max:100',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=',Auth::user()->id)->first();
                DB::beginTransaction();
                $salida = new Salidas;

                $salida->codigo_salida = Salidas::max('id_salida') + 1;
                $salida->numero_documento = $request->numero_documento;
                $salida->id_tipo_salida = 4; // salida tipo traslado
                $salida->condicion_productos = $request->condicion;
                $salida->fecha_salida = $request->fecha_salida;
                $salida->id_bodega = $request->bodega_saliente['id_bodega'];
                $salida->id_bodega_entrante = $request->bodega_entrante['id_bodega'];
                $salida->descripcion_salida = $request->descripcion_salida;
                $salida->u_creacion = Auth::user()->name;
                $salida->estado = 1;
                $salida->id_empresa = $usuario_empresa->id_empresa;
                $salida->save();


                /*$entrada = new InventarioEntradas;
                $entrada->codigo_entrada = InventarioEntradas::max('id_entrada')+1;
                $entrada->id_tipo_entrada = 7;
                $entrada->fecha_entrada = $request->fecha_salida;
                $entrada->id_proveedor = $salida->id_proveedor;
                $entrada->id_bodega = $request->bodega_entrante['id_bodega'];
                $entrada->descripcion_entrada = $request->descripcion_salida;
                $entrada->u_creacion = $salida->u_creacion;
                $entrada->estado = 1;
                $entrada->save();*/

                foreach ($request->detalleProductos as $producto) {
                    $bodega_sub = BodegaProductos::where('id_bodega_producto', $producto['productox']['id_bodega_producto'])->first();
                    /*                    if ($salida->condicion_productos == 8) {
                                          $bodega_sub->cantidad_recuperadas = $bodega_sub->cantidad_recuperadas - $producto['cantidad'];
                                       } elseif ($salida->condicion_productos == 6) {
                                           $bodega_sub->cantidad_obsoletas = $bodega_sub->cantidad_obsoletas - $producto['cantidad'];
                                       } else {
                                           $bodega_sub->cantidad = $bodega_sub->cantidad - $producto['cantidad'];
                                       }

                                       $bodega_sub->save();*/

                    $salida_producto = new SalidaProductos;
                    $salida_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                    $salida_producto->id_salida = $salida->id_salida;
                    $salida_producto->descripcion_producto = $producto['productox']['descripcion'];
                    $salida_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                    $salida_producto->unidad_medida = $producto['productox']['unidad_medida'];
                    $salida_producto->precio_unitario = $producto['precio_unitario'];
                    $salida_producto->cantidad_saliente = $producto['cantidad'];
                    $salida_producto->cantidad_despachada = 0;
                    $salida_producto->cantidad_faltante = 0;
                    $salida_producto->u_creacion = $salida->u_creacion;
                    $salida_producto->id_empresa =$usuario_empresa->id_empresa;
                    $salida_producto->no_documento = $producto['productox']['no_documento'];
                    $salida_producto->save();
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


    public function registrarTrasladoConsignacion(Request $request)
    {
        $messages = [
            'detalleProductos.min' => 'Se requiere agregar un producto por lo menos.',
            'detalleProductos.*.productox.id_producto.required' => 'Seleccione un producto válido',
            'detalleProductos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'detalleProductos.*.cantidad.min' => 'La cantidad debe ser mayor que cero',
        ];

        $rules = [
            //'codigo_salida' => 'required|string|max:25',
            'descripcion_salida' => 'string|max:255|nullable',
            'fecha_salida' => 'required|date',
            'bodega_saliente' => 'required|array|min:1',
            'bodega_saliente.id_bodega' => 'required|integer|min:1',
            'cliente_salida' => 'required|array|min:1',
            'cliente_salida.id_cliente' => 'required|integer|min:1',
            'bodega_entrante' => 'required|array|min:1',
            'bodega_entrante.id_bodega' => 'required|integer|min:1',
            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.precio_unitario' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.cantidad' => 'required|integer|min:1',
            'detalleProductos.*.productox.codigo_sistema' => 'required|string|max:50',
            'detalleProductos.*.productox.descripcion' => 'required|string|max:100',
            'detalleProductos.*.productox.unidad_medida' => 'required|string|max:100',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();
                $salida = new Salidas;

                $salida->codigo_salida = Salidas::max('id_salida') + 1;
                $salida->id_tipo_salida = 15;//4 ahora traslado por consignacion;
                $salida->fecha_salida = $request->fecha_salida;
                $salida->id_bodega = $request->bodega_saliente['id_bodega'];
                $salida->id_cliente = $request->cliente_salida['id_cliente'];
                $salida->id_bodega_entrante = $request->bodega_entrante['id_bodega'];
                $salida->descripcion_salida = $request->descripcion_salida;
                $salida->u_creacion = Auth::user()->usuario;
                $salida->estado = 1;
                $salida->save();


                /*$entrada = new InventarioEntradas;
                $entrada->codigo_entrada = InventarioEntradas::max('id_entrada')+1;
                $entrada->id_tipo_entrada = 7;
                $entrada->fecha_entrada = $request->fecha_salida;
                $entrada->id_proveedor = $salida->id_proveedor;
                $entrada->id_bodega = $request->bodega_entrante['id_bodega'];
                $entrada->descripcion_entrada = $request->descripcion_salida;
                $entrada->u_creacion = $salida->u_creacion;
                $entrada->estado = 1;
                $entrada->save();*/

                foreach ($request->detalleProductos as $producto) {
                    $bodega_sub = BodegaProductos::where('id_bodega_producto', $producto['productox']['id_bodega_producto'])->first();
                    $bodega_sub->cantidad = $bodega_sub->cantidad - $producto['cantidad'];
                    $bodega_sub->save();

                    $salida_producto = new SalidaProductos;
                    $salida_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                    $salida_producto->id_salida = $salida->id_salida;
                    $salida_producto->descripcion_producto = $producto['productox']['descripcion'];
                    $salida_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                    $salida_producto->unidad_medida = $producto['productox']['unidad_medida'];
                    $salida_producto->precio_unitario = $producto['precio_unitario'];
                    $salida_producto->cantidad_saliente = $producto['cantidad'];
                    $salida_producto->cantidad_despachada = 0;
                    $salida_producto->cantidad_faltante = 0;
                    $salida_producto->u_creacion = $salida->u_creacion;
                    $salida_producto->save();

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


    public function registrarDevolucionConsignacion(Request $request)
    {
        $messages = [
            'detalleProductos.min' => 'Se requiere agregar un producto por lo menos.',
            'detalleProductos.*.productox.id_producto.required' => 'Seleccione un producto válido',
            'detalleProductos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'detalleProductos.*.cantidad.min' => 'La cantidad debe ser mayor que cero',
        ];

        $rules = [
            //'codigo_salida' => 'required|string|max:25',
            'descripcion_salida' => 'string|max:255|nullable',
            'fecha_salida' => 'required|date',
            'cliente_salida' => 'required|array|min:1',
            'cliente_salida.id_cliente' => 'required|integer|min:1',
            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.precio_unitario' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/',
            'detalleProductos.*.cantidad' => 'required|integer|min:1',
            'detalleProductos.*.productox.codigo_sistema' => 'required|string|max:50',
            'detalleProductos.*.productox.text' => 'required|string|max:100',
            'detalleProductos.*.productox.unidad_medida' => 'required|string|max:100',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();
                $salida = new Salidas;

                $salida->codigo_salida = Salidas::max('id_salida') + 1;
                $salida->id_tipo_salida = 8;
                $salida->fecha_salida = $request->fecha_salida;
                $salida->id_bodega = 17;
                $salida->id_cliente = $request->cliente_salida['id_cliente'];
                $salida->id_bodega_entrante = 1;
                $salida->descripcion_salida = $request->descripcion_salida;
                $salida->u_creacion = Auth::user()->usuario;
                $salida->estado = 1;
                $salida->save();


                /*$entrada = new InventarioEntradas;
                $entrada->codigo_entrada = InventarioEntradas::max('id_entrada')+1;
                $entrada->id_tipo_entrada = 7;
                $entrada->fecha_entrada = $request->fecha_salida;
                $entrada->id_proveedor = $salida->id_proveedor;
                $entrada->id_bodega = $request->bodega_entrante['id_bodega'];
                $entrada->descripcion_entrada = $request->descripcion_salida;
                $entrada->u_creacion = $salida->u_creacion;
                $entrada->estado = 1;
                $entrada->save();*/

                foreach ($request->detalleProductos as $producto) {
                    $bodega_sub = BodegaProductos::where('id_bodega_producto', $producto['productox']['id_bodega_producto'])->first();
                    $bodega_sub->cantidad = $bodega_sub->cantidad - $producto['cantidad'];
                    $bodega_sub->save();

                    $salida_producto = new SalidaProductos;
                    $salida_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                    $salida_producto->id_salida = $salida->id_salida;
                    $salida_producto->descripcion_producto = $producto['productox']['text'];
                    $salida_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                    $salida_producto->unidad_medida = $producto['productox']['unidad_medida'];
                    $salida_producto->precio_unitario = $producto['precio_unitario'];
                    $salida_producto->cantidad_saliente = $producto['cantidad'];
                    $salida_producto->cantidad_despachada = 0;
                    $salida_producto->cantidad_faltante = 0;
                    $salida_producto->u_creacion = $salida->u_creacion;
                    $salida_producto->save();


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


    public function obtenerNumeroSalida()
    {
        $num['Numero'] = counter()->next('salida');
        // $num['Fecha'] = date('Y-m-d');
        return response()->json([
            'status' => 'success',
            'result' => $num,
            'messages' => null
        ]);
    }


    public function nuevaSalidaRecuperados(Request $request)
    {
        $bodegas = Bodegas::where('activo', 1)->whereIn('id_tipo_bodega', array(1, 2))->get();
        $bodegas_garantia = Bodegas::where('activo', 1)->where('id_tipo_bodega', 4)->get();
        $productos = Productos::
        select(['inventario.productos.id_producto', 'codigo_barra', 'codigo_consecutivo', 'inventario.baterias_detalles.bci', 'codigo_sistema', 'condicion', 'costo_estandar', 'descripcion', DB::raw("CONCAT(inventario.productos.nombre_comercial,' (',inventario.productos.codigo_sistema,')') AS text")])
            ->leftJoin('inventario.baterias_detalles', 'inventario.baterias_detalles.id_producto', 'inventario.productos.id_producto')
            ->where('activo', 1)->whereIn('id_tipo_producto', array(3))->where('condicion', 1)->orderBy('descripcion', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'result' => [
                'bodegas' => $bodegas,
                'bodegas_garantia' => $bodegas_garantia,
                'productos' => $productos,
            ],
            'messages' => null
        ]);
    }


    public function registrarTrasladoRecuperado(Request $request)
    {

        $messages = [
            'detalleProductos.min' => 'Se requiere agregar un producto por lo menos.',
            'detalleProductos.*.productox.id_producto.required' => 'Seleccione un producto válido',
            'detalleProductos.*.producto_garantiax.id_producto.required' => 'Seleccione un producto válido',
            'detalleProductos.*.precio.min' => 'El precio debe ser mayor que cero',
            'detalleProductos.*.cantidad.min' => 'La cantidad debe ser mayor que cero',
        ];


        $rules = [

            'bodega' => 'nullable|array|min:1',
            'bodega.id_bodega' => 'required|integer|min:1',

            'bodega_garantia' => 'nullable|array|min:1',
            'bodega_garantia.id_bodega' => 'required|integer|min:1',
            'estado' => 'required|integer|min:1',

            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.estado' => 'required|integer|min:1|max:8',
            'detalleProductos.*.id_bateria' => 'required|integer|exists:pgsql.inventario.baterias,id_bateria',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();

                $salida = new Salidas;

                $salida->codigo_salida = Salidas::max('id_salida') + 1;
                $salida->id_tipo_salida = 4;
                $salida->condicion_productos = 3;//salen productos en garantia (3)
                $salida->fecha_salida = $request->fecha_salida;//date("Y-m-d H:i:s");
                $salida->id_bodega = $request->bodega_garantia['id_bodega'];
                $salida->id_bodega_entrante = $request->bodega['id_bodega'];
                $salida->descripcion_salida = $request->descripcion_salida;
                $salida->u_creacion = Auth::user()->usuario;
                $salida->fecha_despacho = $request->fecha_salida;//date("Y-m-d H:i:s");
                $salida->u_despacho = Auth::user()->usuario;
                $salida->estado = 2;
                $salida->save();

                $entrada = new Entradas;
                $entrada->codigo_entrada = $salida->codigo_salida;//InventarioEntradas::max('id_entrada')+1;
                $entrada->id_tipo_entrada = 9;///TRASLADO BATERIAS RECUPERADAS
                $entrada->condicion_productos = $request->estado;//entran con estado
                $entrada->fecha_entrada = $salida->fecha_salida;
                $entrada->id_proveedor = $salida->id_proveedor;
                $entrada->id_bodega = $salida->id_bodega_entrante;
                $entrada->id_salida = $salida->id_salida;
                $entrada->descripcion_entrada = $request->descripcion_salida;
                $entrada->u_creacion = $salida->u_creacion;
                $entrada->estado = 1;
                $entrada->save();


                foreach ($request->detalleProductos as $producto) {


                    $bodega_sub = BodegaProductos::where('id_producto', $producto['productox']['id_producto'])
                        ->where('id_bodega', $request->bodega_garantia['id_bodega'])->first();
                    $bodega_sub->cantidad = $bodega_sub->cantidad - 1;
                    $bodega_sub->save();


                    $productoExiste = SalidaProductos::where('id_bodega_producto', $bodega_sub->id_bodega_producto)
                        ->where('id_salida', $salida->id_salida)->first();

                    $salida_baterias = new SalidaProductoBaterias();
                    $salida_baterias->id_bateria = $producto['id_bateria'];

                    $bateria_individual = Baterias::find($salida_baterias->id_bateria);
                    $bateria_individual->estado = $entrada->condicion_productos; //1 nuevo 8 recuperada?
                    $bateria_individual->reservada = true;
                    $bateria_individual->save();

                    $entrada_producto = new EntradaProductos;

                    $bodega_sub2 = BodegaProductos::where('id_bodega', $salida->id_bodega_entrante)->where('id_producto', $producto['productox']['id_producto'])->first();

                    if (!empty($bodega_sub2)) {
                        $entrada_producto->id_bodega_producto = $bodega_sub2['id_bodega_producto'];
                    } else {
                        $nueva_bodega_sub = new BodegaProductos;
                        $nueva_bodega_sub->id_bodega = $salida->id_bodega_entrante;
                        $nueva_bodega_sub->id_producto = $producto['productox']['id_producto'];
                        $nueva_bodega_sub->cantidad = 0;
                        $nueva_bodega_sub->u_creacion = $salida->u_creacion;
                        $nueva_bodega_sub->save();
                        $entrada_producto->id_bodega_producto = $nueva_bodega_sub->id_bodega_producto;
                    }

                    $productoExisteEntrada = EntradaProductos::where('id_bodega_producto', $entrada_producto->id_bodega_producto)
                        ->where('id_entrada', $entrada->id_entrada)->first();

                    if (!$productoExiste && !$productoExisteEntrada) {
                        $salida_producto = new SalidaProductos;
                        $productoxx = Productos::select('id_producto', 'id_unidad_medida')->find($producto['productox']['id_producto']);
                        $unidad_medida = UnidadMedida::find($productoxx['id_unidad_medida']);
                        $salida_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                        $salida_producto->id_salida = $salida->id_salida;
                        $salida_producto->descripcion_producto = $producto['productox']['descripcion'];
                        $salida_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                        $salida_producto->unidad_medida = $unidad_medida['descripcion'];
                        $salida_producto->precio_unitario = $producto['productox']['costo_estandar'];
                        $salida_producto->cantidad_saliente = 1;
                        $salida_producto->cantidad_despachada = 1;
                        $salida_producto->cantidad_faltante = 0;
                        $salida_producto->u_creacion = $salida->u_creacion;
                        $salida_producto->save();

                        $costo_promediox = Productos::select(
                            DB::raw('coalesce(inventario.calcular_costo_promedio(inventario.productos.id_producto),0) as costo_promedio'))
                            ->where('id_producto', $productoxx['id_producto'])->first();

                        $movimiento_producto = new MovimientosProductos();
                        $movimiento_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                        $movimiento_producto->fecha_movimiento = $salida->fecha_despacho;// date("Y-m-d H:i:s");
                        $movimiento_producto->descripcion_movimiento = 'Salida por Traslado de baterías en garantía a ventas No. ' . $salida->codigo_salida;
                        $movimiento_producto->identificador_origen_movimiento = $salida->id_salida;
                        $movimiento_producto->tipo_movimiento = 2;
                        $movimiento_producto->costo = $costo_promediox['costo_promedio'];
                        $movimiento_producto->usuario_registra = Auth::user()->usuario;
                        $movimiento_producto->cantidad_movimiento = $salida_producto->cantidad_saliente * -1;
                        $movimiento_producto->save();

                        $entrada_producto->id_entrada = $entrada->id_entrada;
                        $entrada_producto->codigo_producto = $salida_producto->codigo_producto;
                        $entrada_producto->descripcion_producto = $salida_producto->descripcion_producto;
                        $entrada_producto->unidad_medida = $salida_producto->unidad_medida;
                        $entrada_producto->precio_unitario = $salida_producto->precio_unitario;
                        $entrada_producto->cantidad_solicitada = 1;
                        $entrada_producto->cantidad_recibida = 0;
                        $entrada_producto->cantidad_faltante = 0;
                        $entrada_producto->u_creacion = $entrada->u_creacion;
                        $entrada_producto->save();


                        if ($producto['productox']['id_producto'] == $bodega_sub->id_producto) {
                            $salida_baterias->id_salida_producto = $salida_producto->id_salida_producto;
                            $salida_baterias->save();
                        }


                    } else {
                        $productoExiste->cantidad_saliente = $productoExiste->cantidad_saliente + 1;
                        $productoExiste->cantidad_despachada = $productoExiste->cantidad_despachada + 1;
                        $productoExiste->save();

                        $productoExisteEntrada->cantidad_solicitada = $productoExisteEntrada->cantidad_solicitada + 1;
                        $productoExisteEntrada->save();

                        $salida_baterias->id_salida_producto = $productoExiste->id_salida_producto;
                        $salida_baterias->save();

                        $movimiento_productox = MovimientosProductos::
                        where('id_bodega_producto', $productoExiste->id_bodega_producto)
                            ->where('identificador_origen_movimiento', $productoExiste->id_salida)->where('tipo_movimiento', 2)->first();
                        $movimiento_productox->cantidad_movimiento = $productoExiste->cantidad_despachada * -1;
                        $movimiento_productox->save();
                    }

                }

                //DB::rollBack();
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
     * Cambiar Estado Salida
     *
     * @access    public
     * @param
     * @return JsonResponse
     */

    public function cambiarEstado(Request $request)
    {

        $rules = [
            'id_salida' => 'required',
            'estado' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $salida = Salidas::find($request->id_salida);

            if ($salida->es_editable && $request->estado >= 0 && $request->estado <= 2 && $salida->estado <> $request->estado) {

                $estado_org = $salida->estado;
                $salida->estado = $request->estado;

                if ($request->estado == 0 || $request->estado == 2) {
                    $salida->es_editable = 0;
                }

                $estados[0] = 'Cancelada';
                $estados[1] = 'Emitida';
                $estados[2] = 'Aprobada';

                date_default_timezone_set('America/Managua');
                $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
                $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                $log['fecha_log'] = $dias[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y') . ' a las ' . date('h:i:s A');
                $log['registro'] = 'Cambiado el estado de la salida de ' . $estados[$estado_org] . ' a estado ' . $estados[$request->estado] . ' por usuario ' . Auth::user()->usuario;
                $log_actual = Array(json_decode($salida->log_salida));
                // print_r($log);
                // print_r($log_actual[0]);
                array_push($log_actual[0], $log);
                $salida->log_salida = json_encode($log_actual[0]);
                // echo $entrada->log_entrada;

                $salida->save();

                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);

            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Error al cambiar el estado de la salida, revise si la salida esta bloqueada',
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

    public function reporteSalida($ext, $id_salida)
    {
        // echo $ext;
        //$ext = 'pdf';
        $os = array("pdf");
        if (in_array($ext, $os, true)) {
            $hora_actual = time();
            // Rutas para descarga Reportes local
            $input = env('APP_URL_REPORTES') . 'ReportesalidaInventario';
            $output = env('APP_URL_REPORTES') . $hora_actual . 'ReportesalidaInventario';

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
                    'id_salida' => $id_salida,
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
     * Devolución de productos
     * @param Request $request
     * @return JsonResponse
     *
     * @author octaviom
     * tested
     */
    public function registrarDevolucion(Request $request)
    {

        $messages = [
            'salida_productos.min' => 'Se requiere agregar un producto por lo menos.',
        ];

        $rules = [
            'id_salida' => 'required|integer|exists:pgsql.inventario.salidas,id_salida',
            'salida_productos' => 'required|array|min:1',
            'bodega' => 'required|array|min:1',
            'bodega.id_bodega' => 'required|integer|min:1',
            // 'salida_productos.*.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'salida_productos.*.id_bodega_producto' => 'required|integer|min:1|exists:pgsql.inventario.bodega_productos,id_bodega_producto',
            'salida_productos.*.cantidad_despachada' => 'required|integer',
            'salida_productos.*.cantidad_devuelta' => 'required|integer|min:0|lte:salida_productos.*.cantidad_despachada',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {

            try {

                DB::beginTransaction();
                $salida = Salidas::find($request->id_salida);

                if ($salida->estado == 2) {

                    $entrada = new Entradas;
                    $entrada->codigo_entrada = $salida->codigo_salida;//InventarioEntradas::max('id_entrada')+1;
                    $entrada->id_tipo_entrada = 2;//TIPO DEVOLUCION
                    $entrada->condicion_productos = $salida->condicion_productos;
                    $entrada->fecha_entrada = $request->fecha_salida;//date("Y-m-d H:i:s");
                    $entrada->fecha_recepcion = $request->fecha_despacho;//date("Y-m-d H:i:s");
                    $entrada->numero_documento = 'DEV-' . $salida->numero_documento;
                    $entrada->id_proveedor = $salida->id_proveedor;
                    $entrada->id_bodega = $request->bodega['id_bodega'];
                    $entrada->id_salida = $salida->id_salida;
                    $entrada->descripcion_entrada = $request->descripcion_salida;
                    $entrada->u_creacion = $salida->u_creacion;
                    $entrada->u_recepcion = Auth::user()->name;
                    $entrada->estado = 2;
                    $entrada->save();

                    $dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
                    $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
                    $log['fecha_log'] = $dias[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y') . ' a las ' . date('h:i:s A');
                    $log['registro'] = 'Se ha registrado una devolución para esta salida con código: ' . $salida->codigo_salida . ' por el usuario ' . Auth::user()->name;


//                    $log_actual = json_decode($log['registro'].$log['fecha_log']);
//                    print_r($log['registro'].' - '.$log['fecha_log']);
                    $salida->log_salida = $log['registro'] . ' - ' . $log['fecha_log'];
                    $salida->id_salida_dev = $salida->id_salida;

                    $salida->estado = 3; //Estado : 3 -> devuelta (proceso de devolución realizado)
                    $salida->save();


                    foreach ($request->salida_productos as $producto) {
                        // print_r($producto);
                        if ($producto['cantidad_devuelta'] > 0) {
                            $bodega_sub = BodegaProductos::where('id_bodega', $entrada->id_bodega)->where('id_producto', $producto['bodega_producto']['id_producto'])->first();

                            if ($salida->id_tipo_salida == 7 || $salida->id_tipo_salida == 1) {
                                $entrada_producto = new EntradaProductos;
                                if (!empty($bodega_sub)) {
                                    $entrada_producto->id_bodega_producto = $bodega_sub['id_bodega_producto'];
                                    $bodega_sub->cantidad = $bodega_sub->cantidad + $producto['cantidad_devuelta'];
                                    $bodega_sub->save();
                                } else {
                                    $nueva_bodega_sub = new BodegaProductos;
                                    $nueva_bodega_sub->id_bodega = $entrada->id_bodega;
                                    $nueva_bodega_sub->id_producto = $producto['bodega_producto']['id_producto'];
                                    $nueva_bodega_sub->cantidad = $producto['cantidad_devuelta'];
                                    $nueva_bodega_sub->u_creacion = $salida->u_creacion;
                                    $nueva_bodega_sub->save();
                                    $entrada_producto->id_bodega_producto = $nueva_bodega_sub->id_bodega_producto;
                                }
                                $producto_detalles = Productos::find($producto['bodega_producto']['id_producto']);
                                $unidad_medida = UnidadMedida::find($producto_detalles['id_unidad_medida']);

                                $entrada_producto->id_entrada = $entrada->id_entrada;
                                $entrada_producto->codigo_producto = $producto_detalles['codigo_sistema'];
                                $entrada_producto->descripcion_producto = $producto_detalles['descripcion'];
                                $entrada_producto->unidad_medida = $unidad_medida['descripcion'];
                                $entrada_producto->precio_unitario = $producto_detalles['costo_estandar'];
                                $entrada_producto->cantidad_solicitada = $producto['cantidad_devuelta'];
                                $entrada_producto->cantidad_recibida = $producto['cantidad_devuelta'];
                                $entrada_producto->cantidad_faltante = 0;
                                $entrada_producto->u_creacion = $entrada->u_creacion;
                                $entrada_producto->save();

                            }


                            $costo_promediox = Productos::select(
                                DB::raw('coalesce(inventario.calcular_costo_promedio(inventario.productos.id_producto),0) as costo_promedio'))
                                ->where('id_producto', $producto['bodega_producto']['id_producto'])->first();

                            $movimiento_producto = new MovimientosProductos();
                            $movimiento_producto->id_bodega_producto = $entrada_producto->id_bodega_producto;
                            $movimiento_producto->fecha_movimiento = $entrada->fecha_entrada;//date("Y-m-d H:i:s");
                            $movimiento_producto->descripcion_movimiento = 'Devolución No. ' . $entrada->numero_documento;
                            $movimiento_producto->identificador_origen_movimiento = $entrada->id_entrada;
                            $movimiento_producto->tipo_movimiento = 1;
                            $movimiento_producto->cantidad_movimiento = $producto['cantidad_devuelta'];
                            $movimiento_producto->costo = $costo_promediox['costo_promedio'];
                            $movimiento_producto->usuario_registra = Auth::user()->name;
                            $movimiento_producto->save();
                        }
                    }

                    DB::commit();
                    //DB::rollBack();
                    return response()->json([
                        'status' => 'success',
                        'result' => null,
                        'messages' => null
                    ]);

                } else {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'result' => 'La salida ha sido modificada previamente, no se pueden grabar los cambios',
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


    public function registrarBateria(Request $request)
    {
        $rules = [
            'id_salida' => 'required|integer|min:1',
            'id_producto' => 'required|integer',
            //'id_bodega_producto' => 'required|integer',
            'id_bodega' => 'required|integer',
            'id_salida_producto_bateria' => 'integer|min:0',
            'id_bateria' => 'nullable|integer',
            'estado' => 'required|integer',
            //'fecha_activacion' => ['required','string','min:5','max:5'/*,'regex:/0\[1-9]|10|11|12)/[1-9][0-9]/'*/],

        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try {
                DB::beginTransaction();
                if ($request->estado == 0) {

                    if (!empty($request->id_salida_producto_bateria)) {
                        SalidaProductoBaterias::where('id_salida_producto_bateria', $request->id_salida_producto_bateria)->delete();


                        if (!empty($request->id_bateria)) {
                            $bateria_individual = Baterias::find($request->id_bateria);
                            if ($bateria_individual && $bateria_individual->reservada) {
                                $bateria_individual->reservada = false;
                                $bateria_individual->save();

                                $bodega_producto = BodegaProductos::where('id_bodega', $request->id_bodega)
                                    ->where('id_producto', $request->id_producto)->first();

                                $salida_productox = SalidaProductos::where('id_bodega_producto', $bodega_producto['id_bodega_producto'])
                                    ->where('id_salida', $request->id_salida)->first();

                                $salida_productox->cantidad_despachada = $salida_productox->cantidad_despachada - 1;
                                //$salida_productox->cantidad_faltante = $salida_productox->cantidad_faltante+1;
                                $salida_productox->save();
                            }
                        }

                    }

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'id_salida_producto_bateria' => null,
                        ],
                        'messages' => null
                    ]);

                } else {
                    if (empty($request->id_salida_producto_bateria)) {

                        $salida_baterias = new SalidaProductoBaterias();
                        $salida_baterias->id_bateria = $request->id_bateria;

                        $bodega_producto = BodegaProductos::where('id_bodega', $request->id_bodega)
                            ->where('id_producto', $request->id_producto)->first();

                        $salida_productox = SalidaProductos::where('id_bodega_producto', $bodega_producto['id_bodega_producto'])
                            ->where('id_salida', $request->id_salida)->first();


                        $salida_baterias->id_salida_producto = $salida_productox['id_salida_producto'];
                        $salida_baterias->save();

                        //$salida_producto = InventarioSalidaProductos::find($salida_productox['id_salida_producto']);
                        $salida_productox->cantidad_despachada = $salida_productox->cantidad_despachada + 1;
                        //$salida_productox->cantidad_faltante = $salida_productox->cantidad_faltante-1;
                        $salida_productox->save();


                        $bateria_individual = Baterias::find($request->id_bateria);
                        if ($bateria_individual && !$bateria_individual->reservada) {
                            $bateria_individual->reservada = true;
                            $bateria_individual->save();
                        } else {
                            DB::rollBack();
                            //print_r($bateria_individual);
                            return response()->json([
                                'status' => 'error',
                                'result' => 'Error de base de datos',
                                'messages' => null
                            ]);
                        }

                        DB::commit();
                        return response()->json([
                            'status' => 'success',
                            'result' => [
                                'id_salida_producto_bateria' => $salida_baterias->id_salida_producto_bateria,
                            ],
                            'messages' => null
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'success',
                            'result' => [
                                'id_salida_producto_bateria' => $request->id_salida_producto_bateria,
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

}
