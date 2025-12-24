<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Administrativo\Http\Models;

use BTH\Core\Http\Models\DataType;
use BTH\Core\Http\Models\Model;
use BTH\Utils\Master;
use PGDatabase\Postgres;

class MDocente extends Model
{
    protected string $TABLE_NAME = 'administrativo.docente';
    protected string $ORDER_BY_COLUMNS = 'nombres, apellidos';
    protected bool $EDITABLE = true;
    protected bool $SOFT_DELETE = true;

    private string $item;
    private string $carnet;
    private string $prefijoFormacion;
    private string $nombres;
    private string $apellidos;

    private string $fechaNacimiento;
    private string $genero;
    private string $celular;
    private string $correo;
    private string $direccion;
    private int $especialidadId;

    public function __construct()
    {
        $this->id = 0;
        $this->item = '';
        $this->carnet = '';
        $this->prefijoFormacion = '';
        $this->nombres = '';
        $this->apellidos = '';
        $this->fechaNacimiento = '';
        $this->genero = '';
        $this->celular = '';
        $this->correo = '';
        $this->direccion = '';
        $this->especialidadId = 0;
    }

    public function setRequest(array $request): void
    {
        $attributes = [
            'id' => ['datatype' => DataType::INT, 'default' => 0],
            'item' => ['datatype' => DataType::STRING, 'default' => ''],
            'carnet' => ['datatype' => DataType::STRING, 'default' => ''],
            'prefijo_formacion' => ['datatype' => DataType::STRING_UPPER, 'default' => 'LIC.'],
            'nombres' => ['datatype' => DataType::UTF8_UPPER, 'default' => ''],
            'apellidos' => ['datatype' => DataType::UTF8_UPPER, 'default' => ''],
            'fecha_nacimiento' => ['datatype' => DataType::STRING, 'default' => ''],
            'genero' => ['datatype' => DataType::STRING_UPPER, 'default' => 'M'],
            'celular' => ['datatype' => DataType::STRING, 'default' => ''],
            'correo' => ['datatype' => DataType::STRING, 'default' => ''],
            'direccion' => ['datatype' => DataType::UTF8_UPPER, 'default' => ''],
            'especialidad_id' => ['datatype' => DataType::INT, 'default' => 0]
        ];

        list ('id' => $this->id,
            'item' => $this->item,
            'carnet' => $this->carnet,
            'prefijo_formacion' => $this->prefijoFormacion,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'fecha_nacimiento' => $this->fechaNacimiento,
            'genero' => $this->genero,
            'celular' => $this->celular,
            'correo' => $this->correo,
            'direccion' => $this->direccion,
            'especialidad_id' => $this->especialidadId
            ) = Model::extractDataValues($attributes, $request);
    }

    public function findCredentials(string $email, string $password): array
    {
        $passw = explode('.', $password);
        $item = $passw[0] ?? '';
        $carnet = $passw[1] ?? '';
        $query = sprintf('SELECT * FROM %s WHERE correo=$1 and item=$2 and carnet=$3 and soft_delete is false;', $this->TABLE_NAME);
        $rows = Postgres::fetchAllParams($query, [$email, $item, $carnet]);
        return $rows ? $rows[0] : ['error' => Postgres::getError()];
    }

    public function getData(): array
    {
        return [
            'item' => $this->item,
            'carnet' => $this->carnet,
            'prefijo_formacion' => $this->prefijoFormacion,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'fecha_nacimiento' => $this->fechaNacimiento,
            'genero' => $this->genero,
            'celular' => $this->celular,
            'correo' => $this->correo,
            'direccion' => $this->direccion,
            'especialidad_id' => $this->especialidadId,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function cantidadEstudianteSinNotaPorMateria(): array
    {
        $query = 'select count(bcme.materia_curso_especialidad_docente_id) as cant,';
        $query .= 'bcme.materia_curso_especialidad_docente_id, d.apellidos, d.nombres, c.grado, c.paralelo, e.nombre ';
        $query .= 'from registro.boletin_centralizador_materia_estudiante bcme inner join ';
        $query .= 'estudiante.matricula ma on bcme.matricula_id = ma.id inner join ';
        $query .= 'mallacurricular.materia_curso_especialidad_docente mced on ';
        $query .= 'bcme.materia_curso_especialidad_docente_id = mced.id inner join ';
        $query .= 'mallacurricular.materia_curso_especialidad mce on ';
        $query .= 'mced.materia_curso_especialidad_id = mce.id inner join administrativo.curso_especialidad ce on ';
        $query .= 'mce.curso_especialidad_id = ce.id inner join administrativo.curso c on ce.curso_id = c.id inner join ';
        $query .= 'administrativo.especialidad e on ce.especialidad_id = e.id inner join ';
        $query .= 'mallacurricular.materia m on mce.materia_id = m.id inner join ';
        $query .= 'administrativo.docente d on mced.docente_id = d.id ';
        $query .= 'where ma.gestion = $1 and ';
        if (Master::TRIMESTRE == 1) {
            $query .= 'bcm.promedio1 < 31 ';
        } elseif (Master::TRIMESTRE == 2) {
            $query .= 'bcm.promedio2 < 31 ';
        } else {
            $query .= 'bcm.promedio3 < 31 ';
        }
        $query .= 'and d.soft_delete is false group by bcme.materia_curso_especialidad_docente_id, d.apellidos, d.nombres, c.grado, c.paralelo, e.nombre ';
        $query .= 'order by bcme.materia_curso_especialidad_docente_id;';

        return Postgres::fetchAllParams($query, [date('Y')]);
    }
}
