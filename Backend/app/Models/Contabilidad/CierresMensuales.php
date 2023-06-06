<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class CierresMensuales extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.cierres_mensuales';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_cierre_mensual';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['id_periodo','id_cuenta_contable','saldoperiodoanterior','saldo1','saldo2','saldo3','saldo4','saldo5','saldo6','saldo7','saldo8','saldo9','saldo10','saldo11','saldo12','u_creacion','u_modificacion','estado','ubicacion','clasificacion_contable','id_empresa'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     *
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';



    /**
     * Relación Centro de costo - Empresa
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }

    /**
     * Relacion Cierre Mensual - Periodo Fiscal
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function periodoFiscal()
    {
        return $this->belongsTo('App\Models\ContabilidadPeriodoFiscal','id_periodo_fiscal');
    }

}





