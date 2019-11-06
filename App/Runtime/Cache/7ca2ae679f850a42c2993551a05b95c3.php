<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="__PUBLIC__/js/formValidator-4.0.1.min.js" charset="UTF-8"></script>
<script type="text/javascript" src="__PUBLIC__/js/formValidatorRegex.js" charset="UTF-8"></script>
<style>
	.table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td{
		border:0px;
	}
</style>
<form class="form-horizontal" action="<?php echo U('setting/fieldedit');?>" method="post" name="form1" id="form1">
	<input type="hidden" name="field_id" value="<?php echo ($fields["field_id"]); ?>"/>
	<table class="table form-inline">
		<!-- <tr>
			<th colspan="2"><h4><i class="fa fa-edit">&nbsp; <?php echo L('FIELDS_INFORMATION');?></i></h4></th>
		</tr> -->
		<tr>
			<td width="130px;" class="tdleft"><?php echo L('FIELDS_NAMES');?></td>
			<td><?php echo ($fields["name"]); ?></td>
		<tr>
        <tr>
			<td class="tdleft"><?php echo L('FIELDS_TYPE');?></td>
			<td><?php echo ($fields["form_type_name"]); ?></td>
		<tr>
			<td class="tdleft"><?php echo L('WEATHER_INFORMATION');?></td>
			<td class=""><?php if($fields["is_main"] == 1): echo L('MIAN_INFO'); else: echo L('EXTRA_INFO'); endif; ?></td>
		</tr>
		<?php if($fields['form_type'] == 'box'): ?><tr id="box_type_td" style="">
			<td class="tdleft"><?php echo L('OPTION_TYPE');?></td>
			<td>
				<div class="radio radio-info radio-inline">
					<input type="radio" name="setting[boxtype]" id="settimg_type_1" value="radio" <?php if($fields['setting']['type'] == 'radio'): ?>checked="checked"<?php endif; ?>/>
					<label for="settimg_type_1"> <?php echo L('RADIO');?> </label>
				</div>
				<div class="radio radio-info radio-inline">
					<input type="radio" name="setting[boxtype]" id="settimg_type_2" <?php if($fields['setting']['type'] == 'checkbox'): ?>checked="checked"<?php endif; ?> value="checkbox"/>
					<label for="settimg_type_2"> <?php echo L('MULTISELECT');?> </label>
				</div>
				<div class="radio radio-info radio-inline">
					<input type="radio" name="setting[boxtype]" id="settimg_type_3" <?php if($fields['setting']['type'] == 'select'): ?>checked="checked"<?php endif; ?> value="select"/>
					<label for="settimg_type_3"> <?php echo L('COMBOBOX');?> </label>
				</div>
			</td>
		</tr>
		<tr id="box_data_td">
			<td class="tdleft"><?php echo L('LIST_OF_OPTIONS');?></td>
			<td>
				<textarea class="form-control" id="setting_options" name="setting[options]"><?php echo ($fields["setting"]["options"]); ?></textarea></br>
				<span style="color:red;padding-top: 10px;">*</span><span id="setting_optionsTip"></span>
			</td>
		</tr><?php endif; ?>
		<tr id='field_td'>
			<td class="tdleft"><?php echo L('FIELDS_NAME');?>：</td>
			<td><?php if($fields['operating'] == 0): ?><input type="text" id="field" name="field" class="form-control" value="<?php echo ($fields["field"]); ?>"/><span style="color:red;">*</span><span id="fieldTip"></span><?php else: echo ($fields["field"]); endif; ?></td>
		</tr>
		<tr id="name_td">
			<td class="tdleft"><?php echo L('ID_NAME');?></td>
			<td><input type="text" id="field_name" name="name" class="form-control" value="<?php echo ($fields["name"]); ?>"/><span style="color:red;">*</span><span id="field_nameTip"></span></td>
		</tr> 
		<!-- <tr id="max_length_td">
			<td class="tdleft"><?php echo L('THE_LARGEST_FIELD_LENGTH');?></td>
			<td><input type="text" name="max_length" class="form-control" id="max_length" value="<?php if($fields['max_length'] > 0): echo ($fields["max_length"]); endif; ?>"/>
			<span style="color:red;">*</span><span id="max_lengthTip"></span></td>
		</tr> -->
        <?php if($fields['form_type'] != 'textarea'&& $fields['form_type'] != 'editor' && $fields['form_type'] != 'address' && $fields['form_type'] != 'datetime' ): ?><tr id="default_value_td">
			<td class="tdleft"><?php echo L('DEFAULT_VALUE');?></td>
			<td>
				<input type="text" name="default_value" id="default_value" class="form-control" value="<?php echo ($fields["default_value"]); ?>"/>
				<?php if($fields['form_type'] == 'number'): ?><span style="display:none;color:red;">&nbsp;注：数字类型最大数值为：2147483647</span><?php endif; ?>
				<span id="default_valueTip"></span>
			</td>
		</tr><?php endif; ?>
		<tr id="color_td">
			<td class="tdleft"><?php echo L('COLOR');?></td>
			<td><input class="color form-control" name="color" value="<?php echo (($fields["color"])?($fields["color"]):'333333'); ?>" /></td>
		</tr>
        <tr id="is_validate_td">
			<td class="tdleft"><?php echo L('WHETHER_THE_VALIDATION');?></td>
			<td>
				<div class="radio radio-info radio-inline">
					<input name="is_validate" id="is_validate_1" onclick="validateSwitch(1)" <?php if($fields["is_validate"] == 1): ?>checked="checked"<?php endif; ?> type="radio" value="1"/>
					<label for="is_validate_1"> <?php echo L('IS');?> </label>
				</div>
				<div class="radio radio-info radio-inline">
					<input name="is_validate" id="is_validate_0" onclick="validateSwitch(0)" <?php if($fields["is_validate"] != 1): ?>checked="checked"<?php endif; ?>  type="radio" value="0"/>
					<label for="is_validate_0"> <?php echo L('ISNOT');?> </label>
				</div>
			</td>
		</tr>
		<tr id="is_unique_td" <?php if(!$fields['is_validate']): ?>style="display:none;"<?php endif; ?>>
			<td class="tdleft"><?php echo L('WHETHER_ONLY');?></td>
			<td>
				<div class="radio radio-info radio-inline">
					<input name="is_unique" id="is_unique_1" <?php if($fields["is_unique"] == 1): ?>checked="checked"<?php endif; ?> type="radio" value="1"/>
					<label for="is_unique_1"> <?php echo L('IS');?> </label>
				</div>
				<div class="radio radio-info radio-inline">
					<input name="is_unique" id="is_unique_0" <?php if($fields["is_unique"] != 1): ?>checked="checked"<?php endif; ?> type="radio" value="0"/>
					<label for="is_unique_0"> <?php echo L('ISNOT');?> </label>
				</div>
			</td>
		</tr>
	<?php if($fields['model'] == 'customer' or $fields['model'] == 'leads'): ?><tr id="is_recheck_td" <?php if(!$fields['is_validate']): ?>style="display:none;"<?php endif; ?>>
			<td class="tdleft">是否查重 :</td>
			<td>
				<div class="radio radio-info radio-inline">
					<input name="is_recheck" id="is_recheck_1" <?php if($fields["is_recheck"] == 1): ?>checked="checked"<?php endif; ?> type="radio" value="1"/>
					<label for="is_recheck_1"> <?php echo L('IS');?> </label>
				</div>
				<div class="radio radio-info radio-inline">
					<input name="is_recheck" id="is_recheck_0" <?php if($fields["is_recheck"] != 1): ?>checked="checked"<?php endif; ?> type="radio" value="0"/>
					<label for="is_recheck_0"> <?php echo L('ISNOT');?> </label>
				</div>
			</td>
		</tr><?php endif; ?>
		<tr id="is_null_td" <?php if(!$fields['is_validate']): ?>style="display:none;"<?php endif; ?>>
			<!-- <td class="tdleft"><?php echo L('WHETHER_ALLOW_NULL');?></td> -->
			<td class="tdleft">必填</td>
			<td>
				<div class="radio radio-info radio-inline">
					<input name="is_null" id="is_null_1" <?php if($fields["is_null"] == 1): ?>checked="checked"<?php endif; ?> type="radio" value="1"/>
					<label for="is_null_1"> <?php echo L('IS');?> </label>
				</div>
				<div class="radio radio-info radio-inline">
					<input name="is_null" id="is_null_0" <?php if($fields["is_null"] != 1): ?>checked="checked"<?php endif; ?> type="radio" value="0"/>
					<label for="is_null_0"> <?php echo L('ISNOT');?> </label>
				</div>
			</td>
		</tr>
		<tr id="in_index_td">
			<td class="tdleft"><?php echo L('LIST_PAGE_DISPLAY');?></td>
			<td>
				<div class="radio radio-info radio-inline">
					<input name="in_index" id="in_index_1" <?php if($fields["in_index"] == 1): ?>checked="checked"<?php endif; ?> type="radio" value="1"/>
					<label for="in_index_1"> <?php echo L('IS');?> </label>
				</div>
				<div class="radio radio-info radio-inline">
					<input name="in_index" id="in_index_0" <?php if($fields["in_index"] != 1): ?>checked="checked"<?php endif; ?> type="radio" value="0"/>
					<label for="in_index_0"> <?php echo L('ISNOT');?> </label>
				</div>
			</td>
		</tr>
		<tr id="in_index_td">
			<td class="tdleft"><?php echo L('ADD_PAGE_DISPLAY');?></td>
			<td>
				<div class="radio radio-info radio-inline">
					<input name="in_add" id="in_add_1" <?php if($fields["in_add"] == 1): ?>checked="checked"<?php endif; ?> type="radio" value="1"/>
					<label for="in_add_1"> <?php echo L('IS');?> </label>
				</div>
				<div class="radio radio-info radio-inline">
					<input name="in_add" id="in_add_0" <?php if($fields["in_add"] != 1): ?>checked="checked"<?php endif; ?> type="radio" value="0"/>
					<label for="in_add_0"> <?php echo L('ISNOT');?> </label>
				</div>
			</td>
		</tr>
		<tr id="tips_td">
			<td class="tdleft"><?php echo L('INPUT_PROMPT');?></td>
			<td><input type="text" name="input_tips" class="form-control" value="<?php echo ($fields["input_tips"]); ?>"/></td>
		</tr>
		<!-- <tr>
			<td>&nbsp;</td>
			<td><input class="btn btn-primary" type="submit" value="<?php echo L('SAVE');?>"/> &nbsp;
			<input class="btn" type="button" onclick="javascript:$('#dialog_field_edit').dialog('close');" value="<?php echo L('CANCEL');?>"/></td>
		</tr> -->
	</table>
</form>
<script type="text/javascript">
	$(function(){
        jscolor.bind();
        $.formValidator.initConfig({formID:"form1",debug:false,submitOnce:true,
            onError:function(msg,obj,errorlist){
                $("#errorlist").empty();
                $.map(errorlist,function(msg){
                    $("#errorlist").append("<li>" + msg + "</li>")
                });
                alert_crm(msg);
            },
            submitAfterAjaxPrompt : '<?php echo L('AJAX_VALIDATING_PLEASE_WAIT'); ?>'
        });
        $("#field_name").formValidator({
			tipID:"field_nameTip",
			empty:false,
			onShow:"<?php echo L('FOR_EXAMPLE_THE_ARTICLE_TITLE'); ?>",
			onFocus:"<?php echo L('PLEASE_ENTER_A_NAME'); ?>",
			onCorrect:"√"
		}).inputValidator({
			min:1,
			empty:{
				leftEmpty:false,
				rightEmpty:false,
				emptyError:"<?php echo L('BOTH_SIDES_ARE_NOT_FREE'); ?>"
			},
			onError:":<?php echo L('LABEL_NAME_CANNOT_BE_EMPTY'); ?>"
		});
        <?php if($fields['operating'] == 0): ?>$("#field").formValidator({
			tipID:"fieldTip",
			empty:false,
			onShow:"请勿随意修改，字段名不能是mysql关键字！",
			onFocus:"字段名不能是mysql关键字！<?php echo L('CAN_ONLY_CONSIST_OF_LOWERCASE_ENGLISH'); ?>",
			onCorrect:"字段名不能是mysql关键字！<?php echo L('CAN_ONLY_CONSIST_OF_LOWERCASE_ENGLISH'); ?>"
		}).inputValidator({
			min:1,
			empty:{
				leftEmpty:false,
				rightEmpty:false,
				emptyError:"<?php echo L('BOTH_SIDES_ARE_NOT_FREE'); ?>"
			},
			onError:"<?php echo L('LABEL_NAME_CANNOT_BE_EMPTY'); ?>"
		}).regexValidator({
			regExp:"field",param:'i',
			dataType:"enum",
			onError:"<?php echo L('ONLY_CONSIST_OF_LOWERCASE_ENGLISH'); ?>"});<?php endif; ?>
		type_id = '<?php echo ($fields["form_type"]); ?>';
		if(type_id == 'box'){
			$("#max_length_td").hide();
			$("#default_value").show();
			$("#is_unique").hide();
			$("#setting_options").formValidator({
				tipID:"setting_optionsTip",
				empty:false,
				onShow:"<?php echo L('INPUT_OPTION_VALUE').'<br/>'.L('OPTION1').'<br/>'.L('OPTION2'); ?>",
				onFocus:"<?php echo L('INPUT_OPTION_VALUE').'<br/>'.L('OPTION1').'<br/>'.L('OPTION2'); ?>",
				onCorrect:"格式正确"
			}).inputValidator({
				min:1,
				empty:{
					leftEmpty:false,
					rightEmpty:false,
					emptyError:"<?php echo L('INPUT_OPTION_VALUE').'<br/>'.L('OPTION1').'<br/>'.L('OPTION2'); ?>"
				},
				onError:"选项不能为空"
			});
		}else if(type_id == 'textarea'){
			$("#box_data").hide();
			$("#box_type").hide();
			$("#is_unique").hide();
		}else if(type_id == 'datetime'){
			$("#box_data").hide();
			$("#box_type").hide();
			$("#default_value").show();
			$("#is_unique").hide();
			$("#max_length").hide();
		}else if(type_id == 'editor'){
			$("#box_data").hide();
			$("#box_type").hide();
			$("#default_value").hide();
			$("#is_unique").hide();
			$("#max_length").hide();
		}else if(type_id == 'address'){
			$("#box_data").hide();
			$("#box_type").hide();
			$("#default_value").hide();
			$("#is_unique").hide();
			$("#max_length").hide();
			$("#max_length").hide();
		}else{
			$("#box_data").hide();
			$("#box_type").hide();
		}
        <?php if($fields['form_type'] == 'text' ): ?>$("#max_length").formValidator({tipID:"max_lengthTip",empty:true,onEmpty:'<?php echo L('EDITING_THE_LENGTH_OF_THE_SMALLE'); ?>',onShow:"<?php echo L('EDITING_THE_LENGTH_OF_THE_SMALLE'); ?>",onFocus:"<?php echo L('EDITING_THE_LENGTH_OF_THE_SMALLE'); ?>",onCorrect:"√"}).regexValidator({regExp:"intege1",param:'i',dataType:"enum",onError:"<?php echo L('ONLY_FILL_IN_POSITIVE_INTEGER'); ?>"}).inputValidator({max:1000,type:"value",onError:"<?php echo L('MUST_BE_BETWEEN_1_1000'); ?>"});
        <?php elseif($fields['form_type'] == 'number' ): ?>
        $("#default_value").formValidator({tipID:"default_valueTip",empty:true,onEmpty:'',onShow:" ",onFocus:" ",onCorrect:"√"}).regexValidator({regExp:"intege1",param:'i',dataType:"enum",onError:"<?php echo L('ONLY_FILL_IN_POSITIVE_INTEGER'); ?>"}).inputValidator({min:-2147483647,max:2147483647,type:"value",onError:"<?php echo L('MUST_BE_BETWEEN'); ?>"});
        $("#max_length").formValidator({tipID:"max_lengthTip",empty:true,onEmpty:'<?php echo L('EDITING_THE_LENGTH_OF_THE_SMALLE'); ?>',onShow:"<?php echo L('EDITING_THE_LENGTH_OF_THE_SMALLE'); ?>",onFocus:"<?php echo L('EDITING_THE_LENGTH_OF_THE_SMALLE'); ?>",onCorrect:"√"}).regexValidator({regExp:"intege1",param:'i',dataType:"enum",onError:"<?php echo L('ONLY_FILL_IN_POSITIVE_INTEGER'); ?>"}).inputValidator({max:11,type:"value",onError:"<?php echo L('MUST_BE_BETWEEN_1_11'); ?>"});<?php endif; ?>
	});
	
    function validateSwitch(set_val){
    	// 字段类型
		var form_type = $("#form_type").val();
		//1为开启验证，0为关闭验证
		if(1 == set_val){
			$('#is_unique_td').show();
			if (form_type != 'textarea' && form_type != 'box' && form_type != 'datetime' && form_type != 'address') {
				$('#is_recheck_td').show();
			}
			$('#is_null_td').show();
			//开启后设置默值认为不验证‘是否唯一’和‘是否允许为空’
			$("input[name=is_unique]").last().prop('checked','true');
			$("input[name=is_null]").first().prop('checked','true');
			$("input[name=is_recheck]").last().prop('checked','true');
		}else{
			//如果选择不验证，则设置‘是否唯一’和‘是否允许为空’的值为不验证
			$("input[name=is_validate]").last().prop('checked','true');
			$("input[name=is_unique]").last().prop('checked','true');
			$("input[name=is_recheck]").last().prop('checked','true');
			$("input[name=is_null]").first().prop('checked','true');
			$('#is_unique_td').hide();
			$('#is_recheck_td').hide();
			$('#is_null_td').hide();
		}
	}
</script>