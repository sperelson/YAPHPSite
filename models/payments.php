<?php

namespace Perelson;

class PaymentsModel
{

    public function add($invoiceid, $amount)
    {
        try {
            $con = DB::conn();

            $sqlc = 'select `customerid`
                     from `invoices`
                     where `id` = ?';
            $stmtc = $con->prepare($sqlc);
            $stmtc->bind_param("i", $invoiceid);
            $stmtc->execute();
            $resc = $stmtc->get_result();
            $rowc = $resc->fetch_assoc();
            $stmtc->close();

            if (isset($rowc['customerid'])) {
                $sql = 'insert into `payments` (`customerid`, `amount`) values (?, ?);';
                $stmt = $con->prepare($sql);
                $stmt->bind_param("id", $rowc['customerid'], $amount);
                $stmt->execute();
                $id = $stmt->insert_id;
                $stmt->close();

                $sqlb = 'insert into `invoicepayments` (`invoiceid`, `paymentid`) values (?, ?);';
                $stmtb = $con->prepare($sqlb);
                $stmtb->bind_param("ii", $invoiceid, $id);
                $stmtb->execute();
                $stmtb->close();
            }
        } catch(exception $e) {
            // Do something like logging here
        }
        return true;
    }

    // Todo: add paging
    public function getAll()
    {
        $data = array();
        try {
            $con = DB::conn();
            $sql = 'select `payments`.`id`, `customers`.`name`, `invoices`.`description`, `payments`.`datecreated`,
                   `payments`.`amount` as paid, `invoices`.`amount` as invoiced
                    from `customers`, `invoices`, `payments`, `invoicepayments`
                    where `customers`.`deleted` = 0
                    and `customers`.`id` = `payments`.`customerid`
                    and `payments`.`id` = `invoicepayments`.`paymentid`
                    and `invoices`.`id` = `invoicepayments`.`invoiceid`;';
            if ($res = $con->query($sql)) {
                while ($row = $res->fetch_assoc()) {
                    $data[] = $row;
                }
                $res->free();
            }
        } catch(exception $e) {
            // Do something like logging here
        }
        return $data;
    }

}
