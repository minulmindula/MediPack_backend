<?php
/**
 * Application
 *
 * Base `ActiveRecord` for MySQL database
 *
 * @author       Amil Waduwawara
 * @version      $Id: v1.0.0 2017-Aug-26 Exp $;
 * @copyright    Copyright &copy; Omobio (Pvt.) Ltd.
 */
namespace app\components\db;

use Yii;

class ActiveRecord extends \yii\db\ActiveRecord
{
	const COND_LIKE = '%s LIKE \'%%%s%%\'';


	protected $logger    = null;
	protected $appParams = null;


	public function init()
	{
		// Extract elements only for easier access
		$this->appParams = Yii::$app->params;

		// Restore logger object from HTTP session, if exists
		if (isset(Yii::$app->session) && !$this->logger = Yii::$app->session->get('logger')) {
			$this->logger = Yii::$app->logger;
		} elseif (!isset(Yii::$app->session)) {
			$this->logger = Yii::$app->logger;
		}

		// Update `Yii::$app->logger` as well, else tracking data may not be available outside Web-Controllers
		// NOTE: Will replace
		Yii::$app->set('logger', $this->logger);
	}


	// Begin: User defined methods --------------------------------------------
	public static function buildQueryFromFilters($filters, &$query, $defaultAlias = 'T')
	{
		if (!$filters) {
			return;
		}

		foreach ($filters as $_filter) {
			// Comparison condition
			if (isset($_filter['data']['comparison'])) {
				switch ($_filter['data']['comparison']) {
					case 'eq':
						$comparison = '=';
						break;

					case 'lt':
						$comparison = '<';
						break;

					case 'gt':
						$comparison = '>';
						break;
				}
			}

			// Set the alias
			$_filter['field'] = sprintf(
				'`%s`.`%s`',
				(@$_filter['alias'] ? $_filter['alias'] : $defaultAlias),
				$_filter['field']
			);

			switch ($_filter['data']['type']) {
				case 'boolean':
					$query->andWhere(sprintf('%s = %s', $_filter['field'], $_filter['data']['value']));
					break;

				case 'string':
//					$query->andWhere(sprintf('%s LIKE \'%s\'', $_filter['field'], '%' . $_filter['data']['value'] . '%'));
					$query->andWhere(['LIKE', $_filter['field'], $_filter['data']['value']]);
					break;

				case 'numeric':
					$query->andWhere(sprintf('%s %s %s', $_filter['field'], $comparison, $_filter['data']['value']));
					break;

				case 'list':
					$query->andWhere(sprintf('%s', $_filter['field']), explode(',', $_filter['data']['value']));
					break;

				case 'date':
					$query->andWhere(sprintf('%s %s \'%s\'', $_filter['field'], $comparison, date('Y-m-d', strtotime($_filter['data']['value']))));
					break;

				default:
					break;
			}
		}
	}
	// End: User defined methods ==============================================
}
