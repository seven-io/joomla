<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

use Seven\Joomla\helpers\AbstractMessage;

defined('_JEXEC') or die;

/**
 * @package seven
 * @since    1.0.0
 */
class SevenModelMessage extends AbstractMessage {
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
