<?php if (!defined('THINK_PATH')) exit();?><form class="form-horizontal" id="form_transfer" action="<?php echo U('Customer/transfer_edit');?>" method="post">	
	<table class="table">
	<input type="hidden" name="customer_id" value="<?php echo ($customer_id); ?>"/>
		<tr>
			<td class="tdleft" style="width:120px;border:none;text-align:right;">转移给：</td>
			<td colspan="2" style="border:none;">
				<select class="form-control select2" name="role_id" id="role_id">
					<option value="">--请选择--</option>
					<?php if(is_array($role_list)): $i = 0; $__LIST__ = $role_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['role_id']); ?>"><?php echo ($vo['full_name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>	
				</select>
			</td>
			<td style="border:none;width:30px;"></td>
		</tr>
		<tr>
			<td class="tdleft" style="width:120px;border:none;text-align:right;">转移相关：</td>
			<td colspan="2" style="border:none;">
				
				<span style="width:50%; float:left;">
					<div class="checkbox checkbox-primary">
						<input name="about_ids[]" class="about_id" type="checkbox" value="2" />
						<label for="">商机&nbsp; </label>
					</div>
				</span>
				<span style="width:50%; float:left;">
					<div class="checkbox checkbox-primary">
						<input name="about_ids[]" class="about_id" type="checkbox" value="3" />
						<label for="">合同&nbsp; </label>
					</div>
				</span>
			</td>
			<td style="border:none;"></td>
		</tr>
		<tr>
			<td class="tdleft" style="width:120px;border:none;text-align:right;">转移原因：</td>
			<td colspan="2" style="border:none;">
				<textarea class="form-control" name="transfer_content" ></textarea>
			</td>
			<td style="border:none;"></td>
		</tr>
		<tr>
			<td class="tdleft" style="width:120px;border:none;text-align:right;"></td>
			<td colspan="2" style="border:none;color:#999;">
				<div style="margin-bottom:10px">*转移后该操作将无法恢复！</div>
				<div style="margin-bottom:10px">*转移后客户和联系人负责人将自动变更</div>
				<div style="margin-bottom:10px">*如需变更商机或合同负责人请勾选【商机】或【合同】</div>
			</td>
			<td style="border:none;"></td>
		</tr>
	</table>
</form>
<script>
$(".select2").select2({
	placeholder: "--请选择--"
	// allowClear: true
});
</script>