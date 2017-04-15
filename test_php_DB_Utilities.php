<?php
include "PHP_DB_Utilities.php";

$db_hostname = "localhost";
$db_username = "shohei";
$db_password = "coin1441";
$db_name = "testDB";

$db_table = "testBT";
/*
id INT AUTOINCREMENT PRI
name VARCHAR
age INT
*/

// test class of database_utilities_oop
$sql_oop = new database_utilities_oop($db_hostname, $db_username, $db_password, $db_name);

// get data
$keys = ["id", "name", /*"age"*/];
//$sql_oop->getData($db_table, $keys);
//$sql_oop->getRecentData($db_table, $keys);

// add data
$data = [
	[["id", "name", "age"], ["1", "shohei", 20]],
	[["id", "name", "age"], ["2", "katsuta", 23]],
	[["id", "name", "age"], ["3", "high", 50]],
];
//$sql_oop->insertData($db_table, $data);

// delete data
$deleted = [
	[["name", "age"], ["shohei", "20"]],
	[["id"], ["2"]],
];
//$sql_oop->deleteData($db_table, $deleted);

// update data
$updated = [
	["key" => "name", "value" => "shohei"],
	["key" => "id", "value" => "1"],
];
$up = [
	[["id"], ["2"]],
	[["name", "age"], ["high", "50"]],
];
//$sql_oop->updateData($db_table, $up, $updated);


// test class of database_utilities_pdo
$sql_pdo = new database_utilities_pdo($db_hostname, $db_username, $db_password, $db_name);

// get data
$keys = ["id", "name", /*"age"*/];
$sql_pdo->getData($db_table, $keys);
//$sql_pdo->getRecentData($db_table, $keys);

// add data
$data = [
	[["id", "name", "age"], ["1", "shohei", 20]],
	[["id", "name", "age"], ["2", "katsuta", 23]],
	[["id", "name", "age"], ["3", "high", 50]],
];
//$sql_pdo->insertData($db_table, $data);

// delete data
$deleted = [
	[["name", "age"], ["high", "50"]],
	[["id"], ["2"]],
];
//$sql_pdo->deleteData($db_table, $deleted);

// update data
$updated = [
	["key" => "name", "value" => "shohei"],
	["key" => "id", "value" => "4"],
];
$up = [
	[["id"], ["2"]],
	[["name", "age"], ["high", "50"]],
];
//$sql_pdo->updateData($db_table, $up, $updated);
?>