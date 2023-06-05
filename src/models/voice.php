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
 * @since    1.3.0
 */
class SevenModelVoice extends AbstractMessage {
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
        $this->_saveConfig['text'] = $data['text'];
        $this->_saveConfig['to'] = $data['to'];

        foreach ($this->getRecipients($arr) as $_to) {
            $success = parent::save(array_merge(
                $data, $this->_apiHelper->voice(array_merge($arr, ['to' => $_to]))));
        }

        return $success;
    }
}
