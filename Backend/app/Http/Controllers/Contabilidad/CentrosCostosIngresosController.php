<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\CentrosCostosIngresos;
use PHPJasper\PHPJasper;
use Validator,Auth;
use App\Models\PublicZonas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CentrosCostosIngresosController extends Controller
{
    /**
     * Obtener una lista de centros de costos (con opcion de busqueda y paginacion)
     *
     * @access  public
     * @param Request $request
     * @param CentrosCostosIngresos $centros
     * @return  json(array)
     * @author omcs
     */

    public function obtener(Request $request, CentrosCostosIngresos $centros)
    {
        $centros = $centros->obtenerCentroCostoIngreso($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $centros->total(),
                'rows' => $centros->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de centros de costo sin paginacion
     *
     * @access  public
     * @param Request $request
     * @param CentrosCostosIngresos $centros
     * @return  json(array)
     */

    public function obtenerTodos(Request $request, CentrosCostosIngresos $centros)
    {
        $centros = CentrosCostosIngresos::orderby('id_centro')->get();
        return response()->json([
            'status' => 'success',
            'result' => $centros,
            'messages' => null
        ]);
    }

    /**
     * obtener Rol Especifico
     *
     * @access  public
     * @param
     * @return  json(array)
     * @author omcs
     */

    public function obtenerCentro(Request $request)
    {
        $rules = [
            'id_centro'=> 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $centros = CentrosCostosIngresos::find($request->id_centro);

            if(!empty($centros)){
                return response()->json([
                    'status' => 'success',
                    'result' => $centros,
                    'messages' => null
                ]);

            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_centro'=>["Datos no encontrados"]),
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
     * Crear un nuevo centro de costo
     *
     * @access  public
     * @param
     * @return  json(string)
     * @author omcs
     */

    public function registrar(Request $request)
    {
        $rules = [
            'tipo_centro' => 'required|integer|min:1|max:2',
            'ubicacion' => 'required|integer|min:1|max:2',
            'clasificacion_contable' => 'required|integer|min:0|max:3',
            //  'descripcion' => 'required|string|max:100|unique:pgsql.contabilidad.centros_costos_ingresos,descripcion',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.contabilidad.centros_costos_ingresos')->ignore($request->tipo_centro==1?2:1,'tipo_centro')
            ],
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $centros = new CentrosCostosIngresos();
            //print_r(array('tipo_centro'=>[$request->tipo_centro]));
            $codigo = $centros->obtenerCodigo($request->tipo_centro,$request->ubicacion,$request->clasificacion_contable);

            $nuevo_codigo = json_decode($codigo);
            //$centros->id_centro = CentrosCostosIngresos::max('id_centro') +1;
            $centros->tipo_centro = $request->tipo_centro;
            $centros->ubicacion = $request->ubicacion;
            $centros->clasificacion_contable = $request->clasificacion_contable;
            $centros->descripcion = $request->descripcion;
            $secuencia = $nuevo_codigo->secuencia;

            if($request->tipo_centro===1){
                $centros->codigo = $centros->ubicacion.substr("00{$secuencia}", -3);
            }elseif($request->tipo_centro===2){
                $centros->codigo = $centros->clasificacion_contable.$centros->ubicacion.substr("00{$secuencia}", -3);
            }

            // $centros->codigo = $this->zero_fill($centros->tipo_centro). $this->zero_fill($secuencia);
            $centros -> secuencia =  $secuencia;
            $centros->u_creacion = Auth::user()->name;
            $centros->estado = 1;
            $centros->save();

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
     * Actualizar centro de costo existente
     *
     * @access  public
     * @param
     * @return  json(string)
     * @author omcs
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_centro' => 'required|integer|min:1',
            'descripcion' => [
                'required',
                'string',
                'max:100',
                // Rule::unique('pgsql.contabilidad.centros_costos_ingresos')
                //   ->ignore($request->id_centro,'id_centro')
            ],
        ];



        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $centro = CentrosCostosIngresos::find($request->id_centro);

            $centrox= CentrosCostosIngresos::where('id_centro','!=',$request->id_centro)
                ->where('descripcion',$centro->descripcion)->where('tipo_centro',$centro->tipo_centro)->first();

            if(!empty($centrox)){
                return response()->json([
                    'status' => 'error',
                    'result' => array('descripcion'=>['Ya existe un centro de costo/ingreso con esta descripción']),
                    'messages' => null
                ]);
            }else{
                $centro->descripcion = $request->descripcion;
                $centro->save();


                return response()->json([
                    'status' => 'success',
                    'result' => null,
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
     * Desactiva centro de costo
     *
     * @access  public
     * @param
     * @return  json(string)
     * @author omcs
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_centro' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $centros = CentrosCostosIngresos::find($request->id_centro);
            $centros->estado = 0;
            $centros->save();

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
     * Activa centro de costo
     *
     * @access  public
     * @param
     * @return  json(string)
     * @author omcs
     */

    public function activar(Request $request)
    {
        $rules = [
            'id_centro' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $centros = CentrosCostosIngresos::find($request->id_centro);
            $centros->estado = 1;
            $centros->save();

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

    public function zero_fill ($valor, $long = 2)
    {
        return str_pad($valor, $long, '0', STR_PAD_LEFT);
    }

    public function generarReporte($ext)
    {
        // echo $ext;
        // $ext = 'pdf';
        $os = array("xls", "pdf");
        if (in_array($ext, $os)) {
            $hora_actual = time() ;
            //$input = 'C:/xampp/htdocs/resources/reports/CentroCostoIngreso';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'CentroCostoIngreso';
            $input = '/var/www/html/resources/reports/CentroCostoIngreso';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'CentroCostoIngreso';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'CentroCostoIngreso.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }

}
