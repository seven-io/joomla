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

class Sms77apiModelVoices extends AbstractMessageListModel {
    /**
     * @inheritDoc
     * @since   1.3.0
     */
    public function __construct($config = []) {
        parent::__construct($config, [
            'code',
            'voices.code',
            'sms77_id',
            'voices.sms77_id',
            'eur',
            'voices.eur',
        ], 'voices');
    }
}