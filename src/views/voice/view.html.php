<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 * @since    1.3.0
 */

use Seven\Joomla\helpers\AbstractMessageHtmlView;

defined('_JEXEC') or die;

class SevenViewVoice extends AbstractMessageHtmlView {
    public function __construct($config = []) {
        parent::__construct($config, 'voice', 'COM_SEVEN_WRITE_VOICE');
    }
}
