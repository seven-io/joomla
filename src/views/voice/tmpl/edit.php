<?php
/**
 * @package   sms77api
 * @author     sms77 e.K. <support@sms77.io>
 * @copyright  sms77 e.K.
 * @license    MIT; see LICENSE.txt
 * @link     support@sms77.io
 */

defined('_JEXEC') or die;

use Sms77\Joomla\helpers\ViewEditHelper;

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