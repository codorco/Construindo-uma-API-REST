<?php

// retorna o status atual da API

require_once('../_inc/init.php');

$res->set_status('success');

// integration key
check_integration_key_get();

$res->response();