<?php
/**
 * @package     Seven
 * @subpackage  com_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

namespace Seven\Component\Seven\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

/**
 * Model for listing automations
 *
 * @since  3.1.0
 */
class AutomationsModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings
     *
     * @since   3.1.0
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'trigger_type', 'a.trigger_type',
                'enabled', 'a.enabled',
                'created', 'a.created',
                'modified', 'a.modified',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * @param   string  $ordering   An optional ordering field
     * @param   string  $direction  An optional direction (asc|desc)
     *
     * @return  void
     *
     * @since   3.1.0
     */
    protected function populateState($ordering = 'a.id', $direction = 'DESC')
    {
        parent::populateState($ordering, $direction);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  QueryInterface
     *
     * @since   3.1.0
     */
    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__seven_automations', 'a'));

        // Add subquery for success/total counts from logs
        $subQuery = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__seven_automation_logs', 'l'))
            ->where($db->quoteName('l.automation_id') . ' = ' . $db->quoteName('a.id'));

        $query->select('(' . $subQuery . ') AS total_sent');

        $subQuerySuccess = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__seven_automation_logs', 'l2'))
            ->where($db->quoteName('l2.automation_id') . ' = ' . $db->quoteName('a.id'))
            ->where($db->quoteName('l2.success') . ' = 1');

        $query->select('(' . $subQuerySuccess . ') AS success_count');

        // Filter by enabled state
        $enabled = $this->getState('filter.enabled');

        if (is_numeric($enabled)) {
            $query->where($db->quoteName('a.enabled') . ' = ' . (int) $enabled);
        }

        // Filter by trigger type
        $triggerType = $this->getState('filter.trigger_type');

        if (!empty($triggerType)) {
            $query->where($db->quoteName('a.trigger_type') . ' = ' . $db->quote($triggerType));
        }

        // Filter by search
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true)) . '%');
                $query->where('(' . $db->quoteName('a.title') . ' LIKE ' . $search . ')');
            }
        }

        // Add ordering
        $orderCol = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    /**
     * Get trigger type labels for display
     *
     * @return  array  Associative array of trigger type => label
     *
     * @since   3.1.0
     */
    public function getTriggerLabels(): array
    {
        return [
            'vm_order_confirmed' => 'VirtueMart: Bestellbestätigung',
            'vm_order_status_change' => 'VirtueMart: Statusänderung',
            'vm_order_shipped' => 'VirtueMart: Versendet',
            'vm_order_cancelled' => 'VirtueMart: Storniert',
            'user_registration' => 'Joomla: Registrierung',
            'content_save' => 'Joomla: Content gespeichert',
        ];
    }
}
