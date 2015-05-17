<?php

namespace Perelson;

class InvoicelineModel
{

	public function add($invoiceid, $description, $amount)
	{
		$con = DB::conn();
		$sql = 'insert into `invoicelines` (`invoiceid`, `description`, `amount`) values (?, ?, ?);';
		$stmt = $con->prepare($sql);
		$stmt->bind_param("isd", $invoiceid, $description, $amount);
		$stmt->execute();
		$stmt->close();
	}

	public function delete($invoiceid, $id)
	{
		$con = DB::conn();
		$sql = 'delete from `invoicelines` where `id` = ? and `invoiceid` = ?;';
		$stmt = $con->prepare($sql);
		$stmt->bind_param("ii", $id, $invoiceid);
		$stmt->execute();
		$stmt->close();
	}

	// Todo: add paging
	public function getAll($invoiceid)
	{
		$data = array();
		try {
			$con = DB::conn();
			$sql = 'select `id`, `description`, `datecreated`, `amount`
					from `invoicelines` where `invoiceid` = ?;';
			$stmt = $con->prepare($sql);
			$stmt->bind_param("i", $invoiceid);
			$stmt->execute();

			if ($res = $stmt->get_result()) {
				while ($row = $res->fetch_assoc()) {
					$data[] = $row;
				}
				$res->free();
			}
		} catch(exception $e) {
		}
		return $data;
	}

}
