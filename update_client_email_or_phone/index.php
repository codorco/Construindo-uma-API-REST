<?php

// atualizar email ou telefone do cliente
require_once('../_inc/init.php');

// obter dados JSON
$data = json_decode(file_get_contents("php://input"), true);

// verificar se o método da requisição é válido
check_request_method($request_method, 'PUT');

// verificar campos obrigatórios
$required_fields = ['id'];
if (!check_required_fields_in_json($data, $required_fields)) {
    $res->set_status('error');
    $res->set_error_message('Missing fields.');
    check_integration_key_json($data);
    $res->response();
}

// verificar quais campos de entrada foram fornecidos
$sql = "UPDATE clientes SET ";

$params = [
    'id' => $data['id']
];

if (key_exists('email', $data)) {
    $params['email'] = $data['email'];
    $sql .= "email = :email, ";
}

if (key_exists('telefone', $data)) {
    $params['telefone'] = $data['telefone'];
    $sql .= "telefone = :telefone, ";
}

// se parâmetros tiverem apenas um registro, retornar erro
if (count($params) == 1) {
    $res->set_status('error');
    $res->set_error_message('Email and/or phone is required.');
    check_integration_key_json($data);
    $res->response();
}

// remove a última vírgula da expressão SQL
$sql = substr($sql, 0, strlen($sql) - 2);

// finalizar expressão SQL
$sql .= " WHERE id = :id";

// chave de integração
check_integration_key_json($data);

$db->execute_non_query($sql, $params);
$res->response();
