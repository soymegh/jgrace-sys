<?php

namespace App\Http\Controllers\Bitacora;

use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;
use Validator;
use App\Models\Bitacora\Accesos;
use Illuminate\Http\Request;

class AccesosController extends Controller
{
    /**
     * Obtener una lista de Accesos con PAGINACION
     *
     * @access  public
     * @param Request $request
     * @param Accesos $accesos
     * @return JsonResponse
     */

    public function obtenerAccesos(Request $request, Accesos $accesos)
    {
        $accesos = $accesos->obtenerAccesos($request);


        foreach($accesos->items() as $item ){
            $agent = new Agent();
            $agent->setUserAgent($item['dispositivo']);
            $item['dispositivo']=$agent->platform().' '.$agent->version($agent->platform()) .' '.$agent->browser().' '. (int) $agent->version($agent->browser()).' '. $agent->device();

        }

        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $accesos->total(),
                'rows' => $accesos->items(),
                'usuario' => Auth::user()
            ],
            'messages' => null
        ]);
    }


    /**
     * Obtener una lista de Accesos SIN PAGINACION
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerAccesosReporte(Request $request, Accesos $accesos)
    {
        $accesos = $accesos->obtenerAccesosReporte($request);
        return response()->json([
            'status' => 'success',
            'result' => $accesos,
            'messages' => null
        ]);
    }


    public function generarReporte()
    {
        // echo $ext;
        $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/Accesos';
            // $output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'Accesos';
            $input = '/var/www/html/resources/reports/Accesos';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'Accesos';

            $options = [
                'format' => [$ext],
                'locale' => 'en',
                'params' => [],
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

            return response()->download($output . '.' . $ext ,$hora_actual. 'Accesos.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

}
