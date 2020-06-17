<?php
/**
 * @package sms77api
 * @author sms77 e.K. <support@sms77.io>
 * @copyright  2020-present
 * @license    MIT; see LICENSE.txt
 * @link       http://sms77.io
 */

use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

/**
 * Configurations Controller.
 * @package sms77api
 * @since    1.0.0
 */
class Sms77apiControllerMessages extends AdminController {
    /**
     * The prefix to use with controller messages.
     * @var    string
     * @since  1.0.0
     */
    protected $text_prefix = 'com_sms77api_message';

    /**
     * Method to get a model object, loading it if required.
     * @param string $name The model name. Optional.
     * @param string $prefix The class prefix. Optional.
     * @param array $config Configuration array for model. Optional.
     * @return  \JModelLegacy  The model.
     * @since   1.0.0
     */
    public function getModel($name = 'Message', $prefix = 'Sms77apiModel', $config = ['ignore_request' => true]) {
        return parent::getModel($name, $prefix, $config);
    }
}