<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 * @since    1.0.0
 */

use Seven\Joomla\helpers\AbstractMessageHtmlView;

defined('_JEXEC') or die;

class SevenViewMessage extends AbstractMessageHtmlView {
    public function __construct($config = []) {
        parent::__construct($config, 'message', 'COM_SEVEN_WRITE_SMS');
    }
}
