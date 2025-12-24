<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Core\Http\Controllers;

use BTH\Administrativo\Http\Models\MDocente;
use BTH\Core\Http\Models\MUsuario;
use BTH\Core\Http\Views\VLogin;

class CLogin
{

    public static function form(): void
    {
        $view = new VLogin();
        $view->showLogin();
    }

    public static function authenticate(array $request): void
    {
        $redirectUrl = '/login';
        $inputEmail = $request['email'] ?? '';
        $inputPassword = $request['password'] ?? '';
        $inputRol = $request['rol'] ?? '';

        $modelUsuario = new MUsuario();
        $usuario = $modelUsuario->getUserByEmailAndRol($inputEmail, $inputRol);
        $rol = $usuario ? $usuario['rol'] : '';
        $personaId = $usuario ? (int)$usuario['persona_id'] : 0;
        $password = $usuario ? $usuario['contrasenia'] : '';

        if ($inputRol != $rol || !MUsuario::passwordVerify($inputPassword, $password)) {
            header('Location: ' . $redirectUrl);
            return;
        }

        if ($rol == 'Docente' || $rol == 'Admin') {
            $modelDocente = new MDocente();
            $docente = $modelDocente->find($personaId);

            if (!$docente) {
                header('Location: ' . $redirectUrl);
                return;
            }

            $redirectUrl = '/';
            $correo = $docente['correo'];
            $nombreCompleto = $docente['nombres'] . ' ' . $docente['apellidos'];

            CSession::getInstance()->setDocente($personaId, $correo, $rol, $nombreCompleto);
        }

        header('Location: ' . $redirectUrl);
    }

    public static function logout(): void
    {
        CSession::getInstance()->closeSession();
        header('Location: /login');
    }
}
