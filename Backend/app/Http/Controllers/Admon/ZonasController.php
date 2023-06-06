<?php

namespace App\Http\Controllers\Admon;

use App\Http\Controllers\Controller;
use App\Models\Admon\Departamentos;
use App\Models\Admon\Municipios;
use App\Models\Admon\Sectores;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Admon\ZonasSectores;
use App\Models\Contabilidad\CentrosCostosIngresos;
use App\Models\Admon\Zonas;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ZonasController extends Controller
{
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param
     * @return JsonResponse
     */

    public function obtener(Request $request, Zonas $zonas)
    {
        $zonas = $zonas->obtenerZonas($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $zonas->total(),
                'rows' => $zonas->items()
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

    public function obtenerZonas(Request $request, Zonas $zonas)
    {
        $zonas = Zonas::select('id_zona','descripcion','activo')->where('activo',1)->get();
        return response()->json([
            'status' => 'success',
            'result' => ['zonas' => $zonas],
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

    public function obtenerZona(Request $request)
    {
        $rules = [
            'id_zona'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $zona = Zonas::where('id_zona',$request->id_zona)->with('zonaMunicipio')->get();
            $departamentos = Departamentos::with('municipiosDepartamento')->get();
            /*$centros_ingresos = CentrosCostosIngresos::select('id_centro','descripcion','codigo',DB::raw("concat(codigo,' ',descripcion) as descripcion_completa"))
                ->where('estado',1)->where('tipo_centro',2)->orderby('codigo')->get();

            $centros_costos = CentrosCostosIngresos::select('id_centro','descripcion','codigo',DB::raw("concat(codigo,' ',descripcion) as descripcion_completa"))
                ->where('estado',1)->where('tipo_centro',1)->orderby('codigo')->get();*/
            if(!empty($zona[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => [
                        'zona'=>$zona[0],
                        'departamentos'=>$departamentos
                        /*'centros_costos'=>$centros_costos,
                        'centros_ingresos'=>$centros_ingresos,*/
                    ],
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_zona'=>["Datos no encontrados"]),
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
        ];

        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $zona = new Zonas;
            $zona->descripcion = $request->descripcion;
            $codigo_nuevo = $zona->obtenerCodigoZona();
            $str_length = 3;
            $str = substr("00{$codigo_nuevo['secuencia']}", -$str_length);
//            $zona->id_sector = $request->markers['id_sector'];
            $zona->codigo = $str;
            $zona->u_grabacion = Auth::user()->name;
            $zona->id_empresa = $usuario_empresa->id_empresa;
            $zona->estado = 1;

            foreach ($request->markers as $sector){
                $sector = new ZonasSectores;
                $sector->id_sector = $request->id_sector;
                $sector->id_zona = $zona->id_zona;
                $sector->u_creacion = Auth::user()->name;
                $sector->estado = 1;
                $sector->id_empresa = $usuario_empresa->id_empresa;
                $sector->save();
            }
            $zona->save();

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
            'id_zona' => 'required|integer|min:1',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.public.zonas')->ignore($request->id_zona,'id_zona')
            ],
            'codigo'=>'required|integer|min:1',
            'departamento'=>'required|array|min:1',
            'departamento.id_departamento'=>'required|integer|min:1',
            'zona_municipio'=>'required|array|min:1',
            'zona_municipio.id_municipio'=>'required|integer|min:1',
        ];



        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $zona = Zonas::find($request->id_zona);
            $zona->descripcion = $request->descripcion;
            $zona->u_modificacion = Auth::user()->name;
            $zona->codigo = $request->codigo;
            $zona->id_departamento = $request->departamento['id_departamento'];
            $zona->id_municipio = $request->zona_municipio['id_municipio'];
            $zona->save();

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
            'id_zona' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $zona = Zonas::find($request->id_zona);
            $zona->u_modificacion = Auth::user()->name;
            $zona->estado = 0;
            $zona->save();

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
            'id_zona' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $rol = Zonas::find($request->id_zona);
            $rol->u_modificacion = Auth::user()->name;
            $rol->estado = 1;
            $rol->save();



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
