<?php

	class OpenWeather {
		
        function __construct() {}

		function getWeather($latitude, $longitude, $startTime, $endTime){
			$url = "https://api.getthedata.com/bng2latlong/".urlencode($easting)."/".urlencode($northing);
			$url = 'http://history.openweathermap.org/data/2.5/history/city?lat='.$latitude.'&lon='.$longitude.'&start='.$startTime.'&end='.$endTime.'&appid=e100e1293f27951ce63f34d100f68c5f';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$rawResult = curl_exec($ch);
			$jsonResult = json_decode($rawResult, true);
			curl_close($ch);
			if($jsonResult != false){				
				return $jsonResult;
			}
		}

	}

?>