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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

/**
 * Controller for a single message (SMS)
 *
 * @since  3.0.0
 */
class MessageController extends FormController
{
    /**
     * The view list to redirect to after form save
     *
     * @var    string
     * @since  3.0.0
     */
    protected $view_list = 'messages';

    /**
     * Send SMS - this is called when user clicks "Send"
     *
     * @return  bool  True on success
     *
     * @since   3.0.0
     */
    public function send()
    {
        // Check for request forgeries
        $this->checkToken();

        $app = Factory::getApplication();
        $model = $this->getModel();
        $data = $this->input->post->get('jform', [], 'array');

        // Send the SMS via API
        $result = $model->send($data);

        if ($result['success']) {
            // Save to database for history
            $table = $model->getTable();

            // Extract data from JSON response
            $message = $result['response']['messages'][0] ?? [];

            $saveData = [
                'recipient' => $data['to'] ?? '',
                'text' => $data['text'] ?? '',
                'sender' => $message['sender'] ?? $data['from'] ?? null,
                'response_code' => (int) ($result['response']['success'] ?? 0),
                'response_data' => json_encode($result['response']),
                'created' => Factory::getDate()->toSql(),
            ];

            $table->bind($saveData);
            $table->store();

            $app->enqueueMessage(Text::_('COM_SEVEN_SMS_SENT_SUCCESS'), 'success');
        } else {
            $app->enqueueMessage($result['error'] ?? Text::_('COM_SEVEN_SMS_SENT_ERROR'), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_seven&view=messages', false));

        return $result['success'];
    }
}
