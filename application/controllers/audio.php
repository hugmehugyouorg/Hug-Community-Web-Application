<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Audio extends MY_Controller {

	public function upload()
	{
		if (!$this->ion_auth->logged_in())
		{
			redirect('sign_in', 'refresh');
		}
		
		//if no id or not an admin
		if(!$this->ion_auth->is_admin())
			redirect('dashboard', 'refresh');
					
		//validate form input
		$this->form_validation->set_rules('text', 'Text', 'required|xss_clean');
		$this->form_validation->set_rules('isMessage', 'Is Message?', 'xss_clean|callback_is_message_check');
		
		if ($this->form_validation->run() === TRUE)
		{
			$config['upload_path'] = '/tmp/';
			$config['allowed_types'] = 'mp3';
			$config['encrypt_name'] = TRUE;
			
			$this->load->library('upload', $config);
	
			if ( ! $this->upload->do_upload())
			{
				$error = array('error' => $this->upload->display_errors());
	
				$this->load->view('audio/upload_audio', $error);
			}
			else
			{
				$uploadData = $this->upload->data();
				
				//since we've encrypted filename to ensure unique
				$rawName = explode('.', $uploadData['orig_name']);
				$rawName = $rawName[0];
				
				//raw name should be between 0000 and 0511
				if( ctype_digit($rawName) )
				{
					$audioNum = intval($rawName);
					
					if($audioNum >= 0 || $audioNum <= 511)
					{
						$fullPath = $uploadData['full_path'];
				
						//load data
						$this->load->helper('file');
						$mp3 = read_file($fullPath);
						
						//ug
						if(!$mp3)
						{
							$error = array('error' => 'Error reading file from temporary storage');
	
							$this->load->view('audio/upload_audio', $error);
						}
						else
						{
							$this->load->model('Companion_model');
							
							$id = $this->Companion_model->add_audio($audioNum, $this->input->post('text'), $this->input->post('isMessage') == '1', $mp3, $uploadData['file_size']);
							
							if($id)
							{
								$data = array('upload_data' => $uploadData, 'audioNum' => $audioNum, 'audioText' => $this->input->post('text'), 'audioURL' => $this->Companion_model->getAudioURL($audioNum, TRUE));
		
								$this->load->view('audio/upload_audio_success', $data);
							}
							else
							{
								$error = array('error' => 'There was an issue saving the data.  Please try again.');
	
								$this->load->view('audio/upload_audio', $error);
							}
						}
					}
					else
					{
						$error = array('error' => 'MP3 audio name should be between 0000 and 0511');
	
						$this->load->view('audio/upload_audio', $error);
					}
				}
				else
				{
					$error = array('error' => 'MP3 audio name should be between 0000 and 0511');
	
					$this->load->view('audio/upload_audio', $error);
				}
			}
		}
		else
		{
			$error = array('error' => validation_errors());
			$this->load->view('audio/upload_audio', $error);
		}
	}
	
	protected function hexToBinHandler($errno, $errstr) {
		throw new Exception();
	}
	
	public function is_message_check($str)
	{
		if ($str == NULL || $str && $str == '1')
		{
			return TRUE;
		}
		else
		{
			$this->form_validation->set_message('is_message_check', 'Stop messing around!');
			return FALSE;
		}
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */