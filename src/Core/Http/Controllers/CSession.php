<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Core\Http\Controllers;

class CSession
{
    private static $session;

    private function __construct()
    {
    }

    public static function getInstance(): CSession
    {
        if (!self::$session instanceof self) {
            self::$session = new self();
        }

        return self::$session;
    }

    public function start(): void
    {
        session_start();
    }

    public function setDocente(int $persona_id, string $usuario, string $rol, string $nombreCompleto, array $cursos = [], array $materias = []): void
    {
        $_SESSION['persona_id'] = $persona_id;
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $rol;
        $_SESSION['nombre_completo'] = $nombreCompleto;
        $_SESSION['cursos'] = $cursos;
        $_SESSION['materias'] = $materias;
    }

    public function setDirector(int $persona_id, string $usuario, string $rol, string $nombreCompleto, int $unidad_educativa_id, string $unidad_educativa): void
    {
        $_SESSION['persona_id'] = $persona_id;
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $rol;
        $_SESSION['nombre_completo'] = $nombreCompleto;
        $_SESSION['unidad_educativa_id'] = $unidad_educativa_id;
        $_SESSION['unidad_educativa'] = $unidad_educativa;
    }

    public function getUsuario(): string
    {
        return $_SESSION['usuario'] ?? '';
    }

    public function getPersonaId(): int
    {
        return $_SESSION['persona_id'] ?? 0;
    }

    public function getCursos(): array
    {
        return $_SESSION['cursos'] ?? [];
    }

    public function getMaterias(): array
    {
        return $_SESSION['materias'] ?? [];
    }

    public function getNombreCompleto(): string
    {
        return $_SESSION['nombre_completo'] ?? '';
    }

    public function getRol(): string
    {
        return $_SESSION['rol'] ?? '';
    }

    public function getUnidadEducativaId(): int
    {
        return $_SESSION['unidad_educativa_id'] ?? 0;
    }

    public function getUnidadEducativa(): string
    {
        return $_SESSION['unidad_educativa'] ?? '';
    }

    public function getCursoEspecialidad($cursoEspecialidadId): string
    {
        $cursos = self::getInstance()->getCursos();
        foreach ($cursos as $curso) {
            if ($curso['curso_especialidad_id'] == $cursoEspecialidadId) {
                return $curso['curso'] . ' ' . $curso['paralelo'];
            }
        }
        return '';
    }

    public function getCursoEspecialidadId($cursoEspecialidadId): array
    {
        $cursos = self::getInstance()->getCursos();
        foreach ($cursos as $curso) {
            if ($curso['curso_especialidad_id'] == $cursoEspecialidadId) {
                return $curso;
            }
        }
        return [];
    }

    public function getCursoMateria($materiaCursoEspecialidadId): string
    {
        $materias = self::getInstance()->getMaterias();
        foreach ($materias as $materia) {
            if ($materia['materia_curso_especialidad_id'] == $materiaCursoEspecialidadId) {
                return $materia['curso'] . ' ' . $materia['paralelo'] . ' - ' . $materia['materia'];
            }
        }
        return '';
    }

    public function getCursoMateriaId($materiaCursoEspecialidadId): array
    {
        $materias = self::getInstance()->getMaterias();
        foreach ($materias as $materia) {
            if ($materia['materia_curso_especialidad_id'] == $materiaCursoEspecialidadId) {
                return $materia;
            }
        }
        return [];
    }

    public function isAdmin(): bool
    {
        return $this->getRol() == 'Admin';
    }

    public function isDocenteValid(int $docente_id): bool
    {
        return $this->getPersonaId() == $docente_id;
    }

    public function isDirectorValid($director_id, $unidad_educativa_id): bool
    {
        return $this->getUnidadEducativaId() == $unidad_educativa_id && $this->getPersonaId() == $director_id;
    }

    public function closeSession(): void
    {
        session_unset();
        session_destroy();
    }
}
