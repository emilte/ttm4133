<?php

for ($i = 1; $i <= 44; $i++) {
    $s = "".$i;
    if ($i < 10) {
        $s = "0".$i;
    }

    $url = "https://grp".$s.".ttm4135.item.ntnu.no:90".$s."/login";

    $data = array('username' => 'admin', 'password' => 'admin');

    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) { /* Handle error */ }

    if ($i == "19" || $i == "43") {
        var_dump($result);
        echo $result;
    }
}

?>
