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
 * @since	Version 2.0.0
 * @filesource
 */
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * CodeIgniter APC Caching Class.
 *
 * @category	Core
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Cache_apc extends CI_Driver {

	/**
	 * Class constructor.
	 *
	 * Only present so that an error message is logged
	 * if APC is not available.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		if ( !$this->is_supported())
		{
			log_message('error', 'Cache: Failed to initialize APC; extension not loaded/enabled?');
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get.
	 *
	 * Look for a value in the cache. If it exists, return the data
	 * if not, return FALSE
	 *
	 * @param	string
	 * @return	mixed	value that is stored/FALSE on failure
	 */
	public function get($id)
	{
		$success = FALSE;
		$data = apc_fetch($id, $success);

		return ($success === TRUE) ? $data : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Save.
	 *
	 * @param	string	$id	Cache ID
	 * @param	mixed	$data	Data to store
	 * @param	int	$ttl	Length of time (in seconds) to cache the data
	 * @param	bool	$raw	Whether to store the raw value (unused)
	 * @return	bool	TRUE on success, FALSE on failure
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		return apc_store($id, $data, (int) $ttl);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache.
	 *
	 * @param	mixed	unique identifier of the item in the cache
	 * @return	bool	true on success/false on failure
	 */
	public function delete($id)
	{
		return apc_delete($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value.
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to add
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function increment($id, $offset = 1)
	{
		return apc_inc($id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Decrement a raw value.
	 *
	 * @param	string	$id	Cache ID
	 * @param	int	$offset	Step/value to reduce by
	 * @return	mixed	New value on success or FALSE on failure
	 */
	public function decrement($id, $offset = 1)
	{
		return apc_dec($id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache.
	 *
	 * @return	bool	false on failure/true on success
	 */
	public function clean()
	{
		return apc_clear_cache('user');
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info.
	 *
	 * @param	string	user/filehits
	 * @return	mixed	array on success, false on failure
	 */
	public function cache_info($type = NULL)
	{
		return apc_cache_info($type);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata.
	 *
	 * @param	mixed	key to get cache metadata on
	 * @return	mixed	array on success/false on failure
	 */
	public function get_metadata($id)
	{
		$cache_info = apc_cache_info('user', FALSE);
		if ($cache_info === [] || empty($cache_info['cache_list']))
		{
			return FALSE;
		}

		foreach ($cache_info['cache_list'] as &$entry)
		{
			if ($entry['info'] !== $id)
			{
				continue;
			}

			$success = FALSE;
			$metadata = [
				'expire' => ($entry['ttl'] ? $entry['mtime'] + $entry['ttl'] : 0),
				'mtime'  => $entry['ttl'],
				'data'   => apc_fetch($id, $success),
			];

			return ($success === TRUE) ? $metadata : FALSE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * is_supported().
	 *
	 * Check to see if APC is available on this system, bail if it isn't.
	 *
	 * @return	bool
	 */
	public function is_supported()
	{
		return (extension_loaded('apc') && ini_get('apc.enabled'));
	}
}
