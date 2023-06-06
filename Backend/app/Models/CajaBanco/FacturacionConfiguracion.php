<?php

namespace App\Models\CajaBanco;

use App\Models\Admon\UsuariosEmpresas;
use DB, Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Contabilidad\CuentasContablesVista;

class FacturacionConfiguracion extends Model
{

    public $timestamps = false;
    protected $table = 'cjabnco.configuracion_comprobante_factura';
    protected $primaryKey = 'id_configuracion_factura';

    public function obtener($request)
    {
        $importaciones = $this->select(['*']);
        $importaciones->with('configuracionFacturacuentaContable');
        $importaciones->orderBy('debe_haber', 'asc');
        $importaciones->orderBy('id_configuracion_factura', 'asc');
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $importaciones->where('id_empresa', $usuario_empresa->id_empresa);
        $importaciones->where('estado',1); //Estado 1 -> Activo
        $importaciones->where('id_tipo_configuracion', $request->id_tipo_configuracion);
        return $importaciones->paginate($request->limit);
    }

    public function configuracionFacturacuentaContable()
    {
        return $this->belongsTo(CuentasContablesVista::class, 'id_cuenta_contable')->select('id_cuenta_contable', 'id_tipo_cuenta', 'cta_contable', 'nombre_cuenta', 'nombre_cuenta_completo');
    }

}
