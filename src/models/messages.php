<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 * @since    1.0.0
 */

use Seven\Joomla\helpers\AbstractMessageListModel;

defined('_JEXEC') or die;

class SevenModelMessages extends AbstractMessageListModel {
    /**
     * @inheritDoc
     * @since   1.0.0
     */
    public function __construct($config = []) {
        parent::__construct($config, [
            'response',
            'messages.response',
        ], 'messages');
    }
}
