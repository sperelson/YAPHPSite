<?php

namespace Perelson;

class CustomersController
{

	public function get_index() {
		$customer = new CustomerModel();
		$data['customers'] = $customer->getAll();
		View::show('customers', $data);
	}

}
