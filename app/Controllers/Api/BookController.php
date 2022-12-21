<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BookModel;

class BookController extends ResourceController
{
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function listBook()
    {
        $emp = new BookModel();
            
        //log_message('error', $e->getMessage());
        
		$response = [
			'status' => 200,
			"error" => false,
			'messages' => 'Book list',
			'data' => $emp->findAll()
		];

		return $this->respond($response);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
		$boo = new  BookModel();

		$data = $boo->find($boo_id);
        //$data = $model->where(['id' => $emp_id])->first();

		if (!empty($data)) {

			$response = [
				'status' => 200,
				"error" => false,
				'messages' => 'Single book data',
				'data' => $data
			];

		} else {

			$response = [
				'status' => 500,
				"error" => true,
				'messages' => 'No book found',
				'data' => []
			];
		}

		return $this->respond($response);
	}

   

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function addBook()
	{
		$rules = [
			"isbn" => "required",
			"title" => "required",
			"description" => "required",
			"author" => "required|ageValidation"
		];

		$messages = [
			"isbn" => [
				"required" => "isb required"
			],
			"title" => [
				"required" => "Title required",
			],
			"description" => [
				"required" => "Description is required"
			],
			"author" => [
				"required" => "Author",
				
			]
		];

		if (!$this->validate($rules, $messages)) {

			$response = [
				'status' => 500,
				'error' => true,
				'message' => $this->validator->getErrors(),
				'data' => []
			];
		} else {

			$emp = new BookModel();

			$data['isbn'] = $this->request->getVar("isbn");
			$data['title'] = $this->request->getVar("title");
			$data['description'] = $this->request->getVar("description");
			$data['author'] = $this->request->getVar("author");

			$emp->save($data);

			$response = [
				'status' => 200,
				'error' => false,
				'message' => 'Book added successfully',
				'data' => []
			];
		}

		return $this->respond($response);
	}

    
    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($book_id = null)
    {
		$emp = new BookModel();

		$data = $emp->find($book_id);

		if (!empty($data)) {

			$emp->delete($book_id);

			$response = [
				'status' => 200,
				"error" => false,
				'messages' => 'Book deleted successfully',
				'data' => []
			];

		} else {

			$response = [
				'status' => 500,
				"error" => true,
				'messages' => 'No book found',
				'data' => []
			];
		}

		return $this->respond($response);
    }


	public function updateBook($book_id = null)
	{
		$rules = [
			"isbn" => "required",
			"title" => "required",
			"description" => "required",
			"author" => "required"
		];

		$messages = [
			"isbn" => [
				"required" => "isbn required"
			],
			"title" => [
				"required" => "Title required",
			],
			"description" => [
				"required" => "Description is required"
			],
			"author" => [
				"required" => "Author",
				
			]
		];

		if (!$this->validate($rules, $messages)) {

			$response = [
				'status' => 500,
				'error' => true,
				'message' => $this->validator->getErrors(),
				'data' => []
			];
		} else {

			$boo = new BookModel();

			if ($boo->find($book_id)) {

			$input = $this->request->getRawInput();
			$data['isbn'] = $input["isbn"];
			$data['title'] = $input["title"];
			$data['description'] =$input["description"];;
			$data['author'] = $input["author"];
			print_r($data);

				$boo->update($book_id, $data);

				$response = [
					'status' => 200,
					'error' => false,
					'message' => 'Book updated successfully',
					'data' => []
				];
			}else {

				$response = [
					'status' => 500,
					"error" => true,
					'messages' => 'No Book found',
					'data' => []
				];
			}
		}

		return $this->respond($response);
	}	

}
