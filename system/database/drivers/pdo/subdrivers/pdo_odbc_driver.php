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
 * PDO ODBC Database Adapter Class.
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the query builder
 * class is being used or not.
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/database/
 */
#[\AllowDynamicProperties]
class CI_DB_pdo_odbc_driver extends CI_DB_pdo_driver {

	public $dsn;
    public $hostname;
    public $HOSTNAME;
    public $port;
    public $PORT;
    public $DSN;
    public $database;
    public $DRIVER;
    public $DATABASE;
    public $PROTOCOL;
    public $dbprefix;
    public $_like_escape_chr;
	/**
	 * Sub-driver.
	 *
	 * @var	string
	 */
	public $subdriver = 'odbc';

	/**
	 * Database schema.
	 *
	 * @var	string
	 */
	public $schema = 'public';

	// --------------------------------------------------------------------

	/**
	 * Identifier escape character.
	 *
	 * Must be empty for ODBC.
	 *
	 * @var	string
	 */
	protected $_escape_char = '';

	/**
	 * ESCAPE statement string.
	 *
	 * @var	string
	 */
	protected $_like_escape_str = " {escape '%s'} ";

	/**
	 * ORDER BY random keyword.
	 *
	 * @var	array
	 */
	protected $_random_keyword = ['RND()', 'RND(%d)'];

	// --------------------------------------------------------------------

	/**
	 * Class constructor.
	 *
	 * Builds the DSN if not already set.
	 *
	 * @param	array	$params
	 * @return	void
	 */
	public function __construct($params)
	{
		parent::__construct($params);

		if (empty($this->dsn))
		{
			$this->dsn = 'odbc:';

			// Pre-defined DSN
			if (empty($this->hostname) && empty($this->HOSTNAME) && empty($this->port) && empty($this->PORT))
			{
				if (property_exists($this, 'DSN') && $this->DSN !== null)
				{
					$this->dsn .= 'DSN=' . $this->DSN;
				}
				elseif ( !empty($this->database))
				{
					$this->dsn .= 'DSN=' . $this->database;
				}

				return;
			}

			// If the DSN is not pre-configured - try to build an IBM DB2 connection string
			$this->dsn .= 'DRIVER=' . (property_exists($this, 'DRIVER') && $this->DRIVER !== null ? '{' . $this->DRIVER . '}' : '{IBM DB2 ODBC DRIVER}') . ';';

			if (property_exists($this, 'DATABASE') && $this->DATABASE !== null)
			{
				$this->dsn .= 'DATABASE=' . $this->DATABASE . ';';
			}
			elseif ( !empty($this->database))
			{
				$this->dsn .= 'DATABASE=' . $this->database . ';';
			}

			if (property_exists($this, 'HOSTNAME') && $this->HOSTNAME !== null)
			{
				$this->dsn .= 'HOSTNAME=' . $this->HOSTNAME . ';';
			}
			else
			{
				$this->dsn .= 'HOSTNAME=' . (empty($this->hostname) ? '127.0.0.1;' : $this->hostname . ';');
			}

			if (property_exists($this, 'PORT') && $this->PORT !== null)
			{
				$this->dsn .= 'PORT=' . $this->port . ';';
			}
			elseif ( !empty($this->port))
			{
				$this->dsn .= ';PORT=' . $this->port . ';';
			}

			$this->dsn .= 'PROTOCOL=' . (property_exists($this, 'PROTOCOL') && $this->PROTOCOL !== null ? $this->PROTOCOL . ';' : 'TCPIP;');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Platform-dependent string escape.
	 *
	 * @param	string
	 * @return	string
	 */
	#[\Override]
    protected function _escape_str($str)
	{
		$this->display_error('db_unsupported_feature');
	}

	// --------------------------------------------------------------------

	/**
	 * Determines if a query is a "write" type.
	 *
	 * @param	string	An SQL query string
	 * @return	bool
	 */
	public function is_write_type($sql)
	{
		if (preg_match('#^(INSERT|UPDATE).*RETURNING\s.+(\,\s?.+)*$#is', (string) $sql))
		{
			return FALSE;
		}

		return parent::is_write_type($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Show table query.
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool	$prefix_limit
	 * @return	string
	 */
	protected function _list_tables($prefix_limit = FALSE)
	{
		$sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = '" . $this->schema . "'";

		if ($prefix_limit !== FALSE && $this->dbprefix !== '')
		{
			return $sql . " AND table_name LIKE '" . $this->escape_like_str($this->dbprefix) . "%' "
				. sprintf($this->_like_escape_str, $this->_like_escape_chr);
		}

		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Show column query.
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @param	string	$table
	 * @return	string
	 */
	protected function _list_columns($table = '')
	{
		return 'SELECT column_name FROM information_schema.columns WHERE table_name = ' . $this->escape($table);
	}
}
