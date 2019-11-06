<?php if (!defined('THINK_PATH')) exit();?><div id="reply_list">
	<?php if(is_array($reply_list)): $i = 0; $__LIST__ = $reply_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="ibox-content" id="reply_content_<?php echo ($vo['id']); ?>" style="border:none;border-bottom: 1px dashed #d3d3d6;">
			<div class="social-feed-separated clearfix">
	            <div class="social-feed-box" id="reply_header_<?php echo ($vo['id']); ?>">
	                <div class="social-avatar">
	                    <span class="text-muted" id="status_name_<?php echo ($vo['id']); ?>">类型：<?php echo ($vo['status_name']); ?></span>&nbsp;&nbsp;
	                    <a class="reply_edit" rel="<?php echo ($vo['id']); ?>" href="javascript:void(0);" title="编辑"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
	                    <a class="reply_del" rel="<?php echo ($vo['id']); ?>" href="javascript:void(0);" title="删除"><i class="fa fa-trash-o"></i></a>
	                </div>
	                <div class="social-body" style="padding-left:0px;margin-top:10px; ">
	                    <span id="content_<?php echo ($vo['id']); ?>" style="word-wrap:break-word;line-height: 21px;color: #000;"><?php echo ($vo['content']); ?></span>
	                </div>
	            </div>
	            <div class="social-feed-box" id="reply_edit_<?php echo ($vo['id']); ?>" style="display:none;border-bottom: none;width:100%;">
        			<form id="form_header_<?php echo ($vo['id']); ?>">
        				<input type="hidden" name="type" value="2" />
        				<input type="hidden" name="id" value="<?php echo ($vo['id']); ?>" />
        				<div class="taskboard-stage-rename-w">
    						<div class="form-group">
								<select name="status_id" id="status_id_<?php echo ($vo['id']); ?>" class="form-control select2" style="width:150px;">
									<?php if(is_array($status_list)): $i = 0; $__LIST__ = $status_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo1['id']); ?>" <?php if($vo['status_id'] == $vo1['id']): ?>selected<?php endif; ?> ><?php echo ($vo1['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
								</select>
								<textarea class="form-control" name="content" id="content_reply_<?php echo ($vo['id']); ?>" rows="3" placeholder="快捷沟通内容" style="margin-top:10px;"><?php echo ($vo['content']); ?></textarea>
							</div>
    						<div class="form-group">
    							<div class="col-sm-6">
    								<button class="btn btn-primary btn-block reply_save" rel="<?php echo ($vo['id']); ?>" type="button">保存</button>
    							</div>
    							<div class="col-sm-6">
    								<button class="btn btn-block btn-white reply_cancel" rel="<?php echo ($vo['id']); ?>" type="button">取消</button>
    							</div>
    						</div>
    					</div>
        			</form>
				</div>
	        </div>
		</div><?php endforeach; endif; else: echo "" ;endif; ?>
	<div class="action-wrap" style="margin:10px 0 0 10px;">
		<a class="add-item-toggle" id="toggle_reply" href="javascript:void(0);" >
			<i class="fa fa-plus"></i>&nbsp;&nbsp;添加
		</a>
		<div class="add-item-wrap" id="wrap_reply" style="display: none;">
			<form class="add-item" role="form" id="form_reply" method="post" style="margin-top:15px;">
				<input type="hidden" name="type" value="2" />
				<div class="form-group">
					<select name="status_id" class="form-control select2" style="width:150px;">
						<?php if(is_array($status_list)): $i = 0; $__LIST__ = $status_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
					<textarea class="form-control" name="content" id="content_reply" rows="3" placeholder="快捷沟通内容" style="margin-top:10px;"></textarea>
				</div>
				<div class="form-group text-right">
					<a class="btn btn-sm btn-white add-item-cancel" id="add-item-cancel" href="javascript:void(0);">取消</a>
					<button type="button" class="btn btn-primary add-item-add" id="add_reply" rel="<?php echo ($vo['id']); ?>" style="margin-right:10%;">添加</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
$(".select2").select2();

$('#reply_list').on('click','#toggle_reply',function(){
	$(this).hide();
	$('#wrap_reply').show();
});

$('#reply_list').on('click','#add-item-cancel',function(){
	$('#wrap_reply').hide();
	$('#toggle_reply').show();
});

//添加
$('#wrap_reply').on('click','#add_reply',function(){
	var content = $('#content_reply').val();
	if (content == '') {
		alert_crm('快捷沟通内容不能为空！');
		return false;
	}
	var temp = '';
	$.ajax({
        type: "POST",
        url: "<?php echo U('setting/logreplyadd');?>",
        data:$('#form_reply').serialize(),
        async: true,
        success: function(data) {
            if(data.status == 1){
                $('#wrap_reply').hide();
                temp += '<div class="ibox-content" id="reply_content_'+data.data.id+'" style="border:none;border-bottom: 1px dashed #d3d3d6;">\
							<div class="social-feed-separated clearfix">\
					            <div class="social-feed-box">\
					                <div class="social-avatar">\
					                    <span class="text-muted">类型：'+data.data.status_name+'</span>&nbsp;&nbsp;\
					                    <a class="reply_del" rel="'+data.data.id+'" href="javascript:void(0);" title="删除"><i class="fa fa-trash-o"></i></a>\
					                </div>\
					                <div class="social-body" style="padding-left:0px;margin-top:10px; ">\
					                    <span style="word-wrap:break-word;line-height: 21px;color: #000;">'+data.data.content+'</span>\
					                </div>\
					            </div>\
					        </div>\
						</div>';
				$('#reply_list').append(temp);
            }else{
				swal({
                    title: "操作失败!",
                    text: data.info,
                    type: "error"
                });
            }
        }
    });
});

//编辑
$('#reply_list').on('click','.reply_edit',function(){
	var reply_id = $(this).attr('rel');
	$('#reply_header_'+reply_id).hide();
	$('#reply_edit_'+reply_id).show();
});

$('#reply_list').on('click','.reply_cancel',function(){
	var reply_id = $(this).attr('rel');
	$('#reply_header_'+reply_id).show();
	$('#reply_edit_'+reply_id).hide();
});

$('#reply_list').on('click','.reply_save',function(){
	var reply_id = $(this).attr('rel');
	var content = $('#content_reply_'+reply_id).val();
	if (content == '') {
		alert_crm('快捷沟通内容不能为空！');
		return false;
	}
	var temp = '';
	$.ajax({
        type: "POST",
        url: "<?php echo U('setting/logreplyedit');?>",
        data:$('#form_header_'+reply_id).serialize(),
        async: true,
        success: function(data) {
            if(data.status == 1){
                $('#status_name_'+reply_id).html('类型：'+data.data.status_name);
                $('#content_'+reply_id).html(data.data.content);
                $('#reply_edit_'+reply_id).hide();
                $('#reply_header_'+reply_id).show();
            }else{
				swal({
                    title: "操作失败!",
                    text: data.info,
                    type: "error"
                });
            }
        }
    });
});

//快捷沟通删除
$('#reply_list').on('click','.reply_del',function(){
	var reply_id = $(this).attr('rel');
	swal({
		title: "您确定要删除该快捷沟通日志吗？",
		text: "删除后不可还原，请谨慎操作！",
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
		        type: "POST",
		        url: "<?php echo U('setting/logreplydel');?>",
		        data:{id:reply_id},
		        async: true,
		        success: function(data) {
		            if(data.status == 1){
		                swal("删除成功！", "您已经删除了该记录！", "success");
		                $('#reply_content_'+reply_id).remove();
		            }else{
						swal({
		                    title: "操作失败!",
		                    text: data.info,
		                    type: "error"
		                });
		            }
		        }
		    });
	    } else {
            swal("已取消","您取消了删除操作！","error");
        }
    });
});
</script>