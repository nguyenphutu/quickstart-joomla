<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
include_once ('templates/bootstrap4/helper.php');

JHtml::_('behavior.keepalive');
JHtml::_('b4.behaviorformvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('b4.tooltip');

// JFactory::getDocument()->addScriptDeclaration("
// 	jQuery(document).ready(function(){ 
// 		jQuery('select').addClass('form-control'); 
// 	});
// ");
// Load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);
?>
<div class="row justify-content-center mt-lg-2">
	<div class="col-8 profile-edit <?php echo $this->pageclass_sfx; ?>">
		<script type="text/javascript">
			
			Joomla.twoFactorMethodChange = function(e)
			{
				var selectedPane = 'com_users_twofactor_' + jQuery('#jform_twofactor_method').val();

				jQuery.each(jQuery('#com_users_twofactor_forms_container>div'), function(i, el)
				{
					if (el.id != selectedPane)
					{
						jQuery('#' + el.id).hide(0);
					}
					else
					{
						jQuery('#' + el.id).show(0);
					}
				});
			}
		</script>
		<form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
			<?php // Iterate through the form fieldsets and display each one. ?>
			<?php foreach ($this->form->getFieldsets() as $group => $fieldset) : ?>
				<?php $fields = $this->form->getFieldset($group); ?>
				<?php if (count($fields)) : ?>
					<fieldset>
						<div class="row">
							<div class="col-12">
								<?php // If the fieldset has a label set, display it as the legend. ?>
								<?php if (isset($fieldset->label)) : ?>
									<h3>
										<?php echo JText::_($fieldset->label); ?>
									</h3>
								<?php endif; ?>
								<?php if (isset($fieldset->description) && trim($fieldset->description)) : ?>
									<p>
										<?php echo $this->escape(JText::_($fieldset->description)); ?>
									</p>
								<?php endif; ?>
							</div>
						</div>
						<?php // Iterate through the fields in the set and display them. ?>
						<?php foreach ($fields as $field) : ?>
							<?php if(($field->type === 'Timezone') || ($field->type === 'TemplateStyle')): ?>
								<!-- Timezone here -->
							<?php else: ?>
								<?php // If the field is hidden, just display the input. ?>
								<?php if ($field->hidden) : ?>
									<?php echo $field->input; ?>
								<?php else : ?>
									<div class="form-group row">
										<div class="col-4">
											<label class="col-form-label">
												<?php echo $field->label; ?><?php if (!$field->required && $field->type !== 'Spacer') : ?>
													<span class="optional">
														<?php echo JText::_('COM_USERS_OPTIONAL'); ?>
													</span>
												<?php endif; ?>
											</label>
										</div>
										<div class="col-8">
											<?php if ($field->fieldname === 'password1') : ?>
												<?php // Disables autocomplete ?>
												<input type="password" style="display:none">
											<?php endif; ?>
											<?php if (($field->type === 'Plugins') || ($field->type === 'Language')) : ?>
												<?php
													$attr = '';
													// Initialize some field attributes.
													$attr .= !empty($field->class) ? ' class="form-control ' . $field->class . '"' : 'class="form-control"';
													$attr .= !empty($field->size) ? ' size="' . $field->size . '"' : '';
													$attr .= $field->multiple ? ' multiple' : '';
													$attr .= $field->required ? ' required aria-required="true"' : '';
													$attr .= $field->autofocus ? ' autofocus' : '';
											
													// To avoid user's confusion, readonly="true" should imply disabled="true".
													if ((string) $field->readonly == '1' || (string) $field->readonly == 'true' || (string) $field->disabled == '1'|| (string) $field->disabled == 'true')
													{
														$attr .= ' disabled="disabled"';
													}
											
													// Initialize JavaScript field attributes.
													$attr .= $field->onchange ? ' onchange="' . $field->onchange . '"' : '';
													echo TemplateHelper::genericlist($field->__get('options'), '', trim($attr), 'value', 'text', $field->value, $field->id);
												?>
											<?php elseif(($field->type === 'Timezone') || ($field->type === 'TemplateStyle')): ?>
												<!-- Timezone here -->
											<?php else: ?>
												<?php echo $field->input; ?>
											<?php endif; ?>
										</div>
									</div>
								<?php endif; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					</fieldset>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if (count($this->twofactormethods) > 1) : ?>
				<fieldset>								
					<legend><?php echo JText::_('COM_USERS_PROFILE_TWO_FACTOR_AUTH'); ?></legend>
					<div class="control-group">
						<div class="control-label">
							<label id="jform_twofactor_method-lbl" for="jform_twofactor_method" class="hasTooltip"
								title="<?php echo '<strong>' . JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL') . '</strong><br />' . JText::_('COM_USERS_PROFILE_TWOFACTOR_DESC'); ?>">
								<?php echo JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL'); ?>
							</label>
						</div>
						<div class="controls">
							<?php echo JHtml::_('select.genericlist', $this->twofactormethods, 'jform[twofactor][method]', array('onchange' => 'Joomla.twoFactorMethodChange()'), 'value', 'text', $this->otpConfig->method, 'jform_twofactor_method', false); ?>
						</div>
					</div>
					<div id="com_users_twofactor_forms_container">
						<?php foreach ($this->twofactorform as $form) : ?>
							<?php $style = $form['method'] == $this->otpConfig->method ? 'display: block' : 'display: none'; ?>
							<div id="com_users_twofactor_<?php echo $form['method']; ?>" style="<?php echo $style; ?>">
								<?php echo $form['form']; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</fieldset>
				<fieldset>
					<legend>
						<?php echo JText::_('COM_USERS_PROFILE_OTEPS'); ?>
					</legend>
					<div class="alert alert-info">
						<?php echo JText::_('COM_USERS_PROFILE_OTEPS_DESC'); ?>
					</div>
					<?php if (empty($this->otpConfig->otep)) : ?>
						<div class="alert alert-warning">
							<?php echo JText::_('COM_USERS_PROFILE_OTEPS_WAIT_DESC'); ?>
						</div>
					<?php else : ?>
						<?php foreach ($this->otpConfig->otep as $otep) : ?>
							<span class="span3">
								<?php echo substr($otep, 0, 4); ?>-<?php echo substr($otep, 4, 4); ?>-<?php echo substr($otep, 8, 4); ?>-<?php echo substr($otep, 12, 4); ?>
							</span>
						<?php endforeach; ?>
						<div class="clearfix"></div>
					<?php endif; ?>
				</fieldset>
			<?php endif; ?>
			<div class="form-group row">
				<div class="col-12 text-right">
					<button type="submit" class="btn btn-primary">
						<?php echo JText::_('JSUBMIT'); ?>
					</button>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="profile.save" />
					<a class="btn btn-light" href="<?php echo JRoute::_('index.php?option=com_users&view=profile'); ?>" title="<?php echo JText::_('JCANCEL'); ?>">
						<?php echo JText::_('JCANCEL'); ?>
					</a>
				</div>
			</div>
			<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>