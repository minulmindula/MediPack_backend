<?php
// Rotate the logs using a system's `logrotate` utility
return array(
	'class'   => 'app\components\Logger',

	'logNameAct'   => 'act.log',
	'logNameAudit' => 'audit.log',
	'logNameError' => 'error.log',

	'logNameExtSys' => 'ext-sys.log',
);
