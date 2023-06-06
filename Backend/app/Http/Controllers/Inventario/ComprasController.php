<?php



namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Inventario\Compras;
use App\Models\Inventario\BodegasProductos;
use App\Models\Inventario\EntradaProductos;
use App\Models\Inventario\Entradas;
use App\Models\Inventario\MovimientosProductos;
use PHPJasper\PHPJasper;
use Validator,Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule,DB;
class ComprasController extends Controller
{
    /**
     * Get List of Entradas
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtener(Request $request, Entradas $entradas)
    {
        $entradas = $entradas->obtenerEntradas($request);

        foreach($entradas as $entrada ){
            //   print_r($entrada);
            $items = collect($entrada->entradaProductos);

            $entrada->tot_unidades = $items->sum(function($item) {
                return $item['cantidad_solicitada'];
            });
            $entrada->subtotal = $items->sum(function($item) {
                return $item['precio_unitario']*$item['cantidad_solicitada'];
            });
            $entrada->total_impuesto = $items->sum(function($item) {
                return $item['precio_unitario']*$item['cantidad_solicitada']*($item['impuesto']/100);
            });
            $entrada->total = $entrada->subtotal+$entrada->total_impuesto;
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
     * @param
     * @return  json(array)
     */

    public function obtenerEntrada(Request $request, Entradas $entrada)
    {
        $rules = [
            'id_entrada' => 'required|integer|min:1'
        ];


        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $entrada = $entrada->obtenerEntrada($request);

            return response()->json([
                'status' => 'success',
                'result' => $entrada[0],
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

            if(!$entrada->isEmpty()){
                //print_r($entrada);
                $items = collect($entrada[0]->entradasProductos);
                $entrada[0]->sub_total = $items->sum(function($item) {
                    return $item['cantidad'] * $item['precio_unitario'];
                });

                $entrada[0]->total = $items->sum(function($item) {
                    return $item['cantidad'] * $item['precio_unitario'];
                });

                $entrada[0]->cant_inicial = 0;

                return response()->json([
                    'status' => 'success',
                    'result' => $entrada[0],
                    'messages' => null
                ]);
            }else {
                return response()->json([
                    'status' => 'error',
                    'result' => "No se han encontrado resultados para el código de entrada: ".$request->codigo_entrada,
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
        $data->sub_total = $items->sum(function($item) {
            return $item['cantidad'] * $item['precio_unitario'];
        });

        $data->total = $items->sum(function($item) {
            return $item['cantidad'] * $item['precio_unitario'];
        });

        $data->log_registro = json_decode($data->log_entrada);

        $doc  = 'docs.entrada';
        //print_r($data);
        //return $this->f_pdf($doc, $data);
        return pdf($doc, $data,strtoupper('Entrada '.$data->codigo_entrada));
    }

    /**
     * Create a New User
     *
     * @access 	public
     * @param
     * @return 	json(string)
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
            'codigo_entrada' => 'required|string|max:25',
            'fecha_entrada' => 'required|date',

            'tipo_entrada' => 'required|array|min:1',
            'tipo_entrada.id_tipo_entrada' => 'required|integer|min:1',

            'tipo_pago' => 'required|array|min:1',
            'tipo_pago.id_tipo_pago' => 'required|integer|min:1',

            'bodega' => 'required|array|min:1',
            'bodega.id_bodega' => 'required|integer|min:1',

            'proveedor' => 'required|array|min:1',
            'proveedor.id_proveedor' => 'required|integer|min:1',

            'detalleProductos' => 'required|array|min:1',
            'detalleProductos.*.productox.id_producto' => 'required|integer|exists:pgsql.inventario.productos,id_producto',
            'detalleProductos.*.precio_unitario' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'detalleProductos.*.cantidad' => 'required|integer|min:1',
            'detalleProductos.*.productox.codigo_sistema' => 'required|string|max:50',
            'detalleProductos.*.productox.descripcion' => 'required|string|max:100',
            'detalleProductos.*.productox.unidad_medida.id_unidad_medida' => 'required|integer|min:1',
            'detalleProductos.*.impuesto' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
        ];

        $validator = Validator::make($request->all(), $rules,$messages);
        if (!$validator->fails()) {

            try{

                \Illuminate\Support\Facades\DB::beginTransaction();
                $entrada = new Entradas;

                $entrada->codigo_entrada = $request->codigo_entrada;
                // $entrada->codigo_entrada = counter()->next('entrada');

                $entrada->id_tipo_entrada = $request->tipo_entrada['id_tipo_entrada'];
                $entrada->fecha_entrada = $request->fecha_entrada;
                $entrada->fecha_vencimiento =  date('Y-m-d', strtotime($request->fecha_entrada. ' + '.$request->tipo_pago['plazo_dias'].' days'));
                $entrada->id_proveedor = $request->proveedor['id_proveedor'];
                $entrada->id_bodega = $request->bodega['id_bodega'];
                $entrada->id_tipo_pago = $request->tipo_pago['id_tipo_pago'];

                $entrada->u_creacion = Auth::user()->usuario;
                $entrada->estado = 1;
                $entrada->save();
                /*date_default_timezone_set('America/Managua');
                 $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
                 $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                 $log['fecha_log'] = $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') .' a las '.date('h:i:s A');
                 $log['registro'] = 'Registro de entrada en sistema por '.$entrada->usuario_registra;
                 $entrada->log_entrada = '['.json_encode($log).']';    */

                //counter()->increment('entrada');
                // print_r($request->detalleProductos);
                foreach ($request->detalleProductos as $producto) {
                    $entrada_producto = new EntradaProductos;

                    $bodega_sub = BodegasProductos::where('id_bodega',$request->bodega['id_bodega'])->where('id_producto',$producto['productox']['id_producto'])->get();

                    if(!empty($bodega_sub[0])){
                        $entrada_producto->id_bodega_producto = $bodega_sub[0]['id_bodega_producto'];
                    }else{
                        $nueva_bodega_sub = new BodegasProductos;
                        $nueva_bodega_sub->id_bodega=$request->bodega['id_bodega'];
                        $nueva_bodega_sub->id_producto=$producto['productox']['id_producto'];
                        $nueva_bodega_sub->cantidad=0;
                        $nueva_bodega_sub->u_creacion =$entrada->u_creacion;
                        $nueva_bodega_sub->save();
                        $entrada_producto->id_bodega_producto = $nueva_bodega_sub->id_bodega_producto;
                    }

                    $entrada_producto->id_entrada = $entrada->id_entrada;
                    $entrada_producto->codigo_producto = $producto['productox']['codigo_sistema'];
                    $entrada_producto->descripcion_producto = $producto['productox']['descripcion'];
                    $entrada_producto->unidad_medida = $producto['productox']['unidad_medida']['descripcion'];
                    $entrada_producto->precio_unitario = $producto['precio_unitario'];
                    $entrada_producto->impuesto = $producto['impuesto'];
                    $entrada_producto->cantidad_solicitada = $producto['cantidad'];
                    $entrada_producto->cantidad_recibida = 0;
                    $entrada_producto->cantidad_faltante = 0;
                    $entrada_producto->u_creacion =$entrada->u_creacion;
                    $entrada_producto->save();

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
        $messages = [
            'entrada_productos.min' => 'Se requiere agregar un producto por lo menos.',
            'entrada_productos.*.id_producto.required' => 'Seleccione un producto válido',
            'entrada_productos.*.precio_unitario.min' => 'El precio debe ser mayor que cero',
            'entrada_productos.*.cantidad_solicitada.min' => 'La cantidad debe ser mayor que cero',
        ];

        $rules = [
            'id_entrada' => 'required|integer|exists:pgsql.inventario.entradas,id_entrada',

            'fecha_entrada' => 'required|date',

            'entrada_tipo' => 'required|array|min:1',
            'entrada_tipo.id_tipo_entrada' => 'required|integer|min:1',

            'entrada_tipo_pago' => 'required|array|min:1',
            'entrada_tipo_pago.id_tipo_pago' => 'required|integer|min:1',

            'entrada_bodega' => 'required|array|min:1',
            'entrada_bodega.id_bodega' => 'required|integer|min:1',

            'entrada_proveedor' => 'required|array|min:1',
            'entrada_proveedor.id_proveedor' => 'required|integer|min:1',

            'entrada_productos' => 'required|array|min:1',
            'entrada_productos.*.id_bodega_producto' => 'required|integer|min:0',
            'entrada_productos.*.id_producto' => 'required_if:id_bodega_producto,0|integer|exists:pgsql.inventario.productos,id_producto',
            'entrada_productos.*.precio_unitario' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',
            'entrada_productos.*.cantidad_solicitada' => 'required|integer|min:1',
            'entrada_productos.*.codigo_producto' => 'required|string|max:50',
            'entrada_productos.*.descripcion_producto' => 'required|string|max:100',
            'entrada_productos.*.unidad_medida' => 'required|string|max:50',
            'entrada_productos.*.impuesto' => 'required|numeric|regex:/^\d*(\.\d{1,2})?$/',

        ];

        $validator = Validator::make($request->all(), $rules,$messages);
        if (!$validator->fails()) {

            try{


                DB::beginTransaction();
                $entrada = Entradas::find($request->id_entrada);

                $entrada->id_tipo_entrada = $request->entrada_tipo['id_tipo_entrada'];
                $entrada->fecha_entrada = $request->fecha_entrada;
                $entrada->fecha_vencimiento =  date('Y-m-d', strtotime($request->fecha_entrada. ' + '.$request->entrada_tipo_pago['plazo_dias'].' days'));
                $entrada->id_proveedor = $request->entrada_proveedor['id_proveedor'];
                $entrada->id_bodega = $request->entrada_bodega['id_bodega'];
                $entrada->id_tipo_pago = $request->entrada_tipo_pago['id_tipo_pago'];
                $entrada->save();

                EntradaProductos::where('id_entrada', $request->id_entrada)->delete();//update(['activo' => false]);


                foreach ($request->entrada_productos as $producto) {
                    $entrada_producto = new EntradaProductos;
                    if($producto['id_bodega_producto']==0){
                        if(!empty($producto['bodega_producto'])){
                            $bodega_sub = BodegasProductos::where('id_bodega',$request->entrada_bodega['id_bodega'])->where('id_producto',$producto['bodega_producto']['id_producto'])->get();
                        }else{
                            $bodega_sub = BodegasProductos::where('id_bodega',$request->entrada_bodega['id_bodega'])->where('id_producto',$producto['id_producto'])->get();
                        }

                    }else{
                        $bodega_sub = BodegasProductos::where('id_bodega_producto',$producto['id_bodega_producto'])->get();
                    }
                    if(!empty($bodega_sub[0])){
                        $entrada_producto->id_bodega_producto = $bodega_sub[0]['id_bodega_producto'];
                    }else{
                        $nueva_bodega_sub = new BodegasProductos;
                        $nueva_bodega_sub->id_bodega=$request->entrada_bodega['id_bodega'];
                        if($producto['id_bodega_producto']==0 && !empty($producto['bodega_producto'])){
                            $nueva_bodega_sub->id_producto=$producto['bodega_producto']['id_producto'];
                        }else{
                            $nueva_bodega_sub->id_producto=$producto['id_producto'];
                        }
                        $nueva_bodega_sub->cantidad=0;
                        $nueva_bodega_sub->u_creacion =$entrada->u_creacion;
                        $nueva_bodega_sub->save();
                        $entrada_producto->id_bodega_producto = $nueva_bodega_sub->id_bodega_producto;
                    }
                    $entrada_producto->id_entrada = $entrada->id_entrada;
                    $entrada_producto->codigo_producto = $producto['codigo_producto'];
                    $entrada_producto->descripcion_producto = $producto['descripcion_producto'];
                    $entrada_producto->unidad_medida = $producto['unidad_medida'];
                    $entrada_producto->precio_unitario = $producto['precio_unitario'];
                    $entrada_producto->impuesto = $producto['impuesto'];
                    $entrada_producto->cantidad_solicitada = $producto['cantidad_solicitada'];
                    $entrada_producto->cantidad_recibida = 0;
                    $entrada_producto->cantidad_faltante = 0;
                    $entrada_producto->u_creacion =$entrada->u_creacion;
                    $entrada_producto->save();

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
            'entrada_productos.*.id_entrada_producto' => 'required|integer|min:1|exists:pgsql.inventario.entradas_productos,id_entrada_producto',
            'entrada_productos.*.id_bodega_producto' => 'required|integer|min:1|exists:pgsql.inventario.bodegas_productos,id_bodega_producto',
            'entrada_productos.*.cantidad_solicitada' => 'required|integer|min:1',
            'entrada_productos.*.cantidad_faltante' => 'required|integer|min:0|lte:entrada_productos.*.cantidad_solicitada',
            'entrada_productos.*.cantidad_recibida' => 'required|integer|min:0|lte:entrada_productos.*.cantidad_solicitada',
        ];

        $validator = Validator::make($request->all(), $rules,$messages);
        if (!$validator->fails()) {

            try{

                DB::beginTransaction();
                $entrada = Entradas::find($request->id_entrada);
                $entrada->estado=2;
                $entrada->fecha_recepcion=date("Y-m-d H:i:s");
                $entrada->u_recepcion=Auth::user()->usuario;
                $entrada->save();

                foreach ($request->entrada_productos as $producto) {
                    $bodega_sub = BodegasProductos::where('id_bodega_producto',$producto['id_bodega_producto'])->first();
                    // print_r($bodega_sub);
                    $bodega_sub->cantidad = $bodega_sub->cantidad+$producto['cantidad_recibida'];
                    $bodega_sub->save();

                    $entrada_producto = EntradaProductos::find($producto['id_entrada_producto']);
                    $entrada_producto->cantidad_recibida = $producto['cantidad_recibida'];
                    $entrada_producto->cantidad_faltante = $producto['cantidad_faltante'];
                    $entrada_producto->save();

                    $movimiento_producto = new MovimientosProductos();
                    $movimiento_producto->id_bodega_producto = $bodega_sub->id_bodega_producto;
                    $movimiento_producto->fecha_movimiento = date("Y-m-d H:i:s");
                    $movimiento_producto->descripcion_movimiento = 'Entrada '.$entrada->codigo_entrada;
                    $movimiento_producto->identificador_origen_movimiento = $entrada->id_entrada;
                    $movimiento_producto->tipo_movimiento = 1;
                    $movimiento_producto->cantidad_movimiento = $entrada_producto->cantidad_recibida;
                    $movimiento_producto->costo = $entrada_producto->precio_unitario;
                    $movimiento_producto->usuario_registra = Auth::user()->usuario;
                    $movimiento_producto->save();

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

        $validator = Validator::make($request->all(), $rules,$messages);
        if (!$validator->fails()) {

            try{

                DB::beginTransaction();
                $entradaPorDevolucion = new Entradas;
                $entradaPorDevolucion->codigo_entrada = counter()->next('entrada_devolucion').'-'.$request->entrada_original['codigo_entrada'];
                $entradaPorDevolucion->id_tipo_entrada = 2;
                $entradaPorDevolucion->fecha_entrada = $request->entrada_original['fecha_entrada'];
                $entradaPorDevolucion->id_proveedor = $request->entrada_original['id_proveedor'];
                $entradaPorDevolucion->id_bodega = $request->entrada_original['id_bodega'];
                $entradaPorDevolucion->usuario_registra = Auth::user()->usuario;
                $entradaPorDevolucion->id_entrada_dev = $request->entrada_original['id_entrada'];
                $entradaPorDevolucion->estado = 1;

                date_default_timezone_set('America/Managua');
                $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
                $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                $log['fecha_log'] = $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') .' a las '.date('h:i:s A');
                $log['registro'] = 'Registro de entrada por devolución en sistema por '.$entradaPorDevolucion->usuario_registra;
                $entradaPorDevolucion->log_entrada = '['.json_encode($log).']';

                $entradaPorDevolucion->save();
                counter()->increment('entrada_devolucion');

                //print_r($entradaPorDevolucion);
                //print_r($request->entrada_original['entradas_productos']);
                $unidadesDiferencia = 0;
                foreach ($request->entrada_original['entradas_productos'] as $producto) {

                    if($producto['cantidad_dev']>0){

                        $entrada_productoPorDevolucion = new EntradaProductos;
                        $entrada_productoPorDevolucion->id_bodega_producto = $producto['id_bodega_producto'];
                        $entrada_productoPorDevolucion->id_entrada = $entradaPorDevolucion->id_entrada;
                        $entrada_productoPorDevolucion->descripcion_producto = $producto['descripcion_producto'];
                        $entrada_productoPorDevolucion->codigo_producto = $producto['codigo_producto'];
                        $entrada_productoPorDevolucion->unidad_medida = $producto['unidad_medida'];
                        $entrada_productoPorDevolucion->precio_unitario = $producto['precio_unitario'];
                        $entrada_productoPorDevolucion->cantidad = $producto['cantidad_dev']*-1;
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
                $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
                $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                $log['fecha_log'] = $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') .' a las '.date('h:i:s A');
                $log['registro'] = 'Se ha registrado una devolución para esta entrada con código: '. $entradaPorDevolucion->codigo_entrada.' por el usuario '.Auth::user()->usuario;

                $log_actual = Array(json_decode($entradaOriginal->log_entrada));
                array_push($log_actual[0],$log);
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



    /**
     * Cambiar Estado Entrada
     *
     * @access 	public
     * @param
     * @return 	json(string)
     */

    public function cambiarEstado(Request $request)
    {

        $rules = [
            'id_entrada' => 'required',
            'estado' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $entrada = Entradas::find($request->id_entrada);
            if($entrada->es_editable && $request->estado >= 0 && $request->estado <= 2 && $entrada->estado <> $request->estado){

                $estado_org = $entrada->estado;
                $entrada->estado = $request->estado;

                if($request->estado==0 || $request->estado==2){
                    $entrada->es_editable = 0 ;
                }

                $estados[0] = 'Cancelada';
                $estados[1] = 'Emitida';
                $estados[2] = 'Aprobada';

                date_default_timezone_set('America/Managua');
                $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
                $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                $log['fecha_log'] = $dias[date('w')]." ".date('d')." de ".$meses[date('n')-1]. " del ".date('Y') .' a las '.date('h:i:s A');
                $log['registro'] = 'Cambiado el estado de la entrada de '. $estados[$estado_org].' a estado '.$estados[$request->estado].' por usuario '.Auth::user()->usuario;
                $log_actual = Array(json_decode($entrada->log_entrada));
                // print_r($log);
                // print_r($log_actual[0]);
                array_push($log_actual[0],$log);
                $entrada->log_entrada = json_encode($log_actual[0]);
                // echo $entrada->log_entrada;
                $entrada->save();

                return response()->json([
                    'status' => 'success',
                    'result' => null,
                    'messages' => null
                ]);
            }else{
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
