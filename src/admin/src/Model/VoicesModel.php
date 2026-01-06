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
 * Model for listing voice calls
 *
 * @since  3.0.0
 */
class VoicesModel extends ListModel
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings
     *
     * @since   3.0.0
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'recipient', 'a.recipient',
                'created', 'a.created',
                'code', 'a.code',
                'eur', 'a.eur',
                'seven_id', 'a.seven_id',
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
     * @since   3.0.0
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
     * @since   3.0.0
     */
    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__seven_voices', 'a'));

        // Filter by search
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true)) . '%');
                $query->where('(' . $db->quoteName('a.config') . ' LIKE ' . $search . ')');
            }
        }

        // Filter by date range
        $dateFrom = $this->getState('filter.date_from');
        $dateTo = $this->getState('filter.date_to');

        if (!empty($dateFrom)) {
            $query->where($db->quoteName('a.created') . ' >= ' . $db->quote($dateFrom . ' 00:00:00'));
        }

        if (!empty($dateTo)) {
            $query->where($db->quoteName('a.created') . ' <= ' . $db->quote($dateTo . ' 23:59:59'));
        }

        // Filter by response code
        $code = $this->getState('filter.code');

        if (is_numeric($code)) {
            $query->where($db->quoteName('a.code') . ' = ' . (int) $code);
        }

        // Add ordering
        $orderCol = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
