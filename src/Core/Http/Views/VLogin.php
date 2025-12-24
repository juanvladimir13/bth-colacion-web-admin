<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Core\Http\Views;

class VLogin
{
    public function showLogin(): void
    {
        $title = 'Sistema académico | BTH San Julián';
        $content = '../templates/login/login.html';
        include '../templates/layout/base-login.html';
    }
}
