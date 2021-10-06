<?php

function get_domain_info($domain){
        
    // include_once(FCPATH . "scripts/simplehtmldom/simple_html_dom.php");

    // $domain = "target.ie";
    
    $url = "https://www.weare.ie/whois-result/?whois=$domain";

    //------------------------
    $aContext = array(
        'http' => array(
            'proxy'           => 'tcp://192.168.0.2:3128',
            'request_fulluri' => true,
        ),
    );
    $cxContext = stream_context_create($aContext);

    // $sFile = file_get_contents("http://www.google.com", False, $cxContext);

    // $html = file_get_html($url, False, $cxContext);
    //-------------------------

    $html_string = file_get_contents($url);

    $results = explode("<div class=\"page-content\">", $html_string);
    if(sizeof($results)>1){
        $results = explode("</div>", $results[1]);
        $domain_info = $results[0];
    } else {
        return false;
    }
    
    
    // $html = file_get_html($url);

    // $result_info = $html->find("div.page-content");
    // foreach($result_info as $info){
    //     $domain_info = $info->innertext;
    // }

    // echo "------------------------------------";

    // $domain_info = <<<div
    //     <div class="inner-container padding-top-bottom">
    //     <p>  The result of your domain name search is detailed below:     </p>
    //     You can see your Whois result below:
    //     <p>Domain Name:             target.ie<br>Registry Registrant ID:                  494481-IEDR<br>Registrant Name:                  SOMA MARKETING LIMITED<br>Registry Domain ID:                  702424-IEDR<br>Registrar WHOIS Server:                  whois.weare.ie<br>Registrar URL:                  https://premiumdomains.ie<br>Updated Date:                  26/09/2021<br>Creation Date:                 07/08/2013<br>Registry Expiry Date:          08/08/2022<br>Registrar:                  Premium Domains<br>Registrar Abuse Contact Email:                 support@domainname.ie<br>Registrar Abuse Contact Phone:                   +353.12916198<br>Domain Status:                  Registered<br>Registry Admin ID:                  63833-IEDR<br>Registry Tech ID:                  115578-IEDR<br>Name Server:                  ns1.eftydns.com, ns2.eftydns.com<br>DNSSEC:                  Unsigned<br>    </p>
    //     </div>
    // div;


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
            $date->add(new DateInterval('P70D'));
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