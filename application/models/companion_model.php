<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Companion_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    function get_unassigned_companions()
    {
    	//SEE http://stackoverflow.com/questions/4660871/mysql-select-all-items-from-table-a-if-not-exist-in-table-b
		$query = $this->db->query('SELECT * FROM companions c WHERE NOT EXISTS (SELECT 1 FROM companions_groups cg WHERE c.id = cg.id)');
        return $query;
    }
    
    function assignCompanionToGroup($id, $groupId)
    {
    	$data = array(
		   'companion_id' => $id,
		   'group_id' => $groupId
		);
    	$this->db->insert('companions_groups', $data);
    	
    	if($this->db->affected_rows() == 0)
    		return false;
    	return true;
    }
}