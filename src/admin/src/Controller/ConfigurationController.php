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

/**
 * Controller for a single configuration
 *
 * @since  3.0.0
 */
class ConfigurationController extends FormController
{
    /**
     * The view list to redirect to after form save
     *
     * @var    string
     * @since  3.0.0
     */
    protected $view_list = 'configurations';
}
