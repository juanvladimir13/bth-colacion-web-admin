<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Core\Http\Controllers;

use BTH\Core\Http\Models\MUsuario;
use BTH\Core\Http\Views\VUsuario;

class CUsuario
{
    private MUsuario $model;
    private VUsuario $view;

    public function __construct()
    {
        $this->model = new MUsuario();
        $this->view = new VUsuario();
    }

    public function form(): void
    {
        $personaId = CSession::getInstance()->getPersonaId();
        $rol = CSession::getInstance()->getRol();
        $usuario = $this->model->findPersonaIdAndRol($personaId, $rol);
        $this->view->showForm($usuario);
    }

    public function update(array $request): void
    {
        $personaId = CSession::getInstance()->getPersonaId();
        if ($request['persona_id'] != $personaId) {
            $request['error'] = 'No tiene permiso para modificar este usuario';
            $this->view->showForm($request);
            return;
        }

        $this->model->setRequest($request);
        if ($this->model->getContraseniaNueva() != '') {
            if (!$this->model->isPasswordValid()) {
                $request['error'] = 'Datos no validos o credenciales incorrectas';
                $this->view->showForm($request);
                return;
            }
        }

        $usuario = $this->model->save();
        if ($usuario) {
            CLogin::logout();
            return;
        }
        $request['error'] = 'Datos no validos';
        $this->view->showForm($request);
    }
}
