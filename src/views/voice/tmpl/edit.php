<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
 */

defined('_JEXEC') or die;

use Seven\Joomla\helpers\ViewEditHelper;

new ViewEditHelper(
    $this->_entity->id,

    'voice',

    $this->form->renderField('configuration')
    . $this->form->renderField('text')
    . $this->form->renderField('to')
    . ViewEditHelper::vm($this->form->renderField('shopper_group_id'))
    . $this->form->getInput('id'),

    $this->form->renderField('xml')
);
