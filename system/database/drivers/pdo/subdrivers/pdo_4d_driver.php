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
 * PDO 4D Database Adapter Class.
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
class CI_DB_pdo_4d_driver extends CI_DB_pdo_driver {

	public $dsn;
    public $port;
    public $database;
    public $char_set;
    public $dbprefix;
    public $_like_escape_str;
    public $_like_escape_chr;
    public $qb_limit;
    /**
     * @var never[]
     */
    public $qb_orderby;
    public $qb_offset;
	/**
	 * Sub-driver.
	 *
	 * @var	string
	 */
	public $subdriver = '4d';

	/**
	 * Identifier escape character.
	 *
	 * @var	string[]
	 */
	protected $_escape_char = ['[', ']'];

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
			$this->dsn = '4D:host=' . (empty($this->hostname) ? '127.0.0.1' : $this->hostname);

			if (!empty($this->port)) {
                $this->dsn .= ';port=' . $this->port;
            }
			if (!empty($this->database)) {
                $this->dsn .= ';dbname=' . $this->database;
            }
			if (!empty($this->char_set)) {
                $this->dsn .= ';charset=' . $this->char_set;
            }
		}
		elseif ( !empty($this->char_set) && !str_contains(substr((string) $this->dsn, 3), 'charset='))
		{
			$this->dsn .= ';charset=' . $this->char_set;
		}
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
		$sql = 'SELECT ' . $this->escape_identifiers('TABLE_NAME') . ' FROM ' . $this->escape_identifiers('_USER_TABLES');

		if ($prefix_limit === TRUE && $this->dbprefix !== '')
		{
			$sql .= ' WHERE ' . $this->escape_identifiers('TABLE_NAME') . " LIKE '" . $this->escape_like_str($this->dbprefix) . "%' "
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
		return 'SELECT ' . $this->escape_identifiers('COLUMN_NAME') . ' FROM ' . $this->escape_identifiers('_USER_COLUMNS')
			. ' WHERE ' . $this->escape_identifiers('TABLE_NAME') . ' = ' . $this->escape($table);
	}

	// --------------------------------------------------------------------

	/**
	 * Field data query.
	 *
	 * Generates a platform-specific query so that the column data can be retrieved
	 *
	 * @param	string	$table
	 * @return	string
	 */
	#[\Override]
    protected function _field_data($table)
	{
		return 'SELECT * FROM ' . $this->protect_identifiers($table, TRUE, NULL, FALSE) . ' LIMIT 1';
	}

	// --------------------------------------------------------------------

	/**
	 * Update statement.
	 *
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @param	string	$table
	 * @param	array	$values
	 * @return	string
	 */
	protected function _update($table, $values)
	{
		$this->qb_limit = FALSE;
		$this->qb_orderby = [];
		return parent::_update($table, $values);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete statement.
	 *
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @param	string	$table
	 * @return	string
	 */
	protected function _delete($table)
	{
		$this->qb_limit = FALSE;
		return parent::_delete($table);
	}

	// --------------------------------------------------------------------

	/**
	 * LIMIT.
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @param	string	$sql	SQL Query
	 * @return	string
	 */
	protected function _limit($sql)
	{
		return $sql . ' LIMIT ' . $this->qb_limit . ($this->qb_offset ? ' OFFSET ' . $this->qb_offset : '');
	}

}
