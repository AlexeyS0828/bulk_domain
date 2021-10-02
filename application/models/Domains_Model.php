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

    public function read_domains_csv(){

        // read excel file
        $filename = FCPATH . "data/domain_list.csv";
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
}