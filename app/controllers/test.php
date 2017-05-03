<?php

	require('../../database.php');

	if (isset($_GET['asin'])){
		
		echo strlen($_GET['asin']);
	}
	else echo 'Error';

 ?>