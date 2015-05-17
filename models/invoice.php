<?php

namespace Perelson;

class InvoiceModel
{

    public function add($customerid, $description)
    {
        $con = DB::conn();
        $sql = 'insert into `invoices` (`customerid`, `description`) values (?, ?);';
        $stmt = $con->prepare($sql);
        $stmt->bind_param("is", $customerid, $description);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    public function get($id)
    {
        try {
            $con = DB::conn();
            $sql = 'select `customerid`,
                    `description`,
                    `invoices`.`datecreated`,
                    `amount` as invamount,
                    (select `invoices`.`amount` - ifnull(sum(`payments`.`amount`), 0)
                        from `invoicepayments`, `payments`
                        where `invoicepayments`.`invoiceid` = `invoices`.`id`
                        and `invoicepayments`.`paymentid` = `payments`.`id`
                        ) as outstanding,
                    `customers`.`name`
                    from `invoices`, `customers`
                    where `customers`.`deleted` = 0
                    and `customers`.`id` = `invoices`.`customerid`
                    and `invoices`.`id` = ?';
            $stmt = $con->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();
            return $row;
        } catch(exception $e) {
            // Do something like logging here
        }
    }

    public function update($id, $description)
    {
        $con = DB::conn();
        $sql = 'update `invoices` set $description = ? where `id` = ?;';
        $stmt = $con->prepare($sql);
        $stmt->bind_param("si", $description, $id);
        $stmt->execute();
        $stmt->close();
    }

    // Todo: add paging
    public function getAll()
    {
        $data = array();
        try {
            $con = DB::conn();
            $sql = 'select `invoices`.`id`, `customers`.`name`, `description`, `invoices`.`datecreated`, `amount`,
                   (select count(1) from `invoicelines` where `invoices`.`id` = `invoiceid`) as `lines`,
                   (select `invoices`.`amount` - ifnull(sum(`payments`.`amount`), 0)
                        from `invoicepayments`, `payments`
                        where `invoicepayments`.`invoiceid` = `invoices`.`id`
                        and `invoicepayments`.`paymentid` = `payments`.`id`
                        ) as outstanding
                    from `customers`, `invoices` where `customers`.`deleted` = 0
                    and `customers`.`id` = `invoices`.`customerid`;';
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
