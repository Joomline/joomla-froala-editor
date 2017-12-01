<?php
/**
 * Editor Froala
 *
 * @version 	2.1
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

/** @var $this FroalaViewProfiles */
defined( '_JEXEC' ) or die;// No direct access
JHtml::_( 'bootstrap.tooltip' );
JHtml::_( 'behavior.multiselect' );
JHtml::_( 'formbehavior.chosen', 'select' );
JHtml::_('searchtools.main');
JHtml::_('searchtools.form', '#adminForm', array(
    "filtersHidden" => true,
    "defaultLimit" => "20",
    "searchFieldSelector" => "#filter_search",
    "orderFieldSelector" => "#list_fullordering",
    "formSelector" => "#adminForm"
));

$user = JFactory::getUser();
$userId = $user->get( 'id' );

$listOrder = $this->escape( $this->state->get( 'list.ordering' ) );
$listDirn = $this->escape( $this->state->get( 'list.direction' ) );
$saveOrder = $listOrder == 'ordering';

if ( $saveOrder ) {
	$saveOrderingUrl = 'index.php?option=com_froala&task=profiles.saveOrderAjax&tmpl=component';
	JHtml::_( 'sortablelist.sortable', 'articleList', 'adminForm', strtolower( $listDirn ), $saveOrderingUrl );
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function () {
		var table = document.getElementById("sortTable");
		var direction = document.getElementById("directionTable");
		var order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>

<form action="<?php echo JRoute::_( 'index.php?option=com_froala&view=profiles' ); ?>" method="post" name="adminForm" id="adminForm">

		<div id="j-main-container">

			<table class="table table-striped" id="articleList">
				<thead>
				<tr>
					<th width="1%" class="center hidden-phone" nowrap="nowrap">
						<?php echo JHtml::_( 'searchtools.sort', '', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2' ); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="5%" style="min-width:55px" class="center">
						<?php echo JHtml::_( 'searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder ); ?>
					</th>
					<th>
						<?php echo JHtml::_( 'searchtools.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder ); ?>
					</th>
					<th>
						<?php echo JHtml::_( 'searchtools.sort', 'JGLOBAL_DESCRIPTION', 'description', $listDirn, $listOrder ); ?>
					</th>

                    <th>
                        <?php echo JHtml::_( 'searchtools.sort', 'JTOOLBAR_DEFAULT', '`default`', $listDirn, $listOrder ); ?>
                    </th>
					<th width="1%" class="nowrap hidden-phone">
						<?php echo JHtml::_( 'searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder ); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ( $this->items as $i => $item ) :
					$item->max_ordering = 0;
					$ordering = ( $listOrder == 'a.ordering' );
					$canEdit = $user->authorise( 'core.edit');
					$canCheckin = $user->authorise( 'core.manage');
					$canChange = $user->authorise( 'core.edit.state') && $canCheckin;
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="order nowrap center hidden-phone">
							<?php if ( $canChange ) :
								$disableClassName = '';
								$disabledLabel = '';
								if ( !$saveOrder ) :
									$disabledLabel = JText::_( 'JORDERINGDISABLED' );
									$disableClassName = 'inactive tip-top';
								endif; ?>
								<span class="sortable-handler <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>" rel="tooltip"><i class="icon-menu"></i></span>
								<input type="text" style="display:none" name="order[]" size="5"
									value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
							<?php else : ?>
								<span class="sortable-handler inactive"><i class="icon-menu"></i></span>
							<?php endif; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo JHtml::_( 'grid.id', $i, $item->id ); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_( 'jgrid.published', $item->published, $i, 'profiles.', $canChange, 'cb' ); ?>
							</div>
						</td>
						<td class="nowrap has-context">
							<div class="pull-left">
								<?php if ( $canEdit ) : ?>
									<a href="<?php echo JRoute::_( 'index.php?option=com_froala&task=profile.edit&id=' . $item->id ); ?>" title="<?php echo JText::_( 'JACTION_EDIT' ); ?>">
										<?php echo $this->escape( $item->title ); ?></a>
								<?php else : ?>
									<span title="<?php echo JText::sprintf( 'JFIELD_ALIAS_LABEL', $this->escape( $item->alias ) ); ?>"><?php echo $this->escape( $item->title ); ?></span>
								<?php endif; ?>

							</div>
							<div class="pull-left">
								<?php
								// Create dropdown items
								JHtml::_( 'dropdown.edit', $item->id, 'Profile.' );
								JHtml::_( 'dropdown.divider' );
								if ( $item->published ) :
									JHtml::_( 'dropdown.unpublish', 'cb' . $i, 'Profiles.' );
								else :
									JHtml::_( 'dropdown.publish', 'cb' . $i, 'Profiles.' );
								endif;

								// render dropdown list
								echo JHtml::_( 'dropdown.render' );
								?>
							</div>

						</td>

						<td class="has-context">
							<?php echo $item->description; ?>
						</td>

						<td class="center hidden-phone">
							<?php echo $item->default == 1 ? '<i class="icon-featured"></i>' : ''; ?>
						</td>

						<td class="center hidden-phone">
							<?php echo (int)$item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php echo $this->pagination->getListFooter(); ?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_( 'form.token' ); ?>

		</div>
</form>
<div id="FroalaAdminFooter">
	<a target="_blank" href="http://joomline.org/extensions/scripts-other-developments/froala.html">Froala Editor</a> | Copyright © 2015 <a target="_blank" href="http://joomline.org/">Joomline</a>.
</div>