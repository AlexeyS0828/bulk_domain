<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Domains_Model extends CI_Model{
    private $_tblname = "tbldomains";

    public function index(){
        $query = $this->db
                        ->order_by('expired, Drop_Date')
                        ->get($this->_tblname);
        return $query->result();
    }

    public function add_domain($data){
        $query = $this->db->get_where($this->_tblname, array('Domain_Name' => $data["Domain_Name"]));
        $result = $query->result();
        if (sizeof($result)>0){
            $this->update_domain($result[0]->id, $data);
        } else {
            $this->db->insert($this->_tblname, $data);
        }
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
        $date->add(new DateInterval('P1D'));
        $dropdate = $date->format("Y-m-d");

        $date = new DateTime();
        $date->sub(new DateInterval('P1M'));
        $scandate = $date->format("Y-m-d H:i:s");
        
        $this->db->select('id, Domain_Name, Drop_Date, ScanDate, expired, 0 prescan')
                ->from($this->_tblname)
                ->where("expired=FALSE")
                ->where("Drop_Date <=", $dropdate)
                ->or_where("expired=TRUE")
                ->where("ScanDate <=", $scandate);
        $subQuery1 = $this->db->get_compiled_select();

        $date = new DateTime();
        $date->add(new DateInterval('P5D'));
        $dropdate_before5 = $date->format("Y-m-d");

        $this->db->select('id, Domain_Name, Drop_Date, ScanDate, expired, 1 prescan')
                ->from($this->_tblname)
                ->where("expired=FALSE")
                ->where("Drop_Date >", $dropdate)
                ->where("Drop_Date <=", $dropdate_before5);
        $subQuery2 = $this->db->get_compiled_select();

        $query = $this->db->query($subQuery1 . " UNION " . $subQuery2);
        
        return $query->result();       
    }

    public function update_domain($id, $data){
        $this->db->set($data)
                ->where('id', $id)
                ->update($this->_tblname);
    }

    public function drop_date_format(){
        $query = $this->db->get($this->_tblname);
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

    public function fetch_valid_new($domains){
        $new_domains = [];
        foreach($domains as $domain){
            $domain = trim($domain);
            if($domain == "")
                continue;
            $query = $this->db->get_where("tbldomains", array("Domain_Name"=>$domain));
            if (sizeof($query->result())>0){
                continue;
            }
            $new_domains[] = $domain;
        }

        return $new_domains;
    }

    public function drop_domain($id){
        $this->db->where('id', $id)
                ->delete($this->_tblname);
    }

    public function convert_drop_date(){
        $domains = $this->db->where("expired", 0)
                            ->get($this->_tblname)
                            ->result();
        $count = 0;
        foreach($domains as $domain){
            if($domain->expired == 1 || $domain->Registry_Expiry_Date == NULL){
                continue;
            }
            $count++;
            $date = DateTime::createFromFormat("d/m/Y", $domain->Registry_Expiry_Date);
            $date->add(new DateInterval('P80D'));
            $result_date = $date->format('Y-m-d');
            $this->update_domain($domain->id, array("Drop_Date" => $result_date));
        }
        echo $count;
    }

}