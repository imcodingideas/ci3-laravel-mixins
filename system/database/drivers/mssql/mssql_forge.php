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
 * @since	Version 1.3.0
 * @filesource
 */
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * MS SQL Forge Class.
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/database/
 */
#[\AllowDynamicProperties]
class CI_DB_mssql_forge extends CI_DB_forge {

	/**
	 * CREATE TABLE IF statement.
	 *
	 * @var	string
	 */
	protected $_create_table_if = "IF NOT EXISTS (SELECT * FROM sysobjects WHERE ID = object_id(N'%s') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)\nCREATE TABLE";

	/**
	 * DROP TABLE IF statement.
	 *
	 * @var	string
	 */
	protected $_drop_table_if = "IF EXISTS (SELECT * FROM sysobjects WHERE ID = object_id(N'%s') AND OBJECTPROPERTY(id, N'IsUserTable') = 1)\nDROP TABLE";

	/**
	 * UNSIGNED support.
	 *
	 * @var	array
	 */
	protected $_unsigned = [
		'TINYINT'	=> 'SMALLINT',
		'SMALLINT'	=> 'INT',
		'INT'		=> 'BIGINT',
		'REAL'		=> 'FLOAT',
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
		if (in_array($alter_type, ['ADD', 'DROP'], TRUE))
		{
			return parent::_alter_table($alter_type, $table, $field);
		}

		$sql = 'ALTER TABLE ' . $this->db->escape_identifiers($table) . ' ALTER COLUMN ';
		$sqls = [];
		for ($i = 0, $c = count($field); $i < $c; $i++)
		{
			$sqls[] = $sql . $this->_process_column($field[$i]);
		}

		return $sqls;
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
		if (isset($attributes['CONSTRAINT']) && str_contains((string) $attributes['TYPE'], 'INT'))
		{
			unset($attributes['CONSTRAINT']);
		}

		switch (strtoupper((string) $attributes['TYPE']))
		{
			case 'MEDIUMINT':
				$attributes['TYPE'] = 'INTEGER';
				$attributes['UNSIGNED'] = FALSE;
				return;
			case 'INTEGER':
				$attributes['TYPE'] = 'INT';
				return;
			default: return;
		}
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
		if ( !empty($attributes['AUTO_INCREMENT']) && $attributes['AUTO_INCREMENT'] === TRUE && stripos((string) $field['type'], 'int') !== FALSE)
		{
			$field['auto_increment'] = ' IDENTITY(1,1)';
		}
	}

}
