<?php

// retorna todos os clientes

require_once('../_inc/init.php');

// verifica se o método da requisição é válido
check_request_method($request_method, 'GET');

$results = $db->execute_query("SELECT * FROM clientes");

$res->set_status('success');
$res->set_response_data($results->results);

// campo adicional
$res->set_aditional_field('total_clients', $results->affected_rows);

$res->response();