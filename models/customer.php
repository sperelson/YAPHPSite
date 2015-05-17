<?php

namespace Perelson;

class CustomerModel
{

	public function add($name, $address, $username, $password)
	{
		if (strlen($name) > 0 && strlen($username) > 0 && strlen($password) > 0) {
			$id = false;
			try {
				$con = DB::conn();
				$sql = 'insert into `customers` (`name`, `address`, `username`, `password`) values (?, ?, ?, ?);';
				$stmt = $con->prepare($sql);
				$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
				$stmt->bind_param("ssss", $name, $address, $username, $hashedPassword);
				$stmt->execute();

				$id = $stmt->insert_id;
				$stmt->close();
			} catch(exception $e) {
				return false;
			}
			return $id;
		}
		return false;
	}

	public function get($id)
	{
		$con = DB::conn();
		$sql = 'select `id`, `name`, `address`, `datecreated`, `username`, `balance`, `deleted` from `customers` where `id` = ?;';
		$stmt = $con->prepare($sql);
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$res = $stmt->get_result();
		$row = $res->fetch_assoc();
		$stmt->close();
		return $row;
	}

	public function delete($id)
	{
		try {
			$con = DB::conn();
			$sql = 'update `customers` set `deleted` = 1 where `id` = ?;';
			$stmt = $con->prepare($sql);
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$stmt->close();
		} catch(exception $e) {
		}
	}

	public function update($id, $name, $address, $username, $password)
	{
		$con = DB::conn();

		if ($password === false) {
			$sql = 'update `customers` set `name` = ?, `address` = ?, `username` = ? where `id` = ?;';
			$stmt = $con->prepare($sql);
			$stmt->bind_param("sssi", $name, $address, $username, $id);
		} else {
			$sql = 'update `customers` set  `name` = ?, `address` = ?, `username` = ?, `password` = ? where `id` = ?;';
			$stmt = $con->prepare($sql);
			$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
			$stmt->bind_param("ssssi", $name, $address, $username, $hashedPassword, $id);
		}
		$stmt->execute();
		$stmt->close();
		return true;
	}

	// Todo: add paging
	public function getAll()
	{
		$data = array();
		try {
			$con = DB::conn();
			$sql = 'select `customers`.`id`, `name`, `address`, `datecreated`, `username`, `balance`,
				   (select count(1) from `invoices` where `customerid` = `customers`.`id`) as `invoices`
					from `customers` where `deleted` = 0;';
			if ($res = $con->query($sql)) {
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
