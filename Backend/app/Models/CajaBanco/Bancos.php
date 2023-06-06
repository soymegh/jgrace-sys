<?php

namespace App\Models\CajaBanco;

use App\Models\Admon\UsuariosEmpresas;
use DB, Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bancos extends Model
{

    protected $table = 'cjabnco.bancos';
    protected $primaryKey='id_banco';
    protected $fillable = ['descripcion','u_creacion','u_modificacion','estado','id_empresa'];
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

    /**
     * Obtener Listado de Bancos
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */

    public function obtenerBancos($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $bancos = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $bancos->where($searchField, 'ilike', '%' . $searchValue . '%');
            $bancos->where('id_empresa',$usuario_empresa->id_empresa);
            if($statusValue == 0){
                $bancos->where('estado',1);
                $bancos->where('id_empresa',$usuario_empresa->id_empresa);
            }
            $bancos->orderBy('cjabnco.bancos.id_banco', 'asc');
        }
        return $bancos->paginate($request->limit);
    }

    public function cuentasBancariasBanco()
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        return $this->hasMany('App\Models\Contabilidad\CuentasBancarias','id_banco')
            ->select('id_cuenta_bancaria','id_banco','id_moneda','id_cuenta_contable',
                DB::raw("concat((select b.descripcion from cjabnco.bancos b where b.id_banco = contabilidad.cuentas_bancarias.id_banco),' ',(select moned.descripcion
                from cjabnco.monedas moned where moned.id_moneda = contabilidad.cuentas_bancarias.id_moneda),'(',(select moned.codigo
                from cjabnco.monedas moned where moned.id_moneda = contabilidad.cuentas_bancarias.id_moneda),') ',numero_cuenta) as numero_cuenta")
            )->where('estado',1)->where('id_empresa',$usuario_empresa->id_empresa);
    }


}
