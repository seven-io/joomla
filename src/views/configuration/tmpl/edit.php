<?php
/**
 * @package seven
 * @author seven communications GmbH & Co. KG <support@seven.io>
 * @copyright  2020-present seven communications GmbH & Co. KG
 * @license    MIT; see LICENSE.txt
 * @link       http://www.seven.io
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
			if (task === 'configuration.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		};
JS
);
?>
<form action="<?php echo Route::_('index.php?option=com_seven&layout=edit&id=' . (int)$this->configuration->id); ?>"
      method="post" name="adminForm" enctype="multipart/form-data" id="adminForm"
      class="form-validate">
    <?php echo $this->form->renderField('api_key'); ?>

    <input type="hidden" name="task" value=""/>

    <?php
    echo $this->form->getInput('id');
    echo HTMLHelper::_('form.token');
    ?>
</form>
