<?php



namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Admon\Ajustes;
use App\Models\Inventario\ConsignacionProductosVista;
use PHPJasper\PHPJasper;
use Validator,Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule,DB;
class ConsignacionProductosController extends Controller
{
    /**
     * Obtener Movimientos Productos
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerPorCliente(Request $request, ConsignacionProductosVista $movimientos)
    {
        $rules = [
            'cliente' => 'required|array|min:1',
            'cliente.id_cliente' => 'required|integer|min:1|exists:pgsql.venta.clientes,id_cliente',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $movimientos = $movimientos->obtenerPorCliente($request);

            return response()->json([
                'status' => 'success',
                'result' => $movimientos,
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


    public function obtenerTodos(Request $request, ConsignacionProductosVista $movimientos)
    {
        $movimientos = $movimientos->obtenerTodos($request);
        return response()->json([
            'status' => 'success',
            'result' => $movimientos,
            'messages' => null
        ]);
    }


    public function reporteGeneralConsignacion(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp7/htdocs/resources/reports/ConsignacionProductosGeneral';
                //$output = 'C:/xampp7/htdocs/resources/reports/'.$hora_actual.'ConsignacionProductosGeneral';
                $input = '/var/www/html/resources/reports/ConsignacionProductosGeneral';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'ConsignacionProductosGeneral';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(\Illuminate\Support\Facades\DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
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

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ConsignacionProductosGeneral.' . $request->extension, $headers);

                /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/

            }else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
            /*  exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
              print_r($output);*/
        } else {
            return '';
        }
    }


    public function reporteMovimiento(Request $request)
    {
        $rules = [
            'extension' => 'required|string|max:3',
            'id_cliente' => 'required|integer'
        ];
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $os = array("pdf", "xls");
            if (in_array($request->extension, $os)) {
                $hora_actual = time();
                //$input = 'C:/xampp7/htdocs/resources/reports/ConsignacionProductosCliente';
                //$output = 'C:/xampp7/htdocs/resources/reports/'.$hora_actual.'ConsignacionProductosCliente';
                $input = '/var/www/html/resources/reports/ConsignacionProductosCliente';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'ConsignacionProductosCliente';
                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(\Illuminate\Support\Facades\DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';


                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'id_cliente' => $request->id_cliente,
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

                /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
              print_r($output);*/

                $headers = [
                    'Content-Type' => 'application/pdf',
                ];

                return response()->download($output . '.' . $request->extension, $hora_actual . 'ConsignacionProductosCliente.' . $request->extension, $headers);
            } else {
                return '';
            }
        }
    }




    public function generarReporteHistorialConsignacion(Request $request)
    {
        // echo $ext;
        $rules = [
            'extension' => 'required|string|max:3',
            'id_cliente' => 'required|integer|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $os = array("xls", "pdf");
            //echo gethostname();
            if (in_array($request->extension, $os)) {

                $hora_actual = time();
                //$input = 'C:/xampp7/htdocs/resources/reports/HistorialConsignacion';
                //$output = 'C:/xampp7/htdocs/resources/reports/' .$hora_actual . 'HistorialConsignacion';
                $input = '/var/www/html/resources/reports/HistorialConsignacion';
                $output = '/var/www/html/resources/reports/' . $hora_actual . 'HistorialConsignacion';

                $nombre_empresa = AdmonAjustes::where('id_ajuste',4)->select('valor')->first();
                $logo_empresa = AdmonAjustes::where('id_ajuste',3)->select(DB::raw("substr(valor, 2, length(valor) - 2)::json->>'file_thumbnail' as file_thumbnail"))->first();
                $url = '/var/www/html/resources/reports/';

                $options = [
                    'format' => [$request->extension],
                    'locale' => 'en',
                    'params' => [
                        'id_cliente' => $request->id_cliente,
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

                return response()->download($output . '.' . $request->extension, $hora_actual . 'HistorialConsignacion.' . $request->extension, $headers);
                /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/

            }
            else {
                return response()->json([
                    'status' => 'error',
                    'result' => $validator->messages(),
                    'messages' => null
                ]);
            }
            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }else{
            return '';
        }
    }
}
