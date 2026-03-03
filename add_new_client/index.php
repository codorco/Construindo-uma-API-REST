<?php

// adicionar novo cliente - único. Não permitir repetição de nome

require_once('../_inc/init.php');

// obter dados JSON
$data = json_decode(file_get_contents("php://input"), true);

// verificar se o método da requisição é válido
check_request_method($request_method, 'POST');

// verificar campos obrigatórios
$required_fields = ['nome', 'sexo', 'data_nascimento', 'email', 'telefone', 'morada', 'cidade', 'ativo'];
if(!check_required_fields_in_json($data, $required_fields)){
    $res->set_status('error');
    $res->set_error_message('Missing fields.');
    check_integration_key_json($data);
    $res->response();
}

// chave de integração
check_integration_key_json($data);

// verificar se já existe outro cliente com o mesmo nome
$params = [
    'nome' => $data['nome']
];
$results = $db->execute_query("SELECT id from clientes WHERE nome = :nome", $params);
if($results->affected_rows != 0){
    $res->set_status('error');
    $res->set_error_message('There is another client with the same name.');
    $res->response();
}

// parâmetros
$params = [
    'nome' => $data['nome'],
    'sexo' => $data['sexo'],
    'data_nascimento' => $data['data_nascimento'],
    'email' => $data['email'],
    'telefone' => $data['telefone'],
    'morada' => $data['morada'],
    'cidade' => $data['cidade'],
    'ativo' => $data['ativo']
];

$db->execute_non_query(
    "INSERT INTO clientes VALUES(" . 
    "0, " . 
    ":nome, " . 
    ":sexo, " . 
    ":data_nascimento, " . 
    ":email, " . 
    ":telefone, " . 
    ":morada, " . 
    ":cidade, " . 
    ":ativo)"
    , $params
);

$res->response();