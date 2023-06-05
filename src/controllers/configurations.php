<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

namespace Seven\Joomla\controllers;

use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

/**
 * Configurations Controller.
 * @package seven
 * @since    1.0.0
 */
class SevenControllerConfigurations extends AdminController {
    /**
     * The prefix to use with controller messages.
     * @var    string
     * @since  1.0.0
     */
    protected $text_prefix = 'com_seven_configuration';

    /**
     * Method to get a model object, loading it if required.
     * @param string $name The model name. Optional.
     * @param string $prefix The class prefix. Optional.
     * @param array $config Configuration array for model. Optional.
     * @return  \JModelLegacy  The model.
     * @since   1.0.0
     */
    public function getModel($name = 'Configuration', $prefix = 'SevenModel', $config = ['ignore_request' => true]) {
        return parent::getModel($name, $prefix, $config);
    }
}
