<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Domains extends CI_Controller {
    private $filename = FCPATH . "data/domain_list.csv";
    function __construct(){
        parent::__construct();
        if(!$this->session->userdata('uid'))
            redirect('signin');
        $this->load->helper(array('form', 'url', 'domain_info'));
    }
    public function index(){
        $this->load->model('Domains_Model');
        $domains = $this->Domains_Model->index();
        $this->load->view('domains',['domains'=>$domains]);
    }

    public function read_domains(){
        global $domains;
        $this->load->model('Domains_Model');
        if(file_exists($this->filename)){
            $domains = $this->Domains_Model->read_domains_csv($this->filename);
        }else{
            $domains = array();
        }
        $success = 0;
        $fail = 0;
        $failed_domains = [];
        foreach($domains as $domain){
            $domain_info = get_domain_info($domain);
            $data = [];
            if($domain_info !== false){
                foreach($domain_info as $key=>$value){
                    $new_key = str_replace(" ", "_", $key);
                    $data[$new_key] = $value;
                }
                $success++;
            } else {
                $fail++;
                $failed_domains[] = $domain;
                $data['Domain_name'] = $domain;
                $data['expired'] = 1;
            }
            $this->Domains_Model->add_domain($data);
            
        }
        if(file_exists($this->filename)){
            unlink($this->filename);
        }
        
        $data = array(
           'count' => sizeof($domains),
           'success' => $success,
           'fail' => $fail,
           'fails' => $failed_domains
        );

        $this->load->view('read_domains',['data'=>$data]);
    }

    

    public function add_domains(){
        if(file_exists($this->filename)){
            $error = array('error' => "Exists uploaded csv file. Click <b>[Scan CSV]</b> button on <b>DomainList</b> page.");
        } else {
            $error = array();
        }
        $this->load->view('add_domains', $error);
    }

    public function do_upload(){
        $config['upload_path'] = FCPATH . "data/";
        $config['allowed_types'] = 'csv';

        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('domains_csv'))
        {
                $error = array('error' => $this->upload->display_errors());
                $this->load->view('add_domains', $error);
        }
        else
        {
                $data = array('upload_data' => $this->upload->data());
                rename($data['upload_data']['full_path'], $this->filename);
                $this->read_domains();
        }
    }
}
