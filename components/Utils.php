<?php
/**
 * Application
 *
 * Misc. utility functions
 *
 * @author       Amil Waduwawara
 * @version      $Id: v1.0.0 2017-Aug-25 Exp $;
 * @copyright    Copyright &copy; Omobio (Pvt.) Ltd., Sri Lanka.
 */
namespace app\components;

use Yii;
use Exception;

class Utils
{

	public static function getUniqUserId()
	{
		date_default_timezone_set("Asia/Colombo");
		return date("dYms");
	}

	public static function purify($value)
	{
		if (is_array($value)) {
			foreach ($value as &$_elem) {
				$_elem = self::purify($_elem);
			}

			return $value;
		}

		return strip_tags($value);
	}


	public static function getUniqId()
	{
		// A better unique number value (for transaction handling)
		// NOTE: Casting to int will overflow on 32-bit PHP (for Windows) without giving 19-digit value
		return sprintf('%.0f%03d', 10000000*microtime(true), mt_rand(1, 999));
	}

	public static function encryptPassword($password)
	{
		return md5($password);
	}

	public static function genRandomPassword($isSimplePswd = false)
	{
		$_sec_config = Yii::$app->params['secConfig'];

		if (true === $isSimplePswd) {
			return sprintf(
				$_sec_config['defaultPswd'],
				mt_rand(0, pow(10, $_sec_config['pswdSuffixLen']) - 1)
			);
		}

		// Without confusing characters
		$source = array(
			'lower'   => 'abcdefghjkmnpqrstuvwxyz',      // Lowercase letters
			'upper'   => 'ABCDEFGHJKLMNPQRSTUVWXYZ',     // Uppercase letters
			'digit'   => '23456789',                     // Digits (2..9)
			'special' => '~!@#$%^&*()_+-=\[]{};:,./<>?', // Special symbols
		);

		$pswd = array();

		$s  = $source['lower'];
		$ub = strlen($s) - 1;
		for ($j = 0; $j < 3; $j++) {
			$pswd[] = $s[mt_rand(0, $ub)];
		}

		$s  = $source['upper'];
		$ub = strlen($s) - 1;
		for ($j = 0; $j < 3; $j++) {
			$pswd[] = $s[mt_rand(0, $ub)];
		}

		$s  = $source['digit'];
		$ub = strlen($s) - 1;
		for ($j = 0; $j < 2; $j++) {
			$pswd[] = $s[mt_rand(0, $ub)];
		}

		shuffle($pswd);

		return implode(null, $pswd);
	}

	/**
	 * Password validation
	 *   Rules:
	 *     - should contain some number of characters (e.g.: 8)
	 *     - should not contain all or part of the user's account name
	 *     - should contain at least three out of these four:
	 *         1. one number
	 *         2. one capital alphabet
	 *         3. one small alphabet
	 *         4. one special character (E.g.: !, ?, #, %, *)
	 *
	 * @param    string      $password           Password
	 * @param    string      $username           Username
	 * @param    array       $prevPswds          Previous passwords
	 *
	 * @throws   Exception                       Exception if password is not valid
	 * @return   string                          Encrypted password if it is valid
	 */
	
	public static function getValidPassword($password, $username, $prevPswds)
	{
		$_sec_config = Yii::$app->params['secConfig'];

		if (strlen($password) < $_sec_config['pswdMinLength']) {
			throw new Exception('Password too short', ExitCode::PSWD_SHORT);
		}

		if (false !== strpos(strtolower($password), strtolower($username))) {
			throw new Exception('Password contains user name', ExitCode::PSWD_HAS_UNAME);
		}

		// Extract character sequences of each category (lowercase, uppercase, digit, special)
		if (!preg_match_all('/(?P<lower>[a-z]*)(?P<upper>[A-Z]*)(?P<digit>[\d]*)(?P<special>[\W]*)/', $password, $_matches)) {
			throw new Exception('Password too simple', ExitCode::PSWD_SIMPLE);
		}

		// Remove empty elements
		foreach ($_matches as &$_inner) {
			foreach ($_inner as $_k => $_v) {
				if (!$_v) {
					unset($_inner[$_k]);
				}
			}
		}

		$strength_count = 0;

		if (!empty($_matches['lower']))   { $strength_count++; }
		if (!empty($_matches['upper']))   { $strength_count++; }
		if (!empty($_matches['digit']))   { $strength_count++; }
		if (!empty($_matches['special'])) { $strength_count++; }

		if (3 > $strength_count) {
			throw new Exception('Password too simple', ExitCode::PSWD_SIMPLE);
		}

		$enc_pswd = self::encryptPassword($password);

		if (in_array($enc_pswd, $prevPswds)) {
			throw new Exception('Password already used', ExitCode::PSWD_USED);
		}

		return $enc_pswd;
	}

	public static function formatPhoneNo($phoneNo, $isNoException = false)
	{
		$_config = Yii::$app->params['phoneNo'];

		$phoneNo = trim($phoneNo);

		$_matches = [];
		preg_match_all($_config['pattern'], $phoneNo, $_matches);

		if (@$_matches['nationalno'][0]) {
			return $_config['prefix']['country'] . $_matches['nationalno'][0];
		}

		if ($isNoException) {
			return null;
		}

		throw new Exception('Invalid MSISDN', ExitCode::INVALID_PHONE);
	}

	public static function sendSms($to, $msg)
	{
		Yii::$app->logger->debug("Sending msg; {to:'{$to}', msg:'{$msg}'}");

		if (!$to || !$msg) {
			Yii::$app->logger->actLog('Message not sent; empty recipient or empty message');
			return false;
		}

		return true;
	}
}
