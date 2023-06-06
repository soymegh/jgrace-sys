<?php 

namespace App\Http\Controllers;

use PHPJasper\PHPJasper;
use Validator,Auth;
use App\Models\RRHHNivelesAcademicos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class RRHHNivelesAcademicosController extends Controller
{
    /**
     * Obtener una lista de Roles (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param Request $request
     * @param RRHHNivelesAcademicos $nivel
     * @return  json(array)
     */

    public function obtener(Request $request, RRHHNivelesAcademicos $nivel)
    {
        $nivel = $nivel->obtenerNivelesAcademicos($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $nivel->total(), 
                'rows' => $nivel->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de zonas sin paginacion
     *
     * @access  public
     * @param Request $request
     * @param RRHHNivelesAcademicos $nivel
     * @return  json(array)
     */

    public function obtenerTodas(Request $request, RRHHNivelesAcademicos $nivel)
    {
        $nivel = RRHHNivelesAcademicos::orderby('id_nivel_academico')->get();
        return response()->json([
            'status' => 'success',
            'result' => $nivel,
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

    public function obtenerNivelAcademico(Request $request)
    {
        $rules = [
            'id_nivel_academico'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $nivel = RRHHNivelesAcademicos::find($request->id_nivel_academico);

            if(!empty($nivel)){	
            return response()->json([
                'status' => 'success',
                'result' => $nivel,
                'messages' => null
            ]);

        }
		else{
		  return response()->json([
				'status' => 'error',
				'result' => array('id_nivel_academico'=>["Datos no encontrados"]),
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
            'descripcion' => 'required|string|max:100|unique:pgsql.rrhh.niveles_academicos,descripcion',
            'abreviatura' => 'required|string|max:100|unique:pgsql.rrhh.niveles_academicos,abreviatura',
            'acreditacion' => 'required|string|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {

            $nivel = new RRHHNivelesAcademicos();
            $nivel->descripcion = $request->descripcion;
            $nivel->abreviatura = $request->abreviatura;
            $nivel->acreditacion = $request->acreditacion;
            $nivel->u_grabacion = Auth::user()->usuario;
            $nivel->estado = 1;
            $nivel->save();

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
            'id_nivel_academico' => 'required|integer|min:1',
            'descripcion' => 'required|string|max:100',
            'abreviatura' => 'required|string|max:100',
            'acreditacion' => 'required|string|max:100',
        ];



        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $nivel = RRHHNivelesAcademicos::find($request->id_nivel_academico);
            $nivel->descripcion = $request->descripcion;
            $nivel->abreviatura = $request->abreviatura;
            $nivel->acreditacion = $request->acreditacion;
            $nivel->u_modificacion = Auth::user()->usuario;
            $nivel->save();

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
            'id_nivel_academico' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $nivel = RRHHNivelesAcademicos::find($request->id_nivel_academico);
            $nivel->estado = 0;
            $nivel->save();

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
            'id_nivel_academico' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $nivel = RRHHNivelesAcademicos::find($request->id_nivel_academico);
            $nivel->estado = 1;
            $nivel->save();

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