<?php

include_once("./simplehtmldom/simple_html_dom.php");

$domain = "target.ie";

$url = "https://www.weare.ie/whois-result/?whois=$domain";


global $domains;
echo $domains;
exit;

//------------------------
$aContext = array(
    'http' => array(
        'proxy'           => 'tcp://192.168.0.2:3128',
        'request_fulluri' => true,
    ),
);
$cxContext = stream_context_create($aContext);

$sFile = file_get_contents("http://www.google.com", False, $cxContext);

$html = file_get_html($url, False, $cxContext);
//-------------------------

$html = file_get_html($url);

$result_info = $html->find("div.page-content");
foreach($result_info as $info){
     $domain_info = $info->innertext;
}

echo "------------------------------------";

$domain_info_1 = <<<div
    <div class="inner-container padding-top-bottom">
    <p>  The result of your domain name search is detailed below:     </p>
    You can see your Whois result below:
    <p>Domain Name:             target.ie<br>Registry Registrant ID:                  494481-IEDR<br>Registrant Name:                  SOMA MARKETING LIMITED<br>Registry Domain ID:                  702424-IEDR<br>Registrar WHOIS Server:                  whois.weare.ie<br>Registrar URL:                  https://premiumdomains.ie<br>Updated Date:                  26/09/2021<br>Creation Date:                 07/08/2013<br>Registry Expiry Date:          08/08/2022<br>Registrar:                  Premium Domains<br>Registrar Abuse Contact Email:                 support@domainname.ie<br>Registrar Abuse Contact Phone:                   +353.12916198<br>Domain Status:                  Registered<br>Registry Admin ID:                  63833-IEDR<br>Registry Tech ID:                  115578-IEDR<br>Name Server:                  ns1.eftydns.com, ns2.eftydns.com<br>DNSSEC:                  Unsigned<br>    </p>
    </div>
div;


$s = explode("<p>", $domain_info);
$info = explode("<br>", $s[2]);
unset($info[sizeof($info) - 1]);


$keys = [
    "Domain Name",
    "Registrant Name",
    "Registrar",
    "Creation Date",
    "Updated Date",
    "Registry Expiry Date", // $ekey
    "Drop Date",            // $dkey = $ekey + 70
    "Name Server",
];

$ekey = $keys[5];
$dkey = $keys[6];


$results = [];
foreach($info as $item){
    list($key, $value) = explode(":", $item, 2);
    if (in_array($key, $keys)){
        $results[$key] = trim($value);
    }
}

if (array_key_exists($ekey, $results)){
    $date = new DateTime($results[$ekey]);
    $date->add(new DateInterval('P70D'));
    $results[$dkey] = $date->format('d/m/Y');
}


echo "\n\n\n";
var_dump($results);


/*

Domain Name:             target.ie
Registry Registrant ID:                  494481-IEDR
Registrant Name:                  SOMA MARKETING LIMITED
Registry Domain ID:                  702424-IEDR
Registrar WHOIS Server:                  whois.weare.ie
Registrar URL:                  https://premiumdomains.ie
Updated Date:                  26/09/2021
Creation Date:                 07/08/2013
Registry Expiry Date:          08/08/2022
Registrar:                  Premium Domains
Registrar Abuse Contact Email:                 support@domainname.ie
Registrar Abuse Contact Phone:                   +353.12916198
Domain Status:                  Registered
Registry Admin ID:                  63833-IEDR
Registry Tech ID:                  115578-IEDR
Name Server:                  ns1.eftydns.com, ns2.eftydns.com
DNSSEC:                  Unsigned

*/