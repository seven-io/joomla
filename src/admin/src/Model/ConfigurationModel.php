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

/**
 * Model for a single configuration
 *
 * @since  3.0.0
 */
class ConfigurationModel extends AdminModel
{
    /**
     * The type alias for this content type
     *
     * @var    string
     * @since  3.0.0
     */
    public $typeAlias = 'com_seven.configuration';

    /**
     * Method to get a table object, load it if necessary.
     *
     * @param   string  $name     The table name
     * @param   string  $prefix   The class prefix
     * @param   array   $options  Configuration array for the table
     *
     * @return  \Joomla\CMS\Table\Table
     *
     * @since   3.0.0
     */
    public function getTable($name = 'Configuration', $prefix = 'Administrator', $options = [])
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
     * @since   3.0.0
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_seven.configuration',
            'configuration',
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
     * @since   3.0.0
     */
    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_seven.edit.configuration.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }
}
