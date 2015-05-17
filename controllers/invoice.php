<?php
/* this is the router for this project */
namespace Perelson;

class InvoiceController
{

    public function get_index()
    {
        $data = array();
        $id = App::segment(1);

        if (!empty($id)) {
            $invoice = new InvoiceModel();
            $data = $invoice->get($id);
            $data['invdescription'] = $data['description'];
            unset($data['description']);
            $data['invamount'] = $data['amount'];
            unset($data['amount']);
            $data['id'] = $id;
            $invoice = new InvoicelineModel();
            $data['lines'] = $invoice->getAll($id);
            View::show('invoicelines', $data);
        }
    }

    public function get_pay()
    {
        $data = array();
        $id = App::segment(2);

        if (!empty($id)) {
            $invoice = new InvoiceModel();
            $data = $invoice->get($id);
            $data['id'] = $id;
            View::show('pay', $data);
        }
    }

    // Add an invoice
    public function post_add()
    {
        $customerid = !empty($_POST['customerid']) ? trim($_POST['customerid']) : false;
        $description = !empty($_POST['description']) ? trim($_POST['description']) : false;

        if ($customerid !== false) {
            if ($description !== false) {
                $invoice = new InvoiceModel();
                $id = $invoice->add($customerid, $description);
                if ($id !== false) {
                    Utils::redirect('/invoice/' . $id, 302);
                    return;
                } else {
                    Session::flashFail("Customer Not Added");
                    Session::remember($_POST);
                    Utils::redirect('/customer/invoice/' . $customerid, 302);
                }
            }
            if (empty($description)) Session::errors('description', 'required');
            Session::remember($_POST);
            Utils::redirect('/customer/invoice/' . $customerid, 302);
            return;
        }
        Utils::redirect('/customers', 302);
    }

}
