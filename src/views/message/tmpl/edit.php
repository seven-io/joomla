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

    . $this->form->renderField('flash')
    . $this->form->renderField('no_reload')
    . $this->form->renderField('performance_tracking')
    . $this->form->renderField('unicode')
    . $this->form->renderField('utf8')
);
