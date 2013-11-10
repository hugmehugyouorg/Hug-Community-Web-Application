<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Companion_model extends CI_Model {

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    function get_unassigned_companions()
    {
    	//SEE http://stackoverflow.com/questions/354002/mysql-select-where-not-in-table
		$query = $this->db->query('SELECT companions.id, companions.name, companions.description FROM companions LEFT JOIN companions_groups ON companions.id = companions_groups.companion_id WHERE companions_groups.companion_id is NULL');
        return $query;
    }
    
    function assignCompanionToGroup($id, $groupId)
    {
    	$data = array(
		   'companion_id' => $id,
		   'group_id' => $groupId
		);
    	$this->db->insert('companions_groups', $data);
    	
    	if( $this->db->affected_rows() == 0 )
    		return false;
    	return true;
    }
}