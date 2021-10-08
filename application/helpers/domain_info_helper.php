<?php

function get_domain_info($domain){
    // include_once(FCPATH . "scripts/simplehtmldom/simple_html_dom.php");

    
    $url = "https://www.weare.ie/whois-result/?whois=$domain";

    //------------------------
    // $auth = base64_encode('LOGIN:PASSWORD');
    // $auth = base64_encode('xaytvrtt:skaqc0kxmt37');
    // $aContext = array(
    //     'http' => array(
    //         'proxy'           => 'tcp://' . get_random_proxy(),
    //         'request_fulluri' => true,
    //         'header'          => "Proxy-Authorization: Basic $auth",
    //     ),
    // );
    // $cxContext = stream_context_create($aContext);
    
    // $html_string = file_get_contents($url, false, $cxContext);
    //--------------------------

    $html_string = file_get_contents($url);


    $results = explode("<div class=\"page-content\">", $html_string);
    if(sizeof($results)>1){
        $results = explode("</div>", $results[1]);
        $domain_info = $results[0];
    } else {
        return false;
    }
    
    

    $s = explode("<p>", $domain_info);
    if(sizeof($s)<3){
        return false;
    }
    $info = explode("<br>", $s[2]);
    unset($info[sizeof($info) - 1]);

    $keys = [
        "Domain Name",             // namekey
        "Registrant Name",
        "Registrar",
        "Creation Date",
        "Updated Date",
        "Registry Expiry Date",    // ekey
        "Drop Date",               // dkey
        "Name Server",
    ];

    $ekey = $keys[5];
    $dkey = $keys[6];
    $namekey = $keys[0];

    $results = [];
    foreach($info as $item){
        try{
            list($key, $value) = explode(":", $item, 2);
        }catch(Exception $ex){
            continue;
        }
        if (in_array($key, $keys)){
            $results[$key] = trim($value);
        }
    }

    if (array_key_exists($ekey, $results)){
        try{
            // $date = new DateTime($results[$ekey]);
            $date = DateTime::createFromFormat("d/m/Y", $results[$ekey]);
            $date->add(new DateInterval('P80D'));
            $results[$dkey] = $date->format('Y-m-d');
        } catch(Exception $e){
            echo $domain . " --> ---" .$results[$ekey]. "----" . $e->getMessage() . "<br>";
        }
    }
    
    if(!array_key_exists($namekey, $results) || trim($results[$namekey]) == ""){
        return false;
    }
    return $results;
}


function get_random_proxy(){
    $proxies_file = FCPATH . "data/proxies.txt";
    $f = fopen($proxies_file, "r");
    $proxies = array();
    while(!feof($f)){
        $item = trim(fgets($f));
        if ($item == "")
            continue;
        $proxies[] = $item;
    }
    fclose($f);
    if(sizeof($proxies) == 0){
        return false;
    }
    $proxy = $proxies[random_int(0, sizeof($proxies)-1)];
    return $proxy;
}