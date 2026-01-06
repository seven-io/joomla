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

/**
 * Messages list controller
 *
 * @since  3.0.0
 */
class MessagesController extends AdminController
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
     * @since   3.0.0
     */
    public function getModel($name = 'Message', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
}
