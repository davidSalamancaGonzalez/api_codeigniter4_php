<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use \App\Validation\CustomRules;

class UserController extends ResourceController
{

    public function register()
    {
        //Validaciones
        $rules = [
            "name" => "required",
            "email" => "required|valid_email|is_unique[users.email]|min_length[6]",
            "phone_no" => "required|mobileValidation[phone_no]",
            "cp" => "required|exact_length[5]",
            "password" => "required",
        ];

        //Mensajes error

        $messages = [
            "name" => [
                "required" => "Name is required"
            ],
            "email" => [
                "required" => "Email required",
                "valid_email" => "Email address is not in format"
            ],
            "phone_no" => [
                "required" => "Phone Number is required",
                "mobileValidation" => "Phone is not in format"
            ],
            "cp" => [
                "required" => "Postal code required",
            ],
            "password" => [
                "required" => "password is required"
            ],
        ];

        if (!$this->validate($rules, $messages)) {

            $response = [
                'status' => 500,
                'error' => true,
                'message' => $this->validator->getErrors(),
                'data' => []
            ];
        } else {

            $userModel = new UserModel();

            $data = [
                "name" => $this->request->getVar("name"),
                "email" => $this->request->getVar("email"),
                "phone_no" => $this->request->getVar("phone_no"),
                "cp" => $this->request->getVar("cp"),
                "password" => password_hash($this->request->getVar("password"), PASSWORD_DEFAULT),
            ];

            //Password hash es una función nativa de PHP. PASSWORD_DEFAULT es el tipo de codificación. 

            if ($userModel->insert($data)) {

                $response = [
                    'status' => 200,
                    "error" => false,
                    'messages' => 'Successfully, user has been registered',
                    'data' => []
                ];
            } else {

                $response = [
                    'status' => 500,
                    "error" => true,
                    'messages' => 'Failed to create user',
                    'data' => []
                ];
            }
        }

        return $this->respond($response);
    }

    public function login()
    {
        $rules = [
            "email" => "required|valid_email|min_length[6]",
            "password" => "required",
        ];

        $messages = [
            "email" => [
                "required" => "Email required",
                "valid_email" => "Email address is not in format"
            ],
            "password" => [
                "required" => "password is required"
            ],
        ];

        if (!$this->validate($rules, $messages)) {

            $response = [
                'status' => 500,
                'error' => true,
                'message' => $this->validator->getErrors(),
                'data' => []
            ];
            
        } else {
            $userModel = new UserModel();

            $userdata = $userModel->where("email", $this->request->getVar("email"))->first();

            if (!empty($userdata)) {
//password verify necesita dos parámetros "password" la enviamos nosotros por el formulario y $userdata nos da el password del back.
//de más arriba. Si va bien capturamos variable de entorno getenv('jwt secret'), configuramos el tiempo y codificamos el JWT.
                if (password_verify($this->request->getVar("password"), $userdata['password'])) {

                    $key = getenv('JWT_SECRET');
//DATO TIEMPO TOKEN
                    $iat = time(); // current timestamp value
                    $nbf = $iat + 10;
                    $exp = $iat + 3600;

                    $payload = array(
                        "iat" => $iat, // issued at
                        "nbf" => $nbf, //not before in seconds
                        "exp" => $exp, // expire time in seconds
                        "data" => array(
                                    'id' => $userdata['id'],
                                    'email' => $userdata['email'],
                                    'role' => 4,
                                ),
                    );
                   
                   $token = JWT::encode($payload, $key, 'HS256');
                    $response = [
                        'status' => 200,
                        'error' => false,
                        'messages' => 'User logged In successfully',
                        'data' => [
                            'token' => $token
                        ]
                    ];
                } else {

                    $response = [
                        'status' => 500,
                        'error' => true,
                        'messages' => 'Incorrect details',
                        'data' => []
                    ];
                }
            } else {
                $response = [
                    'status' => 500,
                    'error' => true,
                    'messages' => 'User not found',
                    'data' => []
                ];
               
            }
        }
        return $this->respond($response);
    }

    public function details()
    {
        $key = getenv('JWT_SECRET');

        try {
            //$header = $this->request->getHeader("Authorization");
            //HTTP AUTHORI... es el token que enviamos por la petición GET
            //decode con la key de env
            $token = $this->request->getServer("HTTP_AUTHORIZATION");
            $token = str_replace('Bearer ', '', $token);
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            if ($decoded) {
                $response = [
                    'status' => 200,
                    'error' => false,
                    'messages' => 'User details',
                    'data' => [
                        'profile' => $decoded->data
                    ]
                ];
            }
        } catch (Exception $ex) {
            $response = [
                'status' => 401,
                'error' => true,
                'messages' => 'Access denied',
                'data' => []
            ];
           
        }
        return $this->respond($response);
    }
}
