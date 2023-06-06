<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Impuestos extends Model
{
    use HasFactory;
    const CREATED_AT = 'f_grabacion';
    const UPDATED_AT = 'f_modificacion';
    protected $table = 'public.impuestos';
    protected $primaryKey='id_impuesto';
    protected $fillable = ['descripcion','tasa','estado','u_grabacion','u_modifcacion','id_empresa'];
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
     * Obtener Listado de Paises
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerImpuestos($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $impuesto = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $impuesto->where($searchField, 'ilike', '%' . $searchValue . '%');
            $impuesto->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue == 0){
                $impuesto->where('estado',true);
                $impuesto->where('id_empresa',$usuario_empresa->id_empresa);
            }
            $impuesto->orderBy('id_impuesto', 'asc');
        }
        return $impuesto->paginate($request->limit);
    }
}



