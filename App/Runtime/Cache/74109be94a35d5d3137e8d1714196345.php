<?php if (!defined('THINK_PATH')) exit();?><style>
	.body-span{
		line-height:25px;
		padding-top:0px;
	}
	.form-horizontal .control-label{
		text-align: right;
	}
	.close{font-size:34px;font-weight:400;color:#fff;}
    .close:hover{color:#fff;}
</style>
<div class="modal-header" style="border:none;">
    <b style="font-size:16px;">日程详情</b>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
</div>
<div class="modal-body col-sm-12">
    <?php if($event_info['event_type'] == 1): ?><div class="form-group ">
    		<label class="control-label col-sm-2">内容：</label>
    		<div class="col-sm-10">
    			<span class="body-span"><?php echo ($event_info['subject']); ?></span>
    		</div>
    	</div>
        <div class="form-group">
            <label class="col-sm-2 control-label">开始：</label>
            <div class="col-sm-10">
                <span class="body-span"><?php echo (date('Y-m-d H:i:s',$event_info['start_date'])); ?></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">结束：</label>
            <div class="col-sm-10">
                <span class="body-span"><?php echo (date('Y-m-d H:i:s',$event_info['end_date'])); ?></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">相关<?php echo ($event_info['relevant_name']); ?>：</label>
            <div class="col-sm-10">
                <span class="body-span"><a target="_blank" href="./index.php?m=<?php echo ($event_info['module']); ?>&a=view&id=<?php echo ($event_info['module_id']); ?>"><?php echo ($event_info['module_name']); ?></a></span>
            </div>
        </div>
        <!-- <div class="form-group">
            <label class="col-sm-2 control-label">内容：</label>
            <div class="col-sm-10">
                <span class="body-span"><?php echo ($event_info['venue']); ?></span>
            </div>
        </div> -->
        <div class="form-group">
            <label class="col-sm-2 control-label">描述：</label>
            <div class="col-sm-10">
            	<span class="body-span"><?php echo ($event_info['description']); ?></span>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2">标记：</label>
            <div class="col-sm-10">
                <ul class="color-selector" style="padding-left: 0px;">
                    <li>
                        <div class="radio bg-blue-600">
                            <input id="radio1" type="radio" name="color" <?php if($event_info['color'] == '#62a8ea' || $event_info['color'] == ''): ?>checked="checked"<?php endif; ?> value="#62a8ea">
                            <label for="radio1"></label>
                        </div>
                    </li>
                    <li>
                    	<div class="radio bg-green-600">
                            <input id="radio2" type="radio" name="color" value="#46be8a" <?php if($event_info['color'] == '#46be8a'): ?>checked="checked"<?php endif; ?>>
                            <label for="radio2"></label>
                        </div>
                    </li>
                    <li>
                    	<div class="radio bg-cyan-600">
                            <input id="radio3" type="radio" name="color" value="#57c7d4" <?php if($event_info['color'] == '#57c7d4'): ?>checked="checked"<?php endif; ?>>
                            <label for="radio3"></label>
                        </div>
                    </li>
                    <li>
                    	<div class="radio bg-orange-600">
                            <input id="radio4" type="radio" name="color" value="#f2a654" <?php if($event_info['color'] == '#f2a654'): ?>checked="checked"<?php endif; ?>>
                            <label for="radio4"></label>
                        </div>
                    </li>
                    <li >
                    	<div class="radio bg-red-600">
                            <input id="radio5" type="radio" name="color" value="#f96868" <?php if($event_info['color'] == '#f96868'): ?>checked="checked"<?php endif; ?>>
                            <label for="radio5"></label>
                        </div>
                    </li>
                    <li >
                    	<div class="radio bg-blue-grey-600">
                            <input id="radio6" type="radio" name="color" value="#526069" <?php if($event_info['color'] == '#526069'): ?>checked="checked"<?php endif; ?>>
                            <label for="radio6"></label>
                        </div>
                    </li>
                    <li >
                    	<div class="radio bg-purple-600">
                            <input id="radio7" type="radio" name="color" value="#926dde" <?php if($event_info['color'] == '#926dde'): ?>checked="checked"<?php endif; ?>>
                            <label for="radio7"></label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <div class="form-group">
            <label class="col-sm-2 control-label">类型：</label>
            <div class="col-sm-8">
                <span class="body-span"><?php echo ($event_info['subject']); ?></span>
                <span class="text-info" style="line-height: 26px; margin-left: 10px;">
                    <?php echo ($event_info['remind_info']['visitor_plan']['type']); ?>
                </span>
            </div>
            <div class="col-sm-2">
                <?php if($event_info['remind_info']['visitor_plan']): ?><div style="width: 12px; height: 12px; border-radius: 50%; background-color: <?php echo ($event_info['remind_info']['visitor_plan']['status_color']); ?>; float: left; margin: 3px 3px 0 0;"></div> 
                    <?php echo ($event_info['remind_info']['visitor_plan']['status']); endif; ?>
            </div>
        </div>
        <?php if($event_info['remind_info'] != ''): ?><div class="form-group">
                <label class="col-sm-2 control-label">内容：</label>
                <div class="col-sm-10">
                    <span class="body-span"><?php echo ($event_info['remind_info']['content']); ?></span>
                </div>
            </div><?php endif; ?>
        <div class="form-group">
            <label class="col-sm-2 control-label">相关：</label>
            <div class="col-sm-10">
                <span class="body-span">
                    <?php if($event_info['module'] == 'receivables'): ?><a target="_blank" href="<?php echo U('finance/view','id='.$event_info['module_id'].'&t='.$event_info['t']);?>"><?php echo ($event_info['module_name']); ?></a>
                    <?php else: ?>
                        <a target="_blank" href="<?php echo U($event_info['module'].'/view','id='.$event_info['module_id']);?>"><?php echo ($event_info['module_name']); ?></a><?php endif; ?>
                </span>
            </div>
        </div>
        <?php if($event_info['remind_info']['visitor_plan']): ?><div class="form-group">
                <label class="col-sm-2 control-label">计划时间：</label>
                <div class="col-sm-10">
                    <span class="body-span">
                        <?php echo ($event_info['remind_info']['visitor_plan']['plan_time']); ?>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">延期时间：</label>
                <div class="col-sm-10">
                    <span class="body-span">
                        <?php echo ($event_info['remind_info']['visitor_plan']['delay_time']); ?>
                    </span>
                </div>
            </div><?php endif; endif; ?>
</div>

<div class="modal-footer">
    <div class="form-actions">
        <?php if($event_info['event_type'] == 1 and $not_edit != 1): ?><button class="btn btn-primary" id="edit_dialog" rel="<?php echo ($event_info['event_id']); ?>" data-dismiss="modal" type="button">
                编辑
            </button><?php endif; ?>
        <?php if($event_info['event_type'] != 3): if($event_info['remind_info']['visitor_plan']): ?><a class="btn btn-warning" id="visitor_plan_edit" rel="<?php echo ($event_info['remind_info']['visitor_plan']['id']); ?>" href="javascript:void(0);">延期计划</a>
                <a class="btn btn-danger" id="visitor_plan_delete" rel="<?php echo ($event_info['remind_info']['visitor_plan']['id']); ?>" href="javascript:void(0);">删除计划</a><?php endif; ?>
            <?php if($not_edit != 1): ?><a class="btn btn-default" id="dialog_delete" rel="<?php echo ($event_info['event_id']); ?>" href="javascript:;">删除</a><?php endif; endif; ?>
    </div>
</div>
<div id="visitor_plan_model" style="display: none;">
    <input type="hidden" name="id" value="<?php echo ($event_info['remind_info']['visitor_plan']['id']); ?>">
    <div class="form-group">
        <div class="col-md-4" style="margin: 10px 0;">
            <label class="control-label">计划时间：</label>
        </div>
        <div class="col-md-8" style="margin: 10px 0;">
            <input class="Wdate text-input small-input form-control" name="plan_time" onclick="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm'})"
                value="<?php echo ($event_info['remind_info']['visitor_plan']['plan_time']); ?>"
                onblur="checkform(this);">
        </div>
    </div>
</div>

<div id="dialog_visitor_plan" style="display: none;" title="计划延期">
</div>

<script>
	$('#edit_dialog').click(function(){
        var event_id = $(this).attr('rel');
        $('#editNewEvent').modal('show');
        var url = "<?php echo U('event/edit','event_id=');?>"+event_id;
        $('#edit_event').load(url);
	});

	//删除日程
	$('#dialog_delete').click(function(){
		var event_id = $(this).attr('rel');
		swal({
			title: "您确定要删除该日程吗？",
   			text: "删除后将无法恢复，请谨慎操作！",
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "是的，我要删除！",
	        cancelButtonText:'让我再考虑一下…',
	        closeOnConfirm:false,
	        closeOnCancel:false
		},
		function(isConfirm){
	        if (isConfirm) {
	        	$.ajax({
		            type:'post',
		            url: "<?php echo U('event/delete');?>",
		            data: {event_id: event_id},
		            async: false,
		            success: function (data) {
						if (data.status == 1) {
							swal("删除成功！", "您已经永久删除了信息！", "success");
							location.reload();
						}else{
							swal({
								title: "操作失败！",
								text:data.info,
								type: "error"
							})
							return false;
						}
		            },
		            dataType: 'json'
		        });
	        } else {
	            swal("已取消","您取消了删除操作！","error");
	        }
	    });
    });
    
    // 取消计划
    $('#visitor_plan_delete').on('click', function () {
        let id = $(this).attr('rel');
        swal({
            title: "您确定要取消该访客计划吗？",
            text: "取消后将无法恢复，请谨慎操作！",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "是的，我要取消！",
            cancelButtonText: '让我再考虑一下…',
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: 'post',
                    url: "<?php echo U('remind/visitor_plan_del');?>",
                    data: { id: id },
                    async: false,
                    success: function (data) {
                        if (data.status == 1) {
                            swal("取消成功！", "您已经永久取消了该访客计划！", "success");
                            location.reload();
                        } else {
                            swal({
                                title: "操作失败！",
                                text: data.msg,
                                type: "error"
                            })
                            return false;
                        }
                    },
                    dataType: 'json'
                });
            } else {
                // swal("已取消", "您取消了取消操作！", "error");
                swal.close();
            }
        });
    });

    // 计划修改
    $('#visitor_plan_edit').on('click', function () {
        $('#dialog_visitor_plan').dialog('open');
        $('#dialog_visitor_plan').html($('#visitor_plan_model').html());
    });
    $('#dialog_visitor_plan').dialog({
        autoOpen: false,
        modal: true,
        width: 444,
        maxHeight: 460,
        position: ["center", 100],
        buttons: {
            "确定": function () {
                let data = {};
                data.plan_time = $('#dialog_visitor_plan').find('[name="plan_time"]').val();
                data.type = $('#dialog_visitor_plan').find('[name="visitor_plan_type"]').val();
                data.id = $('#dialog_visitor_plan').find('[name="id"]').val();
                let _dialog = $(this);
                $.ajax({
                    url: '<?php echo U("remind/visitor_plan_edit");?>',
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (res) {
                        if (res.status == 1) {
                            _dialog.dialog('close');
                            swal({
                                title: res.msg,
                                type: 'success'
                            }, function () {
                                location.reload();
                            });
                        } else {
                            swal(res.msg, '', 'error');
                            swal({
                                title: res.msg,
                                type: 'error'
                            }, function () {
                                _dialog.dialog('close');
                            });
                        }
                    }
                });              
            },
            "取消": function () {
                $(this).dialog("close");
            }
        }, 
        close: function () {
            dialog_destroy($(this));
        }
    });
</script>