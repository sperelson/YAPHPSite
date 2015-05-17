<?php

namespace Perelson;

class InvoicelineController
{

	// Add a customer
	public function post_add() {
		$invoiceid = App::segment(2);
		$error = '';

		if (!empty($invoiceid)) {
			$description = !empty($_POST['description']) ? trim($_POST['description']) : false;
			$amount = !empty($_POST['amount']) ? trim($_POST['amount']) : false;
			$proceed = $description and $amount;

			if ($amount === false && isset($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] == 0) {
				$amount = true;
				$proceed = false;
				$error = 'amount cannot be zero<br />';
			}

			if ($proceed != false) {
				$lines = new InvoicelineModel();
				$lines->add($invoiceid, $description, $amount);
				Session::flashClear();
				Session::flashSuccess("Invoice Line Added");
				Utils::redirect('/invoice/' . $invoiceid, 302);
				return;
			}
			if (empty($description)) $error .= 'description required<br />';
			if (empty($amount)) $error .= 'amount required<br />';
			Session::flashClear();
			Session::flashFail($error);
			Session::remember($_POST);
			Utils::redirect('/invoice/' . $invoiceid, 302);
			return;
		}
		Utils::redirect('/invoices', 302);
	}

	public function post_delete() {
		$id = App::segment(2);
		$lineid = App::segment(3);

		if (!empty($id) && !empty($lineid)) {
			$lines = new InvoicelineModel();
			$data = $lines->delete($id, $lineid);
		}
		Utils::redirect('/invoice/' . $id, 302);
	}

}
