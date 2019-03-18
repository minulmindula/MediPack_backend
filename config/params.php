<?php
return [
	'dbDateFormat' => 'Y-m-d',         // Date formats for Database DATE fields
	'dbTimeFormat' => 'Y-m-d H:i:s',   // Date/Time formats for Database DATE fields

	// Security related configs
	'secConfig' => array(
		'maxLoginAttempts' =>  5,      // Number of consecutive bad logins before account lock
		'accountLockTime'  => 15,      // In minutes
//		'pswdLife'         => 90,      // In days, beyond last password change
		'pswdExpiryAlert'  =>  7,      // Alert password expiry before this many days
		'pswdMinLength'    =>  8,      // Minimum password length

		'sessionIdleTime' => 1800,     // Session idle period in seconds
	),

	'phoneNo' => array(
		'pattern' => '/^((((\+?)|([0]{0})|([0]{2}))94)|(0?))?(?P<nationalno>((?P<opcode>\d{2})(?P<subsno>[\d]{7})))$/',

		'prefix' => array(
			'country' => '94',    // Country code
		),
	),

	'msgs' => array(
		// Short message templates
		'sms' => array(
			'username' => 'Your user account has been created in the System. Login name is %s.',
			'pswd'     => 'Your password is %s.',
		),
	),
];
