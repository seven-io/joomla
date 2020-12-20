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
 * @since    1.0.0
 */
class Sms77apiModelMessage extends AbstractMessage {
    public function __construct($config = []) {
        parent::__construct($config, 'message');
    }

    /**
     * @inheritDoc
     * @since   1.0.0
     */
    public function save($data) {
        $arr = $data;
        $arr['to'] = implode(',', $this->getRecipients($arr));

        return '' === $arr['to'] ? false : parent::save(array_merge($data, [
            'response' => json_encode($this->_apiHelper->sms($arr)),
        ]));
    }
}