<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class DocumentosCuentas extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.documentos_cuentas';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_documento_cuenta';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['id_cuenta_contable','cta_contable','cta_contable_padre','id_documento','id_centro','debe','haber','concepto','debe_org','haber_org','id_moneda','estado','id_empresa'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     *
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';


    /**
     * Relación Documentos contables - Empresa
     * @return BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }

    /**
     * Relación Documentos Cuentas - Cuenta contable vista
     * @return BelongsTo
     */
    public function cuentaContable()
    {
        return $this->belongsTo('App\Models\Contabilidad\CuentasContablesVista','id_cuenta_contable');
    }

    /**
     * Relación Documentos Cuentas - Centro de costo ingreso
     * @return BelongsTo
     */
    public function centroCosto()
    {
        return $this->belongsTo('App\Models\Contabilidad\CentrosCostosIngresos','id_centro')->select('id_centro','codigo','descripcion');
    }
}





