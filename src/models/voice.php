<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

use Sms77\Joomla\helpers\AbstractMessage;

defined('_JEXEC') or die;

/**
 * @package sms77api
 * @since    1.3.0
 */
class Sms77apiModelVoice extends AbstractMessage {
    public function __construct($config = []) {
        parent::__construct($config, 'voice');
    }

    /**
     * @inheritDoc
     * @since   1.3.0
     */
    public function save($data) {
        $arr = $data;
        $success = false;

        foreach ($this->getRecipients($arr) as $_to) {
            $success = parent::save(array_merge(
                $data, $this->_apiHelper->voice(array_merge($arr, ['to' => $_to]))));
        }

        return $success;
    }
}