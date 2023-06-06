<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\PeriodosFiscales;
use App\Models\Contabilidad\TasasCambios;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPJasper\Exception\ErrorCommandExecutable;
use PHPJasper\Exception\InvalidCommandExecutable;
use PHPJasper\Exception\InvalidFormat;
use PHPJasper\Exception\InvalidInputFile;
use PHPJasper\Exception\InvalidResourceDirectory;
use PHPJasper\PHPJasper;
use SoapClient;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TasasCambioController extends Controller
{


    public function obtenerTCParalela(Request $request)
    {
        $rules = [
            'fecha' => 'required|date',
            'dias' => 'required|integer|min:0'
        ];
        //echo $request->fecha;
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $nueva_fecha = date('Y-m-d', strtotime($request->fecha . ' + ' . $request->dias . ' days'));

            $tasa = TasasCambios::select('fecha', 'tasa_paralela', 'tasa')->where('fecha', $nueva_fecha)->first();
            if (!empty($tasa)) {

                return response()->json([
                    'status' => 'success',
                    'result' => $tasa,
                    'messages' => null
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => array('fecha' => ["Datos no encontrados"]),
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
     * obtener tasa de cambio por fecha
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerTC(Request $request)
    {
        $rules = [
            'fecha' => 'required|date'
        ];
        //echo $request->fecha;
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $tasa = TasasCambios::where('fecha', $request->fecha)->first();
            if (!empty($tasa)) {

                return response()->json([
                    'status' => 'success',
                    'result' => $tasa,
                    'messages' => null
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'result' => array('fecha' => ["Datos no encontrados"]),
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
     * Obtener Reporte de tasas de cambio
     *
     * @access  public
     * @param
     * @return JsonResponse
     * @author  octaviom
     */

    public function obtenerTasasReporte(Request $request, TasasCambios $tasas)
    {
        $tasas = $tasas->obtenerTasasReporte($request);
        return response()->json([
            'status' => 'success',
            'result' => $tasas,
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de tasas de cambio
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerTasasCambio(Request $request, TasasCambios $tasas)
    {
        $tasas = $tasas->obtenerTasasCambio($request);
        $periodo = PeriodosFiscales::select('id_periodo_fiscal', 'periodo')->with('mesesPeriodo')->get();
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $tasas->total(),
                'rows' => $tasas->items(),
                'periodos' => $periodo
            ],
            'messages' => null
        ]);
    }

    public function actualizarTCParalelas(Request $request)
    {

        $messages = [
            'tasas_nuevas.required' => 'El campo Número Factura es requerido cuando se confirma facturación',
            'tasas_nuevas.*.tasa_paralela.regex' => 'El formato de la tasa de cambio es inválido, se aceptan hasta 4 decimales'
        ];

        $rules = [
            'tasas_nuevas' => 'required|array|min:1',
            'tasas_nuevas.*.tasa_org' => 'required|numeric|regex:/^\d*(\.\d{1,6})?$/|min:0.00',
            'tasas_nuevas.*.tasa_paralela' => 'required|numeric|regex:/^\d*(\.\d{1,4})?$/|min:0.00',
            'tasas_nuevas.*.id_tasa_cambio' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if (!$validator->fails()) {
            $respuesta = response()->json([
                'status' => 'success',
                'result' => null,
                'messages' => null
            ]);
            DB::beginTransaction();
            try {

                foreach ($request->tasas_nuevas as $tasa) {
                    if ($tasa['tasa_paralela'] != $tasa['tasa_org']) {
                        $tasax = TasasCambios::find($tasa['id_tasa_cambio']);
                        $tasax->tasa_paralela = $tasa['tasa_paralela'];
                        $tasax->save();
                    }
                }

                DB::commit();
                return $respuesta;


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

    public function descargarTasasCambio(Request $request, TasasCambios $tasas)
    {
        $mes_anio = $tasas->obtenerMesAnioPendiente($request);
        if (empty($mes_anio[0]['mes']) && empty($mes_anio[0]['anio'])) {
            $mes_anio[0]['mes'] = date('m');
            $mes_anio[0]['anio'] = date('Y');
        }

        /*        $opts = array(
                    'http' => array(
                        'user_agent' => 'PHPSoapClient'
                    )
                );
                $context = stream_context_create($opts);*/
        /*        print_r($mes_anio[0]['mes']);
                print_r($mes_anio[0]['anio']);*/
        if (!empty($mes_anio[0]['mes']) && !empty($mes_anio[0]['anio'])) {

            $servicio = "https://servicios.bcn.gob.ni/Tc_Servicio/ServicioTC.asmx?WSDL"; //url del servicio
            $parametros = array(); //parametros de la llamada

            $parametros['Mes'] = $mes_anio[0]['mes'];
            $parametros['Ano'] = $mes_anio[0]['anio'];

            $options = array(
                'cache_wsdl' => 0,
                'trace' => 1,
                'stream_context' => stream_context_create(array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
//                            'allow_self_signed' => true
                        ),
                        'http' => array(
                            'user_agent' => 'PHPSoapClient'
                        )
                    )
                )
            );


            try {
                $client = new SoapClient($servicio, $options);

                $result = $client->RecuperaTC_Mes($parametros); //llamamos al métdo que nos interesa con los parámetros
                $Class = (array)$result->RecuperaTC_MesResult;

                $ValorDelXML = $Class['any'];
                $xml = simplexml_load_string($ValorDelXML);
                $array = (array)$xml;

                $respuesta = '';
                if (!empty($array) && $array['Tc'] !== null) {
                    $respuesta = $array['Tc'];
                } else {
                    $respuesta = ['Datos no encontrados'];
                }

                try {
                    if (!empty($respuesta[0]) && $respuesta[0] !== 'Datos no encontrados') {

                        DB::beginTransaction();
                        $cantidad = 0;
                        foreach ($respuesta as $dia) {
                            $tasa_cambio = TasasCambios::where('fecha', $dia->Fecha)->first();
                            $tasa_cambio->fecha = $dia->Fecha;
                            $tasa_cambio->tasa = $dia->Valor;
                            $tasa_cambio->save();
                            $cantidad++;
                        }

                        DB::commit();
                        return response()->json([
                            'status' => 'success',
                            'result' => ['Se han registrado ' . $cantidad . ' tasas de cambio para el mes ' . $parametros['Mes'] . ' del año ' . $parametros['Ano'] . '.'],
                            'messages' => null
                        ]);
                    }

                    return response()->json([
                        'status' => 'error',
                        'result' => ['No se han encontrado tasas de cambio para el mes ' . $parametros['Mes'] . ' del año ' . $parametros['Ano'] . '. Espere que sean publicadas en el sitio web del Banco Central'],
                        'messages' => null
                    ]);

                } catch (Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'result' => ['Error de base de datos'],
                        'messages' => $e->getMessage()
                    ]);
                }
            } catch (\SoapFault $e) {
                echo $e->getMessage();
            }
        } else {
            return response()->json([
                'status' => 'error',
                'result' => 'el campo de mes o año está vacíos, favor verificar ambas opciones',
                'messages' => null
            ]);
        }
    }

    /**
     * Generar Reporte Jasper
     *
     * @access  public
     * @param $ext
     * @param $anio
     * @param $mes
     * @return BinaryFileResponse
     * @throws ErrorCommandExecutable
     * @throws InvalidCommandExecutable
     * @throws InvalidFormat
     * @throws InvalidInputFile
     * @throws InvalidResourceDirectory
     */
    public function generarReporte($ext, $anio, $mes)
    {
        // echo $ext;
        // $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time();
            //$input = 'C:/xampp/htdocs/resources/reports/TasaCambio';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'TasaCambio';
            $input = '/var/www/html/resources/reports/TasaCambio';
            $output = '/var/www/html/resources/reports/' . $hora_actual . 'TasaCambio';

            $options = [
                'format' => [$ext],
                'locale' => 'en',
                'params' => [
                    'mes' => $mes,
                    'anio' => $anio
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

            return response()->download($output . '.' . $ext, $hora_actual . 'ReporteTasaCambio.' . $ext, $headers);

            /* exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
                print_r($output);*/
        }
    }

}
