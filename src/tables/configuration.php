<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 * @since  1.0.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Seven\Joomla\helpers\ConfigurationHelper;

class TableConfiguration extends Table {
    /**
     * @param JDatabaseDriver $db Database driver object.
     * @since   1.0.0
     */
    public function __construct(JDatabaseDriver $db) {
        parent::__construct('#__seven_configurations', 'id', $db);
    }

    public function publish($pks = null, $state = 1, $userId = 0) {
        if (1 === $state) {
            (new ConfigurationHelper)->unpublish();
        }

        return parent::publish($pks, $state, $userId);
    }
}
