<title>Idealo-CSV-Order-Confirmer</title>

<?php
	//Load configuration file
	require_once "config.php";
	
	//Get access token
	$accessToken = getAccessToken($url, $username, $password);	
	
	//Read csv file and set fulfillment information
	setFullfillmentInformationsCSV($csvPath, $url, $shopid, $accessToken);
	
	
	
	
	
	
	/*
	* Set carrier and trackingcode for an order
	*
	* @param string csvPath			Path and filename of the csv file, that contains the fullfillment information
	* @param string url				Sandbox or production api url
	* @param string shopid			Id of your Idealo shop
	* @param string accessToken		Access token generated with getAccessToken();
	*/
	function setFullfillmentInformationsCSV($csvPath, $url, $shopid, $accessToken){
		$file = fopen($csvPath, 'r');
		
		//Cycle throught all lines of csv file
		//CSV structure: IdealoOrderId; CarrierName; TrackingCode
		while (($line = fgetcsv($file, 0, ";")) !== FALSE) {
			setFullfillmentInformation($line[0], $line[1], $line[2], $url, $shopid, $accessToken);
		}
		
	}
	
	
	/*
	* Set carrier and trackingcode for an order
	*
	* @param string idealoOrderId	Order id that should be fulfilled
	* @param string carrier			Carrier, that delivers the package
	* @param string trackingCode	Tracking number of the package
	* @param string url				Sandbox or production api url
	* @param string shopid			Id of your Idealo shop
	* @param string accessToken		Access token generated with getAccessToken();
	*/
	function setFullfillmentInformation($idealoOrderId, $carrier, $trackingCode, $url, $shopid, $accessToken){
		echo $url . "/api/v2/shops/" . $shopid . "/orders/" . $idealoOrderId . "/fulfillment";
		
		$ch = curl_init();
		$payload = json_encode( array( "carrier" => $carrier, "trackingCode" => array($trackingCode)) );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($ch, CURLOPT_URL, $url . "/api/v2/shops/" . $shopid . "/orders/" . $idealoOrderId . "/fulfillment");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		   'Content-Type: application/json',
		   'Authorization: Bearer ' . $accessToken
		));
		
	   
		$result = curl_exec ($ch);
				
		echo "<pre>";
		var_dump($result);
		echo "</pre>";	
		
		return json_decode($result, true);
	}
	
	
	
	/*
	* Get Token
	*
	* @param string url				Sandbox or production api url
	* @param string username		API username
	* @param string password		API secret
	* @return string				Returns the access token as string
	*/
	function getAccessToken($url, $username, $password){
		$ch = curl_init($url . "/api/v2/oauth/token");
		curl_setopt($ch, CURLOPT_URL, $url . "/api/v2/oauth/token");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec ($ch);
		
		curl_close($ch);		
		
		return json_decode($result, true)["access_token"];	
	}