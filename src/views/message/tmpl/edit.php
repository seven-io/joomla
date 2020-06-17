<?php
/**
 * @package   sms77api
 * @author     sms77 e.K. <support@sms77.io>
 * @copyright  sms77 e.K.
 * @license    MIT; see LICENSE.txt
 * @link     support@sms77.io
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen');

Factory::getDocument()->addScriptDeclaration(<<<JS
		Joomla.submitbutton = function(task)
		{
			if (task === 'message.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		};
JS
);
?>
<form action="<?php echo Route::_('index.php?option=com_sms77api&layout=edit&id=' . (int)$this->message->id); ?>"
      method="post" name="adminForm" enctype="multipart/form-data" id="adminForm"
      class="form-validate">
    <?php
    echo $this->form->renderField('text')
        . $this->form->renderField('configuration')
        . $this->form->renderField('to'); ?>

    <input type="hidden" name="task" value=""/>

    <?php
    echo $this->form->getInput('id') .
        HTMLHelper::_('form.token'); ?>
</form>