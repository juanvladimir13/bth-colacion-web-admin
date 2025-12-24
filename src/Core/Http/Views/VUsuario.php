<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Core\Http\Views;

class VUsuario
{

    public function showForm(array $usuario): void
    {
        $title = 'Datos de usuario';
        $content = '../templates/usuario/form.html';
        include '../templates/layout/base.html';
    }
}
