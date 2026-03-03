<?php

// retorna todos os clientes por cidade

require_once('../_inc/init.php');

// verifica se o método da requisição é válido
check_request_method($request_method, 'GET');

// chave de integração
check_integration_key_get();

// verifica se o parâmetro solicitado está presente
$results = $db->execute_query(
    "SELECT 'Homens' sexo, COUNT(*) total FROM clientes WHERE sexo = 'm' " . 
    "UNION " . 
    "SELECT 'Mulheres' sexo, COUNT(*) total FROM clientes WHERE sexo = 'f'"
);

$res->set_status('success');
$res->set_response_data($results->results);

$res->response();