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
 * Voice table class
 *
 * @since  3.0.0
 */
class VoiceTable extends Table
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
        parent::__construct('#__seven_voices', 'id', $db);
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
        // Recipient is required
        if (empty($this->recipient)) {
            $this->setError('Recipient is required');
            return false;
        }

        return true;
    }
}
