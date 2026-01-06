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
 * Automation table class
 *
 * @since  3.1.0
 */
class AutomationTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  Database driver object
     *
     * @since   3.1.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__seven_automations', 'id', $db);
    }

    /**
     * Overloaded check method to ensure data integrity.
     *
     * @return  boolean  True on success
     *
     * @since   3.1.0
     */
    public function check(): bool
    {
        // Title is required
        if (empty($this->title)) {
            $this->setError('Title is required');
            return false;
        }

        // Trigger type is required
        if (empty($this->trigger_type)) {
            $this->setError('Trigger type is required');
            return false;
        }

        // Template is required
        if (empty($this->template)) {
            $this->setError('Template is required');
            return false;
        }

        // Validate trigger type
        $validTriggers = [
            'vm_order_confirmed',
            'vm_order_status_change',
            'vm_order_shipped',
            'vm_order_cancelled',
            'user_registration',
            'content_save',
        ];

        if (!in_array($this->trigger_type, $validTriggers)) {
            $this->setError('Invalid trigger type');
            return false;
        }

        // Validate recipient type
        $validRecipientTypes = ['customer', 'admin', 'custom'];
        if (!in_array($this->recipient_type, $validRecipientTypes)) {
            $this->recipient_type = 'customer';
        }

        // If recipient type is custom, custom_recipient is required
        if ($this->recipient_type === 'custom' && empty($this->custom_recipient)) {
            $this->setError('Custom recipient phone number is required');
            return false;
        }

        // Set default sender_id if empty
        if (empty($this->sender_id)) {
            $this->sender_id = 'seven';
        }

        return true;
    }

    /**
     * Method to store a row in the database from the Table instance properties.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   3.1.0
     */
    public function store($updateNulls = true): bool
    {
        $date = date('Y-m-d H:i:s');

        if (empty($this->id)) {
            // New record
            $this->created = $date;
        }

        $this->modified = $date;

        return parent::store($updateNulls);
    }
}
