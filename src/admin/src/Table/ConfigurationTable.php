<?php
/**
 * @package     Seven
 * @subpackage  com_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

namespace Seven\Component\Seven\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

/**
 * Configuration table class
 *
 * @since  3.0.0
 */
class ConfigurationTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  Database driver object
     *
     * @since   3.0.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__seven_configurations', 'id', $db);
    }

    /**
     * Overloaded check method to ensure data integrity.
     *
     * @return  boolean  True on success
     *
     * @since   3.0.0
     */
    public function check(): bool
    {
        // Check for valid API key
        if (empty($this->api_key)) {
            $this->setError('COM_SEVEN_ERROR_API_KEY_REQUIRED');
            return false;
        }

        return true;
    }

    /**
     * Method to store a row in the database from the Table instance properties.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null
     *
     * @return  boolean  True on success
     *
     * @since   3.0.0
     */
    public function store($updateNulls = true): bool
    {
        // If this configuration is being published, unpublish all others
        if ($this->published == 1) {
            $db = $this->getDbo();
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__seven_configurations'))
                ->set($db->quoteName('published') . ' = 0')
                ->where($db->quoteName('id') . ' != ' . (int) $this->id);

            $db->setQuery($query);
            $db->execute();
        }

        return parent::store($updateNulls);
    }
}
