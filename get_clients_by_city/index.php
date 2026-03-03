<?php

// retorna todos os clientes por cidade

require_once('../_inc/init.php');

// verifica se o método da requisição é válido
check_request_method($request_method, 'GET');

// chave de integração
check_integration_key_get();

// verifica se o parâmetro solicitado está presente
if(!isset($_GET['city'])){
    missing_request_parameter('city');
}

$params = [
    ':city' => $_GET['city']
];
$results = $db->execute_query("SELECT * FROM clientes WHERE cidade = :city", $params);

$res->set_status('success');
$res->set_response_data($results->results);

// campo adicional
$res->set_aditional_field('total_clients', $results->affected_rows);

$res->response();