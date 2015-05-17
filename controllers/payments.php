<?php

namespace Perelson;

class PaymentsController
{

    public function get_index()
    {
        $payments = new PaymentsModel();
        $data['payments'] = $payments->getAll();
        View::show('payments', $data);
    }

}
