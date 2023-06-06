<?php 

namespace App\Http\Controllers;

use PHPJasper\PHPJasper;
use Validator,Auth;
use App\Models\RRHHCargos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class RRHHCargosController extends Controller
{
    /**
     * Obtener una lista de cargos
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function obtenerCargos(Request $request, RRHHCargos $cargos)
    {
        $cargos = $cargos->obtenerCargos($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $cargos->total(), 
                'rows' => $cargos->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de cargos sin ningun filtro
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function obtenerTodosCargos(Request $request, RRHHCargos $cargos)
    {
        $cargos = RRHHCargos::where('activo', 1)->get();
        return response()->json([
            'status' => 'success',
            'result' => $cargos,
            'messages' => null
        ]);
    }

    /**
     * obtener tipo de Salida Especifico
     *
     * @access  public
     * @param   
     * @return  json(array)
     */

    public function obtenerCargo(Request $request)
    {
        $rules = [
            'id_cargo' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cargo = RRHHCargos::find($request->id_cargo);

            if(!empty($cargo)){		
            return response()->json([
                'status' => 'success',
                'result' => $cargo,
                'messages' => null
            ]);

        }
        else{
          return response()->json([
                'status' => 'error',
                'result' => array('id_cargo'=>["Datos no encontrados"]),
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
     * Crear un nuevo tipo de Salida
     *
     * @access  public
     * @param   
     * @return  json(string)
     */

    public function registrar(Request $request)
    {
        $rules = [
            'descripcion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.rrhh.cargos')/*->ignore($request->id_cargo,'id_cargo')*/]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cargo = new RRHHCargos;
            $cargo->descripcion = $request->descripcion;
            $cargo->activo = 1;
            $cargo->u_creacion = Auth::user()->usuario;
            $cargo->save();

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
            'id_cargo' => 'required|integer|min:1',
            'descripcion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.rrhh.cargos')->ignore($request->id_cargo,'id_cargo')]
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cargo = RRHHCargos::find($request->id_cargo);
            $cargo->descripcion = $request->descripcion;
            $cargo->save();

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
            'id_cargo' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cargo = RRHHCargos::find($request->id_cargo);
            $cargo->activo = 0;
            $cargo->save();

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


    public function activar(Request $request)
    {
        $rules = [
            'id_cargo' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $cargo = RRHHCargos::find($request->id_cargo);
            $cargo->activo = 1;
            $cargo->save();

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
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteCargos';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteCargos';
             $input = '/var/www/html/resources/reports/ReporteCargos';
             $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteCargos';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteCargos.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

}