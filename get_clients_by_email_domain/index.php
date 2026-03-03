<?php

// retorna todos os clientes por domínio de email

require_once('../_inc/init.php');

// verifica se o método da requisição é válido
check_request_method($request_method, 'GET');

// chave de integração
check_integration_key_get();

// verifica se o parâmetro solicitado está presente
if(!isset($_GET['domain'])){
    missing_request_parameter('domain');
}

$params = [
    ':domain' => '%@' . $_GET['domain'] . "%"
];
$results = $db->execute_query("SELECT * FROM clientes WHERE email LIKE :domain", $params);

$res->set_status('success');
$res->set_response_data($results->results);

// campo adicional
$res->set_aditional_field('total_clients', $results->affected_rows);

$res->response();