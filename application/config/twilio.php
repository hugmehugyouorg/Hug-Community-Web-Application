<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	* Name:  Twilio
	*
	* Author: Ben Edmunds
	*		  ben.edmunds@gmail.com
	*         @benedmunds
	*
	* Location:
	*
	* Created:  03.29.2011
	*
	* Description:  Twilio configuration settings.
	*
	*
	*/

	/**
	 * Mode ("sandbox" or "prod")
	 **/
	$config['mode']   = isset($_SERVER['CI_TWILIO_MODE']) ? $_SERVER['CI_TWILIO_MODE'] : 'prod';

	/**
	 * Account SID
	 **/
	$config['account_sid']   = isset($_SERVER['CI_TWILIO_ACCOUNT_SID']) ? $_SERVER['CI_TWILIO_ACCOUNT_SID'] : 'ACd0568e39511a8b0bb11a33616434618f';

	/**
	 * Auth Token
	 **/
	$config['auth_token']    = isset($_SERVER['CI_TWILIO_AUTH_TOKEN']) ? $_SERVER['CI_TWILIO_AUTH_TOKEN'] : ''; //ask team if necessary

	/**
	 * API Version
	 **/
	$config['api_version']   = '2010-04-01';

	/**
	 * Twilio Phone Number
	 **/
	$config['number']        = isset($_SERVER['CI_TWILIO_NUMBER']) ? $_SERVER['CI_TWILIO_NUMBER'] : '+16122042467';


/* End of file twilio.php */