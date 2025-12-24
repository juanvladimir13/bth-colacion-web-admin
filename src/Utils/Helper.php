<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Utils;

class Helper
{
    public static function estadisticasMateriaTrimestre(array $estudiantes): array
    {
        $estadisticas['reprobados'] = count(array_filter($estudiantes, function ($item) {
            return $item['promedio' . Master::TRIMESTRE] < 51;
        }));

        $estadisticas['destacados'] = count(array_filter($estudiantes, function ($item) {
            return $item['promedio' . Master::TRIMESTRE] > 93;
        }));

        $estadisticas['aprobados'] = count($estudiantes) - $estadisticas['reprobados'];

        return $estadisticas;
    }

    public static function estadisticasCentralizadorTrimestre(array $estudiantes): array
    {
        $estadisticas['reprobados'] = count(array_filter($estudiantes, function ($item) {
            return $item['promedio'] < 51;
        }));

        $estadisticas['destacados'] = count(array_filter($estudiantes, function ($item) {
            return $item['promedio'] > 93;
        }));

        $estadisticas['aprobados'] = count($estudiantes) - $estadisticas['reprobados'];

        return $estadisticas;
    }
}
