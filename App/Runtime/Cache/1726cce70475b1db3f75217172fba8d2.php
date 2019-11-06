<?php if (!defined('THINK_PATH')) exit();?><form name="fenpei_form" class="form-inline" id="fenpei_form" action="<?php echo U('customer/receive');?>"  method="post">
	<?php echo L('WILL_BE_ALLOCATED_TO_THE_CUSTOMERS');?>:
	<input type="hidden" name="customer_id" value="<?php echo ($customer_id); ?>" />
	<input type="hidden" name="owner_role_id" id="owner_role_id" value="<?php echo (session('role_id')); ?>"/>
	<input type="text" name="owner_name" id="owner_name" class="form-control" value="<?php echo (session('full_name')); ?>"/>
	
	<p style="float:right;line-height: 30px;">
		<?php echo L('INFORM_THE_WAY');?><input type="checkbox" name="message_alert" value="1" checked="checked"><?php echo L('STAND_INSIDE_LETTER');?> &nbsp; 
		<?php if(F('sms')): ?><input type="checkbox" name="sms_alert" value="1"><?php echo L('NOTE');?> &nbsp;<?php endif; ?>
		<input type="checkbox" name="email_alert" value="1"><?php echo L('EMAIL');?>
	</p>
</form>
<div style="display:none;" id="dialog-role-list2" title="<?php echo L('SELECT_ALL_THE_PEOPLE');?>">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$("#dialog-role-list2").dialog({
		autoOpen: false,
		modal: true,
		width: 750,
		maxHeight: 400,
		buttons: {
			"确定": function () {
				var item = $('input:radio[name="owner"]:checked').val();
				var name = $('input:radio[name="owner"]:checked').attr('rel');
				$('#owner_name').val(name);
				$('#owner_role_id').val(item);
				$(this).dialog("close");
			},
			"取消": function () {
				$(this).dialog("close");
			}
		},
		position: ["center", 100]
	});
	$(function(){
		$("#owner_name").click(
			function(){
				$('#dialog-role-list2').dialog('open');
				$('#dialog-role-list2').load('<?php echo U("user/listdialog");?>');
			}
		);
	});
</script>