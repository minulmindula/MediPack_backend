<?php
/**
 * Application
 *
 * Base Controller responses with JSON strings
 *
 * @author       Amil Waduwawara
 * @version      $Id: v1.0.0 2017-Aug-26 Exp $;
 * @copyright    Copyright &copy; Omobio (Pvt.) Ltd., Sri Lanka.
 */
namespace app\components;

use Yii;
use yii\web\Response;

class JsonController extends Controller
{
	// Output data
	private $output = array(
		'success' => false,
		'code'    => 200,
		'error'   => null,
		'total'   => 0,
		'data'    => null,
	);


	// To get the detailed errors use this simple self::init()
	public function init1()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
	}

	public function init()
	{
		// Set to output in JSON, even errors
		// Overwrite generic `response` component
		Yii::$app->set('response', [
			'class' => 'yii\web\Response',
			'format' => Response::FORMAT_JSON,

			'on beforeSend' => function($event) {
				$response = $event->sender;

				// Capture any errors and populate in standard output
				// `self::afterAction(...) returns an object;
				// ErrorHandler returns an array
				// $response->data['type']
				//    - yii\base\ErrorException
				if (is_array($response->data)) {
					$this->logger->printStackTrace($response->data);

					$this->setOutputStatus($response->isSuccessful);
					$this->setOutputError($response->data['message'], $response->statusCode);
				}

				// Overwrite output
				$response->data = $this->getOutput();
			},
		]);
	}

	public function beforeAction($action)
	{
		// Clear output buffer to avoid rendering anything else
		@ob_clean();

		return parent::beforeAction($action);
	}

	public function afterAction($action, $result)
	{
		parent::afterAction($action, $result);

		return $this->getOutput();
	}


	public function getOutput()
	{
		return (object) $this->output;
	}

	protected function setOutput($output)
	{
		$this->output = $output;
	}

	protected function setOutputStatus($status = false)
	{
		$this->output['success'] = (boolean) $status;

		if ($this->output['success']) {
			$this->setOutputError();
		}
	}

	protected function setOutputError($error = null, $code = 200)
	{
		$this->output['code']  = $code;
		$this->output['error'] = $error;
	}

	protected function setOutputTotal($total = 0)
	{
		$this->output['total'] = 1*$total;
	}

	protected function setOutputData($data = null, $key = 'data')
	{
		$this->output[$key] = $data;
	}
}
