<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduler_Model extends CI_Model{
    private $_tblname = "tblscheduler";

    public function index(){
        $query = $this->db->get($this->_tblname);
        return $query->result();
    }


    public function add_new_scheduler($domains){
        $sid = $this->db->select_max('scheduler_id')
                ->get($this->_tblname)
                ->result();
        $scheduler_id = intval($sid[0]->scheduler_id) + 1;
        $count = 0;
        foreach($domains as $domain){
            $query = $this->db->get_where($this->_tblname, array("Domain_Name"=>$domain));
            if (sizeof($query->result())>0){
                continue;
            }
            $data = array(
                'Domain_Name' => $domain,
                'scheduler_id' => $scheduler_id
            );
            $this->db->insert($this->_tblname, $data);
            $count++;
        }
        if($count==0){
            return false;
        }
        return $scheduler_id;
    }


    public function fetch_domains($scheduler_id, $limit){
        $domains = [];
        $result = $this->db->select('Domain_Name')
                            ->where('scheduler_id', $scheduler_id)
                            ->limit($limit)
                            ->get($this->_tblname)->result_array();
        foreach($result as $item){
            $domains[] = $item['Domain_Name'];
        }
        return $domains;
    }

    public function remove_domains($domains){
        foreach($domains as $domain){
            $this->db->where("Domain_Name", $domain)
                ->delete($this->_tblname);
        }
    }

    public function fetch_existing_scheduler(){
        return $this->db->select('count(*) count, scheduler_id')
                        ->group_by('scheduler_id')
                        ->get($this->_tblname)
                        ->result();
    }

}