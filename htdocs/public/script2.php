<?php
//The url you wish to send the POST request to
$url = "https://grp19.ttm4135.item.ntnu.no:9019/login";

//The data you want to send via POST
$fields = [
    'username' => "admin",
    'password' => "admin",
];

//url-ify the data for the POST
$fields_string = http_build_query($fields);

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

//So that curl_exec returns the contents of the cURL; rather than echoing it
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

//execute post
$result = curl_exec($ch);
echo $result;
?>
