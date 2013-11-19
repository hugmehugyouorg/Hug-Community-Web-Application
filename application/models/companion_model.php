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
    
    function add_audio($audioNum, $text, $isMessage, $mp3, $size)
    {
    	//check if $audioNum exists in companion_says_audio
    	$exists = $this->db->get_where('companion_says_audio', array('audio_num' => $audioNum))->result();
    	
    	//if couldn't find we will be doing an insert
    	if(!$exists) {
    	
    		//update $mp3 into companion_audio
    		$data = array(
			   'data' => $mp3
			);
    		$this->db->insert('companion_audio', $data);
    	
    		if( $this->db->affected_rows() == 0 )
    			return false;
    		
    		//TODO: this is probably not perfect, use query instead 
    		$companionAudioId = $this->db->insert_id();
    		
    		//update $text and $isMessage into companion_says
    		$data = array(
			   'text' => $text,
			   'is_message' => $isMessage
			);
    		$this->db->insert('companion_says', $data);
    		
    		if( $this->db->affected_rows() == 0 )
    		{	
    			$this->db->where('id', $companionAudioId);
    			$this->db->delete('companion_audio'); 
    			return false;
    		}
    			
    		//TODO: this is probably not perfect, use query instead 
    		$companionSaysId = $this->db->insert_id();
    		
    		//update $audioNum into companion_says_audio
    		$data = array(
			   'audio_num' => $audioNum,
			   'companion_audio_id' => $companionAudioId,
			   'companion_says_id' => $companionSaysId
			);
			
			$this->db->insert('companion_says_audio', $data);
    		
    		if( $this->db->affected_rows() == 0 )
    		{	
    			$this->db->where('id', $companionAudioId);
    			$this->db->delete('companion_audio');
    			$this->db->where('id', $companionSaysId);
    			$this->db->delete('companion_says');  
    			return false;
    		}
    		
    		//TODO: this is probably not perfect, use query instead 
    		$companionSaysAudioId = $this->db->insert_id();
    		
    		return $companionSaysAudioId;
    		
    	}
    	else //we will be updating
    	{
    		$companionSaysAudio = $exists[0];
    		
    		//update $mp3 into companion_audio
    		$data = array(
    			'data' => $mp3
			);
			$this->db->where('id', $companionSaysAudio->companion_audio_id);
    		$result = $this->db->update('companion_audio', $data);
    	
    		if( !$result )
    			return false;
    		
    		//update $text and $isMessage into companion_says
    		$data = array(
			   	'text' => $text,
			   	'is_message' => $isMessage
			);
			$this->db->where('id', $companionSaysAudio->companion_says_id);
    		$result = $this->db->update('companion_says', $data);
    		
    		if( !$result )
    			return false;
    			
    		return $companionSaysAudio->id;
    		
    	}
    }
    
    /**
    * TODO: add cache busting on update by adding a new updated_at field to the companion_audio table
    *		and then append it as a query string to the audio url
    **/
    public function getAudioURL($audioNum, $forceUpdate = FALSE)
	{
		$audio = $this->get_audio_association($audioNum);
		if(!$audio)
		{
			throw new Exception("Audio does not exist for audio num: ".$audioNum);
		}
		else
		{
			$basePath = 'assets/uploads/media/audio';
			$filePath = $basePath.'/'.$audioNum.'.mp3';
			
			//make directory if does not exist
			if (!file_exists($basePath)) {
				$dirExists = mkdir($basePath, 0777, true);
			}
			else
				$dirExists = true;
				
			//if directory doesn't exist after trying to create it then error out
			if(!$dirExists)
				throw new Exception("Can't make audio directory: ".$basePath);
			
			$this->load->helper('file');
			
			//check if stored as a file yet
			$audioFileInfo = get_file_info($filePath, array('readable','writable'));
			
			//if audio found
			if( $audioFileInfo )
			{
				//if readable and not forcing and update then return the file path
				if($audioFileInfo['readable'] && !$forceUpdate)
					return base_url($filePath);
					
				//if forcing and update and not writeable then error out
				if($forceUpdate && !$audioFileInfo['writable'])
					throw new Exception("Can't write audio to file: ".$filePath);
			}
			
			//get audio data
			$audioData = $audio = $this->get_audio_data($audio);
			
			//if couldn't retrieve it then error out
			if(!$audioData)
				throw new Exception("Couldn't read audio data from the database for audio num: ".$audioNum);
				
			if(!write_file($filePath, $audioData))
				throw new Exception("Can't write audio to file: ".$filePath);
			
			return base_url($filePath);
		}
	}
    
    protected function get_audio_association($audioNum)
    {
    	//check if $audioNum exists in companion_says_audio
    	$result = $this->db->get_where('companion_says_audio', array('audio_num' => $audioNum))->result();
    	
    	if($result)
    		return $result[0];
    	
    	return null;
    }
    
    protected function get_audio_data($companionSaysAudio)
    {
    	$this->db->where('id', $companionSaysAudio->companion_audio_id);
    	$result = $this->db->get('companion_audio')->result();
    	
		if( !$result )
			return null;
			
		$companionAudio = $result[0];
		
		return $companionAudio->data;
    }
}