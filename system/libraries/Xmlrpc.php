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
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') || exit('No direct script access allowed');

if ( !function_exists('xml_parser_create'))
{
	show_error('Your PHP installation does not support XML');
}

// ------------------------------------------------------------------------

/**
 * XML-RPC request handler class.
 *
 * @category	XML-RPC
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/libraries/xmlrpc.html
 */
#[\AllowDynamicProperties]
class CI_Xmlrpc {

	/**
	 * Debug flag.
	 *
	 * @var	bool
	 */
	public $debug = FALSE;

	/**
	 * I4 data type.
	 *
	 * @var	string
	 */
	public $xmlrpcI4 = 'i4';

	/**
	 * Integer data type.
	 *
	 * @var	string
	 */
	public $xmlrpcInt = 'int';

	/**
	 * Boolean data type.
	 *
	 * @var	string
	 */
	public $xmlrpcBoolean = 'boolean';

	/**
	 * Double data type.
	 *
	 * @var	string
	 */
	public $xmlrpcDouble = 'double';

	/**
	 * String data type.
	 *
	 * @var	string
	 */
	public $xmlrpcString = 'string';

	/**
	 * DateTime format.
	 *
	 * @var	string
	 */
	public $xmlrpcDateTime = 'dateTime.iso8601';

	/**
	 * Base64 data type.
	 *
	 * @var	string
	 */
	public $xmlrpcBase64 = 'base64';

	/**
	 * Array data type.
	 *
	 * @var	string
	 */
	public $xmlrpcArray = 'array';

	/**
	 * Struct data type.
	 *
	 * @var	string
	 */
	public $xmlrpcStruct = 'struct';

	/**
	 * Data types list.
	 *
	 * @var	array
	 */
	public $xmlrpcTypes = [];

	/**
	 * Valid parents list.
	 *
	 * @var	array
	 */
	public $valid_parents = ['BOOLEAN' => ['VALUE'],
			'I4'				=> ['VALUE'],
			'INT'				=> ['VALUE'],
			'STRING'			=> ['VALUE'],
			'DOUBLE'			=> ['VALUE'],
			'DATETIME.ISO8601'	=> ['VALUE'],
			'BASE64'			=> ['VALUE'],
			'ARRAY'			=> ['VALUE'],
			'STRUCT'			=> ['VALUE'],
			'PARAM'			=> ['PARAMS'],
			'METHODNAME'		=> ['METHODCALL'],
			'PARAMS'			=> ['METHODCALL', 'METHODRESPONSE'],
			'MEMBER'			=> ['STRUCT'],
			'NAME'				=> ['MEMBER'],
			'DATA'				=> ['ARRAY'],
			'FAULT'			=> ['METHODRESPONSE'],
			'VALUE'			=> ['MEMBER', 'DATA', 'PARAM', 'FAULT'],
		];

	/**
	 * Response error numbers list.
	 *
	 * @var	array
	 */
	public $xmlrpcerr = [];

	/**
	 * Response error messages list.
	 *
	 * @var	string[]
	 */
	public $xmlrpcstr = [];

	/**
	 * Encoding charset.
	 *
	 * @var	string
	 */
	public $xmlrpc_defencoding = 'UTF-8';

	/**
	 * XML-RPC client name.
	 *
	 * @var	string
	 */
	public $xmlrpcName = 'XML-RPC for CodeIgniter';

	/**
	 * XML-RPC version.
	 *
	 * @var	string
	 */
	public $xmlrpcVersion = '1.1';

	/**
	 * Start of user errors.
	 *
	 * @var	int
	 */
	public $xmlrpcerruser = 800;

	/**
	 * Start of XML parse errors.
	 *
	 * @var	int
	 */
	public $xmlrpcerrxml = 100;

	/**
	 * Backslash replacement value.
	 *
	 * @var	string
	 */
	public $xmlrpc_backslash = '';

	/**
	 * XML-RPC Client object.
	 *
	 * @var	object
	 */
	public $client;

	/**
	 * XML-RPC Method name.
	 *
	 * @var	string
	 */
	public $method;

	/**
	 * XML-RPC Data.
	 *
	 * @var	array
	 */
	public $data;

	/**
	 * XML-RPC Message.
	 *
	 * @var	string
	 */
	public $message = '';

	/**
	 * Request error message.
	 *
	 * @var	string
	 */
	public $error = '';

	/**
	 * XML-RPC result object.
	 *
	 * @var	object
	 */
	public $result;

	/**
	 * XML-RPC Response.
	 *
	 * @var	array
	 */
	public $response = []; // Response from remote server

	/**
	 * XSS Filter flag.
	 *
	 * @var	bool
	 */
	public $xss_clean = TRUE;

	// --------------------------------------------------------------------

	/**
	 * Constructor.
	 *
	 * Initializes property default values
	 *
	 * @param	array	$config
	 * @return	void
	 */
	public function __construct($config = [])
	{
		$this->xmlrpc_backslash = chr(92) . chr(92);

		// Types for info sent back and forth
		$this->xmlrpcTypes = [
			$this->xmlrpcI4	 		=> '1',
			$this->xmlrpcInt		=> '1',
			$this->xmlrpcBoolean	=> '1',
			$this->xmlrpcString		=> '1',
			$this->xmlrpcDouble		=> '1',
			$this->xmlrpcDateTime	=> '1',
			$this->xmlrpcBase64		=> '1',
			$this->xmlrpcArray		=> '2',
			$this->xmlrpcStruct		=> '3',
		];

		// XML-RPC Responses
		$this->xmlrpcerr['unknown_method'] = '1';
		$this->xmlrpcstr['unknown_method'] = 'This is not a known method for this XML-RPC Server';
		$this->xmlrpcerr['invalid_return'] = '2';
		$this->xmlrpcstr['invalid_return'] = 'The XML data received was either invalid or not in the correct form for XML-RPC. Turn on debugging to examine the XML data further.';
		$this->xmlrpcerr['incorrect_params'] = '3';
		$this->xmlrpcstr['incorrect_params'] = 'Incorrect parameters were passed to method';
		$this->xmlrpcerr['introspect_unknown'] = '4';
		$this->xmlrpcstr['introspect_unknown'] = 'Cannot inspect signature for request: method unknown';
		$this->xmlrpcerr['http_error'] = '5';
		$this->xmlrpcstr['http_error'] = "Did not receive a '200 OK' response from remote server.";
		$this->xmlrpcerr['no_data'] = '6';
		$this->xmlrpcstr['no_data'] = 'No data received from server.';

		$this->initialize($config);

		log_message('info', 'XML-RPC Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize.
	 *
	 * @param	array	$config
	 * @return	void
	 */
	public function initialize($config = [])
	{
		if (count($config) > 0)
		{
			foreach ($config as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Parse server URL.
	 *
	 * @param	string	$url
	 * @param	int	$port
	 * @param	string	$proxy
	 * @param	int	$proxy_port
	 * @return	void
	 */
	public function server($url, $port = 80, $proxy = FALSE, $proxy_port = 8080)
	{
		if (stripos($url, 'http') !== 0)
		{
			$url = 'http://' . $url;
		}

		$parts = parse_url($url);

		if (isset($parts['user'], $parts['pass']))
		{
			$parts['host'] = $parts['user'] . ':' . $parts['pass'] . '@' . $parts['host'];
		}

		$path = $parts['path'] ?? '/';

		if ( isset($parts['query']) && ($parts['query'] !== '' && $parts['query'] !== '0'))
		{
			$path .= '?' . $parts['query'];
		}

		$this->client = new XML_RPC_Client($path, $parts['host'], $port, $proxy, $proxy_port);
	}

	// --------------------------------------------------------------------

	/**
	 * Set Timeout.
	 *
	 * @param	int	$seconds
	 * @return	void
	 */
	public function timeout($seconds = 5)
	{
		if ($this->client !== NULL && is_int($seconds))
		{
			$this->client->timeout = $seconds;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set Methods.
	 *
	 * @param	string	$function	Method name
	 * @return	void
	 */
	public function method($function)
	{
		$this->method = $function;
	}

	// --------------------------------------------------------------------

	/**
	 * Take Array of Data and Create Objects.
	 *
	 * @param	array	$incoming
	 * @return	void
	 */
	public function request($incoming)
	{
		if ( !is_array($incoming))
		{
			// Send Error
			return;
		}

		$this->data = [];

		foreach ($incoming as $key => $value)
		{
			$this->data[$key] = $this->values_parsing($value);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set Debug.
	 *
	 * @param	bool	$flag
	 * @return	void
	 */
	public function set_debug($flag = TRUE)
	{
		$this->debug = ($flag === TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Values Parsing.
	 *
	 * @param	mixed	$value
	 * @return	object
	 */
	public function values_parsing($value)
	{
		if (is_array($value) && array_key_exists(0, $value))
		{
			if ( !isset($value[1], $this->xmlrpcTypes[$value[1]]))
			{
				$temp = new XML_RPC_Values($value[0], (is_array($value[0]) ? 'array' : 'string'));
			}
			else
			{
				if (is_array($value[0]) && ($value[1] === 'struct' || $value[1] === 'array'))
				{
					foreach (array_keys($value[0]) as $k)
					{
						$value[0][$k] = $this->values_parsing($value[0][$k]);
					}
				}

				$temp = new XML_RPC_Values($value[0], $value[1]);
			}
		}
		else
		{
			$temp = new XML_RPC_Values($value, 'string');
		}

		return $temp;
	}

	// --------------------------------------------------------------------

	/**
	 * Sends XML-RPC Request.
	 *
	 * @return	bool
	 */
	public function send_request()
	{
		$this->message = new XML_RPC_Message($this->method, $this->data);
		$this->message->debug = $this->debug;

		if ( !$this->result = $this->client->send($this->message) || !is_object($this->result->val))
		{
			$this->error = $this->result->errstr;
			return FALSE;
		}

		$this->response = $this->result->decode();
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns Error.
	 *
	 * @return	string
	 */
	public function display_error()
	{
		return $this->error;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns Remote Server Response.
	 *
	 * @return	string
	 */
	public function display_response()
	{
		return $this->response;
	}

	// --------------------------------------------------------------------

	/**
	 * Sends an Error Message for Server Request.
	 *
	 * @param	int	$number
	 * @param	string	$message
	 * @return	object
	 */
	public function send_error_message($number, $message)
	{
		return new XML_RPC_Response(0, $number, $message);
	}

	// --------------------------------------------------------------------

	/**
	 * Send Response for Server Request.
	 *
	 * @param	array	$response
	 * @return	object
	 */
	public function send_response($response)
	{
		// $response should be array of values, which will be parsed
		// based on their data and type into a valid group of XML-RPC values
		return new XML_RPC_Response($this->values_parsing($response));
	}

} // END XML_RPC Class

/**
 * XML-RPC Client class.
 *
 * @category	XML-RPC
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/libraries/xmlrpc.html
 */
#[\AllowDynamicProperties]
class XML_RPC_Client extends CI_Xmlrpc
{
	/**
	 * Server hostname.
	 *
	 * @var	string
	 */
	public $server = '';

	/**
	 * Server username.
	 *
	 * @var	string
	 */
	public $username;

	/**
	 * Server password.
	 *
	 * @var	string
	 */
	public $password;

	/**
	 * Error number.
	 *
	 * @var	string
	 */
	public $errno = '';

	/**
	 * Error message.
	 *
	 * @var	string
	 */
	public $errstring = '';

	/**
	 * Timeout in seconds.
	 *
	 * @var	int
	 */
	public $timeout = 5;

	/**
	 * No Multicall flag.
	 *
	 * @var	bool
	 */
	public $no_multicall = FALSE;

	// --------------------------------------------------------------------

	/**
	 * Constructor.
	 *
	 * @param	string	$path
	 * @param	object	$server
	 * @param	int	$port
	 * @param	string	$proxy
	 * @param	int	$proxy_port
	 * @return	void
	 */
	public function __construct(/**
     * Path.
     */
    public $path,
	    $server, /**
     * Server port.
     */
    public $port = 80, /**
     * Proxy hostname.
     */
    public $proxy = FALSE, /**
     * Proxy port.
     */
    public $proxy_port = 8080
	)
	{
		parent::__construct();

		$url = parse_url('http://' . $server);

		if (isset($url['user'], $url['pass']))
		{
			$this->username = $url['user'];
			$this->password = $url['pass'];
		}
		$this->server = $url['host'];
	}

	// --------------------------------------------------------------------

	/**
	 * Send message.
	 *
	 * @param	mixed	$msg
	 * @return	object
	 */
	public function send($msg)
	{
		if (is_array($msg))
		{
			// Multi-call disabled
			return new XML_RPC_Response(0, $this->xmlrpcerr['multicall_recursion'], $this->xmlrpcstr['multicall_recursion']);
		}

		return $this->sendPayload($msg);
	}

	// --------------------------------------------------------------------

	/**
	 * Send payload.
	 *
	 * @param	object	$msg
	 * @return	object
	 */
	public function sendPayload($msg)
	{
		if ($this->proxy === FALSE)
		{
			$server = $this->server;
			$port = $this->port;
		}
		else
		{
			$server = $this->proxy;
			$port = $this->proxy_port;
		}

		$fp = @fsockopen($server, $port, $this->errno, $this->errstring, $this->timeout);

		if ( !is_resource($fp))
		{
			error_log($this->xmlrpcstr['http_error']);
			return new XML_RPC_Response(0, $this->xmlrpcerr['http_error'], $this->xmlrpcstr['http_error']);
		}

		if (empty($msg->payload))
		{
			// $msg = XML_RPC_Messages
			$msg->createPayload();
		}

		$r = "\r\n";
		$op = 'POST ' . $this->path . ' HTTP/1.0' . $r
			. 'Host: ' . $this->server . $r
			. 'Content-Type: text/xml' . $r
			. ($this->username !== null && $this->password !== null ? 'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password) . $r : '')
			. 'User-Agent: ' . $this->xmlrpcName . $r
			. 'Content-Length: ' . strlen((string) $msg->payload) . $r . $r
			. $msg->payload;

		stream_set_timeout($fp, $this->timeout); // set timeout for subsequent operations

		for ($written = $timestamp = 0, $length = strlen($op); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($op, $written))) === FALSE)
			{
				break;
			}
			// See https://bugs.php.net/bug.php?id=39598 and http://php.net/manual/en/function.fwrite.php#96951
			elseif ($result === 0)
			{
				if ($timestamp === 0)
				{
					$timestamp = time();
				}
				elseif ($timestamp < (time() - $this->timeout))
				{
					$result = FALSE;
					break;
				}
			}
			else
			{
				$timestamp = 0;
			}
		}

		if ($result === FALSE)
		{
			error_log($this->xmlrpcstr['http_error']);
			return new XML_RPC_Response(0, $this->xmlrpcerr['http_error'], $this->xmlrpcstr['http_error']);
		}

		$resp = $msg->parseResponse($fp);
		fclose($fp);
		return $resp;
	}

} // END XML_RPC_Client Class

/**
 * XML-RPC Response class.
 *
 * @category	XML-RPC
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/libraries/xmlrpc.html
 */
#[\AllowDynamicProperties]
class XML_RPC_Response
{

	/**
	 * Value.
	 *
	 * @var	mixed
	 */
	public $val = 0;

	/**
	 * Error number.
	 *
	 * @var	int
	 */
	public $errno = 0;

	/**
	 * Error message.
	 *
	 * @var	string
	 */
	public $errstr = '';

	/**
	 * Headers list.
	 *
	 * @var	array
	 */
	public $headers = [];

	/**
	 * XSS Filter flag.
	 *
	 * @var	bool
	 */
	public $xss_clean = TRUE;

	// --------------------------------------------------------------------

	/**
	 * Constructor.
	 *
	 * @param	mixed	$val
	 * @param	int	$code
	 * @param	string	$fstr
	 * @return	void
	 */
	public function __construct($val, $code = 0, $fstr = '')
	{
		if ($code !== 0)
		{
			// error
			$this->errno = $code;
			$this->errstr = htmlspecialchars(
			    $fstr,
			    (is_php('5.4') ? ENT_XML1 | ENT_NOQUOTES : ENT_NOQUOTES),
			    'UTF-8'
			);
		}
		elseif ( !is_object($val))
		{
			// programmer error, not an object
			error_log("Invalid type '" . gettype($val) . "' (value: " . $val . ') passed to XML_RPC_Response. Defaulting to empty value.');
			$this->val = new XML_RPC_Values();
		}
		else
		{
			$this->val = $val;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fault code.
	 *
	 * @return	int
	 */
	public function faultCode()
	{
		return $this->errno;
	}

	// --------------------------------------------------------------------

	/**
	 * Fault string.
	 *
	 * @return	string
	 */
	public function faultString()
	{
		return $this->errstr;
	}

	// --------------------------------------------------------------------

	/**
	 * Value.
	 *
	 * @return	mixed
	 */
	public function value()
	{
		return $this->val;
	}

	// --------------------------------------------------------------------

	/**
	 * Prepare response.
	 *
	 * @return	string	xml
	 */
	public function prepare_response()
	{
		return "<methodResponse>\n"
			. ($this->errno
				? '<fault>
	<value>
		<struct>
			<member>
				<name>faultCode</name>
				<value><int>' . $this->errno . '</int></value>
			</member>
			<member>
				<name>faultString</name>
				<value><string>' . $this->errstr . '</string></value>
			</member>
		</struct>
	</value>
</fault>'
				: "<params>\n<param>\n" . $this->val->serialize_class() . "</param>\n</params>")
			. "\n</methodResponse>";
	}

	// --------------------------------------------------------------------

	/**
	 * Decode.
	 *
	 * @param	mixed	$array
	 * @return	array
	 */
	public function decode($array = NULL)
	{
		$CI = &get_instance();

		if (is_array($array))
		{
			foreach ($array as $key => &$value)
			{
				if (is_array($value))
				{
					$array[$key] = $this->decode($value);
				}
				elseif ($this->xss_clean)
				{
					$array[$key] = $CI->security->xss_clean($value);
				}
			}

			return $array;
		}

		$result = $this->xmlrpc_decoder($this->val);

		if (is_array($result))
		{
			$result = $this->decode($result);
		}
		elseif ($this->xss_clean)
		{
			$result = $CI->security->xss_clean($result);
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * XML-RPC Object to PHP Types.
	 *
	 * @param	object
	 * @return	array
	 */
	public function xmlrpc_decoder($xmlrpc_val)
	{
		$kind = $xmlrpc_val->kindOf();

		if ($kind === 'scalar')
		{
			return $xmlrpc_val->scalarval();
		}
		elseif ($kind === 'array')
		{
			reset($xmlrpc_val->me);
			$b = current($xmlrpc_val->me);
			$arr = [];

			for ($i = 0, $size = count($b); $i < $size; $i++)
			{
				$arr[] = $this->xmlrpc_decoder($xmlrpc_val->me['array'][$i]);
			}
			return $arr;
		}
		elseif ($kind === 'struct')
		{
			reset($xmlrpc_val->me['struct']);
			$arr = [];

			foreach ($xmlrpc_val->me['struct'] as $key => &$value)
			{
				$arr[$key] = $this->xmlrpc_decoder($value);
			}

			return $arr;
		}
        return null;
	}

	// --------------------------------------------------------------------

	/**
	 * ISO-8601 time to server or UTC time.
	 *
	 * @param	string
	 * @param	bool
	 * @return	int	unix timestamp
	 */
	public function iso8601_decode($time, $utc = FALSE)
	{
		// Return a time in the localtime, or UTC
		$t = 0;
		if (preg_match('/(\d{4})(\d{2})(\d{2})T(\d{2}):(\d{2}):(\d{2})/', (string) $time, $regs))
		{
			$fnc = ($utc === TRUE) ? 'gmmktime' : 'mktime';
			$t = $fnc($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
		}
		return $t;
	}

} // END XML_RPC_Response Class

/**
 * XML-RPC Message class.
 *
 * @category	XML-RPC
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/libraries/xmlrpc.html
 */
#[\AllowDynamicProperties]
class XML_RPC_Message extends CI_Xmlrpc
{

	/**
	 * Payload.
	 *
	 * @var	string
	 */
	public $payload;

	/**
	 * Parameter list.
	 *
	 * @var	array
	 */
	public $params = [];

	/**
	 * XH?
	 *
	 * @var	array
	 */
	public $xh = [];

	// --------------------------------------------------------------------
    /**
     * Constructor.
     *
     * @param string $method_name
     * @param	array	$pars
     * @return	void
     */
    public function __construct(/**
     * Method name.
     */
    public $method_name,
        $pars = FALSE
    )
	{
		parent::__construct();
		if (is_array($pars) && count($pars) > 0)
		{
			for ($i = 0, $c = count($pars); $i < $c; $i++)
			{
				// $pars[$i] = XML_RPC_Values
				$this->params[] = $pars[$i];
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Create Payload to Send.
	 *
	 * @return	void
	 */
	public function createPayload()
	{
		$this->payload = '<?xml version="1.0"?' . ">\r\n<methodCall>\r\n"
				. '<methodName>' . $this->method_name . "</methodName>\r\n"
				. "<params>\r\n";

		for ($i = 0, $c = count($this->params); $i < $c; $i++)
		{
			// $p = XML_RPC_Values
			$p = $this->params[$i];
			$this->payload .= "<param>\r\n" . $p->serialize_class() . "</param>\r\n";
		}

		$this->payload .= "</params>\r\n</methodCall>\r\n";
	}

	// --------------------------------------------------------------------

	/**
	 * Parse External XML-RPC Server's Response.
	 *
	 * @param	resource
	 * @return	object
	 */
	public function parseResponse($fp)
	{
		$data = '';

		while ($datum = fread($fp, 4096))
		{
			$data .= $datum;
		}

		// Display HTTP content for debugging
		if ($this->debug === TRUE)
		{
			echo "<pre>---DATA---\n" . htmlspecialchars($data) . "\n---END DATA---\n\n</pre>";
		}

		// Check for data
		if ($data === '')
		{
			error_log($this->xmlrpcstr['no_data']);
			return new XML_RPC_Response(0, $this->xmlrpcerr['no_data'], $this->xmlrpcstr['no_data']);
		}

		// Check for HTTP 200 Response
		if (str_starts_with($data, 'HTTP') && !preg_match('/^HTTP\/[0-9\.]+ 200 /', $data))
		{
			$errstr = substr($data, 0, strpos($data, "\n") - 1);
			return new XML_RPC_Response(0, $this->xmlrpcerr['http_error'], $this->xmlrpcstr['http_error'] . ' (' . $errstr . ')');
		}

		//-------------------------------------
		// Create and Set Up XML Parser
		//-------------------------------------

		$parser = xml_parser_create($this->xmlrpc_defencoding);
		$pname = (string) $parser;
		$this->xh[$pname] = [
			'isf'		=> 0,
			'ac'		=> '',
			'headers'	=> [],
			'stack'		=> [],
			'valuestack'	=> [],
			'isf_reason'	=> 0,
		];

		xml_set_object($parser, $this);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, TRUE);
		xml_set_element_handler($parser, 'open_tag', 'closing_tag');
		xml_set_character_data_handler($parser, 'character_data');
		//xml_set_default_handler($parser, 'default_handler');

		// Get headers
		$lines = explode("\r\n", $data);
		while (($line = array_shift($lines)))
		{
			if (strlen($line) < 1)
			{
				break;
			}
			$this->xh[$pname]['headers'][] = $line;
		}
		$data = implode("\r\n", $lines);

		// Parse XML data
		if ( xml_parse($parser, $data, TRUE) === 0)
		{
			$errstr = sprintf(
			    'XML error: %s at line %d',
			    xml_error_string(xml_get_error_code($parser)),
			    xml_get_current_line_number($parser)
			);

			$r = new XML_RPC_Response(0, $this->xmlrpcerr['invalid_return'], $this->xmlrpcstr['invalid_return']);
			xml_parser_free($parser);
			return $r;
		}
		xml_parser_free($parser);

		// Got ourselves some badness, it seems
		if ($this->xh[$pname]['isf'] > 1)
		{
			if ($this->debug === TRUE)
			{
				echo "---Invalid Return---\n" . $this->xh[$pname]['isf_reason'] . "---Invalid Return---\n\n";
			}

			return new XML_RPC_Response(0, $this->xmlrpcerr['invalid_return'], $this->xmlrpcstr['invalid_return'] . ' ' . $this->xh[$pname]['isf_reason']);
		}
		elseif ( !is_object($this->xh[$pname]['value']))
		{
			return new XML_RPC_Response(0, $this->xmlrpcerr['invalid_return'], $this->xmlrpcstr['invalid_return'] . ' ' . $this->xh[$pname]['isf_reason']);
		}

		// Display XML content for debugging
		if ($this->debug === TRUE)
		{
			echo '<pre>';

			if (count($this->xh[$pname]['headers']) > 0)
			{
				echo "---HEADERS---\n";
				foreach ($this->xh[$pname]['headers'] as $header)
				{
					echo $header . "\n";
				}
				echo "---END HEADERS---\n\n";
			}

			echo "---DATA---\n" . htmlspecialchars($data) . "\n---END DATA---\n\n---PARSED---\n";
			var_dump($this->xh[$pname]['value']);
			echo "\n---END PARSED---</pre>";
		}

		// Send response
		$v = $this->xh[$pname]['value'];
		if ($this->xh[$pname]['isf'])
		{
			$errno_v = $v->me['struct']['faultCode'];
			$errstr_v = $v->me['struct']['faultString'];
			$errno = $errno_v->scalarval();

			if ($errno === 0)
			{
				// FAULT returned, errno needs to reflect that
				$errno = -1;
			}

			$r = new XML_RPC_Response($v, $errno, $errstr_v->scalarval());
		}
		else
		{
			$r = new XML_RPC_Response($v);
		}

		$r->headers = $this->xh[$pname]['headers'];
		return $r;
	}

	// --------------------------------------------------------------------

	// ------------------------------------
	//  Begin Return Message Parsing section
	// ------------------------------------

	// quick explanation of components:
	//   ac - used to accumulate values
	//   isf - used to indicate a fault
	//   lv - used to indicate "looking for a value": implements
	//		the logic to allow values with no types to be strings
	//   params - used to store parameters in method calls
	//   method - used to store method name
	//	 stack - array with parent tree of the xml element,
	//			 used to validate the nesting of elements

	// --------------------------------------------------------------------

	/**
	 * Start Element Handler.
	 *
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function open_tag($the_parser, $name)
	{
		$the_parser = (string) $the_parser;

		// If invalid nesting, then return
		if ($this->xh[$the_parser]['isf'] > 1) {
            return;
        }

		// Evaluate and check for correct nesting of XML elements
		if (count($this->xh[$the_parser]['stack']) === 0)
		{
			if ($name !== 'METHODRESPONSE' && $name !== 'METHODCALL')
			{
				$this->xh[$the_parser]['isf'] = 2;
				$this->xh[$the_parser]['isf_reason'] = 'Top level XML-RPC element is missing';
				return;
			}
		}
		// not top level element: see if parent is OK
		elseif ( !in_array($this->xh[$the_parser]['stack'][0], $this->valid_parents[$name], TRUE))
		{
			$this->xh[$the_parser]['isf'] = 2;
			$this->xh[$the_parser]['isf_reason'] = 'XML-RPC element ' . $name . ' cannot be child of ' . $this->xh[$the_parser]['stack'][0];
			return;
		}

		switch ($name)
		{
			case 'STRUCT':
			case 'ARRAY':
				// Creates array for child elements
				$cur_val = ['value' => [], 'type' => $name];
				array_unshift($this->xh[$the_parser]['valuestack'], $cur_val);
				break;
			case 'METHODNAME':
			case 'NAME':
				$this->xh[$the_parser]['ac'] = '';
				break;
			case 'FAULT':
				$this->xh[$the_parser]['isf'] = 1;
				break;
			case 'PARAM':
				$this->xh[$the_parser]['value'] = NULL;
				break;
			case 'VALUE':
				$this->xh[$the_parser]['vt'] = 'value';
				$this->xh[$the_parser]['ac'] = '';
				$this->xh[$the_parser]['lv'] = 1;
				break;
			case 'I4':
			case 'INT':
			case 'STRING':
			case 'BOOLEAN':
			case 'DOUBLE':
			case 'DATETIME.ISO8601':
			case 'BASE64':
				if ($this->xh[$the_parser]['vt'] !== 'value')
				{
					//two data elements inside a value: an error occurred!
					$this->xh[$the_parser]['isf'] = 2;
					$this->xh[$the_parser]['isf_reason'] = 'There is a ' . $name . ' element following a '
										. $this->xh[$the_parser]['vt'] . ' element inside a single value';
					return;
				}

				$this->xh[$the_parser]['ac'] = '';
				break;
			case 'MEMBER':
				// Set name of <member> to nothing to prevent errors later if no <name> is found
				$this->xh[$the_parser]['valuestack'][0]['name'] = '';

				// Set NULL value to check to see if value passed for this param/member
				$this->xh[$the_parser]['value'] = NULL;
				break;
			case 'DATA':
			case 'METHODCALL':
			case 'METHODRESPONSE':
			case 'PARAMS':
				// valid elements that add little to processing
				break;
			default:
				/// An Invalid Element is Found, so we have trouble
				$this->xh[$the_parser]['isf'] = 2;
				$this->xh[$the_parser]['isf_reason'] = 'Invalid XML-RPC element found: ' . $name;
				break;
		}

		// Add current element name to stack, to allow validation of nesting
		array_unshift($this->xh[$the_parser]['stack'], $name);

		if ($name !== 'VALUE') {
            $this->xh[$the_parser]['lv'] = 0;
        }
	}

	// --------------------------------------------------------------------

	/**
	 * End Element Handler.
	 *
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function closing_tag($the_parser, $name)
	{
		$the_parser = (string) $the_parser;

		if ($this->xh[$the_parser]['isf'] > 1) {
            return;
        }

		// Remove current element from stack and set variable
		// NOTE: If the XML validates, then we do not have to worry about
		// the opening and closing of elements. Nesting is checked on the opening
		// tag so we be safe there as well.

		array_shift($this->xh[$the_parser]['stack']);

		switch ($name)
		{
			case 'STRUCT':
			case 'ARRAY':
				$cur_val = array_shift($this->xh[$the_parser]['valuestack']);
				$this->xh[$the_parser]['value'] = $cur_val['values'] ?? [];
				$this->xh[$the_parser]['vt'] = strtolower((string) $name);
				break;
			case 'NAME':
				$this->xh[$the_parser]['valuestack'][0]['name'] = $this->xh[$the_parser]['ac'];
				break;
			case 'BOOLEAN':
			case 'I4':
			case 'INT':
			case 'STRING':
			case 'DOUBLE':
			case 'DATETIME.ISO8601':
			case 'BASE64':
				$this->xh[$the_parser]['vt'] = strtolower((string) $name);

				if ($name === 'STRING')
				{
					$this->xh[$the_parser]['value'] = $this->xh[$the_parser]['ac'];
				}
				elseif ($name === 'DATETIME.ISO8601')
				{
					$this->xh[$the_parser]['vt'] = $this->xmlrpcDateTime;
					$this->xh[$the_parser]['value'] = $this->xh[$the_parser]['ac'];
				}
				elseif ($name === 'BASE64')
				{
					$this->xh[$the_parser]['value'] = base64_decode((string) $this->xh[$the_parser]['ac']);
				}
				elseif ($name === 'BOOLEAN')
				{
					// Translated BOOLEAN values to TRUE AND FALSE
					$this->xh[$the_parser]['value'] = (bool) $this->xh[$the_parser]['ac'];
				}
				elseif ($name == 'DOUBLE')
				{
					// we have a DOUBLE
					// we must check that only 0123456789-.<space> are characters here
					$this->xh[$the_parser]['value'] = preg_match('/^[+-]?[eE0-9\t \.]+$/', (string) $this->xh[$the_parser]['ac'])
										? (float) $this->xh[$the_parser]['ac']
										: 'ERROR_NON_NUMERIC_FOUND';
				}
				else
				{
					// we have an I4/INT
					// we must check that only 0123456789-<space> are characters here
					$this->xh[$the_parser]['value'] = preg_match('/^[+-]?[0-9\t ]+$/', (string) $this->xh[$the_parser]['ac'])
										? (int) $this->xh[$the_parser]['ac']
										: 'ERROR_NON_NUMERIC_FOUND';
				}
				$this->xh[$the_parser]['ac'] = '';
				$this->xh[$the_parser]['lv'] = 3; // indicate we've found a value
				break;
			case 'VALUE':
				// This if() detects if no scalar was inside <VALUE></VALUE>
				if ($this->xh[$the_parser]['vt'] == 'value')
				{
					$this->xh[$the_parser]['value'] = $this->xh[$the_parser]['ac'];
					$this->xh[$the_parser]['vt'] = $this->xmlrpcString;
				}

				// build the XML-RPC value out of the data received, and substitute it
				$temp = new XML_RPC_Values($this->xh[$the_parser]['value'], $this->xh[$the_parser]['vt']);

				if (count($this->xh[$the_parser]['valuestack']) && $this->xh[$the_parser]['valuestack'][0]['type'] === 'ARRAY')
				{
					// Array
					$this->xh[$the_parser]['valuestack'][0]['values'][] = $temp;
				}
				else
				{
					// Struct
					$this->xh[$the_parser]['value'] = $temp;
				}
				break;
			case 'MEMBER':
				$this->xh[$the_parser]['ac'] = '';

				// If value add to array in the stack for the last element built
				if ($this->xh[$the_parser]['value'])
				{
					$this->xh[$the_parser]['valuestack'][0]['values'][$this->xh[$the_parser]['valuestack'][0]['name']] = $this->xh[$the_parser]['value'];
				}
				break;
			case 'DATA':
				$this->xh[$the_parser]['ac'] = '';
				break;
			case 'PARAM':
				if ($this->xh[$the_parser]['value'])
				{
					$this->xh[$the_parser]['params'][] = $this->xh[$the_parser]['value'];
				}
				break;
			case 'METHODNAME':
				$this->xh[$the_parser]['method'] = ltrim((string) $this->xh[$the_parser]['ac']);
				break;
			case 'PARAMS':
			case 'FAULT':
			case 'METHODCALL':
			case 'METHORESPONSE':
				// We're all good kids with nuthin' to do
				break;
			default:
				// End of an Invalid Element. Taken care of during the opening tag though
				break;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Parse character data.
	 *
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	public function character_data($the_parser, $data)
	{
		$the_parser = (string) $the_parser;

		if ($this->xh[$the_parser]['isf'] > 1) {
            return;
        } // XML Fault found already

		// If a value has not been found
		if ($this->xh[$the_parser]['lv'] !== 3)
		{
			if ($this->xh[$the_parser]['lv'] === 1)
			{
				$this->xh[$the_parser]['lv'] = 2; // Found a value
			}

			if ( !isset($this->xh[$the_parser]['ac']))
			{
				$this->xh[$the_parser]['ac'] = '';
			}

			$this->xh[$the_parser]['ac'] .= $data;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add parameter.
	 *
	 * @param	mixed
	 * @return	void
	 */
	public function addParam($par)
	{
		$this->params[] = $par;
	}

	// --------------------------------------------------------------------
    /**
     * Output parameters.
     *
     * @return	array
     */
    public function output_parameters(array $array = [])
	{
		$CI = &get_instance();

		if ( $array !== [])
		{
			foreach ($array as $key => &$value)
			{
				if (is_array($value))
				{
					$array[$key] = $this->output_parameters($value);
				}
				elseif ($key !== 'bits' && $this->xss_clean)
				{
					// 'bits' is for the MetaWeblog API image bits
					// @todo - this needs to be made more general purpose
					$array[$key] = $CI->security->xss_clean($value);
				}
			}

			return $array;
		}

		$parameters = [];

		for ($i = 0, $c = count($this->params); $i < $c; $i++)
		{
			$a_param = $this->decode_message($this->params[$i]);

			if (is_array($a_param))
			{
				$parameters[] = $this->output_parameters($a_param);
			}
			else
			{
				$parameters[] = ($this->xss_clean) ? $CI->security->xss_clean($a_param) : $a_param;
			}
		}

		return $parameters;
	}

	// --------------------------------------------------------------------

	/**
	 * Decode message.
	 *
	 * @param	object
	 * @return	mixed
	 */
	public function decode_message($param)
	{
		$kind = $param->kindOf();

		if ($kind === 'scalar')
		{
			return $param->scalarval();
		}
		elseif ($kind === 'array')
		{
			reset($param->me);
			$b = current($param->me);
			$arr = [];

			for ($i = 0, $c = count($b); $i < $c; $i++)
			{
				$arr[] = $this->decode_message($param->me['array'][$i]);
			}

			return $arr;
		}
		elseif ($kind === 'struct')
		{
			reset($param->me['struct']);
			$arr = [];

			foreach ($param->me['struct'] as $key => &$value)
			{
				$arr[$key] = $this->decode_message($value);
			}

			return $arr;
		}
        return null;
	}

} // END XML_RPC_Message Class

/**
 * XML-RPC Values class.
 *
 * @category	XML-RPC
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/userguide3/libraries/xmlrpc.html
 */
#[\AllowDynamicProperties]
class XML_RPC_Values extends CI_Xmlrpc
{
	/**
	 * Value data.
	 *
	 * @var	array
	 */
	public $me = [];

	/**
	 * Value type.
	 *
	 * @var	int
	 */
	public $mytype = 0;

	// --------------------------------------------------------------------

	/**
	 * Constructor.
	 *
	 * @param	mixed	$val
	 * @param	string	$type
	 * @return	void
	 */
	public function __construct($val = -1, $type = '')
	{
		parent::__construct();

		if ($val !== -1 || $type !== '')
		{
			$type = $type === '' ? 'string' : $type;

			if ($this->xmlrpcTypes[$type] == 1)
			{
				$this->addScalar($val, $type);
			}
			elseif ($this->xmlrpcTypes[$type] == 2)
			{
				$this->addArray($val);
			}
			elseif ($this->xmlrpcTypes[$type] == 3)
			{
				$this->addStruct($val);
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add scalar value.
	 *
	 * @param	scalar
	 * @param	string
	 * @return	int
	 */
	public function addScalar($val, $type = 'string')
	{
		$typeof = $this->xmlrpcTypes[$type];

		if ($this->mytype === 1)
		{
			echo '<strong>XML_RPC_Values</strong>: scalar can have only one value<br />';
			return 0;
		}

		if ($typeof != 1)
		{
			echo '<strong>XML_RPC_Values</strong>: not a scalar type (${typeof})<br />';
			return 0;
		}

		if ($type === $this->xmlrpcBoolean)
		{
			$val = (int) (strcasecmp((string) $val, 'true') === 0 || $val === 1 || $val === TRUE && strcasecmp($val, 'false'));
		}

		if ($this->mytype === 2)
		{
			// adding to an array here
			$ar = $this->me['array'];
			$ar[] = new XML_RPC_Values($val, $type);
			$this->me['array'] = $ar;
		}
		else
		{
			// a scalar, so set the value and remember we're scalar
			$this->me[$type] = $val;
			$this->mytype = $typeof;
		}

		return 1;
	}

	// --------------------------------------------------------------------

	/**
	 * Add array value.
	 *
	 * @param	array
	 * @return	int
	 */
	public function addArray($vals)
	{
		if ($this->mytype !== 0)
		{
			echo '<strong>XML_RPC_Values</strong>: already initialized as a [' . $this->kindOf() . ']<br />';
			return 0;
		}

		$this->mytype = $this->xmlrpcTypes['array'];
		$this->me['array'] = $vals;
		return 1;
	}

	// --------------------------------------------------------------------

	/**
	 * Add struct value.
	 *
	 * @param	object
	 * @return	int
	 */
	public function addStruct($vals)
	{
		if ($this->mytype !== 0)
		{
			echo '<strong>XML_RPC_Values</strong>: already initialized as a [' . $this->kindOf() . ']<br />';
			return 0;
		}
		$this->mytype = $this->xmlrpcTypes['struct'];
		$this->me['struct'] = $vals;
		return 1;
	}

	// --------------------------------------------------------------------

	/**
	 * Get value type.
	 *
	 * @return	string
	 */
	public function kindOf()
	{
		return match ($this->mytype) {
            3 => 'struct',
            2 => 'array',
            1 => 'scalar',
            default => 'undef',
        };
	}

	// --------------------------------------------------------------------

	/**
	 * Serialize data.
	 *
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	public function serializedata($typ, $val)
	{
		$rs = '';

		switch ($this->xmlrpcTypes[$typ])
		{
			case 3:
				// struct
				$rs .= "<struct>\n";
				reset($val);
				foreach ($val as $key2 => &$val2)
				{
					$rs .= "<member>\n<name>{$key2}</name>\n" . $this->serializeval($val2) . "</member>\n";
				}
				$rs .= '</struct>';
				break;
			case 2:
				// array
				$rs .= "<array>\n<data>\n";
				for ($i = 0, $c = count($val); $i < $c; $i++)
				{
					$rs .= $this->serializeval($val[$i]);
				}
				$rs .= "</data>\n</array>\n";
				break;
			case 1:
				// others
				match ($typ) {
                    $this->xmlrpcBase64 => $rs .= '<' . $typ . '>' . base64_encode( (string) $val) . '</' . $typ . ">\n",
                    $this->xmlrpcBoolean => $rs .= '<' . $typ . '>' . ( (bool) $val ? '1' : '0') . '</' . $typ . ">\n",
                    $this->xmlrpcString => $rs .= '<' . $typ . '>' . htmlspecialchars( (string) $val) . '</' . $typ . ">\n",
                    default => $rs .= '<' . $typ . '>' . $val . '</' . $typ . ">\n",
                };
			default:
				break;
		}

		return $rs;
	}

	// --------------------------------------------------------------------

	/**
	 * Serialize class.
	 *
	 * @return	string
	 */
	public function serialize_class()
	{
		return $this->serializeval($this);
	}

	// --------------------------------------------------------------------

	/**
	 * Serialize value.
	 *
	 * @param	object
	 * @return	string
	 */
	public function serializeval($o)
	{
		$array = $o->me;
		[$value, $type] = [reset($array), key($array)];
		return "<value>\n" . $this->serializedata($type, $value) . "</value>\n";
	}

	// --------------------------------------------------------------------

	/**
	 * Scalar value.
	 *
	 * @return	mixed
	 */
	public function scalarval()
	{
		return reset($this->me);
	}

	// --------------------------------------------------------------------

	/**
	 * Encode time in ISO-8601 form.
	 * Useful for sending time in XML-RPC.
	 *
	 * @param	int	unix timestamp
	 * @param	bool
	 * @return	string
	 */
	public function iso8601_encode($time, $utc = FALSE)
	{
		return ($utc) ? date('Ymd\TH:i:s', $time) : gmdate('Ymd\TH:i:s', $time);
	}

} // END XML_RPC_Values Class
