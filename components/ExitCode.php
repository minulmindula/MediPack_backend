<?php
/**
 * Application
 *
 * The common repository of each and every exit code for APIs
 *
 * @author       Amil Waduwawara
 * @version      $Id: v1.0.0 2017-Aug-25 Exp $;
 * @copyright    Copyright &copy; Omobio (Pvt.) Ltd., Sri Lanka.
 */
namespace app\components;

class ExitCode
{
	// HTTP Codes
	const HTTP_BAD_REQUEST = 400;
	const HTTP_FORBIDDEN   = 403;
	const HTTP_ERROR       = 409;


	const INVALID_REQUEST   = 1000;
// 	const INSUFFICIENT_DATA = 1010;
// 	const SAVE_FAILED       = 1020;
// 	const DUPLICATE_ENTRY   = 1030;
// 	const DELETE_FAILED     = 1040;
// 	const UPLOAD_FAILED     = 1050;
// 	const INVALID_FORMAT    = 1060;
// 	const DUPLICATE_CONTENT = 1070;
	const REC_NOT_FOUND     = 1080;
// 	const USER_NOT_FOUND    = 1085;
// 	const USER_AMRM_OR_DSR  = 1086;
// 	const OPEN_FAILED       = 1090;
// 	const WRITE_FAILED      = 1095;
// 	const CLOSE_FAILED      = 1099;

// 	const API_ERROR         = 2000;
// 	const INVALID_RESPONSE  = 2010;

	const PSWD_SHORT        = 3010;
	const PSWD_HAS_UNAME    = 3020;
	const PSWD_SIMPLE       = 3030;
	const PSWD_NO_MATCH     = 3040;    // Passwords do not match
	const PSWD_WRONG_CURR   = 3050;    // Wrong current password
	const PSWD_USED         = 3060;    // Password already been used

	const AUTH_FAILED       = 5000;
// 	const ACC_LOCKED        = 5010;
// 	const FORCE_CHANGE_PSWD = 5020;
// 	const PSWD_EXPIRY       = 5030;
// 	const PSWD_EXPIRED      = 5040;
	const ACC_DISABLED      = 5050;

// 	// Invalid input values
	const INVALID_PHONE     = 6001;

// 	// Database errors
// 	const DB_DEADLOCK       = 7001;
// 	const DB_CONN_ERROR     = 7999;
}
