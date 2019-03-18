<?php
/**
 * Application
 *
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 *
 * @author       Amil Waduwawara
 * @version      $Id: v1.0.0 2017-Aug-21 Exp $;
 * @copyright    Copyright &copy; Omobio (Pvt.) Ltd., Sri Lanka.
 */
namespace app\components;

use Yii;
use yii\web\HttpException;

class Controller extends \yii\web\Controller
{
	protected $session    = null;
	protected $fwddFor    = null;
	protected $proxies    = null;
	protected $remoteAddr = null;
	protected $logger     = null;
	protected $user       = null;

	private $actionName = null;


	/**
	 * Generic authorization validation
	 *
	 * @param    CInlineAction         $action
	 * @throws   CHttpException
	 * @return   boolean
	 */

	public function beforeAction($action)
	{
		// Clear output buffer to avoid rendering anything else
		@ob_clean();

		// Begin: Strip metacharacters ------------------
		// GET variables have already been handled in `index.php`
		foreach ($_POST as &$_var) {
			$_var = Utils::purify($_var);
		}
		// End: Strip metacharacters --------------------

		$this->session = Yii::$app->session;

		// Set remote address; extract the first element if multiple addresses found
		//  - X-Forwarded-For: client, proxy1, proxy2, ...
		if (!$_fhxff = @$_SERVER['HTTP_X_FORWARDED_FOR']) {
			$_fhxff = @$_SERVER['REMOTE_ADDR'];
		}
		$_hxff = preg_split('/[\s,]+/', $_fhxff, -1, PREG_SPLIT_NO_EMPTY);

		$this->remoteAddr = array_shift($_hxff);
		$this->proxies    = implode(',', $_hxff);

		// Restore logger object from HTTP session, if exists
		if (!$this->logger = $this->session->get('logger')) {
			$this->logger = Yii::$app->logger;
		}

		$this->logger->setHostAddr(@$_SERVER['SERVER_ADDR']);   // Apache
//		$this->logger->setHostAddr(@$_SERVER['LOCAL_ADDR']);    // IIS

		$this->logger->setFwdFor($_fhxff);
		$this->logger->setRemoteAddr($this->remoteAddr);
		$this->logger->setSessionId($this->session->getId());

		// Update `Yii::$app->logger` as well, else tracking data may not be available outside Web-Controllers
		// NOTE: Will replace
		Yii::$app->set('logger', $this->logger);

		$this->actionName = "{$action->controller->id}/{$action->id}";

		// Gather necessary infor about browser and client
		$this->logger->actLog(sprintf(
			"Request {action:`%s`, remoteaddr:`%s`, fwdfor:`%s`, useragent:`%s`, referer:`%s`}",
			$this->actionName,
			@$_SERVER['REMOTE_ADDR'],
			@$_SERVER['HTTP_X_FORWARDED_FOR'],
			@$_SERVER['HTTP_USER_AGENT'],
			@$_SERVER['HTTP_REFERER']
		));

		// Restore user object from HTTP session, if exists
		$this->user = $this->session->get('user');

		return parent::beforeAction($action);
	}


	public function afterAction($action, $result)
	{
		$result = parent::afterAction($action, $result);

		$this->updateSessionParams();

		return $result;
	}


	// Begin: User defined methods --------------------------------------------
	/**
	 * Should be `public static`
	 *
	 * Will be called as a call-back from accessControl validation
	 *
	 * @throws   HttpException
	 */
	public static function showNotAuth($rule, $action)
	{
		throw new HttpException(401, 'Unauthorized');
	}

	protected function getFwddFor()
	{
		return $this->fwddFor;
	}

	protected function getProxies()
	{
		return $this->proxies;
	}

	protected function getRemoteAddr()
	{
		return $this->remoteAddr;
	}

	protected function generateAuthKey()
	{
		return md5($this->remoteAddr . @$_SERVER['HTTP_USER_AGENT']);
	}

	protected function updateSessionParams()
	{
		// Save for later use
		$this->session->set('user', $this->user);
		$this->session->set('logger', $this->logger);
	}

	protected function checkAuthnz($allowedRoles)
	{
		if (in_array(@$this->user->roleId, $allowedRoles)) {
			return true;
		}

		throw new HttpException(403, 'You are not authorized to perform this action');
	}

	/**
	 * Cross-domain request handling
	 *
	 * NOTE:
	 *  - Need to do be called before `self::accessRules()` validations as preflight requests do NOT contain cookie data
	 */
	protected function setupCorsHeaders()
	{
		$req_headers = getallheaders();

		// Value receieves as `Origin` or `origin`
		if (!$_origin = @$req_headers['Origin']) {
			$_origin = @$req_headers['origin'];
		}

		if ($_origin) {
//			header('HTTP/1.1 200 OK');
			header("Access-Control-Allow-Origin: {$_origin}");
			header('Access-Control-Allow-Methods: GET, POST');
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');

			// Stop going forward if request is not GET or POST
			if (!in_array($_SERVER['REQUEST_METHOD'], array('GET', 'POST'))) {
				Yii::app()->end();
			}
		}
	}
	// End: User defined methods ==============================================
}
