<?php
/**
 * Editor Froala
 *
 * @version 	2.1
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
/** @var $this FroalaViewProfile */
defined( '_JEXEC' ) or die;// No direct access
JHtml::_('bootstrap.tooltip');
JHtml::_( 'behavior.formvalidation' );
JHtml::_( 'behavior.keepalive' );
JHtml::_( 'formbehavior.chosen', 'select' );
$input = JFactory::getApplication()->input;
$fieldsets = $this->form->getFieldsets();
?>
<script type="text/javascript">
	Joomla.submitbutton = function (task) {
		if (task == 'profile.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape( JText::_( 'JGLOBAL_VALIDATION_FORM_FAILED' ) ); ?>');
		}
	}
</script>
<form action="<?php echo JRoute::_( 'index.php?option=com_froala&id=' . $this->form->getValue( 'id' ) ); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
    <div class="form-inline form-inline-header">
        <?php
        echo $this->form->renderField('title');
        echo $this->form->renderField('description');
        ?>
    </div>
	<div class="form-horizontal">
		<?php echo JHtml::_( 'bootstrap.startTabSet', 'myTab', array( 'active' => 'default' ) ); ?>
        <?php foreach($fieldsets as $fieldset):?>
            <?php echo JHtml::_( 'bootstrap.addTab', 'myTab', $fieldset->name, JText::_( $fieldset->label, true ) ); ?>
            <div class="row-fluid form-horizontal-desktop">
                <div class="span12">
                <?php foreach($this->form->getFieldset($fieldset->name) as $field) : ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $field->title; ?></div>
                        <div class="controls"><?php echo $field->input; ?></div>
                    </div>
                <?php endforeach;?>
                </div>
            </div>
            <?php echo JHtml::_( 'bootstrap.endTab' ); ?>
        <?php endforeach;?>

		<?php echo JHtml::_( 'bootstrap.endTabSet' ); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $input->getCmd( 'return' ); ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>