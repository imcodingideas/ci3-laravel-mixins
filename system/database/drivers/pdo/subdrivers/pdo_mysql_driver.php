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
 * PDO MySQL Database Adapter Class.
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
class CI_DB_pdo_mysql_driver extends CI_DB_pdo_driver {

	public $dsn;
    public $port;
    public $database;
    public $char_set;
    public $encrypt;
    public $db_debug;
    /**
     * @var never[]
     */
    public $data_cache;
    public $conn_id;
    public $dbprefix;
    public $qb_join;
    public $qb_from;
	/**
	 * Sub-driver.
	 *
	 * @var	string
	 */
	public $subdriver = 'mysql';

	/**
	 * Compression flag.
	 *
	 * @var	bool
	 */
	public $compress = FALSE;

	/**
	 * Strict ON flag.
	 *
	 * Whether we're running in strict SQL mode.
	 *
	 * @var	bool
	 */
	public $stricton;

	// --------------------------------------------------------------------

	/**
	 * Identifier escape character.
	 *
	 * @var	string
	 */
	protected $_escape_char = '`';

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
			$this->dsn = 'mysql:host=' . (empty($this->hostname) ? '127.0.0.1' : $this->hostname);

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
		elseif ( !empty($this->char_set) && !str_contains(substr((string) $this->dsn, 6), 'charset='))
		{
			$this->dsn .= ';charset=' . $this->char_set;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Database connection.
	 *
	 * @param	bool	$persistent
	 * @return	object
	 */
	#[\Override]
    public function db_connect($persistent = FALSE)
	{
		if ($this->stricton !== null)
		{
			if ($this->stricton)
			{
				$sql = 'CONCAT(@@sql_mode, ",", "STRICT_ALL_TABLES")';
			}
			else
			{
				$sql = 'REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                                        @@sql_mode,
                                        "STRICT_ALL_TABLES,", ""),
                                        ",STRICT_ALL_TABLES", ""),
                                        "STRICT_ALL_TABLES", ""),
                                        "STRICT_TRANS_TABLES,", ""),
                                        ",STRICT_TRANS_TABLES", ""),
                                        "STRICT_TRANS_TABLES", "")';
			}

			if (empty($this->options[PDO::MYSQL_ATTR_INIT_COMMAND]))
				{
					$this->options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET SESSION sql_mode = ' . $sql;
				}
				else
				{
					$this->options[PDO::MYSQL_ATTR_INIT_COMMAND] .= ', @@session.sql_mode = ' . $sql;
				}
		}

		if ($this->compress === TRUE)
		{
			$this->options[PDO::MYSQL_ATTR_COMPRESS] = TRUE;
		}

		if (is_array($this->encrypt))
		{
			$ssl = [];
			if (!empty($this->encrypt['ssl_key'])) {
                $ssl[PDO::MYSQL_ATTR_SSL_KEY] = $this->encrypt['ssl_key'];
            }
			if (!empty($this->encrypt['ssl_cert'])) {
                $ssl[PDO::MYSQL_ATTR_SSL_CERT] = $this->encrypt['ssl_cert'];
            }
			if (!empty($this->encrypt['ssl_ca'])) {
                $ssl[PDO::MYSQL_ATTR_SSL_CA] = $this->encrypt['ssl_ca'];
            }
			if (!empty($this->encrypt['ssl_capath'])) {
                $ssl[PDO::MYSQL_ATTR_SSL_CAPATH] = $this->encrypt['ssl_capath'];
            }
			if (!empty($this->encrypt['ssl_cipher'])) {
                $ssl[PDO::MYSQL_ATTR_SSL_CIPHER] = $this->encrypt['ssl_cipher'];
            }

			if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT') && isset($this->encrypt['ssl_verify']))
			{
				$ssl[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $this->encrypt['ssl_verify'];
			}

			// DO NOT use array_merge() here!
			// It re-indexes numeric keys and the PDO_MYSQL_ATTR_SSL_* constants are integers.
			if ($ssl !== []) {
                $this->options += $ssl;
            }
		}

		// Prior to version 5.7.3, MySQL silently downgrades to an unencrypted connection if SSL setup fails
		if (
			($pdo = parent::db_connect($persistent)) !== FALSE
			&& $ssl !== []
			&& version_compare($pdo->getAttribute(PDO::ATTR_CLIENT_VERSION), '5.7.3', '<=')
			&& empty($pdo->query("SHOW STATUS LIKE 'ssl_cipher'")->fetchObject()->Value)
		)
		{
			$message = 'PDO_MYSQL was configured for an SSL connection, but got an unencrypted connection instead!';
			log_message('error', $message);
			return ($this->db_debug) ? $this->display_error($message, '', TRUE) : FALSE;
		}

		return $pdo;
	}

	// --------------------------------------------------------------------

	/**
	 * Select the database.
	 *
	 * @param	string	$database
	 * @return	bool
	 */
	public function db_select($database = '')
	{
		if ($database === '')
		{
			$database = $this->database;
		}

		if (FALSE !== $this->simple_query('USE ' . $this->escape_identifiers($database)))
		{
			$this->database = $database;
			$this->data_cache = [];
			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Begin Transaction.
	 *
	 * @return	bool
	 */
	#[\Override]
    protected function _trans_begin()
	{
		$this->conn_id->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
		return $this->conn_id->beginTransaction();
	}

	// --------------------------------------------------------------------

	/**
	 * Commit Transaction.
	 *
	 * @return	bool
	 */
	#[\Override]
    protected function _trans_commit()
	{
		if ($this->conn_id->commit())
		{
			$this->conn_id->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Rollback Transaction.
	 *
	 * @return	bool
	 */
	#[\Override]
    protected function _trans_rollback()
	{
		if ($this->conn_id->rollBack())
		{
			$this->conn_id->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
			return TRUE;
		}

		return FALSE;
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
		$sql = 'SHOW TABLES FROM ' . $this->_escape_char . $this->database . $this->_escape_char;

		if ($prefix_limit === TRUE && $this->dbprefix !== '')
		{
			return $sql . " LIKE '" . $this->escape_like_str($this->dbprefix) . "%'";
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
		return 'SHOW COLUMNS FROM ' . $this->protect_identifiers($table, TRUE, NULL, FALSE);
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an object with field data.
	 *
	 * @param	string	$table
	 * @return	array
	 */
	public function field_data($table)
	{
		if (($query = $this->query('SHOW COLUMNS FROM ' . $this->protect_identifiers($table, TRUE, NULL, FALSE))) === FALSE)
		{
			return FALSE;
		}
		$query = $query->result_object();

		$retval = [];
		for ($i = 0, $c = count($query); $i < $c; $i++)
		{
			$retval[$i] = new stdClass();
			$retval[$i]->name = $query[$i]->Field;

			sscanf(
			    $query[$i]->Type,
			    '%[a-z](%d)',
			    $retval[$i]->type,
			    $retval[$i]->max_length
			);

			$retval[$i]->default = $query[$i]->Default;
			$retval[$i]->primary_key = (int) ($query[$i]->Key === 'PRI');
		}

		return $retval;
	}

	// --------------------------------------------------------------------

	/**
	 * Truncate statement.
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 *
	 * If the database does not support the TRUNCATE statement,
	 * then this method maps to 'DELETE FROM table'
	 *
	 * @param	string	$table
	 * @return	string
	 */
	#[\Override]
    protected function _truncate($table)
	{
		return 'TRUNCATE ' . $table;
	}

	// --------------------------------------------------------------------

	/**
	 * FROM tables.
	 *
	 * Groups tables in FROM clauses if needed, so there is no confusion
	 * about operator precedence.
	 *
	 * @return	string
	 */
	protected function _from_tables()
	{
		if ( !empty($this->qb_join) && count($this->qb_from) > 1)
		{
			return '(' . implode(', ', $this->qb_from) . ')';
		}

		return implode(', ', $this->qb_from);
	}

}
