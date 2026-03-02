<?php

class Response
{
    private $status;
    private $error_message;
    private $response_data;
    private $integration_key;

    private $aditional_fields = [];

    public function __construct()
    {
        $this->status = 'success';
        $this->error_message = '';
        $this->response_data = null;
        $this->integration_key = null;
    }

    public function set_status($status = 'success')
    {
        $this->status = $status;
    }

    public function set_error_message($message)
    {
        $this->error_message = $message;
    }

    public function set_response_data($data)
    {
        $this->response_data = $data;
    }

    public function set_integration_key($key)
    {
        $this->integration_key = $key;
    }

    public function set_aditional_field($field_name, $field_value)
    {
        if(!key_exists($field_name, $this->aditional_fields)){
            $this->aditional_fields[$field_name] = $field_value;
        }
    }

    public function response()
    {
        $tmp = [];
        $tmp['status'] = $this->status;
        if (!empty($this->error_message)) {
            $tmp['error_message'] = $this->error_message;
        }
        $tmp['data'] = $this->response_data;

        // campos adicionais
        if(!empty($this->aditional_fields)){
            foreach($this->aditional_fields as $key => $value){
                $tmp[$key] = $value;
            }
        }
        
        // integration key
        if(!empty($this->integration_key)){
            $tmp['integration_key'] = $this->integration_key;
        }

        $tmp['time_response'] = time();
        $tmp['api_version'] = API_VERSION;

        echo json_encode($tmp, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
