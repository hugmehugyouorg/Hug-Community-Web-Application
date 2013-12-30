<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
 * Secure Anchor Link
 *
 * Creates a secure anchor based on the local URL.
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the link title
 * @param	mixed	any attributes
 * @return	string
 */
if ( ! function_exists('secure_anchor'))
{
	function secure_anchor($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		$site_url = secure_link($uri);

		if ($title == '')
		{
			$title = $site_url;
		}

		if ($attributes != '')
		{
			$attributes = _parse_attributes($attributes);
		}

		return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
	}
}

if ( ! function_exists('secure_link'))
{
	function secure_link($uri = '')
	{
		if ( ! is_array($uri))
		{
			$site_url = ( ! preg_match('!^\w+://! i', $uri)) ? site_url($uri) : $uri;
		}
		else
		{
			$site_url = site_url($uri);
		}

		$strHTTPs = substr($site_url, 0, 5);
		
		if( substr($strHTTPs, 0, 4) == 'http' && $strHTTPs != 'https' )
			$site_url = 'https' . substr($site_url, 4);

		return $site_url;
	}
}

// ------------------------------------------------------------------------