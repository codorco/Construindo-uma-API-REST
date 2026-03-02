<?php

// define o cabeçalho da resposta como JSON
header("Content-Type: application/json; charset=UTF-8");  

require_once('config.php');
require_once('Database.php');
require_once('Response.php');
require_once('Helper.php');

// define o fuso horário da API
date_default_timezone_set('Europe/Lisbon');

// prepara a resposta
$res = new Response();

// verifica se a API está ativada ou em manutenção
if(!API_ACTIVE){
    $res->set_status('error');
    $res->set_error_message(API_MESSAGE);
    $res->response();
}

// obtém o método da requisição (verbo HTTP)
$request_method = $_SERVER['REQUEST_METHOD'];

// verifica se há credenciais HTTP Basic Auth
if (!isset($_SERVER['PHP_AUTH_USER'])){
    $res->set_status('error');
    $res->set_error_message('Missing authentication credentials.');
    $res->response();
}

// define opções do MySQL
$mysql_config = [
    'host' => MYSQL_HOST,
    'database' => MYSQL_DATABASE,
    'username' => MYSQL_USER,
    'password' => MYSQL_PASS
];

// cria objeto de banco de dados
$db = new Database($mysql_config);

// verifica se a requisição tem credenciais válidas
$username = $_SERVER['PHP_AUTH_USER'];
$password = $_SERVER['PHP_AUTH_PW'];
$params = [
    ':username' => $username
];
$results = $db->execute_query("SELECT * FROM users WHERE username = :username", $params);
if($results->affected_rows == 0){
    $res->set_status('error');
    $res->set_error_message('Invalid credentials.');
    $res->response();
}

if(!password_verify($password, $results->results[0]->passwrd)){
    $res->set_status('error');
    $res->set_error_message('Invalid credentials.');
    $res->response();
}