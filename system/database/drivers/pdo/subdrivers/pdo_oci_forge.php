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
 * PDO Oracle Forge Class.
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/database/
 */
#[\AllowDynamicProperties]
class CI_DB_pdo_oci_forge extends CI_DB_pdo_forge {

	/**
	 * CREATE DATABASE statement.
	 *
	 * @var	string
	 */
	protected $_create_database = FALSE;

	/**
	 * CREATE TABLE IF statement.
	 *
	 * @var	string
	 */
	protected $_create_table_if = FALSE;

	/**
	 * DROP DATABASE statement.
	 *
	 * @var	string
	 */
	protected $_drop_database = FALSE;

	/**
	 * UNSIGNED support.
	 *
	 * @var	bool|array
	 */
	protected $_unsigned = FALSE;

	/**
	 * NULL value representation in CREATE/ALTER TABLE statements.
	 *
	 * @var	string
	 */
	protected $_null = 'NULL';

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
		if ($alter_type === 'DROP')
		{
			return parent::_alter_table($alter_type, $table, $field);
		}
		elseif ($alter_type === 'CHANGE')
		{
			$alter_type = 'MODIFY';
		}

		$sql = 'ALTER TABLE ' . $this->db->escape_identifiers($table);
		$sqls = [];
		for ($i = 0, $c = count($field); $i < $c; $i++)
		{
			if ($field[$i]['_literal'] !== FALSE)
			{
				$field[$i] = "\n\t" . $field[$i]['_literal'];
			}
			else
			{
				$field[$i]['_literal'] = "\n\t" . $this->_process_column($field[$i]);

				if ( !empty($field[$i]['comment']))
				{
					$sqls[] = 'COMMENT ON COLUMN '
						. $this->db->escape_identifiers($table) . '.' . $this->db->escape_identifiers($field[$i]['name'])
						. ' IS ' . $field[$i]['comment'];
				}

				if ($alter_type === 'MODIFY' && !empty($field[$i]['new_name']))
				{
					$sqls[] = $sql . ' RENAME COLUMN ' . $this->db->escape_identifiers($field[$i]['name'])
						. ' TO ' . $this->db->escape_identifiers($field[$i]['new_name']);
				}
			}
		}

		$sql .= ' ' . $alter_type . ' ';
		$sql .= (count($field) === 1)
				? $field[0]
				: '(' . implode(',', $field) . ')';

		// RENAME COLUMN must be executed after MODIFY
		array_unshift($sqls, $sql);
		return $sql;
	}

	// --------------------------------------------------------------------

	/**
	 * Field attribute AUTO_INCREMENT.
	 *
	 * @param	array	&$attributes
	 * @param	array	&$field
	 * @return	void
	 */
	#[\Override]
    protected function _attr_auto_increment(&$attributes, &$field)
	{
		if ( !empty($attributes['AUTO_INCREMENT']) && $attributes['AUTO_INCREMENT'] === TRUE && stripos((string) $field['type'], 'number') !== FALSE && version_compare($this->db->version(), '12.1', '>='))
		{
			$field['auto_increment'] = ' GENERATED ALWAYS AS IDENTITY';
		}
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
		return $this->db->escape_identifiers($field['name'])
			. ' ' . $field['type'] . $field['length']
			. $field['unsigned']
			. $field['default']
			. $field['auto_increment']
			. $field['null']
			. $field['unique'];
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
			case 'MEDIUMINT':
			case 'INT':
			case 'BIGINT':
				$attributes['TYPE'] = 'NUMBER';
				return;
			default: return;
		}
	}
}
