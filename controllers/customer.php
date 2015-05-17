<?php

namespace Perelson;

class CustomerController
{

	// Add a customer
	public function post_add() {
		$name = !empty($_POST['name']) ? trim($_POST['name']) : false;
		$address = !empty($_POST['address']) ? trim($_POST['address']) : false;
		$username = !empty($_POST['username']) ? trim($_POST['username']) : false;
		$password = !empty($_POST['password']) ? trim($_POST['password']) : false;
		$proceed = $name and $address and $username and $password;

		if (strlen($password) < 6) {
			$proceed = false;
			Session::errors('password', 'must be 6 or more characters');
		}

		if ($proceed !== false) {
			$customer = new CustomerModel();
			$id = $customer->add($name, $address, $username, $password);
			if ($id !== false) {
				Session::flashClear();
				Session::flashSuccess("Customer Added");
				Utils::redirect('/customer/' . $id, 302);
				return;
			} else {
				Session::flashFail("Customer Not Added");
				Session::remember($_POST);
				Utils::redirect('/customer/add', 302);
				return;
			}
		}
		if (empty($name)) Session::errors('name', 'required');
		if (empty($address)) Session::errors('address', 'required');
		if (empty($username)) Session::errors('username', 'required');
		if (empty($password)) Session::errors('password', 'required');
		Session::remember($_POST);
		Utils::redirect('/customer/add', 302);
	}

	public function post_update() {
		$id = App::segment(2);

		if (!empty($id)) {
			$name = !empty($_POST['name']) ? trim($_POST['name']) : false;
			$address = !empty($_POST['address']) ? trim($_POST['address']) : false;
			$username = !empty($_POST['username']) ? trim($_POST['username']) : false;
			$password = !empty(trim($_POST['password'])) ? trim($_POST['password']) : false;
			$proceed = $name and $address and $username;

			if ($password !== false && strlen($password) < 6) {
				$proceed = false;
				Session::errors('password', 'must be 6 or more characters');
			}

			if ($proceed !== false) {
				$customer = new CustomerModel();
				$res = $customer->update($id, $name, $address, $username, $password);

				if ($res !== false) {
					Session::flashSuccess("Customer Updated");
					Utils::redirect('/customer/' . $id, 302);
					return;
				} else {
					Session::flashFail("Customer Not Updated");
					Session::remember($_POST);
					Utils::redirect('/customer/' . $id, 302);
					return;
				}
			}
			if (empty($name)) Session::errors('name', 'required');
			if (empty($address)) Session::errors('address', 'required');
			if (empty($username)) Session::errors('username', 'required');
			if (empty($password)) Session::errors('password', 'required');
			Session::remember($_POST);
			Utils::redirect('/customer/' . $id, 302);
			return;
		}
		Utils::redirect('/customers', 302);
	}

	// Get a customer for editing
	public function get_index() {
		$data = array();
		$id = App::segment(1);

		if (!empty($id)) {
			$customer = new CustomerModel();
			$data = $customer->get($id);
		}
		$data['update'] = true;
		$data['id'] = $id;
		View::show('addcustomer', $data);
	}

	// Get a customer for editing
	public function get_add() {
		View::show('addcustomer');
	}

	// Delete a customer (sets a flag)
	public function post_delete() {
		$id = App::segment(2);

		if (!empty($id)) {
			$customer = new CustomerModel();
			$data = $customer->delete($id);
		}
		Utils::redirect('/customers', 302);
	}

	// Get a customer for editing
	public function get_invoice() {
		$data = array();
		$id = App::segment(2);

		if (!empty($id)) {
			$customer = new CustomerModel();
			$data = $customer->get($id);
			$data['customerid'] = $id;
			View::show('addinvoice', $data);
			return;
		}
		Utils::redirect('/customers', 302);
	}

}
