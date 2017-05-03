<?php 
	
	require('../../database.php');

	if(isset($_POST['ASIN'])){

		$ASIN = $_POST['ASIN'];

//checking if asin is present
		$conn = DB();
		$query = $conn -> prepare("SELECT ASIN FROM Amazon WHERE ASIN = :ASIN");
		$query -> execute(array(':ASIN' => $ASIN));

		if($query -> rowCount()==0){

			if(isset($_POST['Title']) && isset($_POST['MPN']) && isset($_POST['Price'])){

				$Title = $_POST['Title'];
				$MPN = $_POST['MPN'];
				$Price = $_POST['Price'];

				$conn = DB();

				$stmt = "INSERT INTO Amazon (ASIN, Title, MPN, Price, date_added)

						VALUES (:ASIN, :Title, :MPN, :Price, NOW())";

				$query = $conn -> prepare($stmt);
				$query -> execute(array(
					':ASIN' => $ASIN,
					':Title' => $Title,
					':MPN' => $MPN,
					':Price' => $Price));

				echo 'true';
			}
		}

		else echo 'false';

		exit();
	}

	if(isset($_GET['ASIN'])){

		$AWSAccessKeyId = 'AKIAIOWFZ4KTTJAKNLFQ';
		$AssociateTag = 'q0d9b-20';
		$ItemId = $_GET['ASIN'];
		$Operation = 'ItemLookup';
		$ResponseGroup = 'OfferFull';
		$ResponseGroup = 'ItemAttributes';
		$Service = 'AWSECommerceService';
		$Timestamp_encoded = urlencode(gmdate("Y-m-d\TH:i:s\Z", time()));
		$Version = '2013-08-01';

		$secretKey = 'DL6rUpqfXpMuQEVmiGGYgudKa0ePlbaR8OX4OjHB';



		$myString =  'AWSAccessKeyId=' . $AWSAccessKeyId . '&AssociateTag=' . $AssociateTag .'&ItemId=' . $ItemId . '&Operation=' . $Operation . '&ResponseGroup=' . $ResponseGroup . '&Service=' . $Service . '&Timestamp=' . $Timestamp_encoded . '&Version=' . $Version;

		$stringToSign = "GET\nwebservices.amazon.com\n/onca/xml\n" . $myString;

		$signature = base64_encode(hash_hmac('SHA256', $stringToSign, $secretKey, True));
		$urlEncodedSignature = urlencode($signature);
		$requestUrl = 'http://webservices.amazon.com/onca/xml?' . $myString . '&Signature=' . $urlEncodedSignature;

		$response = file_get_contents($requestUrl);
		$parsedXml = simplexml_load_string($response);

		$dom = new DOMDocument;
		$dom -> loadXml($response);

		$totalOffers = $dom -> getElementsByTagName('Item')->length;

		if($totalOffers!=0){

			$asin = $parsedXml -> Items -> Item -> ASIN;
			$title = $parsedXml -> Items -> Item -> ItemAttributes -> Title;
			$mpn = $parsedXml -> Items -> Item -> ItemAttributes -> MPN;
			$price = $parsedXml -> Items -> Item -> ItemAttributes -> ListPrice -> FormattedPrice;

			$arr = array("Exists" => "true", "ASIN" => "$asin", "Title" => "$title", "MPN" => "$mpn", "Price" => "$price");
			echo json_encode($arr);
		}
		else{

			$arr = array("Exists" => "false");
			echo json_encode($arr);
		}
		
		exit();
	}

	else{

		$conn = DB();

		$result = $conn -> query('SELECT * FROM Amazon ORDER BY date_added ASC');

		if($result -> rowCount() !== 0){
			while($row = $result -> fetch()){
				$asin = $row['ASIN'];
				$title = $row['Title'];
				$mpn = $row['MPN'];
				$price = $row['Price'];

				include('../views/row.part.php');

			}
		}
	}

?>