<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

use Sms77\Joomla\helpers\AbstractMessagesHtml;

defined('_JEXEC') or die;

/**
 * @package sms77api
 * @since    1.3.0
 */
class sms77apiViewvoices extends AbstractMessagesHtml {
    public function __construct($config = []) {
        parent::__construct($config, 'COM_SMS77API_VOICE_MESSAGES', 'voice');
    }
}