<?php

namespace App\Models\Inventario;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;
use App\Models\Contabilidad\CentrosCostosIngresos;
use App\Models\Contabilidad\CuentasContablesVista;

class ConfiguracionInventario extends Model {

    use HasFactory;

    protected $table = 'inventario.configuracion_comprobante';
    protected $primaryKey='id_configuracion_comprobante';
    protected $fillable = ['debe_haber','descripcion_movimiento','u_modificacion','id_empresa','estado','id_cuenta_contable','nombre_seccion','id_tipo_configuracion','id_centro_costo','id_cat_auxiliar_cxc','codigo_centro_costo','codigo_centro_costo','codigo_auxiliar'];
    const UPDATED_AT = 'f_modificacion';

    public function obtener($request)
    {
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        $importaciones = $this->select(['*']);
        $importaciones->with('configuracionImportacioncuentaContable','configuracionCentroCosto'); //,'configuracionAuxiliares'
        $importaciones->orderBy('debe_haber', 'asc');
        $importaciones->orderBy('id_configuracion_comprobante', 'asc');
        $importaciones->where('id_empresa',$usuario_empresa->id_empresa);
        $importaciones->where('id_tipo_configuracion', $request->id_tipo_configuracion);
        return $importaciones->paginate($request->limit);
    }

    public function configuracionImportacioncuentaContable()
    {
        return $this->belongsTo(CuentasContablesVista::class,'id_cuenta_contaable')->select('id_cuenta_contable','cta_contable','nombre_cuenta','nombre_cuenta_completo','requiere_aux','id_centro_costo','codigo_centro_costo','codigo_auxiliar','id_cat_auxiliar_cxc');
    }

    public function configuracionCentroCosto()
    {
        return $this->belongsTo(CentrosCostosIngresos::class,'id_centro_costo','id_centro')->select('*');
    }

    /*public function configuracionAuxiliares()
    {
        return $this->belongsTo('App\Models\CuentasXCobrar\CatAuxiliar','id_cat_auxiliar_cxc')->select('*');
    }*/
}
