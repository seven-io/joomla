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

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Controller for a single automation
 *
 * @since  3.1.0
 */
class AutomationController extends FormController
{
    /**
     * The view list to redirect to after form save
     *
     * @var    string
     * @since  3.1.0
     */
    protected $view_list = 'automations';

    /**
     * Test an automation by sending a test SMS
     *
     * @return  void
     *
     * @since   3.1.0
     */
    public function test(): void
    {
        // Check for request forgeries
        $this->checkToken();

        $id = $this->input->getInt('id', 0);
        $recipient = $this->input->getString('test_recipient', '');

        if (empty($id)) {
            $this->setMessage(Text::_('COM_SEVEN_ERROR_NO_AUTOMATION_SELECTED'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_seven&view=automations', false));
            return;
        }

        if (empty($recipient)) {
            $this->setMessage(Text::_('COM_SEVEN_ERROR_NO_TEST_RECIPIENT'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_seven&view=automation&layout=edit&id=' . $id, false));
            return;
        }

        /** @var \Seven\Component\Seven\Administrator\Model\AutomationModel $model */
        $model = $this->getModel();
        $result = $model->test($id, $recipient);

        if ($result['success']) {
            $this->setMessage(Text::_('COM_SEVEN_TEST_SMS_SENT_SUCCESSFULLY'));
        } else {
            $error = $result['error'] ?? Text::_('COM_SEVEN_TEST_SMS_FAILED');
            $this->setMessage(Text::sprintf('COM_SEVEN_TEST_SMS_FAILED_WITH_ERROR', $error), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_seven&view=automation&layout=edit&id=' . $id, false));
    }

    /**
     * Duplicate an automation
     *
     * @return  void
     *
     * @since   3.1.0
     */
    public function duplicate(): void
    {
        // Check for request forgeries
        $this->checkToken();

        $id = $this->input->getInt('id', 0);

        if (empty($id)) {
            $this->setMessage(Text::_('COM_SEVEN_ERROR_NO_AUTOMATION_SELECTED'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_seven&view=automations', false));
            return;
        }

        /** @var \Seven\Component\Seven\Administrator\Model\AutomationModel $model */
        $model = $this->getModel();
        $newId = $model->duplicate($id);

        if ($newId) {
            $this->setMessage(Text::_('COM_SEVEN_AUTOMATION_DUPLICATED'));
            $this->setRedirect(Route::_('index.php?option=com_seven&view=automation&layout=edit&id=' . $newId, false));
        } else {
            $this->setMessage(Text::_('COM_SEVEN_ERROR_DUPLICATING_AUTOMATION'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_seven&view=automations', false));
        }
    }
}
