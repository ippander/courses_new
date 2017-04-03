<?php

require('config.php');

function addAccount($account) {

	$pdo = pdo();

	$stmt = $pdo->prepare("
		INSERT INTO account (email, password)
		VALUES (?, ?)
	");

	// $pdo->beginTransaction();
	$stmt->execute([$account->email, password_hash($account->password, PASSWORD_DEFAULT)]);
	// $pdo->commit();

	unset($account->password);
	$account->id = $pdo->lastInsertId();

	return $account;
}

function getAccounts() {

	$stmt = pdo()->prepare("SELECT * FROM account");
	$stmt->execute();
	
	return $stmt->fetchAll();
}

function getAccount($id) {

	$stmt = pdo()->prepare("
		SELECT *
		FROM account a LEFT JOIN person p ON (a.id = p.account_id)
		WHERE a.id = ?
		");

	$stmt->execute([$id]);
	
	return $stmt->fetch();
}

function getCustomers($accountId) {

	$stmt = pdo()->prepare("SELECT * FROM person WHERE account_id = ?");
	$stmt->execute([$accountId]);

	return $stmt->fetch();	
}
?>