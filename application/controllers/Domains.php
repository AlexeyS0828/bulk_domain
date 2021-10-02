<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Domains extends CI_Controller {
    //Validating login
    function __construct(){
    parent::__construct();
    if(!$this->session->userdata('uid'))
        redirect('signin');
    }
    public function index(){
        $this->load->model('Domains_Model');
        $domains = $this->Domains_Model->index();
        $this->load->view('domains',['domains'=>$domains]);
    }

    public function read_domains(){
        global $domains;
        $this->load->model('Domains_Model');
        $domains = $this->Domains_Model->read_domains_csv();

        $success = 0;
        $fail = 0;
        $failed_domains = [];
        foreach($domains as $domain){
            $domain_info = $this->get_domain_info($domain);
            if($domain_info !== false){
                $data = [];
                foreach($domain_info as $key=>$value){
                    $new_key = str_replace(" ", "_", $key);
                    $data[$new_key] = $value;
                }
                $this->Domains_Model->add_domain($data);
                $success++;
            } else {
                $fail++;
                $failed_domains[] = $domain;
            }
            if($success + $fail >=5){
                break;
            }
        }
        
        $data = array(
           'count' => sizeof($domains),
           'success' => $success,
           'fail' => $fail,
           'fails' => $failed_domains
        );

        $this->load->view('read_domains',['data'=>$data]);
    }

    public function get_domain_info($domain){
        
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
            list($key, $value) = explode(":", $item, 2);
            if (in_array($key, $keys)){
                $results[$key] = trim($value);
            }
        }

        if (array_key_exists($ekey, $results)){
            try{
                // $date = new DateTime($results[$ekey]);
                $date = DateTime::createFromFormat("d/m/Y", $results[$ekey]);
                $date->add(new DateInterval('P70D'));
                $results[$dkey] = $date->format('d/m/Y');
            } catch(Exception $e){
                echo $domain . " --> ---" .$results[$ekey]. "----" . $e->getMessage() . "<br>";
            }
        }
        
        if(!array_key_exists($namekey, $results) || trim($results[$namekey]) == ""){
            return false;
        }
        return $results;
    }
}
