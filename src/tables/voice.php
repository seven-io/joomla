<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
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
        parent::__construct('#__seven_voices', 'id', $db);
    }
}
