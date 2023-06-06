<?php

namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\Departamentos;
use App\Models\Admon\Municipios;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Contabilidad\CentrosCostosIngresos;
use App\Models\Admon\Sectores;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use function Symfony\Component\ErrorHandler\Exception\setCode;

class SectoresController extends Controller
{
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtener(Request $request, Sectores $sectores)
    {
        $sectores = $sectores->obtenerZonas($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $sectores->total(),
                'rows' => $sectores->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de zonas sin paginacion
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerSectores(Request $request, Sectores $sectores)
    {
        $sectores = Sectores::with('sectoresDepartamento')->get();
        return response()->json([
            'status' => 'success',
            'result' => ['sectores' => $sectores],
            'messages' => null
        ]);
    }

    /**
     * obtener Rol Especifico
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtenerSector(Request $request)
    {
        $rules = [
            'id_sector'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $sectores = Sectores::where('id_sector',$request->id_sector)->with('sectorDepartamento')->get();
            $departamentos = Departamentos::with('sectoresDepartamento')->get();
            $center = Sectores::select('id_sector','descripcion','latitud', 'longitud','id_departamento')->where('id_sector',$request->id_sector)->first();
            //$posicion = Sectores::select(DB::raw("CONCAT(public.sectores.latitud,',',public.sectores.longitud) AS coordenadas"))->where('id_sector',$request->id_sector)->first();
            /*$centros_ingresos = CentrosCostosIngresos::select('id_centro','descripcion','codigo',DB::raw("concat(codigo,' ',descripcion) as descripcion_completa"))
                ->where('estado',1)->where('tipo_centro',2)->orderby('codigo')->get();

            $centros_costos = CentrosCostosIngresos::select('id_centro','descripcion','codigo',DB::raw("concat(codigo,' ',descripcion) as descripcion_completa"))
                ->where('estado',1)->where('tipo_centro',1)->orderby('codigo')->get();*/
            if(!empty($sectores[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'sectores'=>$sectores[0],
                        'departamentos'=>$departamentos,
                        'center' => $center
                        /*'centros_costos'=>$centros_costos,
                        'centros_ingresos'=>$centros_ingresos,*/
                    ],
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_sector'=>["Datos no encontrados"]),
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
     * Crear un nuevo rol
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function registrar(Request $request)
    {
        $rules = [
            'descripcion' => 'required|string|max:100|unique:pgsql.public.zonas,descripcion',
            'longitud' => 'required|float|min:1',
            'latitud' => 'required|float|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $sectores = new Sectores;
            $sectores->descripcion = $request->descripcion;
            $sectores->id_municipio = $request->id_municipio;
            $sectores->u_grabacion = Auth::user()->name;
            $sectores->estado = 1;
            $sectores->save();

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
            'id_sector' => 'required|integer|min:1',
            'descripcion' => [
                'required',
                'string',
                'max:100',
            ],
            'codigo'=>'required|integer|min:1',
            'departamento'=>'required|array|min:1',
            'departamento.id_departamento'=>'required|integer|min:1',
            'zona_municipio'=>'required|array|min:1',
            'zona_municipio.id_municipio'=>'required|integer|min:1',
        ];



        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $sectores = Sectores::find($request->id_sector);
            $sectores->descripcion = $request->sectores;
            $sectores->u_modificacion = Auth::user()->name;
            $sectores->id_municipio = $request->municipio['id_municipio'];
            $sectores->longitud = $request->longitud;
            $sectores->latitud = $request->latitud;
            $sectores->save();

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
            'id_sector' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $sectores = Sectores::find($request->id_sector);
            $sectores->u_modificacion = Auth::user()->name;
            $sectores->estado = 0;
            $sectores->save();

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
            'id_sectore' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $sectores = Sectores::find($request->id_sector);
            $sectores->u_modificacion = Auth::user()->name;
            $sectores->estado = 1;
            $sectores->save();



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
    public function nuevo(Request $request)/*cambiar esta chuchada : nota: ya no, ya lo hice*/
    {
        $centros_ingresos = CentrosCostosIngresos::select('id_centro','descripcion','codigo',DB::raw("concat(codigo,' ',descripcion) as descripcion_completa"))
            ->where('estado',1)->where('tipo_centro',2)->orderby('codigo')->get();

        $centros_costos = CentrosCostosIngresos::select('id_centro','descripcion','codigo',DB::raw("concat(codigo,' ',descripcion) as descripcion_completa"))
            ->where('estado',1)->where('tipo_centro',1)->orderby('codigo')->get();

        return response()->json([
            'status' => 'success',
            'result' => [
                'centros_costos' => $centros_costos,
                'centros_ingresos' => $centros_ingresos,
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
