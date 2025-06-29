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
 * CodeIgniter Session Memcached Driver.
 *
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	https://codeigniter.com/userguide3/libraries/sessions.html
 */
#[\AllowDynamicProperties]
class CI_Session_memcached_driver extends CI_Session_driver implements CI_Session_driver_interface {

	/**
	 * Memcached instance.
	 *
	 * @var	Memcached
	 */
	protected $_memcached;

	/**
	 * Key prefix.
	 *
	 * @var	string
	 */
	protected $_key_prefix = 'ci_session:';

	/**
	 * Lock key.
	 *
	 * @var	string
	 */
	protected $_lock_key;

	// ------------------------------------------------------------------------

	/**
	 * Class constructor.
	 *
	 * @param	array	$params	Configuration parameters
	 * @return	void
	 */
	public function __construct(&$params)
	{
		parent::__construct($params);

		if (empty($this->_config['save_path']))
		{
			log_message('error', 'Session: No Memcached save path configured.');
		}

		if ($this->_config['match_ip'] === TRUE)
		{
			$this->_key_prefix .= $_SERVER['REMOTE_ADDR'] . ':';
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Open.
	 *
	 * Sanitizes save_path and initializes connections.
	 *
	 * @param	string	$save_path	Server path(s)
	 * @param	string	$name		Session cookie name, unused
	 * @return	bool
	 */
	public function open($save_path, $name)
	{
		$this->_memcached = new Memcached();
		$this->_memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE); // required for touch() usage
		$server_list = [];
		foreach ($this->_memcached->getServerList() as $server)
		{
			$server_list[] = $server['host'] . ':' . $server['port'];
		}

		if ( !preg_match_all('#,?([^,:]+)\:(\d{1,5})(?:\:(\d+))?#', (string) $this->_config['save_path'], $matches, PREG_SET_ORDER))
		{
			$this->_memcached = NULL;
			log_message('error', 'Session: Invalid Memcached save path format: ' . $this->_config['save_path']);
			return $this->_failure;
		}

		foreach ($matches as $match)
		{
			// If Memcached already has this server (or if the port is invalid), skip it
			if (in_array($match[1] . ':' . $match[2], $server_list, TRUE))
			{
				log_message('debug', 'Session: Memcached server pool already has ' . $match[1] . ':' . $match[2]);
				continue;
			}

			if ( !$this->_memcached->addServer($match[1], $match[2], $match[3] ?? 0))
			{
				log_message('error', 'Could not add ' . $match[1] . ':' . $match[2] . ' to Memcached server pool.');
			}
			else
			{
				$server_list[] = $match[1] . ':' . $match[2];
			}
		}

		if ($server_list === [])
		{
			log_message('error', 'Session: Memcached server pool is empty.');
			return $this->_failure;
		}

		$this->php5_validate_id();

		return $this->_success;
	}

	// ------------------------------------------------------------------------

	/**
	 * Read.
	 *
	 * Reads session data and acquires a lock
	 *
	 * @param	string	$session_id	Session ID
	 * @return	string	Serialized session data
	 */
	public function read($session_id)
	{
		if ($this->_memcached !== null && $this->_get_lock($session_id))
		{
			// Needed by write() to detect session_regenerate_id() calls
			$this->_session_id = $session_id;

			$session_data = (string) $this->_memcached->get($this->_key_prefix . $session_id);
			$this->_fingerprint = md5($session_data);
			return $session_data;
		}

		return $this->_failure;
	}

	// ------------------------------------------------------------------------

	/**
	 * Write.
	 *
	 * Writes (create / update) session data
	 *
	 * @param	string	$session_id	Session ID
	 * @param	string	$session_data	Serialized session data
	 * @return	bool
	 */
	public function write($session_id, $session_data)
	{
		if ( $this->_memcached === null && $this->_lock_key === null)
		{
			return $this->_failure;
		}
		// Was the ID regenerated?
		elseif ($session_id !== $this->_session_id)
		{
			if ( !$this->_release_lock() || !$this->_get_lock($session_id))
			{
				return $this->_failure;
			}

			$this->_fingerprint = md5('');
			$this->_session_id = $session_id;
		}

		$key = $this->_key_prefix . $session_id;

		$this->_memcached->replace($this->_lock_key, time(), 300);
		if ($this->_fingerprint !== ($fingerprint = md5($session_data)))
		{
			if ($this->_memcached->set($key, $session_data, $this->_config['expiration']))
			{
				$this->_fingerprint = $fingerprint;
				return $this->_success;
			}

			return $this->_failure;
		}
		elseif (
			$this->_memcached->touch($key, $this->_config['expiration']) || $this->_memcached->getResultCode() === Memcached::RES_NOTFOUND && $this->_memcached->set($key, $session_data, $this->_config['expiration'])
		)
		{
			return $this->_success;
		}

		return $this->_failure;
	}

	// ------------------------------------------------------------------------

	/**
	 * Close.
	 *
	 * Releases locks and closes connection.
	 *
	 * @return	bool
	 */
	public function close()
	{
		if ($this->_memcached !== null)
		{
			$this->_release_lock();
			if ( !$this->_memcached->quit())
			{
				return $this->_failure;
			}

			$this->_memcached = NULL;
			return $this->_success;
		}

		return $this->_failure;
	}

	// ------------------------------------------------------------------------

	/**
	 * Destroy.
	 *
	 * Destroys the current session.
	 *
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	public function destroy($session_id)
	{
		if ($this->_memcached !== null && $this->_lock_key !== null)
		{
			$this->_memcached->delete($this->_key_prefix . $session_id);
			$this->_cookie_destroy();
			return $this->_success;
		}

		return $this->_failure;
	}

	// ------------------------------------------------------------------------

	/**
	 * Garbage Collector.
	 *
	 * Deletes expired sessions
	 *
	 * @param	int 	$maxlifetime	Maximum lifetime of sessions
	 * @return	bool
	 */
	public function gc($maxlifetime)
	{
		// Not necessary, Memcached takes care of that.
		return $this->_success;
	}

	// --------------------------------------------------------------------

	/**
	 * Update Timestamp.
	 *
	 * Update session timestamp without modifying data
	 *
	 * @param	string	$id	Session ID
	 * @param	string	$data	Unknown & unused
	 * @return	bool
	 */
	public function updateTimestamp($id, $unknown)
	{
		return $this->_memcached->touch($this->_key_prefix . $id, $this->_config['expiration']);
	}

	// --------------------------------------------------------------------

	/**
	 * Validate ID.
	 *
	 * Checks whether a session ID record exists server-side,
	 * to enforce session.use_strict_mode.
	 *
	 * @param	string	$id	Session ID
	 * @return	bool
	 */
	public function validateId($id)
	{
		$this->_memcached->get($this->_key_prefix . $id);
		return ($this->_memcached->getResultCode() === Memcached::RES_SUCCESS);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get lock.
	 *
	 * Acquires an (emulated) lock.
	 *
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	#[\Override]
    protected function _get_lock($session_id)
	{
		// PHP 7 reuses the SessionHandler object on regeneration,
		// so we need to check here if the lock key is for the
		// correct session ID.
		if ($this->_lock_key === $this->_key_prefix . $session_id . ':lock')
		{
			if ( !$this->_memcached->replace($this->_lock_key, time(), 300))
			{
				return $this->_memcached->getResultCode() === Memcached::RES_NOTFOUND && $this->_memcached->add($this->_lock_key, time(), 300);
			}

			return TRUE;
		}

		// 30 attempts to obtain a lock, in case another request already has it
		$lock_key = $this->_key_prefix . $session_id . ':lock';
		$attempt = 0;
		do
		{
			if ($this->_memcached->get($lock_key))
			{
				sleep(1);
				continue;
			}

			$method = ($this->_memcached->getResultCode() === Memcached::RES_NOTFOUND) ? 'add' : 'set';
			if ( !$this->_memcached->$method($lock_key, time(), 300))
			{
				log_message('error', 'Session: Error while trying to obtain lock for ' . $this->_key_prefix . $session_id);
				return FALSE;
			}

			$this->_lock_key = $lock_key;
			break;
		}
		while (++$attempt < 30);

		if ($attempt === 30)
		{
			log_message('error', 'Session: Unable to obtain lock for ' . $this->_key_prefix . $session_id . ' after 30 attempts, aborting.');
			return FALSE;
		}

		$this->_lock = TRUE;
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Release lock.
	 *
	 * Releases a previously acquired lock
	 *
	 * @return	bool
	 */
	#[\Override]
    protected function _release_lock()
	{
		if ($this->_memcached !== null && $this->_lock_key !== null && $this->_lock)
		{
			if ( !$this->_memcached->delete($this->_lock_key) && $this->_memcached->getResultCode() !== Memcached::RES_NOTFOUND)
			{
				log_message('error', 'Session: Error while trying to free lock for ' . $this->_lock_key);
				return FALSE;
			}

			$this->_lock_key = NULL;
			$this->_lock = FALSE;
		}

		return TRUE;
	}
}
