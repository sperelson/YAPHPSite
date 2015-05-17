<?php

namespace Perelson;

class OperatorModel
{

    public function authOperator($username, $password)
    {
        $con = DB::conn();
        $sql = 'select `name`, `password` from `operators` where `username` = ?;';
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if (password_verify($password, $row['password'])) {
            // Set up the session
            Session::create($row['name'], $username);
            return true;
        }
        return false;
    }

    public function createOperator($name, $username, $password)
    {
        if (strlen($name) > 0 && strlen($username) > 0 && strlen($password) > 0) {
            $con = DB::conn();
            $sqlb = 'select count(1) as opexists from `operators` where `username` = ?;';
            $stmtb = $con->prepare($sqlb);
            $stmtb->bind_param("s", $username);
            $stmtb->execute();
            $resb = $stmtb->get_result();
            $rowb = $resb->fetch_assoc();
            $stmtb->close();

            if ($rowb['opexists'] == 0) {
                $sql = 'insert into `operators` (`name`, `username`, `password`) values (?, ?, ?);';
                $stmt = $con->prepare($sql);
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt->bind_param("sss", $name, $username, $hashedPassword);
                $stmt->execute();
                $stmt->close();
            }
            return true;
        }
        return false;
    }

}
