<?php
/**
 * @package     Seven
 * @subpackage  com_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

namespace Seven\Component\Seven\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Automations list controller
 *
 * @since  3.1.0
 */
class AutomationsController extends AdminController
{
    /**
     * Proxy for getModel
     *
     * @param   string  $name    The model name
     * @param   string  $prefix  The class prefix
     * @param   array   $config  Configuration array for the model
     *
     * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel|bool
     *
     * @since   3.1.0
     */
    public function getModel($name = 'Automation', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Toggle automation enabled state
     *
     * @return  void
     *
     * @since   3.1.0
     */
    public function toggle(): void
    {
        // Check for request forgeries
        $this->checkToken();

        $ids = $this->input->get('cid', [], 'array');

        if (empty($ids)) {
            $this->setMessage(Text::_('COM_SEVEN_ERROR_NO_AUTOMATION_SELECTED'), 'error');
        } else {
            $model = $this->getModel();
            $table = $model->getTable();

            $toggled = 0;
            foreach ($ids as $id) {
                if ($table->load((int) $id)) {
                    $table->enabled = $table->enabled ? 0 : 1;
                    if ($table->store()) {
                        $toggled++;
                    }
                }
            }

            if ($toggled > 0) {
                $this->setMessage(Text::plural('COM_SEVEN_N_AUTOMATIONS_TOGGLED', $toggled));
            } else {
                $this->setMessage(Text::_('COM_SEVEN_ERROR_TOGGLING_AUTOMATIONS'), 'error');
            }
        }

        $this->setRedirect(Route::_('index.php?option=com_seven&view=automations', false));
    }
}
