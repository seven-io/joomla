<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 * @since    1.0.0
 */

use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

class Sms77apiModelMessages extends ListModel {
    /**
     * @param array $config An optional associative array of configuration settings.
     * @since   1.0.0
     */
    public function __construct($config = []) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id',
                'messages.id',
                'config',
                'messages.config',
                'created',
                'messages.created',
                'response',
                'messages.response',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     * Note. Calling getState in this method will result in recursion.
     * @param string $ordering An optional ordering field.
     * @param string $direction An optional direction (asc|desc).
     * @return  void
     * @since   1.0.0
     */
    protected function populateState($ordering = 'messages.id', $direction = 'ASC') {
        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a \JDatabaseQuery object for retrieving the data set from a database.
     * @return  \JDatabaseQuery  A \JDatabaseQuery object to retrieve the data set.
     * @since   1.0.0
     */
    protected function getListQuery() {
        $db = $this->getDbo();
        $query = parent::getListQuery()
            ->select($db->quoteName([
                'messages.id',
                'messages.created',
                'messages.response',
                'messages.config',
            ]))
            ->from($db->quoteName('#__sms77api_messages', 'messages'));

        $search = $this->getState('filter.search');
        if ($search) {
            $query->where($db->quoteName('messages.response') . ' LIKE ' . $db->quote('%' . $search . '%'));
        }

        $query->order($db->escape($this->state->get('list.ordering', 'messages.id'))
            . ' ' . $db->escape($this->state->get('list.direction', 'ASC')));

        return $query;
    }
}