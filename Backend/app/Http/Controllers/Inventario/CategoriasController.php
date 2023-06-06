<?php



namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Admon\UsuariosEmpresas;
use App\Models\Inventario\CategoriasProductos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPJasper\PHPJasper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule,DB;
class CategoriasController extends Controller
{
    /**
     * Obtener una lista de categorias
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtener(Request $request, CategoriasProductos $categorias)
    {
        $categorias = $categorias->obtener($request);
        return response()->json([
            'status' => 'success',
            'result' => [
                'total' => $categorias->total(),
                'rows' => $categorias->items()
            ],
            'messages' => null
        ]);
    }

    /**
     * Obtener una lista de categorias sin ningun filtro
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerTodas(Request $request, CategoriasProductos $categorias)
    {
        $categorias = CategoriasProductos::where('estado',1)->get();
        return response()->json([
            'status' => 'success',
            'result' => $categorias,
            'messages' => null
        ]);
    }

    /**
     * obtener categoria Especifica
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function obtenerCategoria(Request $request)
    {
        $rules = [
            'id_categoria' => 'required|integer|min:1'
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $categoria = CategoriasProductos::
            where('id_categoria',$request->id_categoria)
                ->get();

            if(!empty($categoria[0])){
                return response()->json([
                    'status' => 'success',
                    'result' => $categoria[0],
                    'messages' => null
                ]);
            }
            else{
                return response()->json([
                    'status' => 'error',
                    'result' => array('id_categoria'=>["Datos no encontrados"]),
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
     * Crear una nueva categoria
     *
     * @access  public
     * @param
     * @return  json(string)
     */
    public function registrar(Request $request)
    {
        $rules = [
            'nombre_categoria' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.inventario.categorias_productos')],
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.inventario.categorias_productos')],
        ];
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $categoria = new CategoriasProductos;
            $categoria->nombre_categoria = $request->nombre_categoria;
            $categoria->descripcion = $request->descripcion;
            $categoria->id_empresa = $usuario_empresa->id_empresa;
            $categoria->u_creacion = Auth::user()->name;
            $categoria->estado = 1;
            $categoria->save();

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
     * Actualizar Categoria existente
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function actualizar(Request $request)
    {
        $rules = [
            'id_categoria' => 'required|integer|min:1',
            'nombre_categoria' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pgsql.inventario.categorias_productos')->ignore($request->id_categoria,'id_categoria')],
            'descripcion' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pgsql.inventario.categorias_productos')->ignore($request->id_categoria,'id_categoria')],
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $categoria = CategoriasProductos::find($request->id_categoria);
            $categoria->nombre_categoria = $request->nombre_categoria;
            $categoria->descripcion = $request->descripcion;
            $categoria->u_modificacion = Auth::user()->name;
            $categoria->save();

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
     * Desactiva Categoria
     *
     * @access  public
     * @param
     * @return  json(string)
     */

    public function desactivar(Request $request)
    {
        $rules = [
            'id_categoria' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $categoria = CategoriasProductos::find($request->id_categoria);
            $categoria->estado = 0;
            $categoria->u_modificacion = Auth::user()->usuario;
            $categoria->save();

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
     * Activa Categoria
     *
     * @access  public
     * @param
     * @return  json(string)
     */
    public function activar(Request $request)
    {
        $rules = [
            'id_categoria' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);
        if (!$validator->fails()) {
            $categoria = CategoriasProductos::find($request->id_categoria);
            $categoria->estado = 1;
            $categoria->u_modificacion = Auth::user()->usuario;
            $categoria->save();

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
            //$input = 'C:/xampp/htdocs/resources/reports/ReporteCategoriaProduto';
            //$output = 'C:/xampp/htdocs/resources/reports/' .$hora_actual . 'ReporteCategoriaProduto';
            $input = '/var/www/html/resources/reports/ReporteCategoriaProduto';
            $output = '/var/www/html/resources/reports/'.$hora_actual.'ReporteCategoriaProduto';

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

            return response()->download($output . '.' . $ext ,$hora_actual. 'ReporteCategoriaProduto.' . $ext, $headers);

            /*exec($jasper->process($input,$output,$options)->output().' 2>&1', $output);
               print_r($output);*/
        }
    }
}
