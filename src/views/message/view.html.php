<?php
/**
 * @package   sms77api
 * @author     sms77 e.K. <support@sms77.io>
 * @copyright  sms77 e.K.
 * @license    MIT; see LICENSE.txt
 * @link     support@sms77.io
 * @since    1.0.0
 */

use Sms77\Joomla\helpers\AbstractMessageHtmlView;

defined('_JEXEC') or die;

class Sms77apiViewMessage extends AbstractMessageHtmlView {
    public function __construct($config = []) {
        parent::__construct($config, 'message', 'COM_SMS77API_WRITE_SMS');
    }
}