<?php

/**
 * @author juanvladimir13 <juanvladimir13@gmail.com>
 * @see https://github.com/juanvladimir13
 */

require '../vendor/autoload.php';

use Bramus\Router\Router;
use BTH\Core\Api\Request;
use BTH\Core\Http\Controllers\{CLogin, CSession, CUsuario};

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, X-Requested-With, Accept");
header("Access-Control-Allow-Credentials: true");

const LOCUTORES = [
    'juanmartinezmamani@gmail.com',
    'festeban.suarez0903@gmail.com',
    'villarpandodiazdorcas@gmail.com',
    'eleuteriarochaleon932@gmail.com',
    'juanvladimir13@gmail.com',
];

const CONTROL = [
    'vargaspablod86@gmail.com',
    'juanvladimir13@gmail.com',
    'tarifaesther222@gmail.com',
    'calizayaamy2@gmail.com'
];

CSession::getInstance()->start();
if (array_key_exists('usuario', $_SESSION)) {
    $router = new Router();
    $router->get('/', function () {
        include '../templates/inicio.html';
    });

    $router->get('/plantel', function () {
        include '../templates/plantel/index.html';
    });

    $router->get('/programa', function () {
        include '../templates/programa/index.html';
    });

    $router->mount('/locutor', function () use ($router) {
        if (in_array(CSession::getInstance()->getUsuario(), LOCUTORES, true)) {
            $router->get('/', function () {
                include '../templates/locutor/index.html';
            });

            $router->get('/(\d+)', function ($id) {
                include "../templates/locutor/$id/index.html";
            });
        }
    });

    $router->mount('/control', function () use ($router) {
        if (in_array(CSession::getInstance()->getUsuario(), CONTROL, true)) {
            $router->get('/', function () {
                include '../templates/control/index.html';
            });

            $router->get('/(\d+)', function ($id) {
                include "../templates/control/$id/index.html";
            });
        }
    });

    $router->mount('/usuario', function () use ($router) {
        $router->get('/', '\BTH\Core\Http\Controllers\CUsuario@form');
        $router->post('/', function () {
            $controller = new CUsuario();
            $controller->update([...$_POST]);
        });
    });

    $router->post('/logout', function () {
        CLogin::logout();
    });

    $router->set404(function () {
        include '../templates/plantel/index.html';
    });
    $router->run();
} else {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

    if ($_SERVER['REQUEST_METHOD'] == 'GET' && ($requestUri == '/login' || $requestUri == '/')) {
        CLogin::form();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $requestUri == '/login') {
        CLogin::authenticate([...$_POST]);
    }

    $api_urls = ['/api/contacto/store', '/api/contacto', '/api/estudiante', '/api/centralizador', '/api/centralizador/materia', '/login', '/'];
    if (!in_array($requestUri, $api_urls)) {
        header('Location: /login');
    }
}
