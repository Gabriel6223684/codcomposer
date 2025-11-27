<?php

use app\controller\User;
use app\controller\Home;
use app\controller\Cliente;
use Slim\Routing\RouteCollectorProxy;
use app\controller\Fornecedor;

$app->get('/', Home::class . ':home');

$app->get('/home', Home::class . ':home');

$app->group('/usuario', function (RouteCollectorProxy $group) {
    $group->get('/lista', User::class . ':lista');
    $group->get('/cadastro', User::class . ':cadastro');
    $group->post('/listuser', User::class . ':listuser');
    $group->post('/insert', User::class . ':insert');
});
$app->group('/cliente', function (RouteCollectorProxy $group) {
    $group->get('/lista', Cliente::class . ':lista');
    $group->get('/cadastro', Cliente::class . ':cadastro');
    $group->post('/listacliente', Cliente::class . ':listacliente');
    $group->post('/insert', Cliente::class . ':insert');
});

$app->post('/usuario/editar', \app\controller\User::class . ':editar');
$app->delete('/usuario/excluir/{id}', \app\controller\User::class . ':excluir');

$app->post('/cliente/editar', \app\controller\Cliente::class . ':editar');
$app->delete('/cliente/excluir/{id}', \app\controller\Cliente::class . ':excluir');

$app->post('/fornecedor/editar', \app\controller\Fornecedor::class . ':editar');
$app->delete('/fornecedor/excluir/{id}', \app\controller\Fornecedor::class . ':excluir');

$app->group('/fornecedor', function (RouteCollectorProxy $group) {
    $group->get('/lista', Fornecedor::class . ':lista');
    $group->get('/cadastro', Fornecedor::class . ':cadastro');
    $group->post('/listafornecedor', Fornecedor::class . ':listafornecedor');
    $group->post('/insert', Fornecedor::class . ':insert');
});