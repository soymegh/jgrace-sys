<?php 

namespace App\Http\Controllers;

use App\Models\PublicDepartamentos;
use App\Models\PublicMunicipios;
use App\Models\RRHHContratoGeneralMerecedor;
use App\Models\RRHHNivelesEstudios;
use App\Models\RRHHTiposActosJuridicos;
use Illuminate\Support\Facades\DB;
use PHPJasper\PHPJasper;
use Validator,Auth;
use App\Models\RRHHNivelesAcademicos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class RRHHContratoGeneralMerecedorController extends Controller
{
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param Request $request
     * @param RRHHContratoGeneralMerecedor $contrato
     * @return  json(array)
     */

    public function obtener(Request $request, RRHHContratoGeneralMerecedor $contrato)
    {
        $contrato = $contrato->obtenerContratosMerecedores($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $contrato->total(), 
                'rows' => $contrato->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de zonas sin paginacion
     *
     * @access  public
     * @param Request $request
     * @param RRHHContratoGeneralInterno $contrato
     * @return  json(array)
     */

    public function obtenerTodas(Request $request, RRHHContratoGeneralMerecedor $contrato)
    {
        $contrato = RRHHContratoGeneralMerecedor::select('id_contrato_dgeneral_merecedor','nombre_representante','caracter_cargo','id_tipo_acto_juridico','caracter_legal',
            DB::raw("CONCAT((select t.descripcion from rrhh.tipos_actos_juridicos t where t.id_tipo_acto_juridico = rrhh.contratos_dgenerales_merecedores.id_tipo_acto_juridico),'(',nombre_representante,')') AS representante_acto"))->orderby('id_contrato_dgeneral_merecedor')->get();
        return response()->json([
            'status' => 'success',
            'result' => $contrato,
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

    public function obtenerContratoGeneral(Request $request)
    {
        $rules = [
            'id_contrato_dgeneral_merecedor'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $contrato = RRHHContratoGeneralMerecedor::where('id_contrato_dgeneral_merecedor',$request->id_contrato_dgeneral_merecedor)->with('contratoMerecedorNivelEstudio','contratoMerecedorNivelAcademico','contratoMerecedorDepartamento','contratoMerecedorDepartamentoDomicilio','contratoMerecedorTipoActoJuridico','contratoMerecedorDepartamentoLibrado')->first();
            $nivel_academico = RRHHNivelesAcademicos::select('id_nivel_academico','descripcion')->get();
            $nivel_estudio = RRHHNivelesEstudios::select('id_niveles_estudios','descripcion')->get();
            $departamento = PublicDepartamentos::select('id_departamento','descripcion')->get();
            $domicilio = PublicDepartamentos::select('id_departamento','descripcion')->get();
            $departamento_librado = PublicDepartamentos::select('id_departamento','descripcion')->get();
            $tipo_acto = RRHHTiposActosJuridicos::select('id_tipo_acto_juridico','descripcion')->get();

            if(!empty($contrato)){	
            return response()->json([
                'status' => 'success',
                'result' => [
                    'contrato' => $contrato,
                    'nivel_academico' => $nivel_academico,
                    'nivel_estudio' => $nivel_estudio,
                    'domicilio' => $domicilio,
                    'departamento' => $departamento,
                    'departamento_librado' => $departamento_librado,
                    'tipo_acto' => $tipo_acto,
                ],
                'messages' => null
            ]);

        }
		else{
		  return response()->json([
				'status' => 'error',
				'result' => array('id_contrato_dgeneral_merecedor'=>["Datos no encontrados"]),
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
     * @return  json(string)
     */

    public function registrar(Request $request)
    {
        $rules = [
            'nombre_representante' => 'required|string|max:100',
            'estado_civil' => 'required|integer',
            'nivel' =>  'nullable|array|min:1',
            'nivel_estudio' =>  'nullable|array|min:1',
            'tipo_acto' =>  'nullable|array|min:1',
            'caracter_cargo' => 'required|string|max:150',
            'caracter_legal' => 'required|string|max:150',
            'no_escritura_publica' => 'required|integer|min:1',
            'nombre_notario_publico' => 'required|string|max:100',
            'no_asiento_librodiario' => 'required|integer|min:1',
            'no_inscrito' => 'required|integer|min:1',
            'no_tomo' => 'required|integer|min:1',
            'domicilio' => 'required|array|min:1',
            'departamento' => 'required|array|min:1',
            'denominacion' => 'required|string|max:100',
            'no_identificacion' => 'required|string|max:18',
            'nombre_empresa' => 'required|string|max:100',
            'descripcion_contractual' => 'required|string|max:150',
            'no_ruc' => 'required|string|max:18',
            'fecha_inscripcion' => 'required|date',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            
            
            $contrato = new RRHHContratoGeneralMerecedor;
            $contrato->nombre_representante = $request->nombre_representante;
            $contrato->estado_civil = $request->estado_civil;
            $contrato->id_nivel_academico = $request->nivel['id_nivel_academico'];
            $contrato->id_nivel_estudio = $request->nivel_estudio['id_niveles_estudios'];
            $contrato->id_tipo_acto_juridico = $request->tipo_acto['id_tipo_acto_juridico'];
            $contrato->caracter_cargo = $request->caracter_cargo;
            $contrato->caracter_legal = $request->caracter_legal;
            $contrato->no_escritura_publica = $request->no_escritura_publica;
            $contrato->no_escritura_publica_letras = $request->no_escritura_publica_letras;
            $contrato->nombre_notario_publico = $request->nombre_notario_publico;
            $contrato->no_asiento_librodiario = $request->no_asiento_librodiario;
            $contrato->no_inscrito = $request->no_inscrito;
            $contrato->no_tomo = $request->no_tomo;
            $contrato->domicilio = $request->domicilio['id_departamento'];
            $contrato->departemento = $request->departamento['id_departamento'];
            $contrato->departamento_librado = $request->departamento_librado['id_departamento'];
            $contrato->denominacion = $request->denominacion;
            $contrato->no_identificacion = $request->no_identificacion;
            $contrato->nombre_empresa = $request->nombre_empresa;
            $contrato->descripcion_contractual = $request->descripcion_contractual;
            $contrato->no_ruc = $request->no_ruc;
            $contrato->fecha_inscripcion = $request->fecha_inscripcion;
            $contrato->fecha_inscripcion_letras = $request->fecha_inscripcion_letra;
            $contrato->fecha_librada = $request->fecha_librada;
            $contrato->fecha_librada_letras = $request->fecha_librada_letras;
            $contrato->paginas = $request->paginas;
            //$contrato->u_grabacion = Auth::user()->usuario;
           // $contrato->activo = 1;
            $contrato->save();

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
            'id_contrato_dgeneral_merecedor' => 'required|integer|min:1'

        ];



        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $contrato = RRHHContratoGeneralMerecedor::find($request->id_contrato_dgeneral_merecedor);
            $contrato->nombre_representante = $request->nombre_representante;
            $contrato->estado_civil = $request->estado_civil;
            $contrato->id_nivel_academico = $request->contrato_merecedor_nivel_academico['id_nivel_academico'];
            $contrato->id_nivel_estudio = $request->contrato_merecedor_nivel_estudio['id_niveles_estudios'];
            $contrato->id_tipo_acto_juridico = $request->contrato_merecedor_tipo_acto_juridico['id_tipo_acto_juridico'];
            $contrato->caracter_cargo = $request->caracter_cargo;
            $contrato->caracter_legal = $request->caracter_legal;
            $contrato->no_escritura_publica = $request->no_escritura_publica;
            $contrato->no_escritura_publica_letras = $request->no_escritura_publica_letras;
            $contrato->nombre_notario_publico = $request->nombre_notario_publico;
            $contrato->no_asiento_librodiario = $request->no_asiento_librodiario;
            $contrato->no_inscrito = $request->no_inscrito;
            $contrato->no_tomo = $request->no_tomo;
            $contrato->domicilio = $request->contrato_merecedor_departamento_domicilio['id_departamento'];
            $contrato->departemento = $request->contrato_merecedor_departamento['id_departamento'];
            $contrato->departamento_librado = $request->contrato_merecedor_departamento_librado['id_departamento'];
            $contrato->denominacion = $request->denominacion;
            $contrato->no_identificacion = $request->no_identificacion;
            $contrato->nombre_empresa = $request->nombre_empresa;
            $contrato->descripcion_contractual = $request->descripcion_contractual;
            $contrato->no_ruc = $request->no_ruc;
            $contrato->fecha_inscripcion = $request->fecha_inscripcion;
            $contrato->fecha_inscripcion_letras = $request->fecha_inscripcion_letra;
            $contrato->fecha_librada = $request->fecha_librada;
            $contrato->fecha_librada_letras = $request->fecha_librada_letras;
            $contrato->paginas = $request->paginas;
            //$contrato->u_grabacion = Auth::user()->usuario;
            // $contrato->activo = 1;
            $contrato->save();;

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
            $contrato = PublicZonas::find($request->id_zona);
            $contrato->activo = 0;
            $contrato->save();

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
            $rol = PublicZonas::find($request->id_zona);
            $rol->activo = 1;
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