<?php

namespace App\Models\Inventario;

use  Illuminate\Database\Eloquent\Model;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;
use App\Models\Inventario\BodegaProductos;

class Bodegas extends Model
{
    use HasFactory;
    protected $table = 'inventario.bodegas';
    protected $primaryKey='id_bodega';
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $fillable = ['unidad_medida','u_grabacion','u_modificacion','estado','permite_venta'];

    public function buscar($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $sucusales = $this->select(['id_bodega',DB::raw("descripcion AS text")]);
        $sucusales->where('estado', 1);
        $sucusales->where('id_empresa',$usuario_empresa->id_empresa);

        if (!empty($request->q)) {
            $searchValue = $request->q;
            $sucusales->where('descripcion', 'ILIKE', '%' . $searchValue . '%');
        }
        $sucusales->orderBy('id_bodega', 'asc');
        return $sucusales->limit(6)->get();
    }

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
     * Get List of Groups
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtener($request)
    {
        $bodegas = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $bodegas->where($searchField, 'ilike', '%' . $searchValue . '%');
            $bodegas->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue == 0){
                $bodegas->where('estado',1);
                $bodegas->where('id_empresa',$usuario_empresa->id_empresa);
            }
            $bodegas->orderBy('id_bodega', 'asc');
        }
        $bodegas->with('sucursalBodega');
        $bodegas->with('tipoBodega');
        return $bodegas->paginate($request->limit);
    }

    public function centroCostoBodega()
    {
        return $this->belongsTo('App\Models\Contabilidad\CentroCostoIngreso','id_centro')->select('id_centro','descripcion');
    }

    public function sucursalBodega()
    {
        return $this->belongsTo('App\Models\Admon\Sucursales','id_sucursal')->select(['id_sucursal','descripcion']);
    }

    public function tipoBodega()
    {
        return $this->belongsTo('App\Models\Inventario\TipoBodega','id_tipo_bodega')->select(['id_tipo_bodega','descripcion']);
    }

    /*public function productosBodega()
    {
        return $this->hasMany('App\Models\InventarioBodegas', 'inventario.bodegas_productos', 'id_bodega', 'id_producto')->using('App\Models\InventarioBodegaProductos');
    }*/

    public function productosBodega()
    {
        return $this->hasMany(BodegaProductos::class,'id_bodega')->where('cantidad', '>', 0);
    }

}
