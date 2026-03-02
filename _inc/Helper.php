<?php

function check_request_method($request_method, $expected_request_method)
{
    if(strtoupper($request_method) !== strtoupper($expected_request_method)){
        global $res;
        $res->set_status('error');
        $res->set_error_message('Invalid request method. Expected ' . strtoupper($expected_request_method));
        $res->response();
    }
}