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
 * @since	Version 2.1.0
 * @filesource
 */
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * CUBRID Forge Class.
 *
 * @category	Database
 * @author		Esen Sagynov
 * @link		https://codeigniter.com/userguide3/database/
 */
#[\AllowDynamicProperties]
class CI_DB_cubrid_forge extends CI_DB_forge {

	/**
	 * CREATE DATABASE statement.
	 *
	 * @var	string
	 */
	protected $_create_database = FALSE;

	/**
	 * CREATE TABLE keys flag.
	 *
	 * Whether table keys are created from within the
	 * CREATE TABLE statement.
	 *
	 * @var	bool
	 */
	protected $_create_table_keys = TRUE;

	/**
	 * DROP DATABASE statement.
	 *
	 * @var	string
	 */
	protected $_drop_database = FALSE;

	/**
	 * CREATE TABLE IF statement.
	 *
	 * @var	string
	 */
	protected $_create_table_if = FALSE;

	/**
	 * UNSIGNED support.
	 *
	 * @var	array
	 */
	protected $_unsigned = [
		'SHORT'		=> 'INTEGER',
		'SMALLINT'	=> 'INTEGER',
		'INT'		=> 'BIGINT',
		'INTEGER'	=> 'BIGINT',
		'BIGINT'	=> 'NUMERIC',
		'FLOAT'		=> 'DOUBLE',
		'REAL'		=> 'DOUBLE',
	];

	// --------------------------------------------------------------------

	/**
	 * ALTER TABLE.
	 *
	 * @param	string	$alter_type	ALTER type
	 * @param	string	$table		Table name
	 * @param	mixed	$field		Column definition
	 * @return	string|string[]
	 */
	#[\Override]
    protected function _alter_table($alter_type, $table, $field)
	{
		if (in_array($alter_type, ['DROP', 'ADD'], TRUE))
		{
			return parent::_alter_table($alter_type, $table, $field);
		}

		$sql = 'ALTER TABLE ' . $this->db->escape_identifiers($table);
		$sqls = [];
		for ($i = 0, $c = count($field); $i < $c; $i++)
		{
			if ($field[$i]['_literal'] !== FALSE)
			{
				$sqls[] = $sql . ' CHANGE ' . $field[$i]['_literal'];
			}
			else
			{
				$alter_type = empty($field[$i]['new_name']) ? ' MODIFY ' : ' CHANGE ';
				$sqls[] = $sql . $alter_type . $this->_process_column($field[$i]);
			}
		}

		return $sqls;
	}

	// --------------------------------------------------------------------

	/**
	 * Process column.
	 *
	 * @param	array	$field
	 * @return	string
	 */
	#[\Override]
    protected function _process_column($field)
	{
		$extra_clause = isset($field['after'])
			? ' AFTER ' . $this->db->escape_identifiers($field['after']) : '';

		if (($extra_clause === '' || $extra_clause === '0') && isset($field['first']) && $field['first'] === TRUE)
		{
			$extra_clause = ' FIRST';
		}

		return $this->db->escape_identifiers($field['name'])
			. (empty($field['new_name']) ? '' : ' ' . $this->db->escape_identifiers($field['new_name']))
			. ' ' . $field['type'] . $field['length']
			. $field['unsigned']
			. $field['null']
			. $field['default']
			. $field['auto_increment']
			. $field['unique']
			. $extra_clause;
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute TYPE.
	 *
	 * Performs a data type mapping between different databases.
	 *
	 * @param	array	&$attributes
	 * @return	void
	 */
	#[\Override]
    protected function _attr_type(&$attributes)
	{
		switch (strtoupper((string) $attributes['TYPE']))
		{
			case 'TINYINT':
				$attributes['TYPE'] = 'SMALLINT';
				$attributes['UNSIGNED'] = FALSE;
				return;
			case 'MEDIUMINT':
				$attributes['TYPE'] = 'INTEGER';
				$attributes['UNSIGNED'] = FALSE;
				return;
			case 'LONGTEXT':
				$attributes['TYPE'] = 'STRING';
				return;
			default: return;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Process indexes.
	 *
	 * @param	string	$table	(ignored)
	 * @return	string
	 */
	#[\Override]
    protected function _process_indexes($table)
	{
		$sql = '';

		for ($i = 0, $c = count($this->keys); $i < $c; $i++)
		{
			if (is_array($this->keys[$i]))
			{
				for ($i2 = 0, $c2 = count($this->keys[$i]); $i2 < $c2; $i2++)
				{
					if ( !isset($this->fields[$this->keys[$i][$i2]]))
					{
						unset($this->keys[$i][$i2]);
						continue;
					}
				}
			}
			elseif ( !isset($this->fields[$this->keys[$i]]))
			{
				unset($this->keys[$i]);
				continue;
			}

			if (!is_array($this->keys[$i])) {
                $this->keys[$i] = [$this->keys[$i]];
            }

			$sql .= ",\n\tKEY " . $this->db->escape_identifiers(implode('_', $this->keys[$i]))
				. ' (' . implode(', ', $this->db->escape_identifiers($this->keys[$i])) . ')';
		}

		$this->keys = [];

		return $sql;
	}

}
