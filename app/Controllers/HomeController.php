<?php

namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;

class HomeController extends ResourceController
{
    public function index()
    {
        $response = [
            'status' => 500,
            "error" => true,
            'messages' => 'SUFRIENDO EN PHP',
            'data' => []
        ];
        return $this->respond($response);
    }
}

