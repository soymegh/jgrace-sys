<?php

namespace App\Http\Controllers;

use App\Models\ContabilidadCuentasContables;
use App\Models\ContabilidadPeriodoFiscal;
use App\Models\ContabilidadPeriodoMeses;
use App\Models\PublicDepartamentos;
use App\Models\PublicMunicipios;
use App\Models\RRHHContratoGeneralInterno;
use App\Models\RRHHContratoGeneralMerecedor;
use App\Models\RRHHContratoSolicitud;
use App\Models\RRHHPlanillasControles;
use App\Models\RRHHNivelesEstudios;
use App\Models\RRHHContratoTipos;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;
use Validator,Auth;
use App\Models\RRHHNivelesAcademicos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\PublicSucursales;

class RRHHPlanillasControlesController extends Controller
{
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param Request $request
     * @param RRHHPlanillasControles $planilla
     * @return  json(array)
     */

    public function obtener(Request $request, RRHHPlanillasControles $planilla)
    {
        $planilla = $planilla->obtenerPlanillasControles($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $planilla->total(),
                'rows' => $planilla->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de zonas sin paginacion
     *
     * @access  public
     * @param Request $request
     * @param RRHHPlanillasControles $planilla
     * @return  json(array)
     */

    public function obtenerTodas(Request $request, RRHHPlanillasControles $planilla)
    {
        $planilla = RRHHPlanillasControles::orderby('id_planilla_control')->get();
        return response()->json([
            'status' => 'success',
            'result' => $planilla,
            'messages' => null
        ]);
    }

    /**
     * obtener Rol Especifico
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerPlanillaControl(Request $request)
    {
        $rules = [
            'id_planilla_control'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $planilla = RRHHPlanillasControles::where('id_planilla_control',$request->id_planilla_control)->with('planillaControlSucursal')->with('planillaPeriodoProceso')->with('planillaMesProceso')->first();
            $sucursales = PublicSucursales::select('id_sucursal','descripcion')->get();
            $periodos = ContabilidadPeriodoFiscal::select('id_periodo_fiscal','periodo')->with('mesesPeriodo')->get();
            if(!empty($planilla)){
                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'planilla' => $planilla,
                        'sucursales' => $sucursales,
                        'periodos' => $periodos
                    ],
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_planilla_control'=>["Datos no encontrados"]),
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
     * Crear un nueva planilla
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function registrar(Request $request)
    {
        $rules = [
            // 'sucursal.id_sucursal' => 'required|integer|min:1',
            //'codigo_planilla' => 'required|integer',
            'tipo_planilla' => 'required|string',
            'descripcion' => 'required|string|max:100|unique:pgsql.rrhh.planillas_controles,descripcion',
            'f_desde' => 'required|date',
            'f_hasta' => 'required|date',
            'anio.periodo' => 'required|integer',
            'mes.mes' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $str_length = 3;
            $secuencia = RRHHPlanillasControles::select(DB::raw('COALESCE(max(codigo_planilla::integer),0) + 1 as codigo' )) -> where('tipo_planilla',$request->tipo_planilla)->first();
            $planilla = new RRHHPlanillasControles();
            // $planilla->id_sucursal = $request->sucursal['id_sucursal'];
            $planilla->id_sucursal = 1; //No se utiliza sucursal para el control de planilla, set 1 para no guardar null
            $codigo = $secuencia['codigo'];
            $str = substr("00{$codigo}", -$str_length);
            $planilla->codigo_planilla = $str;
            $planilla->tipo_planilla = $request->tipo_planilla;
            $planilla->descripcion = $request->descripcion;
            $planilla->f_desde = $request->f_desde;
            $planilla->f_hasta = $request->f_hasta;
            $planilla->anio_proceso = $request->anio['periodo'];
            $planilla->mes_proceso = $request->mes['mes'];
            // $planilla->f_calculo = $request->f_calculo;
            $planilla->u_grabacion = Auth::user()->usuario;
            $planilla->estado = 1;
            $planilla->save();

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

    /**
     * Actualizar Rol existente
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_planilla_control' => 'required|integer|min:1',
            //'planilla_control_sucursal.id_sucursal' => 'required|integer|min:1',
            //'codigo_planilla' => 'required|integer',
            'tipo_planilla' => 'required|string',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                //Rule::unique('pgsql.rrhh.planillas_controles,descripcion')->ignore($request->id_planilla_control,'id_planilla_control')
            ],
            'f_desde' => 'required|date',
            'f_hasta' => 'required|date',
            //'anio_proceso.periodo' => 'required|integer',
            //'mes_proceso.mes' => 'required|integer',

        ];



        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $planilla = RRHHPlanillasControles::find($request->id_planilla_control);
            $planilla->id_sucursal = 1;
            $planilla->tipo_planilla = $request->tipo_planilla;
            $planilla->descripcion = $request->descripcion;
            $planilla->f_desde = $request->f_desde;
            $planilla->f_hasta = $request->f_hasta;
            $planilla->anio_proceso = $request->anio_proceso['periodo'];
            $planilla->mes_proceso = $request->mes_proceso['mes'];
            // $planilla->f_calculo = $request->f_calculo;
            $planilla->u_modificacion = Auth::user()->usuario;
            $planilla->estado = 1;
            $planilla->save();

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

    /**
     * Desactiva rol
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_planilla_control' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $planilla = RRHHPlanillasControles::find($request->id_planilla_control);
            $planilla->estado = 0;
            $planilla->save();

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


    /**
     * Activa rol
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function activar(Request $request)
    {
        $rules = [
            'id_planilla_control' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $planilla = RRHHPlanillasControles::find($request->id_planilla_control);
            $planilla->estado = 1;
            $planilla->save();

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
        $sucursales = PublicSucursales::select('id_sucursal','descripcion')->get();

        $periodos = ContabilidadPeriodoFiscal::select('id_periodo_fiscal','periodo')->with('mesesPeriodo')->get();


        return response()->json([
            'status' => 'success',
            'result' => [
                'sucursales' => $sucursales,
                'periodos' => $periodos
            ],
            'messages' => null
        ]);

    }

    public function generarReporte($ext)
    {
        // echo $ext;
        // $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteZonas';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteZonas';
            $input = '/var/www/html/resources/reports/ReporteZonas';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteZonas';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteZonas.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

}