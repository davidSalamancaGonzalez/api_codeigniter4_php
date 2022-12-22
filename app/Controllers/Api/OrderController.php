<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;


class OrderController extends ResourceController
{

    private $db;

    function __construct(){
        $this->db = db_connect();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function listOrder()
    {
        $builder = $this->db->table('orders');
        $builder->select('*');
        $builder->join('users', 'users.id_user = orders.user_id','left');
        $builder->join('books', 'books.id_book = orders.book_id','left'); 
        $query = $builder->get();
    
        $response = [
            'status' => 200,
            "error" => false,
            'messages' => 'List Order',
            'data' => $query->getResultArray()
        ];
    
        return $this->respond($response);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($user_id = null)
    {
        $builder = $this->db->table('orders');
        $builder->select('id_book');
        $builder->join('users', 'users.id_user = orders.user_id','left');
        $builder->join('books', 'books.id_book = orders.book_id','left'); 
        $builder->where(['id_user' => $user_id]);
        $query = $builder->get();
    
        $response = [
            'status' => 200,
            "error" => false,
            'messages' => 'List Order',
            'data' => $query->getResultArray()
        ];
    
        return $this->respond($response);
    }

     
    public function orderShow($order_id)
{     
    $builder = $this->db->table('orders');
    $builder->select('*');
    $builder->join('books', 'books.id_book = orders.book_id','left');
    $builder->join('users', 'users.id_user = orders.user_id','left');
    $builder->where(['id_order' => $order_id]);
    $query = $builder->get();

	$response = [
		'status' => 200,
		"error" => false,
		'messages' => 'List Order',
		'data' => $query->getResultArray()
	];

	return $this->respond($response);
}


}
