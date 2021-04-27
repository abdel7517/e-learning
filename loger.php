<?php 

$cURLConnection = curl_init();

curl_setopt($cURLConnection, CURLOPT_URL, 'http://localhost:8000/logerChecker');
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

$apiResponse = curl_exec($cURLConnection);
$http_code = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
curl_close($cURLConnection);


// $apiResponse - données disponibles de la requête API
$jsonArrayResponse = json_decode($apiResponse);
echo $apiResponse;