<?php

/**
 * CodeIgniter.
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2019 - 2022, CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @copyright	Copyright (c) 2019 - 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 3.0.0
 * @filesource
 */
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * CodeIgniter Session Driver Class.
 *
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	https://codeigniter.com/userguide3/libraries/sessions.html
 */
#[\AllowDynamicProperties]
abstract class CI_Session_driver {

	protected $_config;

	/**
	 * Data fingerprint.
	 *
	 * @var	bool
	 */
	protected $_fingerprint;

	/**
	 * Lock placeholder.
	 *
	 * @var	mixed
	 */
	protected $_lock = FALSE;

	/**
	 * Read session ID.
	 *
	 * Used to detect session_regenerate_id() calls because PHP only calls
	 * write() after regenerating the ID.
	 *
	 * @var	string
	 */
	protected $_session_id;

	/**
	 * Success and failure return values.
	 *
	 * Necessary due to a bug in all PHP 5 versions where return values
	 * from userspace handlers are not handled properly. PHP 7 fixes the
	 * bug, so we need to return different values depending on the version.
	 *
	 * @see	https://wiki.php.net/rfc/session.user.return-value
	 * @var	mixed
	 */
	protected $_success;
	protected $_failure;

	// ------------------------------------------------------------------------

	/**
	 * Class constructor.
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	public function __construct(&$params)
	{
		$this->_config = &$params;

		if (is_php('7'))
		{
			$this->_success = TRUE;
			$this->_failure = FALSE;
		}
		else
		{
			$this->_success = 0;
			$this->_failure = -1;
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * PHP 5.x validate ID.
	 *
	 * Enforces session.use_strict_mode
	 *
	 * @return	void
	 */
	public function php5_validate_id()
	{
		if ($this->_success === 0 && isset($_COOKIE[$this->_config['cookie_name']]) && !$this->validateId($_COOKIE[$this->_config['cookie_name']]))
		{
			unset($_COOKIE[$this->_config['cookie_name']]);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Cookie destroy.
	 *
	 * Internal method to force removal of a cookie by the client
	 * when session_destroy() is called.
	 *
	 * @return	bool
	 */
	protected function _cookie_destroy()
	{
		if ( !is_php('7.3'))
		{
			$header = 'Set-Cookie: ' . $this->_config['cookie_name'] . '=';
			$header .= '; Expires=' . gmdate('D, d-M-Y H:i:s T', 1) . '; Max-Age=-1';
			$header .= '; Path=' . $this->_config['cookie_path'];
			$header .= ($this->_config['cookie_domain'] !== '' ? '; Domain=' . $this->_config['cookie_domain'] : '');
			$header .= ($this->_config['cookie_secure'] ? '; Secure' : '') . '; HttpOnly; SameSite=' . $this->_config['cookie_samesite'];
			header($header);
			return null;
		}

		return setcookie(
		    $this->_config['cookie_name'],
		    '',
		    [
				'expires' => 1,
				'path' => $this->_config['cookie_path'],
				'domain' => $this->_config['cookie_domain'],
				'secure' => $this->_config['cookie_secure'],
				'httponly' => TRUE,
				'samesite' => $this->_config['cookie_samesite'],
			]
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get lock.
	 *
	 * A dummy method allowing drivers with no locking functionality
	 * (databases other than PostgreSQL and MySQL) to act as if they
	 * do acquire a lock.
	 *
	 * @param	string	$session_id
	 * @return	bool
	 */
	protected function _get_lock($session_id)
	{
		$this->_lock = TRUE;
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Release lock.
	 *
	 * @return	bool
	 */
	protected function _release_lock()
	{
		if ($this->_lock)
		{
			$this->_lock = FALSE;
		}

		return TRUE;
	}
}
