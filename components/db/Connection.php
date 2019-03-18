<?php
/**
 * Application
 *
 * Oracle Wrapper Connection class to eliminate below erro for Model Genarator:
 *   ORA-00942: table or view does not exist
 *
 * @author       Amil Waduwawara
 * @version      $Id: v1.0.0 2017-Aug-26 Exp $;
 * @copyright    Copyright &copy; Omobio (Pvt.) Ltd.
 */
namespace app\components\db;

class Connection extends \yii\db\Connection
{
}
