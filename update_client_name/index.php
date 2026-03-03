<?php

// atualizar nome do cliente

require_once('../_inc/init.php');

// obter dados JSON
$data = json_decode(file_get_contents("php://input"), true);

// verificar se o método da requisição é válido
check_request_method($request_method, 'PUT');

// verificar campos obrigatórios
$required_fields = ['id','nome'];
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
    'id' => $data['id'],
    'nome' => $data['nome']
];

$db->execute_non_query(
    "UPDATE clientes SET " . 
    "nome = :nome " . 
    "WHERE id = :id"
    , $params
);

$res->response();