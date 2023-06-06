<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Areas extends Model
{
    protected $table = 'public.areas';
    protected $primaryKey='id_area';
    protected $fillable = ['codigo','descripcion','estado'];
    protected $dateFormat = 'Y-m-d H:i:s.u';
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';

    public function buscar($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $sucusales = $this->select(['id_area',DB::raw("descripcion AS text")]);
        $sucusales->where('estado', 1);
        $sucusales->where('id_empresa',$usuario_empresa->id_empresa);

        if (!empty($request->q)) {
            $searchValue = $request->q;
            $sucusales->where('descripcion', 'ILIKE', '%' . $searchValue . '%');
            $sucusales->where('id_empresa',$usuario_empresa->id_empresa);
        }
        $sucusales->orderBy('id_area', 'asc');
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
     * Obtener Lista de cuentasBancarias
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */



    public function obtener($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $areas = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $areas->where($searchField, 'ilike', '%' . $searchValue . '%');
            $areas->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue === 0){
                $areas->where('estado',1);
                $areas->where('id_empresa',$usuario_empresa->id_empresa);
            }
        }
        $areas->with('cuentaContableArea');

        $areas->with(['direccionArea' => function($query) {
            $query->with('direccionSucursal');
        }]);

        $areas->orderby('codigo');
        return $areas->paginate($request->limit);
    }


    public function cuentaContableArea()
    {
        return $this->belongsTo('App\Models\Contabilidad\CuentasContablesVista','id_cuenta_contable');
    }

    public function direccionArea()
    {
        return $this->belongsTo('App\Models\Admon\Direcciones','id_direccion');
    }

    public function trabajadoresArea()
    {
        return $this->hasMany('App\Models\RRHHTrabajadores','id_area')->select('id_trabajador','id_area','primer_apellido','primer_nombre','segundo_apellido','segundo_nombre','codigo',DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ',segundo_apellido) AS nombre_completo"));
    }
}



