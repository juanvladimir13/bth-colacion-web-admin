<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Core\Http\Models;

use PGDatabase\Postgres;

class MUsuario extends Model
{
    protected string $TABLE_NAME = 'usuario.usuario';
    protected string $ORDER_BY_COLUMNS = 'nombre, rol';
    protected bool $EDITABLE = true;
    protected bool $SOFT_DELETE = true;

    private string $nombre;
    private string $contrasenia;
    private string $contraseniaNueva;
    private string $contraseniaConfirmada;
    private string $rol;
    private string $nombreTemp;
    private string $contraseniaTemp;
    private string $token;
    private int $personaId;

    public function __construct()
    {
        $this->id = 0;
        $this->nombre = '';
        $this->contrasenia = '';
        $this->contraseniaNueva = '';
        $this->contraseniaConfirmada = '';
        $this->rol = '';
        $this->nombreTemp = '';
        $this->contraseniaTemp = '';
        $this->token = '';
        $this->personaId = 0;
    }

    public function setRequest(array $request): void
    {
        $attributes = [
            'id' => ['datatype' => 'int', 'default' => 0],
            'nombre' => ['datatype' => 'utf-8', 'default' => ''],
            'contrasenia' => ['datatype' => 'utf-8', 'default' => ''],
            'contrasenia-nueva' => ['datatype' => 'utf-8', 'default' => ''],
            'contrasenia-confirmada' => ['datatype' => 'utf-8', 'default' => ''],
        ];

        list('id' => $this->id,
            'nombre' => $this->nombre,
            'contrasenia' => $this->contrasenia,
            'contrasenia-nueva' => $this->contraseniaNueva,
            'contrasenia-confirmada' => $this->contraseniaConfirmada
            ) = Model::extractDataValues($attributes, $request);
    }

    public function getData(): array
    {
        $data = [
            'nombre' => $this->nombre,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->contraseniaNueva != '') {
            $data['contrasenia'] = self::passwordHash($this->contraseniaNueva);
        }
        return $data;
    }

    public function isPasswordValid(): bool
    {
        $model = $this->find($this->id);
        $passwordHash = $model['contrasenia'] ?? '';
        $dataPasswordValid = strlen($this->contraseniaNueva) > 0 &&
            strlen($this->contraseniaConfirmada) > 0 &&
            strlen($this->contrasenia) > 0;
        $passwordValid = ($this->contraseniaNueva == $this->contraseniaConfirmada) && self::passwordVerify($this->contrasenia, $passwordHash);
        return $dataPasswordValid && $passwordValid;
    }

    public function getUserByEmailAndRol(string $email, string $rol): array
    {
        $query = sprintf('SELECT * FROM %s WHERE nombre=$1 and rol=$2 and editable is true and soft_delete is false;', $this->TABLE_NAME);
        $rows = Postgres::fetchAllParams($query, [$email, $rol]);
        return $rows ? $rows[0] : [];
    }

    public function findPersonaIdAndRol(int $personaId, string $rol): array
    {
        $query = sprintf('SELECT * FROM %s WHERE persona_id=$1 and rol=$2 and soft_delete is false;', $this->TABLE_NAME);
        $rows = Postgres::fetchAllParams($query, [$personaId, $rol]);
        return $rows ? $rows[0] : [];
    }

    public static function passwordHash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 13]);
    }

    public static function passwordVerify(string $passwordPlain, string $passwordHash): bool
    {
        return \password_verify($passwordPlain, $passwordHash);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getContrasenia(): string
    {
        return $this->contrasenia;
    }

    public function getRol(): string
    {
        return $this->rol;
    }

    public function getNombreTemp(): string
    {
        return $this->nombreTemp;
    }

    public function getContraseniaTemp(): string
    {
        return $this->contraseniaTemp;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getPersonaId(): int
    {
        return $this->personaId;
    }

    public function getContraseniaNueva(): string
    {
        return $this->contraseniaNueva;
    }
}
