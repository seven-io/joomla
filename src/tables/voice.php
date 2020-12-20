<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 * @since  1.3.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;

class TableVoice extends Table {
    /**
     * @param JDatabaseDriver $db Database driver object.
     * @since   1.0.0
     */
    public function __construct(JDatabaseDriver $db) {
        parent::__construct('#__sms77api_voices', 'id', $db);
    }
}