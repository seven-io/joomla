<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

if (!Factory::getUser()->authorise('core.manage', 'com_sms77api')) {
    throw new InvalidArgumentException(Text::_('JERROR_ALERTNOAUTHOR'), 404);
}

require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/configuration.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/sms77api.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/exceptions/ApiKeyMismatchException.php';

$controller = BaseController::getInstance('sms77api');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();