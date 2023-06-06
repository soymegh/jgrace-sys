<?php

namespace App\Models\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class Proveedores extends Model {

    use HasFactory;

    protected $table = 'inventario.proveedores';
    protected $primaryKey='id_proveedor';
    protected $fillable = ['nombre_proveedor','direccion','contacto_proveedor','numero_ruc','fecha_ingreso','estado'];
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $dateFormat = 'Y-m-d H:i:s.u';


    /**
     * Replace Field
     *
     * @access 	public
     * @param
     * @return 	string
     */

    public function replaceField($field, $fields = [])
    {
        if (in_array($field, $fields)) {
            return $fields[$field];
        }

        return $field;
    }


    /**
     * Buscar cuentas contables
     *
     * @access  public
     * @param
     * @return  json(array)
     */

    public function buscar($request)
    {

        $proveedores = $this->select(['*',DB::raw("CONCAT(inventario.proveedores.nombre_comercial,' (',inventario.proveedores.numero_ruc,')') AS text")]);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if ((!empty($request->q))) {
            $searchValue = $request->q;
            $proveedores->where('estado', 1);
            $proveedores->where('nombre_comercial', 'ILIKE', '%' . $searchValue . '%')->orWhere('inventario.proveedores.numero_ruc', 'ILIKE', '%' . $searchValue . '%');
            $proveedores->where('id_empresa',$usuario_empresa->id_empresa);
            $proveedores->orderBy('nombre_comercial', 'asc');
            return $proveedores->limit(6)->get();
        }else{
            $proveedores->where('estado', 1);
            $proveedores->where('nombre_comercial',  'ILIKE', '%Controls%');
            $proveedores->where('id_empresa',$usuario_empresa->id_empresa);
            return $proveedores->limit(1)->get();
        }

    }

    /**
     * Obtener Lista de Proveedores
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtener($request)
    {
        $proveedores = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            if($searchField=='id_tipo_proveedor'){
                $proveedores->where($searchField, $searchValue);
                $proveedores->where('id_empresa',$usuario_empresa->id_empresa);
            }else{
                $proveedores->where($searchField, 'ilike', '%' . $searchValue . '%');
                $proveedores->where('id_empresa',$usuario_empresa->id_empresa);
            }
            if($statusValue == 0){
                $proveedores->where('estado',1);
                $proveedores->where('id_empresa',$usuario_empresa->id_empresa);
            }
            $proveedores->with('proveedorTipo');
            $proveedores->orderBy('id_proveedor', 'asc');
        }
        return $proveedores->paginate($request->limit);
    }


    /**
     * Obtener Lista de Proveedores por producto
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerProveedoresProducto($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $proveedores = $this->select(['inventario.proveedor.id_proveedor']);
        $proveedores->leftJoin('inventario.producto_proveedor', 'inventario.producto_proveedor.id_proveedor', '=', 'inventario.proveedor.id_proveedor');
        $proveedores->where('inventario.producto_proveedor.id_producto', $request->id_producto);
        $proveedores->where('id_empresa',$usuario_empresa->id_empresa);
        $proveedores->orderBy('inventario.proveedor.id_proveedor', 'asc');
        return $proveedores->pluck('id_proveedor')->toArray();
    }


    /**
     * Obtener Codigo Proveedor
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerCodigoProveedor($clasificacion_contable)
    {
        $codigo = $this->select([DB::raw("COALESCE(max(secuencia),0)+1 as secuencia")]);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if((!empty($clasificacion_contable))){
            $codigo->where('clasificacion_contable',$clasificacion_contable);
            $codigo->where('id_empresa',$usuario_empresa->id_empresa);
        }
        return $codigo->first();
    }

    public function proveedorTipo()
    {
        return $this->belongsTo('App\Models\Inventario\TipoProveedor','id_tipo_proveedor');
    }

    public function municipioProveedor()
    {
        return $this->belongsTo('App\Models\Admon\Municipios','id_municipio');
    }

    /* public function proveedoresLista()
     {
         return $this->hasMany('App\Models\InventarioProductoProveedor','id_proveedor');
     }*/

}
