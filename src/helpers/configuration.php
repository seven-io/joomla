<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

/**
 * Configuration helper.
 * @package sms77api
 * @since    1.0.0
 */
class ConfigurationHelper {
    const tableName = '#__sms77api_configurations';

    /**
     * @param string $vName The name of the current view.
     * @return  void
     * @since   1.0.0
     */
    public function addSubmenu($vName) {
        HTMLHelper::_('sidebar.addEntry', Text::_('COM_SMS77API'),
            'index.php?option=com_sms77api&view=configurations',
            $vName === 'configurations');
    }

    public function table() {
        return Table::getInstance('Configuration', 'Table');
    }

    public function byId($id) {
        $db = $this->table()->getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->where('id = ' . (int)$id);
        $query->from(self::tableName);
        $db->setQuery($query);
        return $db->loadObject();
    }

    public function publishActive(array &$data) {
        $apiKey = $data['api_key'];

        $apiHelper = new Sms77apiHelper($apiKey);

        if (!$apiHelper->isValidApiKey()) {
            throw new ApiKeyMismatchException();
        }

        if (null !== $apiKey && $this->unpublish()) {
            $data['published'] = 1;
        }

        return $data;
    }

    public function unpublish() {
        $bools = [];

        $table = $this->table();
        $db = $table->getDbo();
        $db->setQuery(
            $db->getQuery(true)->select('*')->from(self::tableName));

        foreach ($db->loadObjectList() as $configuration) {
            $configuration->published = 0;
            $bools[] = $table->save($configuration);
        }

        return true === array_unique($bools)[0];
    }

    public static function hasVirtueMart() {
        return JComponentHelper::isEnabled('com_virtuemart');
    }
}