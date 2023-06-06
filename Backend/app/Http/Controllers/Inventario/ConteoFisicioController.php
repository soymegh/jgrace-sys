<?php



namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Inventario\Bodegas;
use App\Models\Inventario\ConteoFisico;
use App\Models\Inventario\ConteoFisicoBaterias;
use App\Models\Inventario\EntradaInicialProductos;
use App\Models\Inventario\Productos;
use PHPJasper\PHPJasper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ConteoFisicioController extends Controller
{
    public function obtener(Request $request, ConteoFisico $conteos)
    {
        $conteos = $conteos->obtener($request);

        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $conteos->total(),
                'rows' => $conteos->items()
            ],
            'messages' => null
        ]);
    }

    public function obtenerConteo(Request $request, ConteoFisico $conteo_fisico)
    {
        $rules = [
            'id_conteo_fisico' => 'required|integer|min:1',
            'cargar_dependencias' => 'required|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $conteo_fisico = $conteo_fisico->obtenerConteoFisico($request);
            $nombre_empresa = Ajustes::where('id_ajuste',4)->select('valor')->first();
            $direccion_empresa = Ajustes::where('id_ajuste',5)->select('valor')->first();

            $consolidado = InventarioConteoFisicoBaterias::
            select('id_conteo_fisico','id_producto', \Illuminate\Support\Facades\DB::Raw('count(*) as cantidad_prod'))
                ->with('productoSimple')
                ->where('id_conteo_fisico',$request->id_conteo_fisico)->orderBy('id_producto', 'desc')
                ->groupBy(array('id_conteo_fisico','id_producto'))
                ->get();

            if(!empty($conteo_fisico)){

                if($request->cargar_dependencias){
                    //if($conteo_fisico->tipo_productos==2){
                    $bodegas =Bodegas::where('activo', 1)->select('id_bodega','id_tipo_bodega','descripcion')->whereIn('id_tipo_bodega',array(1,2,3,5))->get();

                    $consolidado = InventarioConteoFisicoBaterias::
                    select('id_producto', DB::Raw('count(*) as cantidad_prod'))
                        ->with('productoSimple')
                        ->where('id_conteo_fisico',$request->id_conteo_fisico)->orderBy('id_producto', 'desc')
                        ->groupBy(array('id_conteo_fisico','id_producto'))
                        ->get();

                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'conteo_fisico' => $conteo_fisico,
                            'bodegas' => $bodegas,
                            'consolidado' => $consolidado,
                        ],
                        'messages' => null
                    ]);
                    /*}else{
                        $productos = Productos::select('id_producto','descripcion','codigo_barra',DB::raw("CONCAT(inventario.productos.descripcion,' (',inventario.productos.codigo_barra,')') AS text"))->where('activo', 1)->whereIn('tipo_producto',array(3))->with('unidadMedida')->with('productoDetallesBaterias')->orderby('id_producto')->get();
                        $bodegas =Bodegas::where('activo', 1)->select('id_bodega','descripcion')->get();

                        return response()->json([
                            'status' => 'success',
                            'result' => [
                                'entrada_inicial' => $conteo_fisico,
                                'bodegas' => $bodegas,
                                'productos' => $productos,
                            ],
                            'messages' => null
                        ]);
                    }*/



                }else{
                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'conteo_fisico' => $conteo_fisico,
                            'nombre_empresa'=>$nombre_empresa->valor,
                            'direccion_empresa'=>$direccion_empresa->valor,
                            'consolidado'=>$consolidado
                        ],
                        'messages' => null
                    ]);
                }



            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_conteo_fisico'=>["Datos no encontrados"]),
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

    public function nuevo(Request $request){
        $productos = Productos::select('id_producto','codigo_barra',DB::raw("CONCAT(inventario.productos.descripcion,' (',inventario.productos.codigo_barra,')') AS text"))
            ->where('activo', 1)->whereIn('id_tipo_producto',array(3))
            ->with('unidadMedida')
            ->with('productoDetallesBaterias')
            ->whereHas('productoDetallesBaterias', function($q){
                $q->whereNotIn('id_submarca', array(7));
            })
            ->orderby('id_producto')->get();



        $bodegas =Bodegas::where('activo', 1)->select('id_bodega','descripcion')->get();

        $conteo_fisico = new ConteoFisico();
        $conteo_fisico->id_bodega = null;
        $conteo_fisico->fecha_conteo = date("Y-m-d H:i:s");
        $conteo_fisico->estado = 99;
        $conteo_fisico->id_trabajador = Auth::user()->id_empleado;

        $conteo_fisico->save();

        return response()->json([
            'status' => 'success',
            'result' => [
                'productos' => $productos,
                'bodegas' => $bodegas,
                'id_conteo_fisico' => $conteo_fisico->id_conteo_fisico,
            ],
            'messages' => null
        ]);
    }



    public function nuevoManual(Request $request){
        $productos = Productos::select(['id_producto','codigo_barra','codigo_consecutivo','codigo_sistema','condicion','costo_estandar','descripcion',DB::raw("CONCAT(inventario.productos.nombre_comercial,' (',inventario.productos.codigo_barra,')') AS text")])
            ->where('activo', 1)->whereIn('id_tipo_producto',array(3))
            ->with('unidadMedida')
            ->whereHas('productoDetallesBaterias', function($q){
                $q->whereIn('id_submarca', array(7));
            })
            /*->whereHas('productoDetallesBaterias', function($q){
                $q->whereIn('id_submarca', array(7));
            })*/
            ->orderby('id_producto')->get();

        $productos_usados = Productos::select(['id_producto','codigo_barra','codigo_consecutivo','codigo_sistema','condicion','costo_estandar','descripcion',DB::raw("CONCAT(inventario.productos.nombre_comercial,' (',inventario.productos.codigo_sistema,')') AS text")])
            ->where('activo', 1)->whereIn('id_tipo_producto',array(3))
            ->with('unidadMedida')
            ->Where('condicion',2)
            ->orderby('id_producto')->get();

        $productos_herramientas = Productos::select('id_producto','id_unidad_medida','condicion','codigo_barra',DB::raw("CONCAT(inventario.productos.descripcion,' (',inventario.productos.codigo_barra,')') AS text"))
            ->where('activo', 1)->whereIn('id_tipo_producto',array(1))
            ->with('unidadMedida')
            ->Where('condicion',1)
            ->orderby('id_producto')->get();

        $merged = $productos->merge($productos_herramientas);

        $bodegas =Bodegas::where('activo', 1)->select('id_bodega','id_tipo_bodega','descripcion')->whereIn('id_tipo_bodega',array(1,2,3,5))->get();

        /*$conteo_fisico = new ConteoFisico();
        $conteo_fisico->id_bodega = null;
        $conteo_fisico->fecha_entrada = date("Y-m-d H:i:s");
        $conteo_fisico->estado = 99;
        $conteo_fisico->id_trabajador = Auth::user()->id_empleado;

        $conteo_fisico->save();*/

        return response()->json([
            'status' => 'success',
            'result' => [
                'productos' => $merged,
                'productos_usados' => $productos_usados,
                'bodegas' => $bodegas,
                //'id_entrada_inicial' => $conteo_fisico->id_entrada_inicial,
            ],
            'messages' => null
        ]);
    }

    public function registrarBateria(Request $request)
    {
        $rules = [
            'id_conteo_fisico' => 'required|integer|min:1',
            'codigo_garantia' => 'required|string|max:50',
            'id_producto' => 'required|integer',
            'id_bateria' => 'required|integer',
            'id_conteo_fisico_bateria' => 'nullable|integer',
            'estado' => 'required|integer',

        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try{
                DB::beginTransaction();
                if($request->estado == 0){

                    if(!empty($request->id_conteo_fisico_bateria)){
                        ConteoFisicoBaterias::where('id_conteo_fisico_bateria', $request->id_conteo_fisico_bateria)->delete();
                    }

                    DB::commit();
                    return response()->json([
                        'status' => 'success',
                        'result' => [
                            'id_conteo_fisico_bateria'=> null,
                        ],
                        'messages' => null
                    ]);

                }else{
                    if(!empty($request->id_conteo_fisico_bateria)){

                        /*$bateria_individual = ConteoFisicoBaterias::find($request->id_conteo_fisico_bateria);
                        $bateria_individual->fecha_activacion = $request->fecha_activacion;
                        $bateria_individual->save();

                        DB::commit();
                        return response()->json([
                            'status' => 'success',
                            'result' => [
                                'id_entrada_inicial_bateria'=> $bateria_individual->id_entrada_inicial_bateria,
                            ],
                            'messages' => null
                        ]);*/
                    }else{

                        //$bateriaRegistrada = EntradaInicialProductos::where('codigo_garantia',$request->codigo_garantiax)->where('id_entrada_inicial',$request->id_entrada_inicial)->first();
                        $bateriaRegistrada = ConteoFisicoBaterias::WhereRaw("upper(codigo_garantia) = upper(?)",[$request->codigo_garantia])->where('id_conteo_fisico',$request->id_conteo_fisico)->first();



                        if(empty($bateriaRegistrada)){
                            $bateria_individual = new ConteoFisicoBaterias();
                            $bateria_individual->id_bateria = $request->id_bateria;
                            $bateria_individual->id_producto = $request->id_producto;
                            $bateria_individual->id_conteo_fisico = $request->id_conteo_fisico;
                            $bateria_individual->codigo_garantia =  $request->codigo_garantia;
                            $bateria_individual->save();
                            DB::commit();

                            return response()->json([
                                'status' => 'success',
                                'result' => [
                                    'id_conteo_fisico_bateria'=> $bateria_individual->id_conteo_fisico_bateria,
                                ],
                                'messages' => null
                            ]);

                        }else{

                            return response()->json([
                                'status' => 'success',
                                'result' => [
                                    'id_conteo_fisico_bateria'=> $bateriaRegistrada->id_conteo_fisico_bateria,
                                ],
                                'messages' => null
                            ]);
                        }


                    }

                }



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


    public function registrar(Request $request)
    {
        $rules = [
            'id_conteo_fisico' => 'required|integer|exists:pgsql.inventario.conteo_fisico,id_conteo_fisico',
            'conteo_bodega' => 'required|array|min:1',
            'conteo_bodega.id_bodega' => 'required|integer|min:1',
            'detalle_baterias' => 'required|array|min:1',
            'detalle_baterias.*.codigo_garantia' => 'required|string|max:50',
            'detalle_baterias.*.id_conteo_fisico_bateria' => 'nullable|integer',
            'detalle_baterias.*.id_bateria' => 'required|integer',
            'detalle_baterias.*.estado' => 'required|integer',

        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try{

                DB::beginTransaction();
                $entrada = ConteoFisico::find($request->id_conteo_fisico);
                $entrada->id_bodega = $request->conteo_bodega['id_bodega'];
                $entrada->estado = 1;
                $entrada->save();

                foreach ($request->detalle_baterias as $bateria) {

                    if($bateria['estado'] == 0){///Baterias eliminadas

                        if ($bateria['registrada']){///Baterias registradas
                            if(!empty($bateria['id_conteo_fisico_bateria'])){
                                EntradaInicialProductos::where('id_conteo_fisico_bateria', $bateria['id_conteo_fisico_bateria'])->delete();
                            }
                        }

                    }else //baterias validas
                    {
                        if (!$bateria['registrada']){///Baterias no registradas
                            if(empty($bateria['id_conteo_fisico_bateria'])){

                                $bateria_individual = new ConteoFisicoBaterias();
                                $bateria_individual->id_bateria = $bateria['id_bateria'];
                                $bateria_individual->id_producto = $bateria['id_producto'];
                                $bateria_individual->id_conteo_fisico = $request->id_conteo_fisico;
                                $bateria_individual->codigo_garantia =  $bateria['codigo_garantia'];
                                $bateria_individual->save();

                            }
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



    public function actualizar(Request $request)
    {
        $rules = [
            'id_conteo_fisico' => 'required|integer|exists:pgsql.inventario.conteo_fisico,id_conteo_fisico',
            'conteo_bodega' => 'required|array|min:1',
            'conteo_bodega.id_bodega' => 'required|integer|min:1',
            'conteo_baterias' => 'required|array|min:1',
            'conteo_baterias.*.codigo_garantia' => 'required|string|max:50',
            'conteo_baterias.*.id_conteo_fisico_bateria' => 'nullable|integer',
            'conteo_baterias.*.id_bateria' => 'required|integer',
            'conteo_baterias.*.estado' => 'required|integer',

        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            try{

                DB::beginTransaction();
                $entrada = ConteoFisico::find($request->id_conteo_fisico);
                $entrada->id_bodega = $request->conteo_bodega['id_bodega'];
                $entrada->estado = 1;
                $entrada->save();

                foreach ($request->conteo_baterias as $bateria) {

                    if($bateria['estado'] == 0){///Baterias eliminadas

                        if ($bateria['registrada']){///Baterias registradas
                            if(!empty($bateria['id_conteo_fisico_bateria'])){
                                EntradaInicialProductos::where('id_conteo_fisico_bateria', $bateria['id_conteo_fisico_bateria'])->delete();
                            }
                        }

                    }else //baterias validas
                    {
                        if (!$bateria['registrada']){///Baterias no registradas
                            if(empty($bateria['id_conteo_fisico_bateria'])){

                                $bateria_individual = new ConteoFisicoBaterias();
                                $bateria_individual->id_bateria = $bateria['id_bateria'];
                                $bateria_individual->id_producto = $bateria['id_producto'];
                                $bateria_individual->id_conteo_fisico = $request->id_conteo_fisico;
                                $bateria_individual->codigo_garantia =  $bateria['codigo_garantia'];
                                $bateria_individual->save();

                            }
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


    public function generarReporteConteoBaterias(Request $request)
    {
        // echo $ext;
        $rules = [
            'id_conteo_fisico' => 'required|integer|min:1',
            'extension' => 'required|string|max:4'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $os = array("xls", "pdf","html");
            //echo gethostname();
            if (in_array($request->extension, $os)) {

                $hora_actual = time();
                //$input = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/EstadoCuentaClienteDetalle';
                //$output = '/Applications/XAMPP/xamppfiles/htdocs/resources/reports/'.$hora_actual.'EstadoCuentaClienteDetalle';
                $nombre_repo = 'ReporteConteoFisico';

                $input = '/var/www/html/resources/reports/'.$nombre_repo;
                $output = '/var/www/html/resources/reports/' . $hora_actual . $nombre_repo;

                //$input = 'C:/xampp7/htdocs/resources/reports/'.$nombre_repo;
                //$output = 'C:/xampp7/htdocs/resources/reports/' .$hora_actual . $nombre_repo;

                $nombre_empresa = Ajustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = Ajustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';
                //$url = 'C:/xampp7/htdocs/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'id_conteo_fisico' => $request->id_conteo_fisico,
                        'entrada' => $request->id_conteo_fisico,
                        'directorioReports'=>$url,
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

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                if($request->extension == 'html'){
                    $headers = [
                        'Content-Type' => 'text/html',
                    ];
                }

                /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ReporteConteoFisico.' . $request->extension, $headers);

            }
            else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }

        }else{
            return '';
        }
    }
}
