<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Domains_Model extends CI_Model{
    private $_tbname = "tbldomains";

    public function index(){
        $query = $this->db->get($this->_tbname);
        return $query->result();
    }

    public function add_domain($data){
        $this->db->insert($this->_tbname, $data);
    }

    public function read_domains_csv($filename){

        // read excel file
        $row = 1;
        $domains = array();
        if (($handle = fopen($filename, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
                $num = count($data);
                // echo "<p> $num fields in line $row: <br /></p>\n";
                $row++;
                for ($c=0; $c < $num; $c++) {
                    // echo $data[$c] . "<br />\n";
                    $domain = $data[$c];
                    $query = $this->db->get_where("tbldomains", array("Domain_Name"=>$domain));
                    if (sizeof($query->result())>0){
                        continue;
                    }
                    $domains[] = $domain;
                }
            }
            fclose($handle);
        }
        return $domains;
    }

    public function get_scan_domains(){
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $dropdate = $date->format("Y-m-d");

        $date = new DateTime();
        $date->sub(new DateInterval('P1M'));
        $scandate = $date->format("Y-m-d H:i:s");
        
        $this->db->select('id, Domain_Name, Drop_Date, ScanDate, expired')
                ->where("expired=FALSE")
                ->where("Drop_Date <=", $dropdate)
                ->or_where("expired=TRUE")
                ->where("ScanDate <=", $scandate);
        $query = $this->db->get($this->_tbname);

        return $query->result();
       
    }

    public function update_domain($id, $data){
        $this->db->set($data)
                ->where('id', $id)
                ->update($this->_tbname);
    }

    public function drop_date_format(){
        $query = $this->db->get($this->_tbname);
        $rows = $query->result();
        foreach($rows as $row){
            if($row->Drop_Date != null){
                $date = DateTime::createFromFormat("d/m/Y", $row->Drop_Date);
                $dropdate = $date->format('Y-m-d');
                $data = array('Drop_Date'=>$dropdate);
                $this->update_domain($row->id, $data);
            }
        }
    }


}