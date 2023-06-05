<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

use Seven\Joomla\helpers\AbstractMessagesHtml;

defined('_JEXEC') or die;

/**
 * @package seven
 * @since    1.0.0
 */
class sevenViewmessages extends AbstractMessagesHtml {
    public function __construct($config = []) {
        parent::__construct($config, 'COM_SEVEN_MESSAGES', 'message');
    }
}
