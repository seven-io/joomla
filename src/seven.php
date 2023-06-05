<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

require __DIR__ . '/vendor/autoload.php';

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

if (!Factory::getUser()->authorise('core.manage', 'com_seven')) {
    throw new InvalidArgumentException(Text::_('JERROR_ALERTNOAUTHOR'), 404);
}

$controller = BaseController::getInstance('seven');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
