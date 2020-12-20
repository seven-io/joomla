<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 * @since    1.0.0
 */

namespace Sms77\Joomla\helpers;

use JDatabaseQuery;
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

abstract class AbstractMessageListModel extends ListModel {
    /**
     * @var array
     * @since 1.3.0
     */
    private $_filterFields;

    /**
     * @var string
     * @since 1.3.0
     */
    private $_table;

    private $_defaultFilterFields;

    private $_tablePrefix = '#__sms77api_';

    /**
     * @param array $config An optional associative array of configuration settings.
     * @param array $filterFields
     * @param string $table
     * @since 1.3.0
     */
    public function __construct($config = [], array $filterFields, $table) {
        $this->_filterFields = $filterFields;
        $this->_table = $table;
        $this->_defaultFilterFields = [
            'id',
            "$table.id",
            'config',
            "$table.config",
            'created',
            "$table.created",
        ];

        if (empty($config['filter_fields'])) {
            $config['filter_fields'] =
                array_merge($this->_defaultFilterFields, $filterFields);
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
    protected function populateState($ordering = null, $direction = 'ASC') {
        parent::populateState($ordering ?: "$this->_table.id", $direction);
    }

    /**
     * Method to get a \JDatabaseQuery object for retrieving the data set from a database.
     * @return  JDatabaseQuery  A \JDatabaseQuery object to retrieve the data set.
     * @since   1.0.0
     */
    protected function getListQuery() {
        $db = $this->getDbo();
        $query = parent::getListQuery()
            ->select($db->quoteName(
                array_filter(
                    array_merge($this->_defaultFilterFields, $this->_filterFields),
                    static function ($f) {
                        return false !== strpos($f, '.');
                    })))
            ->from($db->quoteName($this->_tablePrefix . $this->_table, $this->_table));

        $search = $this->getState('filter.search');
        if ($search) {
            foreach ($this->_filterFields as $field) {
                $query->where($db->quoteName("$this->_table.$field")
                    . ' LIKE ' . $db->quote("%$search%"));
            }
        }

        $query->order(
            $db->escape(
                $this->state->get('list.ordering', "$this->_table.id"))
            . ' ' . $db->escape(
                $this->state->get('list.direction', 'ASC')));

        return $query;
    }
}