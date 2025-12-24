<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Utils;

use Laminas\Permissions\Rbac\Rbac;
use Laminas\Permissions\Rbac\Role;
use Laminas\Permissions\Rbac\RoleInterface;

class AccessControl
{
    private Rbac $rbac;
    private RoleInterface $role;
    private static $session;

    private function __construct()
    {
        $this->rbac = new Rbac();
        $this->rbac->setCreateMissingRoles(true);

        $director = new Role('director');
        $director->addPermission('ue.curso.list');
        $director->addPermission('ue.curso.excel');

        $docente = new Role('docente');
        $docente->addPermission('curso.findAll');
        $docente->addPermission('curso.find');
        $docente->addPermission('curso.estudiante.findAll');
        $docente->addPermission('curso.estudiante.update');

        $docente->addPermission('centralizador.curso.trimestre.findAll');
        $docente->addPermission('centralizador.curso.trimestre.excel');
        $docente->addPermission('centralizador.curso.estudiante.find');
        $docente->addPermission('centralizador.curso.estudiante.update');

        $docente->addPermission('kardex.curso.findAll');
        $docente->addPermission('kardex.curso.estudiante.find');
        $docente->addPermission('kardex.curso.estudiante.update');

        $admin = new Role('admin');
        $admin->addPermission('estudiante.find');
        $admin->addPermission('centralizador.ue.curso.excel');

        $admin->addChild($director);
        $admin->addChild($docente);

        $this->rbac->addRole($director);
        $this->rbac->addRole($docente);
        $this->rbac->addRole($admin);
    }

    public static function getInstance(): AccessControl
    {
        if (!self::$session instanceof self) {
            self::$session = new self();
        }

        return self::$session;
    }

    private function loadRole(string $roleName): void
    {
        $this->role = $this->rbac->getRole($roleName);
    }

    private function hasPermission($router, $url, $permission, $methods = 'GET'): void
    {
        $role = $this->role;

        $router->before($methods, $url, function () use ($role, $permission) {
            if (!$role->hasPermission($permission)) {
                header('HTTP/1.1 404 Not Found');
                header('Location: /page-not-found');
            }
        });
    }

    public function proccess($router, string $roleName): void
    {
        try {
            $this->loadRole($roleName);
        } catch (\Exception $exception) {
            header('HTTP/1.1 404 Not Found');
            header('Location: /page-not-found');
            return;
        }

        $this->hasPermission($router, '/', 'curso.findAll');
        $this->hasPermission($router, '/academico/cursos', 'curso.findAll');

        $this->hasPermission($router, '/academico/curso/(\d+)', 'curso.find');
        $this->hasPermission($router, '/academico/curso/(\d+)/estudiante/(\d+)', 'curso.estudiante.findAll');
        $this->hasPermission($router, '/academico/estudiante', 'curso.estudiante.update', 'POST');

        $this->hasPermission($router, '/academico/centralizador/curso/(\d+)/trimestre/(\d+)', 'centralizador.curso.trimestre.findAll');
        $this->hasPermission($router, '/academico/centralizador/curso/(\d+)/trimestre/(\d+)/excel', 'centralizador.curso.trimestre.excel');
        $this->hasPermission($router, '/academico/centralizador/(\d+)/curso/(\d+)/estudiante/(\d+)', 'centralizador.curso.estudiante.find');
        $this->hasPermission($router, '/academico/centralizador', 'centralizador.curso.estudiante.update', 'POST');

        $this->hasPermission($router, '/academico/kardex/curso/(\d+)', 'kardex.curso.findAll');
        $this->hasPermission($router, '/academico/kardex/(\d+)/curso/(\d+)/estudiante/(\d+)', 'kardex.curso.estudiante.find');
        $this->hasPermission($router, '/academico/kardex', 'kardex.curso.estudiante.update', 'POST');
    }
}
