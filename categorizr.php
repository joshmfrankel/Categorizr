<?php 
/**
* Categorizr Version 1.1
* http://www.brettjankord.com/2012/01/16/categorizr-a-modern-device-detection-script/
* Written by Brett Jankord - Copyright © 2011
* Thanks to Josh Eisma for helping with code review
*
* Big thanks to Rob Manson and http://mob-labs.com for their work on
* the Not-Device Detection strategy:
* http://smartmobtoolkit.wordpress.com/2009/01/26/not-device-detection-javascript-perl-and-php-code/
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU Lesser General Public License for more details.
* You should have received a copy of the GNU General Public License
* and GNU Lesser General Public License
* along with this program. If not, see http://www.gnu.org/licenses/.
**/

/**
 * Class structure & helper methods added and extended
 * www.joshmfrankel.com
 * @author  	Joshua Frankel <joshmfrankel@gmail.com>
 * @copyright 	2012
 * @since  		1.2
 * @version  	1.2
 */
class Categorizr {


 	////////////////////////
	// PRIVATE PROPERTIES //
 	////////////////////////

	/**
	 * The session variable name to hold the device type
	 * @var string
	 */
	private $_category					= 'category';

	/**
	 * The query string key set to force the device type
	 * @var string
	 */
	private $_queryStringKey			= 'view';

	/**
	 * A toggle variable to force tablets to display as desktops
	 * @var boolean
	 */
	private $_displayTabletsAsDesktop	= FALSE;

	/**
	 * A toggle variable to force TVs to display as desktops
	 * @var boolean
	 */
	private $_displayTvAsDesktop		= FALSE;

	/**
	 * The User Agent variable
	 * @var string
	 */
	private $_ua;


 	///////////////////////
	// PUBLIC PROPERTIES //
 	///////////////////////

	/**
	 * The determined device type
	 * @var string
	 */
	public $deviceType;

	/**
	 * The query string value if existing
	 * @var string
	 */
	public $queryStringValue;

	/**
	 * Constructor Method 
	 */
	public function __construct() {

		//Start the session if it doesn't exist
		$this->_initializeSession();

		//Set the User Agent
		$this->_ua = $_SERVER['HTTP_USER_AGENT'];

		//Set the query string value
		$this->queryStringValue = $_GET[$this->_queryStringKey];

		//Set the device type from the query string only if set
		if (isset($queryStringValue)) {
			$this->_setDeviceFromQueryString();
		}

		//If the device type is not yet set
		//AND the session has not been set via querystring
		if (!$this->deviceType && !$_SESSION[$this->_queryStringKey]) {
			$this->_setDeviceFromUserAgent();
		}

		//Force tablets to display as desktops
		$this->deviceType = ($this->deviceType == 'tablet' && $this->_displayTabletsAsDesktop) ? 'desktop' : $this->deviceType;

		//Force TVs to display as desktops
		$this->deviceType = ($this->deviceType == 'tv' && $this->_displayTvAsDesktop) ? 'desktop' : $this->deviceType;

		//Set the Session variable from the gathered deviceType
		$_SESSION[$this->_category] = $this->deviceType;
	}

 	/////////////////////
	// PRIVATE METHODS //
 	/////////////////////

	/**
	 * Start a new session if there currently is no valid session id
	 * @access private
	 */
	private function _initializeSession() {
		(session_id()) ? '' : session_start();
	}

	/**
	 * Set the device from the query String
	 * @access private
	 */
	private function _setDeviceFromQueryString() {

		switch ($this->queryStringValue) {

			case 'desktop':
				$this->deviceType = "desktop";
				break;

			case 'tablet':
				$this->deviceType = "tablet";
				break;

			case 'tv':
				$this->deviceType = "tv";
				break;

			case 'mobile':
				$this->deviceType = "mobile";
				break;
		}
	}


	/**
	 * Set the Device from the User Agent
	 * This will expand to include more types however the last
	 * type must always be mobile
	 * @access private
	 */
	private function _setDeviceFromUserAgent() {

		if ((preg_match('/GoogleTV|SmartTV|Internet.TV|NetCast|NETTV|AppleTV|boxee|Kylo|Roku|DLNADOC|CE\-HTML/i', $this->_ua)))
		{
			$this->deviceType = "tv";
		}
		// Check if user agent is a TV Based Gaming Console
		else if ((preg_match('/Xbox|PLAYSTATION.3|Wii/i', $this->_ua)))
		{
			$this->deviceType = "tv";
		}  
		// Check if user agent is a Tablet
		else if((preg_match('/iP(a|ro)d/i', $this->_ua)) || (preg_match('/tablet/i', $this->_ua)) && (!preg_match('/RX-34/i', $this->_ua)) || (preg_match('/FOLIO/i', $this->_ua)))
		{
			$this->deviceType = "tablet";
		}
		// Check if user agent is an Android Tablet
		else if ((preg_match('/Linux/i', $this->_ua)) && (preg_match('/Android/i', $this->_ua)) && (!preg_match('/Fennec|mobi|HTC.Magic|HTCX06HT|Nexus.One|SC-02B|fone.945/i', $this->_ua)))
		{
			$this->deviceType = "tablet";
		}
		// Check if user agent is a Kindle or Kindle Fire
		else if ((preg_match('/Kindle/i', $this->_ua)) || (preg_match('/Mac.OS/i', $this->_ua)) && (preg_match('/Silk/i', $this->_ua)))
		{
			$this->deviceType = "tablet";
		}
		// Check if user agent is a pre Android 3.0 Tablet
		else if ((preg_match('/GT-P10|SC-01C|SHW-M180S|SGH-T849|SCH-I800|SHW-M180L|SPH-P100|SGH-I987|zt180|HTC(.Flyer|\_Flyer)|Sprint.ATP51|ViewPad7|pandigital(sprnova|nova)|Ideos.S7|Dell.Streak.7|Advent.Vega|A101IT|A70BHT|MID7015|Next2|nook/i', $this->_ua)) || (preg_match('/MB511/i', $this->_ua)) && (preg_match('/RUTEM/i', $this->_ua)))
		{
			$this->deviceType = "tablet";
		} 
		// Check if user agent is unique Mobile User Agent	
		else if ((preg_match('/BOLT|Fennec|Iris|Maemo|Minimo|Mobi|mowser|NetFront|Novarra|Prism|RX-34|Skyfire|Tear|XV6875|XV6975|Google.Wireless.Transcoder/i', $this->_ua)))
		{
			$this->deviceType = "mobile";
		}
		// Check if user agent is an odd Opera User Agent - http://goo.gl/nK90K
		else if ((preg_match('/Opera/i', $this->_ua)) && (preg_match('/Windows.NT.5/i', $this->_ua)) && (preg_match('/HTC|Xda|Mini|Vario|SAMSUNG\-GT\-i8000|SAMSUNG\-SGH\-i9/i', $this->_ua)))
		{
			$this->deviceType = "mobile";
		}
		// Check if user agent is Windows Desktop
		else if ((preg_match('/Windows.(NT|XP|ME|9)/', $this->_ua)) && (!preg_match('/Phone/i', $this->_ua)) || (preg_match('/Win(9|.9|NT)/i', $this->_ua)))
		{
			$this->deviceType = "desktop";
		}  
		// Check if agent is Mac Desktop
		else if ((preg_match('/Macintosh|PowerPC/i', $this->_ua)) && (!preg_match('/Silk/i', $this->_ua)))
		{
			$this->deviceType = "desktop";
		} 
		// Check if user agent is a Linux Desktop
		else if ((preg_match('/Linux/i', $this->_ua)) && (preg_match('/X11/i', $this->_ua)))
		{
			$this->deviceType = "desktop";
		} 
		// Check if user agent is a Solaris, SunOS, BSD Desktop
		else if ((preg_match('/Solaris|SunOS|BSD/i', $this->_ua)))
		{
			$this->deviceType = "desktop";
		}
		// Check if user agent is a Desktop BOT/Crawler/Spider
		else if ((preg_match('/Bot|Crawler|Spider|Yahoo|ia_archiver|Covario-IDS|findlinks|DataparkSearch|larbin|Mediapartners-Google|NG-Search|Snappy|Teoma|Jeeves|TinEye/i', $this->_ua)) && (!preg_match('/Mobile/i', $this->_ua)))
		{
			$this->deviceType = "desktop";
		}  
		// Otherwise assume it is a Mobile Device
		else {
			$this->deviceType = "mobile";
		}
	}

	
 	///////////////////////////
	// PUBLIC HELPER METHODS //
 	///////////////////////////
	
	/**
	 * Conditional check to see if the device is a desktop
	 * @return boolean 
	 */
	public function isDesktop() {
		return ($this->deviceType == 'desktop') ? TRUE : FALSE;
	}

	/**
	 * Conditional check to see if the device is a tablet
	 * @return boolean 
	 */
	public function isTablet() {
		return ($this->deviceType == 'tablet') ? TRUE : FALSE;
	}

	/**
	 * Conditional check to see if the device is a tv
	 * @return boolean 
	 */
	public function isTv() {
		return ($this->deviceType == 'tv') ? TRUE : FALSE;
	}

	/**
	 * Conditional check to see if the device is mobile
	 * @return boolean 
	 */
	public function isMobile() {
		return ($this->deviceType == 'mobile') ? TRUE : FALSE;
	}

	 
}

?>