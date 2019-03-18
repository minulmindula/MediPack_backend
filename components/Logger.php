<?php
/**
 * Application
 *
 * Logger library
 *
 * @author       Amil Waduwawara
 * @version      $Id: v1.0.0 2017-Aug-25 Exp $;
 * @copyright    Copyright &copy; Omobio (Pvt.) Ltd., Sri Lanka.
 */
namespace app\components;

use yii\base\Component;

class Logger extends Component
{
	// Config parameters; must be public
	public $logBase = 'C:/error_log/';

	public $logNameAct   = 'act.log';
	public $logNameAudit = 'audit.log';
	public $logNameError = 'error.log';

	// For external systems
	public $logNameExtSys = 'ext-sys.log';

	private $remoteAddr = null;

	private $logParts = array(
		'hostAddr'         => '-',
		'sessionId'        => '-',
		'xFwddFor'         => '-',       // Actual X-Forwarded-For value or Client IP Address
		'loggedInUserId'   => '-',       // Logged in user Id
		'loggedInUserName' => '-',       // Logged in user name
	);

	private $delim = '|';


	/**
	 * Interface requirement
	 */
	public function init()
	{
		parent::init();

		$this->logNameAct   = "{$this->logBase}{$this->logNameAct}";
		$this->logNameAudit = "{$this->logBase}{$this->logNameAudit}";
		$this->logNameError = "{$this->logBase}{$this->logNameError}";
	}


	public function setHostAddr($value = '-')
	{
		$this->logParts['hostAddr'] = $value;
	}

	public function setFwdFor($value = '-')
	{
		$this->logParts['xFwddFor'] = $value;
	}

	public function setRemoteAddr($value = '-')
	{
		$this->remoteAddr = $value;
	}

	public function setSessionId($value = '-')
	{
		$this->logParts['sessionId'] = $value;
	}

	public function setLoggedInUserId($value = '-')
	{
		$this->logParts['loggedInUserId'] = $value;
	}

	public function setLoggedInUserName($value = '-')
	{
		$this->logParts['loggedInUserName'] = $value;
	}


	public function actLog($msg, $extApiStatus = false)
	{
		$msg = date('Y-m-d H:i:s') .
				$this->delim .
				implode($this->delim, $this->logParts) .
				"{$this->delim}{$msg}\n";

		file_put_contents(
			$this->logNameAct,
			$msg,
			FILE_APPEND
		);
	}

	public function extSysLog($sysCode, $reqType, $url, $params = null)
	{
		$msg = date('Y-m-d H:i:s') .
				$this->delim .
				implode($this->delim, array_merge($this->logParts, array(
					$sysCode,
					$reqType,
					$url,
					$params,
				))) .
				"\n";

		@file_put_contents(
			$this->logNameExtSys,
			$msg,
			FILE_APPEND
		);
	}

	public function printStackTrace($error)
	{
		// Delimitted parts
		$_parts = array_merge($this->logParts, array(
			sprintf('%s@%d', basename(@$error['file']), @$error['line']),
			$error['code'],
			$error['type'],
			$error['message'],
			"Stack trace:\n"
		));

		@file_put_contents(
			$this->logNameError,
			date('Y-m-d H:i:s') . $this->delim . implode($this->delim, $_parts) . implode("\n", $error['stack-trace']) . "\n",
			FILE_APPEND
		);
	}

	public function auditLog($module, $action, $isSuccess, $extra = null)
	{
		$extra = func_get_args();

		// Remove all proceeding args till `$extra`
		array_shift($extra);
		array_shift($extra);
		array_shift($extra);

		$_msg = date('Y-m-d H:i:s') .
				$this->delim .
				implode($this->delim, array_merge($this->logParts, array(
					$module,
					$action,
				), $extra)) . "\n";

		@file_put_contents(
			$this->logNameAudit,
			$_msg,
			FILE_APPEND
		);
	}

	public function debug()
	{
		$args = func_get_args();

		foreach ($args as &$_arg) {
			$_arg = print_r($_arg, true);
		}

		// Back-tracing info
		$_dbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

		// Prepend values
		array_unshift($args, sprintf('%s::%s@%d', @$_dbt[1]['class'], @$_dbt[1]['function'], @$_dbt[0]['line']));

		$out = "[{$this->logParts['sessionId']}] [DEBUG] " . implode(', ', $args);

		error_log($out);
// 		error_log(sprintf('%s, %s', date('Y-m-d H:i:s'), $out));
	}
}
