<?php

// deletar cliente

require_once('../_inc/init.php');

// obter dados JSON
$data = json_decode(file_get_contents("php://input"), true);

// verificar se o método da requisição é válido
check_request_method($request_method, 'DELETE');

// verificar campos obrigatórios
$required_fields = ['id'];
if(!check_required_fields_in_json($data, $required_fields)){
    $res->set_status('error');
    $res->set_error_message('Missing fields.');
    check_integration_key_json($data);
    $res->response();
}

// chave de integração
check_integration_key_json($data);

// parâmetros
$params = [
    'id' => $data['id']
];

$db->execute_non_query(
    "DELETE FROM clientes WHERE id = :id"
    , $params
);

$res->response();