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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Seven\Component\Seven\Administrator\Service\AutomationService;
use Seven\Component\Seven\Administrator\Service\TemplateProcessor;

/**
 * Model for a single automation
 *
 * @since  3.1.0
 */
class AutomationModel extends AdminModel
{
    /**
     * The type alias for this content type
     *
     * @var    string
     * @since  3.1.0
     */
    public $typeAlias = 'com_seven.automation';

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name
     * @param   string  $prefix   The class prefix
     * @param   array   $options  Configuration array for the table
     *
     * @return  \Joomla\CMS\Table\Table
     *
     * @since   3.1.0
     */
    public function getTable($name = 'Automation', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * Method to get the form.
     *
     * @param   array    $data      Data for the form
     * @param   boolean  $loadData  True if the form is to load its own data
     *
     * @return  \Joomla\CMS\Form\Form|boolean
     *
     * @since   3.1.0
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_seven.automation',
            'automation',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form
     *
     * @since   3.1.0
     */
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_seven.edit.automation.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Get trigger type options for dropdown
     *
     * @return  array  Array of trigger options
     *
     * @since   3.1.0
     */
    public function getTriggerOptions(): array
    {
        return [
            '' => '- Select Trigger -',
            'vm_order_confirmed' => 'VirtueMart: Order Confirmed',
            'vm_order_status_change' => 'VirtueMart: Order Status Changed',
            'vm_order_shipped' => 'VirtueMart: Order Shipped',
            'vm_order_cancelled' => 'VirtueMart: Order Cancelled',
            'user_registration' => 'Joomla: User Registration',
            'content_save' => 'Joomla: Content Saved',
        ];
    }

    /**
     * Get available variables for all trigger types
     *
     * @return  array  Associative array of trigger types and their variables
     *
     * @since   3.1.0
     */
    public function getVariableDefinitions(): array
    {
        return TemplateProcessor::getAllVariableDefinitions();
    }

    /**
     * Test the automation with a test recipient
     *
     * @param   int     $id         The automation ID
     * @param   string  $recipient  The test recipient phone number
     *
     * @return  array  Test result
     *
     * @since   3.1.0
     */
    public function test(int $id, string $recipient): array
    {
        $automationService = new AutomationService();
        return $automationService->testAutomation($id, $recipient);
    }

    /**
     * Duplicate an automation
     *
     * @param   int  $id  The automation ID to duplicate
     *
     * @return  int|false  The new automation ID or false on failure
     *
     * @since   3.1.0
     */
    public function duplicate(int $id)
    {
        $table = $this->getTable();

        if (!$table->load($id)) {
            $this->setError($table->getError());
            return false;
        }

        // Reset ID and modify title
        $table->id = 0;
        $table->title = $table->title . ' (Copy)';
        $table->enabled = 0;
        $table->created = null;
        $table->modified = null;

        if (!$table->store()) {
            $this->setError($table->getError());
            return false;
        }

        return $table->id;
    }

    /**
     * Get automation logs
     *
     * @param   int  $id     The automation ID
     * @param   int  $limit  Maximum logs to return
     *
     * @return  array  Array of log objects
     *
     * @since   3.1.0
     */
    public function getLogs(int $id, int $limit = 50): array
    {
        $automationService = new AutomationService();
        return $automationService->getAutomationLogs($id, $limit);
    }

    /**
     * Get automation statistics
     *
     * @param   int  $id  The automation ID
     *
     * @return  object  Statistics object
     *
     * @since   3.1.0
     */
    public function getStatistics(int $id): object
    {
        $automationService = new AutomationService();
        return $automationService->getStatistics($id);
    }
}
