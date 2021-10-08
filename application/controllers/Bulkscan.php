<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Bulkscan extends CI_Controller {
    private $filepath = FCPATH . "data/";

    function __construct(){
        parent::__construct();
        $this->load->helper(array('domain_info'));
        $this->load->library('email');
    }

    public function index(){
        $this->load->model('Domains_Model');
        $domains = $this->Domains_Model->get_scan_domains();
        $f = fopen($this->filepath."result.txt", "a");
        fwrite($f, "start scan " . sizeof($domains) . "domains: " . date("Y-m-d H:i:s") . "\n");
        fclose($f);

        $report = "";
        $expired_report = "";
        foreach($domains as $domain){
            $domain_info = get_domain_info($domain->Domain_Name);
            $data = [];
            if($domain_info !== false){
                foreach($domain_info as $key=>$value){
                    $new_key = str_replace(" ", "_", $key);
                    $data[$new_key] = $value;
                }
                if($domain->Drop_Date == $data['Drop_Date']){
                    $report .= $domain->Domain_Name . " \n ";
                }
                $data['expired'] = 0;
            } else {
                $data['expired'] = 1;
                if($domain->expired == 0){
                    $expired_report .= $domain->Domain_Name . " \n ";
                }
            }
            $data['ScanDate'] = date("Y-m-d H:i:s");
            $this->Domains_Model->update_domain($domain->id, $data);

        }
        
        $f = fopen($this->filepath."result.txt", "a");
        fwrite($f, "completed scan:" . date("Y-m-d H:i:s") . "\n\n");
        fclose($f);
        if($expired_report != ""){
            $expired_report = "This domains are dropped. \n\n" . $expired_report;
        }
        if($report != ""){
            $report = "This domains will dropped soon." . $report;
        }
        if($report != "" || $expired_report != ""){
            $this->email->from('scan@domainname.ie', 'Bulk Scanner');
            $this->email->to('support@domainname.ie');

            $this->email->subject('These domain will expire soon');
            $this->email->message($report . "\n\n\n" . $expired_report);

            $this->email->send();
        }
        
    }

}