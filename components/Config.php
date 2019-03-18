<?php
/**
 * Application
 *
 * The common repository of constants
 *
 * @author       Amil Waduwawara
 * @version      $Id: v1.0.0 2017-Aug-25 Exp $;
 * @copyright    Copyright &copy; Omobio (Pvt.) Ltd., Sri Lanka.
 */
namespace app\components;

class Config
{
	// Tree id delim
	const DELIM = '::';

	const STATUS_DISABLED    = 0;
	const STATUS_ENABLED     = 1;
	const STATUS_TEMP_LOCKED = 2;

	const PAGE_SIZE = 15;
}
