<?php

namespace App\Models\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ajustes extends Model
{
    use HasFactory;

    /**
     * Se establece el schema y tabla de la base de datos
     * estamos apuntando.
     *
     * @var string
     */
    protected $table = 'admon.ajustes';

    /**
     * Aca se define una primaryKey protegida, diferente a la que Eloquent
     * establece por defecto.
     *
     * @var int
     */
    protected $primaryKey = 'id_ajuste';

    /**
     * Estacemos los atributos que pueden asignar en masa
     *
     * @var array<int, string>
     */
    protected $fillable =['identificador','valor','id_empresa'];

    /**
     * Aca definimos el nombre de los campos de tipo timestamp
     *
     * @var array<int, string>
     */
    const CREATED_AT = 'f_creacion';
    const UPDATED_AT = 'f_modificacion';


    protected $hidden = ['uploaded_logo'];
}



