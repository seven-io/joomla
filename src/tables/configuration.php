<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 * @since  1.0.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Sms77\Joomla\helpers\ConfigurationHelper;

class TableConfiguration extends Table {
    /**
     * @param JDatabaseDriver $db Database driver object.
     * @since   1.0.0
     */
    public function __construct(JDatabaseDriver $db) {
        parent::__construct('#__sms77api_configurations', 'id', $db);
    }

    public function publish($pks = null, $state = 1, $userId = 0) {
        if (1 === $state) {
            (new ConfigurationHelper)->unpublish();
        }

        return parent::publish($pks, $state, $userId);
    }
}