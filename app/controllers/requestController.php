<?php 
	
	require('../../database.php');

	class Amazon{

		function getTimestamp(){
			return urlencode(gmdate("Y-m-d\TH:i:s\Z", time()));
		}

		function generateUrl($ItemId, $Operation, $ResponseGroup, $Service){

			require('../../apiConfig.php');

			$prepend = "GET\nwebservices.amazon.com\n/onca/xml\n";

			$prependUrl = "http://webservices.amazon.com/onca/xml?";
			
			$url =  'AWSAccessKeyId=' . AWSAccessKeyId .
					'&AssociateTag=' . AssociateTag .
					'&ItemId=' . $ItemId .
					'&Operation=' . $Operation .
					'&ResponseGroup=' . $ResponseGroup .
					'&Service=' . $Service .
					'&Timestamp=' . $this -> getTimestamp() .
					'&Version=' . Version;

			$signature = urlencode(base64_encode(hash_hmac('SHA256', $prepend . $url, SecretKey, True)));

			$requestUrl = $prependUrl . $url . '&Signature=' . $signature;

			return $requestUrl;
		}
	}

	if(isset($_POST['ASIN'])){

		$ASIN = $_POST['ASIN'];

		if(!existsInDB($ASIN)){

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

		$ASIN = $_GET['ASIN'];

		if(strlen($ASIN)==10){
			$amazon = new Amazon();

			$requestUrl = $amazon -> generateUrl($ASIN, "ItemLookup", "ItemAttributes", "AWSECommerceService");

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

				$arr = array("Exist" => "true", "ASIN" => "$asin", "Title" => "$title", "MPN" => "$mpn", "Price" => "$price");
				echo json_encode($arr);
			}

			else{

				$arr = array("Exist" => "false");
				echo json_encode($arr);
			}
		}

		else{
			$arr = array("Exist" => "length");
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