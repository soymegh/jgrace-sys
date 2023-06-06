<?php

namespace App\Models\Contabilidad;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class Anexos extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.anexos';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_anexo';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['descripcion','id_estado_financiero','posicion_anexo','u_creacion','u_modificacion','estado','id_empresa'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     *
     */
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
     * Obtener lista de anexos segun el id empresa del usurio
     *
     * @access 	public
     * @param
     * @return 	json(array)
     */
    public function obtenerAnexos($request)
    {
        $anexos = $this->select(['*']);
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
            $anexos->where($searchField, 'ilike', '%' . $searchValue . '%');
            //$menus->where('admon.menus.activo',1);
            $anexos -> where('id_empresa',$usuario_empresa->id_empresa);
        }
        $anexos->with('anexosEstadoFinanciero');
        $anexos->orderBy('contabilidad.anexos.id_anexo');
        return $anexos->paginate($request->limit);
    }

    /**
     * Relación Anexo - Empresa
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }


    /**
     * Relación Anexo - Estado Financiero
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function anexosEstadoFinanciero()
    {
        return $this->belongsTo('App\Models\ContabilidadEstadosFinancieros','id_estado_financiero');
    }

}





