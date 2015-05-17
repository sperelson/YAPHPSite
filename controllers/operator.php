<?php

namespace Perelson;

class OperatorController
{
    public function get_init()
    {
        $operator = new OperatorModel();
        $operator->createOperator('Oper Ator', 'ops', '123456');
        echo("Operator user initialised. Login with ops:123456");
    }

    public function get_login()
    {
        $data = array();

        if (!empty($_GET["back"])) {
            $data = array('back' => $_GET["back"]);
        }
        View::show('login', $data);
    }

    public function post_login()
    {
        $username = !empty($_POST['username']) ? trim($_POST['username']) : false;
        $password = !empty($_POST['password']) ? trim($_POST['password']) : false;

        if ($username !== false && $password !== false) {
            $operator = new OperatorModel();
            if ($operator->authOperator($username, $password)) {
                if (!empty($_GET["back"])) {
                    $url = urldecode($_GET["back"]);
                    Utils::redirect($url, 302);
                }
                Utils::redirect('/invoices', 302);
            }
        }
        Utils::redirect('/', 302);
    }

    public function get_logout()
    {
        Session::destroy();
        Utils::redirect('/', 302);
    }

}
