<?php

// retorna um cliente por id

require_once('../_inc/init.php');

// verificar se o método da requisição é válido
check_request_method($request_method, 'GET');

// chave de integração
check_integration_key_get();

// verifica se o id solicitado está presente
if(!isset($_GET['id'])){
    missing_request_parameter('id');
}

$params = [
    ':id' => $_GET['id']
];
$results = $db->execute_query("SELECT * FROM clientes WHERE id = :id", $params);

$res->set_status('success');
$res->set_response_data($results->results);
$res->response();