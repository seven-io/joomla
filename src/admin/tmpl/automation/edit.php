<?php
/**
 * @package     Seven
 * @subpackage  com_seven
 *
 * @copyright   Copyright (C) seven communications GmbH & Co. KG. All rights reserved.
 * @license     MIT
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Seven\Component\Seven\Administrator\View\Automation\HtmlView $this */

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$isNew = ($this->item->id == 0);
?>
<form action="<?php echo Route::_('index.php?option=com_seven&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate">

    <div class="row">
        <div class="col-lg-8">
            <div class="main-card">
                <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>

                <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_SEVEN_AUTOMATION_DETAILS')); ?>
                <div class="row">
                    <div class="col-12">
                        <fieldset class="adminform">
                            <?php echo $this->form->renderField('title'); ?>
                            <?php echo $this->form->renderField('trigger_type'); ?>
                            <?php echo $this->form->renderField('enabled'); ?>
                            <?php echo $this->form->renderField('template'); ?>
                            <?php echo $this->form->renderField('sender_id'); ?>
                            <?php echo $this->form->renderField('recipient_type'); ?>
                            <?php echo $this->form->renderField('custom_recipient'); ?>
                        </fieldset>
                    </div>
                </div>
                <?php echo HTMLHelper::_('uitab.endTab'); ?>

                <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'options', Text::_('COM_SEVEN_OPTIONS')); ?>
                <div class="row">
                    <div class="col-12">
                        <fieldset class="adminform">
                            <?php echo $this->form->renderField('flash'); ?>
                            <?php echo $this->form->renderField('unicode'); ?>
                            <?php echo $this->form->renderField('performance_tracking'); ?>
                        </fieldset>
                    </div>
                </div>
                <?php echo HTMLHelper::_('uitab.endTab'); ?>

                <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Available Variables Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <span class="icon-code" aria-hidden="true"></span>
                        <?php echo Text::_('COM_SEVEN_AVAILABLE_VARIABLES'); ?>
                    </h4>
                </div>
                <div class="card-body" id="variablesContainer">
                    <p class="text-muted" id="variablesPlaceholder">
                        <?php echo Text::_('COM_SEVEN_SELECT_TRIGGER_FOR_VARIABLES'); ?>
                    </p>
                    <div id="variablesList" class="list-group list-group-flush" style="display: none;"></div>
                </div>
            </div>

            <!-- Test SMS Card -->
            <?php if (!$isNew) : ?>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <span class="icon-mobile" aria-hidden="true"></span>
                        <?php echo Text::_('COM_SEVEN_TEST_AUTOMATION'); ?>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="test_recipient" class="form-label">
                            <?php echo Text::_('COM_SEVEN_TEST_RECIPIENT'); ?>
                        </label>
                        <input type="tel" id="test_recipient" name="test_recipient" class="form-control"
                               placeholder="+49123456789">
                    </div>
                    <button type="button" class="btn btn-primary" id="testAutomationBtn">
                        <span class="icon-paper-plane" aria-hidden="true"></span>
                        <?php echo Text::_('COM_SEVEN_SEND_TEST'); ?>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo $this->form->renderField('id'); ?>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const triggerSelect = document.querySelector('[name="jform[trigger_type]"]');
    const variablesPlaceholder = document.getElementById('variablesPlaceholder');
    const variablesList = document.getElementById('variablesList');
    const templateField = document.querySelector('[name="jform[template]"]');

    const variablesByTrigger = <?php echo json_encode($this->variableDefinitions); ?>;

    function updateVariables() {
        const trigger = triggerSelect.value;

        if (trigger && variablesByTrigger[trigger]) {
            variablesPlaceholder.style.display = 'none';
            variablesList.style.display = 'block';
            variablesList.innerHTML = '';

            variablesByTrigger[trigger].forEach(function(variable) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action py-2';
                btn.textContent = '{' + variable + '}';
                btn.dataset.variable = '{' + variable + '}';

                btn.addEventListener('click', function() {
                    insertVariable(this.dataset.variable);
                });

                variablesList.appendChild(btn);
            });
        } else {
            variablesPlaceholder.style.display = 'block';
            variablesList.style.display = 'none';
        }
    }

    function insertVariable(variable) {
        if (!templateField) return;

        const pos = templateField.selectionStart;
        const val = templateField.value;
        templateField.value = val.substring(0, pos) + variable + val.substring(pos);
        templateField.focus();
        templateField.selectionStart = templateField.selectionEnd = pos + variable.length;
    }

    triggerSelect.addEventListener('change', updateVariables);
    updateVariables();

    // Test button functionality
    const testBtn = document.getElementById('testAutomationBtn');
    if (testBtn) {
        testBtn.addEventListener('click', function() {
            const recipient = document.getElementById('test_recipient').value;
            if (!recipient) {
                alert('<?php echo Text::_('COM_SEVEN_ERROR_NO_TEST_RECIPIENT', true); ?>');
                return;
            }

            const form = document.getElementById('adminForm');
            const taskInput = form.querySelector('input[name="task"]');
            taskInput.value = 'automation.test';

            form.submit();
        });
    }
});
</script>
