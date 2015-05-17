<?php

namespace Perelson;

class PaymentController
{

    public function post_index()
    {
        $invoiceid = App::segment(1);

        if (!empty($invoiceid)) {
            $amount = !empty($_POST['amount']) ? trim($_POST['amount']) : false;
            $proceed = $amount;

            if ($amount === false && isset($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] == 0) {
                $amount = true;
                $proceed = false;
                $error = 'amount cannot be zero<br />';
            }
            if ($proceed) {
                $pay = new PaymentsModel();
                $res = $pay->add($invoiceid, $amount);

                if ($res) {
                    Session::flashSuccess("Payment Made");
                    Utils::redirect('/payments', 302);
                    return;
                }
            }
            if ($amount === false) Session::errors('amount', 'required');
            if ($amount === true) Session::errors('amount', 'cannot be zero');
            Utils::redirect('/invoice/pay/' . $invoiceid, 302);
            return;
        }
        Utils::redirect('/invoices', 302);
    }

    // Add a payment
    public function post_add()
    {
        $username = !empty($_POST['username']) ? trim($_POST['username']) : false;
        $password = !empty($_POST['password']) ? trim($_POST['password']) : false;

        if ($username !== false && $password !== false) {
            $operator = new OperatorModel();
            if ($operator->authOperator($username, $password)) {
                Utils::redirect('/invoices', 302);
            }
        }
        Utils::redirect('/', 302);
    }

}
