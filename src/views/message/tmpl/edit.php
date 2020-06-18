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
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('adminForm');
            const to = document.getElementById('jform_to');
            const shopperGroup = document.getElementById('jform_shopper_group');
            
            const hasShopper = () => '' !== shopperGroup.value;
            
            Joomla.submitbutton = task => {
                if (hasShopper()) {
                    to.value = '';
                }
                
                if (task === 'message.cancel' || document.formvalidator.isValid(form)) {
                    Joomla.submitform(task, form);
                }
            };
            		
            shopperGroup.addEventListener('change', () => to.disabled = hasShopper()); 
        });
JS
);

$fields = ['text', 'configuration', 'to'];
if (JComponentHelper::isEnabled('com_virtuemart')) {
    $fields[] = 'shopper_group';

    $lang = JFactory::getLanguage();
    $lang->load('com_virtuemart', JPATH_ADMINISTRATOR . '/components/com_virtuemart');
    $lang->load('com_virtuemart_shoppers', JPATH_ROOT . '/components/com_virtuemart');
}
?>
<form action="<?php echo Route::_("index.php?option=com_sms77api&layout=edit&id={$this->message->id}") ?>"
      method="post" name="adminForm" enctype="multipart/form-data" id="adminForm"
      class="form-validate">
    <input type="hidden" name="task" value=""/>

    <?php
    foreach ($fields as $field) {
        echo $this->form->renderField($field);
    }

    echo $this->form->getInput('id');
    echo HTMLHelper::_('form.token');
    ?>
</form>