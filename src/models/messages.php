<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 * @since    1.0.0
 */

use Sms77\Joomla\helpers\AbstractMessageListModel;

defined('_JEXEC') or die;

class Sms77apiModelMessages extends AbstractMessageListModel {
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