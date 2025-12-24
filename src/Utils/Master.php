<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Utils;

class Master
{
    public const TRIMESTRE = 2;

    public const TRIMESTRES = [
        1 => 'PRIMER',
        2 => 'SEGUNDO',
        3 => 'TERCER'
    ];

    public const PARALELOS = [
        'A' => 'A',
        'B' => 'B',
        'C' => 'C',
        'D' => 'D',
        'E' => 'E',
        'F' => 'F',
        'G' => 'G',
    ];

    public const GRADOS = [
        '4' => 'CUARTO',
        '5' => 'QUINTO',
        '6' => 'SEXTO'
    ];

    public const ESPECIALIDADES = [
        1 => 'SISTEMAS INFORMÁTICOS',
        2 => 'CONSTRUCCIÓN CIVIL',
        3 => 'CONTADURÍA GENERAL',
        4 => 'ELECTRÓNICA',
        5 => 'GASTRONOMÍA',
        6 => 'MECÁNICA AUTOMOTRIZ',
        7 => 'TEXTILES Y CONFECCIONES'
    ];

    public const GRADO_ACADEMICO = [
        'TEC.' => 'TECNICO',
        'NORM.' => 'NORMALISTA',
        'LIC.' => 'LICENCIATURA',
        'ING.' => 'INGENIERIA',
        'MSC.' => 'MAESTRIA',
    ];

    public const UNIDADES_EDUCATIVAS = [
        1 => '12 DE ABRIL',
        5 => '27 DE ABRIL',
        6 => 'CNL. CIRO MEALLA',
        7 => 'DARIO MAYSER',
        10 => 'EL PROGRESO',
        11 => 'ELIZARDO PEREZ',
        13 => 'ENRIQUE DE OSSO III',
        17 => 'GREGORIO LOPEZ M.',
        19 => 'GUILLERMO JORDAN',
        20 => 'JOSE DAVID BERRIOS',
        21 => 'JUAN CARLOS BARRIENTOS PEREZ',
        25 => 'PATUJU',
        29 => 'SANTA CLARA',
    ];

    public static function getTrimestreName(int $trimestre): string
    {
        return self::TRIMESTRES[$trimestre] ?? 'PRIMER';
    }

    public static function getGradoName(string $grado): string
    {
        return self::GRADOS[$grado] ?? 'TODOS';
    }

    public static function getEspecialidadName(int $especialidadId): string
    {
        return self::ESPECIALIDADES[$especialidadId] ?? 'SN';
    }

    public static function getUnidadEducativaName(int $unidadEducativaId): string
    {
        return self::UNIDADES_EDUCATIVAS[$unidadEducativaId] ?? 'SN';
    }

    public static function getUnidadesEducativas(): array
    {
        return self::UNIDADES_EDUCATIVAS;
    }
}
