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

    'message',

    $this->form->renderField('configuration')
    . $this->form->renderField('text')
    . $this->form->renderField('to')
    . ViewEditHelper::vm($this->form->renderField('shopper_group_id'))
    . $this->form->getInput('id'),

    $this->form->renderField('delay')
    . $this->form->renderField('foreign_id')
    . $this->form->renderField('from')
    . $this->form->renderField('label')
    . $this->form->renderField('ttl')
    . $this->form->renderField('udh')

    . $this->form->renderField('debug')
    . $this->form->renderField('flash')
    . $this->form->renderField('no_reload')
    . $this->form->renderField('performance_tracking')
    . $this->form->renderField('unicode')
    . $this->form->renderField('utf8')
);