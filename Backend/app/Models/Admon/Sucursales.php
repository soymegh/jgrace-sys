<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventario\Bodegas;

class Sucursales extends Model
{
    use HasFactory;

    protected $table = 'public.sucursales';
    protected $primaryKey='id_sucursal';
    protected $fillable = ['descripcion','estado'];
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
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

    public function buscar($request)
    {
        $sucusales = $this->select(['id_sucursal','serie',DB::raw("descripcion AS text")]);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $sucusales->where('estado', 1);
        $sucusales->where('id_empresa',$usuario_empresa->id_empresa);
        $sucusales->with('sucursalBodegas');

        if (!empty($request->q)) {
            $searchValue = $request->q;
            $sucusales->where('descripcion', 'ILIKE', '%' . $searchValue . '%');
            $sucusales->where('id_empresa',$usuario_empresa->id_empresa);
        }
        $sucusales->orderBy('id_sucursal', 'asc');
        return $sucusales->limit(6)->get();
    }

    /**
     * Obtener lista de Roles
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerSucursales($request)
    {
        $roles = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $roles->where($searchField, 'ilike', '%' . $searchValue . '%');
            $roles->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue === 0){
                $roles->where('estado',1);
                $roles->where('id_empresa',$usuario_empresa->id_empresa);
            }
            $roles->orderBy('public.sucursales.id_sucursal', 'asc');
        }
        return $roles->paginate($request->limit);
    }

    public function sucursalBodegas()
    {
        return $this->hasMany(Bodegas::class,'id_sucursal')->select(['id_bodega','descripcion','permite_venta','estado', 'id_sucursal'])->where('estado', 1)->where('permite_venta', 1);
    }

    public function sucursalBodegasTodas()
    {
        return $this->hasMany(Bodegas::class,'id_sucursal')->select(['id_bodega','descripcion','permite_venta','estado', 'id_sucursal','id_tipo_bodega'])->where('estado', 1);
    }

    public function sucursalBodegaVentas()
    {
        return $this->hasMany(Bodegas::class,'id_sucursal')
            ->select(['id_bodega','descripcion','permite_venta','estado', 'id_sucursal','id_tipo_bodega'])
            ->where('estado', 1)
            ->whereIn('id_tipo_bodega', array(1,2))
            ->where('permite_venta', 1);
    }

    public function sucursalBodegaAjustes()
    {
        return $this->hasMany(Bodegas::class,'id_sucursal')
            ->select(['id_bodega','descripcion','permite_venta','estado', 'id_sucursal','id_tipo_bodega'])
            ->where('estado', 1)
            ->whereIn('id_tipo_bodega', array(4));
    }

    public function sucursalBodegaUsado()
    {
        return $this->hasMany(Bodegas::class,'id_sucursal')
            ->select(['id_bodega','descripcion','permite_venta','estado', 'id_sucursal','id_tipo_bodega'])
            ->where('estado', 1)
            ->where('id_tipo_bodega', 3)
            ->where('permite_venta', 1);
    }


}





