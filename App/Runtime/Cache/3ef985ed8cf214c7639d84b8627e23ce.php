<?php if (!defined('THINK_PATH')) exit();?><form name="fenpei_form" id="fenpei_form" action="<?php echo U('leads/receive');?>"  method="post">
	<input type="hidden" name="id" value="<?php echo ($leads_id); ?>"/>
	<?php echo L('ASSIGN_LEADS_TO_SOMEONE');?>
	<input type="hidden" name="owner_role_id" id="owner_role_id" value="<?php echo ($role_info['role_id']); ?>"/>
	<input type="text" name="owner_name" class="form-control" style="display: initial;width: 200px;" id="owner_name" value="<?php echo ($role_info['user_name']); ?>"/>
	<p style="width:300px; float:right;"><?php echo L('NOTIFICATION_METHODS');?>
		<input type="checkbox" name="message_alert" value="1" checked="checked"><?php echo L('MESSAGE');?> &nbsp; 
		<?php if(F('sms')): ?><input type="checkbox" name="sms_alert" value="1"><?php echo L('SMS');?> &nbsp;<?php endif; ?>
		<input type="checkbox" name="email_alert" value="1"><?php echo L('EMAIL');?>
	</p>
</form>
<div style="display:none;" id="dialog-role-list2" title="<?php echo L('SELECT_CONTACTS_OWMER');?>">
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
	width: 800,
	maxHeight: 400,
	buttons: {
		"确定": function () {
			var item = $('input:radio[name="owner"]:checked').val();
			var name = $('input:radio[name="owner"]:checked').parent().parent().next().html();
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