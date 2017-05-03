<?php

	define('HOST', 'localhost');
	define('DB', 'quantum-db');
	define('CHARSET', 'utf8mb4');
	define('USER', 'root');
	define('PASS', 'root');

	function DB(){
		$db = new PDO('mysql:host='.HOST.';dbname='.DB.';charset='.CHARSET, USER, PASS);
		return $db;
	}

	function existsInDB($ASIN){
		$conn = DB();
		$query = $conn -> prepare("SELECT ASIN FROM Amazon WHERE ASIN = :ASIN");
		$query -> execute(array(":ASIN" => $ASIN));

		if($query -> rowCount()==0){
			return false;
		}
		else return true;
	}

?>