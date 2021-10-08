<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Domains extends CI_Controller {
    private $filename = FCPATH . "data/domain_list.csv";
    function __construct(){
        parent::__construct();
        if(!$this->session->userdata('uid'))
            redirect('signin');
        $this->load->helper(array('form', 'url', 'domain_info'));
        $this->load->model('Domains_Model');
        $this->load->model('Scheduler_Model');
    }
    public function index(){
        $domains = $this->Domains_Model->index();
        $this->load->view('domains',['domains'=>$domains]);
    }

    public function read_domains(){
        // global $domains;
        if(file_exists($this->filename)){
            $domains = $this->Domains_Model->read_domains_csv($this->filename);
        }else{
            $domains = array();
        }

        $scheduler_id = $this->Scheduler_Model->add_new_scheduler($domains);
        if(!$scheduler_id){
            $error = array('error' => "There is no domains to add.");
            $this->load->view('add_domains', $error);
            return;
        }

        $data = array(
            'count' => sizeof($domains),
            'success' => 0,
            'fail' => 0,
            'fails' => [],
            'scheduler_id' => $scheduler_id
         );
        // $data = $this->scan_domains($domains);
        $this->load->view('read_domains',['data'=>$data]);
    }

    private function scan_domains($domains){
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
                $data['Domain_Name'] = $domain;
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

        return $data;
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

    public function do_upload_text(){
        $post_data = $this->input->post();
        $domains_str = $post_data['domains'];
        $domains = explode("\n", trim($domains_str));
        $new_domains = $this->Domains_Model->fetch_valid_new($domains);
        $scheduler_id = $this->Scheduler_Model->add_new_scheduler($new_domains);
        if(!$scheduler_id){
            $error = array('error' => "There is no domains to add.");
            $this->load->view('add_domains', $error);
            return;
        }
        // $data = $this->scan_domains($new_domains);
        $data = array(
            'count' => sizeof($new_domains),
            'success' => 0,
            'fail' => 0,
            'fails' => [],
            'scheduler_id' => $scheduler_id
         );
        $this->load->view('read_domains',['data'=>$data]);
    }

    public function run_scheduler(){
        $limit = 50;
        $post_data = $this->input->post();
        $scheduler_id = $post_data['id'];
        $domains = $this->Scheduler_Model->fetch_domains($scheduler_id, $limit);
        $data = $this->scan_domains($domains);
        $this->Scheduler_Model->remove_domains($domains);
        $data['scheduler_id'] = $scheduler_id;
        echo json_encode(array("result" => $data));
    }

    public function run_existing_scheduler(){
        $schedulers = $this->Scheduler_Model->fetch_existing_scheduler();
        if(sizeof($schedulers) > 0){
            $scheduler_id = $schedulers[0]->scheduler_id;
            $count = $schedulers[0]->count;
            $data = array(
                'count' => $count,
                'success' => 0,
                'fail' => 0,
                'fails' => [],
                'scheduler_id' => $scheduler_id,
                'message' => sizeof($schedulers) . "schedulers exists"
             );
            $this->load->view('read_domains',['data'=>$data]);
        }
    }

    public function drop_domain(){
        $id = $this->input->post('id');
        $this->Domains_Model->drop_domain($id);
        echo 'success';
    }
}
