<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\EmployeeModel;
use \App\Validation\CustomRules;

class EmployeeController extends ResourceController
{
    public function addEmployee()
	{
		$rules = [
			"name" => "required",
			"email" => "required|valid_email|is_unique[employees.email]|min_length[6]",
			"phone_no" => "required",
			"age" => "required|ageValidation"
		];

		$messages = [
			"name" => [
				"required" => "Nombre requerido, siusplau"
			],
			"email" => [
				"required" => "EMAAAAAAILLLLLLLLLLLLLLL",
				"valid_email" => "Email address is not JEJE in format",
				"is_unique" => "Email address already exists"
			],
			"phone_no" => [
				"required" => "Phone Number is required"
			],
			"age" => [
				"required" => "Inserta tu edad",
				"ageValidation" => "Mínimo 18 años para registrarte"
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

			$emp = new EmployeeModel();

			$data['name'] = $this->request->getVar("name");
			$data['email'] = $this->request->getVar("email");
			$data['phone_no'] = $this->request->getVar("phone_no");
			$data['age'] = $this->request->getVar("age");

			$emp->save($data);

			$response = [
				'status' => 200,
				'error' => false,
				'message' => 'Employee added successfully',
				'data' => []
			];
		}

		return $this->respond($response);
	}

	public function listEmployee()
	{
		$emp = new EmployeeModel();
            
        //log_message('error', $e->getMessage());
        
		$response = [
			'status' => 200,
			"error" => false,
			'messages' => 'Employee list',
			'data' => $emp->findAll()
		];

		return $this->respond($response);
	}

	public function showEmployee($emp_id)
	{
		$emp = new EmployeeModel();

		$data = $emp->find($emp_id);
        //$data = $model->where(['id' => $emp_id])->first();

		if (!empty($data)) {

			$response = [
				'status' => 200,
				"error" => false,
				'messages' => 'Single employee data',
				'data' => $data
			];

		} else {

			$response = [
				'status' => 500,
				"error" => true,
				'messages' => 'No employee found',
				'data' => []
			];
		}

		return $this->respond($response);
	}

	public function updateEmployee($emp_id)
	{
		$rules = [
			"name" => "required",
			"email" => "required|valid_email|min_length[6]",
			"phone_no" => "required",
		];

		$messages = [
			"name" => [
				"required" => "Name is required"
			],
			"email" => [
				"required" => "Email required",
				"valid_email" => "Email address is not in format"
			],
			"phone_no" => [
				"required" => "Phone Number is required"
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

			$emp = new EmployeeModel();

			if ($emp->find($emp_id)) {


				$data['name'] = $this->request->getVar("name");
				$data['email'] = $this->request->getVar("email");
				$data['phone_no'] = $this->request->getVar("phone_no");

				$emp->update($emp_id, $data);

				$response = [
					'status' => 200,
					'error' => false,
					'message' => 'Employee updated successfully',
					'data' => []
				];
			}else {

				$response = [
					'status' => 500,
					"error" => true,
					'messages' => 'No employee found',
					'data' => []
				];
			}
		}

		return $this->respond($response);
	}

	public function deleteEmployee($emp_id)
	{
		$emp = new EmployeeModel();

		$data = $emp->find($emp_id);

		if (!empty($data)) {

			$emp->delete($emp_id);

			$response = [
				'status' => 200,
				"error" => false,
				'messages' => 'Employee deleted successfully',
				'data' => []
			];

		} else {

			$response = [
				'status' => 500,
				"error" => true,
				'messages' => 'No employee found',
				'data' => []
			];
		}

		return $this->respond($response);
	}
}
