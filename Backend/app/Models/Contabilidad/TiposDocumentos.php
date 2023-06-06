<?php

namespace App\Models\Contabilidad;

use App\Models\Admon\UsuariosEmpresas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\ErrorHandler\ErrorRenderer\addElementToGhost;

class TiposDocumentos extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'contabilidad.tipos_documentos';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_tipo_doc';

    /**
     * Establecemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['descripcion','secuencia','permite_registro_manual','prefijo','u_creacion','u_modificacion','estado','id_empresa'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     *
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';
    protected $dateFormat = 'Y-m-d H:i:s.u';


    /**
     * Get next counter value for the provided key
     *
     * @param  string $key
     * @return string
     */
    public function codigoSiguiente($id_tipo_documento)
    {
        $tipo = $this->select(['*'])->where('id_tipo_doc', $id_tipo_documento)->first();

        if(!$tipo) {
            throw new Exception('No record for counter found');
        }
        return $tipo->prefijo.$tipo->secuencia;
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
     * Obtener Lista de cuenta según el id_empresa
     * @param $request
     * @return mixed
     */
    public function obtener($request)
    {
        $tipos = $this->select(['*']);
        $usuario_empresa = UsuariosEmpresas::where('id_usuario', '=', Auth::user()->id)->first();
        if (!empty($request->search['field'])) {
            $searchField = $request->search['field'];
            $searchValue = $request->search['value'];
            $statusValue = $request->search['status'];
            $tipos->where($searchField, 'ilike', '%' . $searchValue . '%');
            if($statusValue == 0){
                $tipos->where('estado',true);
                $tipos -> where('id_empresa',$usuario_empresa->id_empresa);
            }
        }
        $tipos->orderBy('contabilidad.tipos_documentos.id_tipo_doc');
        return $tipos->paginate($request->limit);
    }



    /**
     * Relación Periodos Fiscal - Empresa
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Empresa(){
        return $this->belongsTo('App\Models\Admon\Empresas','id_empresa');
    }


    /**
     * Relación Tipos de cuentas - Estado finaciero
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipoCuentaEstadoFinanciero()
    {
        return $this->belongsTo('App\Models\Contabilidad\EstadosFinancieros','id_estado_financiero');
    }

}





