<?php

namespace Perelson;

class invoicesController
{

	public function get_index() {
		$invoice = new InvoiceModel();
		$data['invoices'] = $invoice->getAll();
		View::show('invoices', $data);
	}

}
