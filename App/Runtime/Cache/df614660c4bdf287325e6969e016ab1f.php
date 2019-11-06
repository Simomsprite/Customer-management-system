<?php if (!defined('THINK_PATH')) exit();?><style>
	.folder-list li {line-height: 35px;}
</style>
<div>
	<?php if($_GET['module'] == ''): ?><div class="ibox-content" style="border: none;padding-bottom: 5px;">
			<div class="row">
				<div class="col-md-6">
					<ul class="folder-list" style="padding: 0">
			            <li>
			                <i class="fa fa-inbox "></i> 今日跟进数
			                <span class="label label-success pull-right" style="margin-top: 10px;"><?php echo ($total_array['log_count']); ?></span>
			            </li>
			            <li>
			                <i class="fa fa-comments"></i> 线索相关数</a>
			                <span class="label label-warning pull-right" style="margin-top: 10px;"><?php echo ($total_array['leads_count']); ?></span>
			            </li>
			        </ul>
				</div>
				<div class="col-md-6">
					<ul class="folder-list" style="padding: 0">
			            <li>
			                <i class="fa fa-user"></i> 客户相关数</a>
			                <span class="label label-primary pull-right" style="margin-top: 10px;"><?php echo ($total_array['customer_count']); ?></span>
			            </li>
			            <li>
			                <i class="fa fa-coffee"></i> 商机相关数
			                <span class="label label-info pull-right" style="margin-top: 10px;"><?php echo ($total_array['business_count']); ?></span>
			            </li>
			        </ul>
				</div>
			</div>
		</div><?php endif; ?>
	<?php if(empty($log_list)): ?><div style="background-color:#fff;"><div style="text-align:center;color:#E4E4E4;font-size:22px;font-weight:700;padding:15px 0px;">
	<img src="__PUBLIC__/img/exclamation_mark.png" style="margin-top:-3px;">&nbsp;&nbsp;暂无数据
</div></div>
	<?php else: ?>
	<?php if(is_array($log_list)): $i = 0; $__LIST__ = $log_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="ibox-content" style="border:none;margin-top: 5px;border-top: 1px dashed #d3d3d6;">
			<div class="social-feed-separated clearfix">
	            <div class="social-feed-box">
	            	<?php if($vo['sign'] == 1): ?><div class="social-avatar">
                            <img alt="image" style="width:35px;height:35px;" class="img-circle" src="<?php echo ($vo['user']['thumb_path']); ?>">
                            <a class="role_info name-colors"  rel="<?php echo ($vo['user']['role_id']); ?>" href="javascript:void(0);"><?php echo ($vo['user']['full_name']); ?></a>&nbsp;&nbsp;
                            <span class="text-muted">发布了一条签到记录</span>&nbsp;&nbsp;&nbsp;
                            <span class="text-muted" ><?php echo (date("Y-m-d H:i",$vo['create_date'])); ?></span>
                            <div class="text-muted" style="padding:0 15px 0 50px;">
                                <div class="conent0" style="width:100%;line-height: 30px;">
                                    <img style="width:15px;height:15px;vertical-align:text-bottom;" src="__PUBLIC__/img/location.png"/>
                                    <span style="color:#666"><?php echo ($vo['sign_info']['address']); ?></span>
                                    <input class="longitude" type="hidden" rel="<?php echo ($vo['sign_info']['y']); ?>"/>
                                    <!-- <a href="javascript:void(0);" style="font-size:12px;" class="map" >显示地图</a> -->
                                    <div id="allmap<?php echo ($vo['log_id']); ?>" rel="allmap<?php echo ($vo['log_id']); ?>" class="allmap"></div>
                                    <input class="latitude" type="hidden" rel="<?php echo ($vo['sign_info']['x']); ?>"/>
                                </div>
                                <div class="conent0" style="line-height: 30px;">
                                    <span style="color:#000">签到说明：<?php echo ($vo['sign_info']['log']); ?></span>
                                </div>
                                <div class="conent0" style="line-height: 30px;">
                                    <div style="color:#000">现场照片：</div>
                                    <?php if(is_array($vo['sign_info']['sign_img'])): $i = 0; $__LIST__ = $vo['sign_info']['sign_img'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="box-secondary" rel="<?php echo ($vo["img_id"]); ?>" style="width:100px;height:100px;float:left;margin-left:5px;">
                                            <a href="<?php echo ($v["path"]); ?>" target="_self" class="litebox_file" data-litebox-group="group-<?php echo ($vo['log_id']); ?>">
                                                <img src="<?php echo ($v["path"]); ?>" class="thumbnail thumb100" style="width:100%;height:100%;">
                                            </a>
                                        </div><?php endforeach; endif; else: echo "" ;endif; ?>
                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
		                <div class="social-avatar">
		                    <img alt="image" style="width:35px;height:35px;" class="img-circle" src="<?php echo ($vo['user']['thumb_path']); ?>">
		                    <a class="role_info name-colors"  rel="<?php echo ($vo["user"]["role_id"]); ?>" href="javascript:void(0);"><?php echo ($vo['user']['full_name']); ?></a>&nbsp;&nbsp;
		                    <span class="text-muted">发布了一条快速记录</span>&nbsp;&nbsp;&nbsp;
		                    <span class="text-muted"><?php echo (date("Y-m-d  H:i",$vo["create_date"])); ?>&nbsp;&nbsp;<a title="沟通类型" href="javascript:void(0);"><?php echo ($vo['status_name']); ?></a></span>
		                </div><?php endif; ?>
	                <div class="social-body">
	                    <span style="word-wrap:break-word;line-height: 21px;color: #000;"><?php echo ($vo['content']); ?></span>
	                	<div class="social-avatar" style="margin-top: 10px;">
	                		<?php if(!empty($vo["nextstep_time"])): ?><span class="text-muted pull-right">下次联系时间：<?php echo (date("Y-m-d  H:i",$vo["nextstep_time"])); ?></span><?php endif; ?>
		                	<?php if($vo['contacts_id']): ?><span class="text-muted pull-right" style="margin-right: 20px;">
	                                相关联系人：<a href="<?php echo U('contacts/view','id='.$vo['contacts_id']);?>" target="_blank"><?php echo ($vo['contacts_name']); ?>（<?php echo ($vo['contacts_phone']); ?>）</a>
	                            </span><?php endif; ?>
		                </div>
	                    <?php if($vo['url']): ?><div class="log-relation">
	                        	<i class="fa fa-bookmark"></i>&nbsp;<span>相关<?php echo ($vo['module_name']); ?> : <?php echo ($vo['url']); ?></span>
	                        </div><?php endif; ?>
	                </div>
	            </div>
	        </div>
		</div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
</div>
<link href="__PUBLIC__/css/litebox.css" rel="stylesheet" type="text/css">
<script src="__PUBLIC__/js/litebox.min.js" type="text/javascript"></script>
<script src="__PUBLIC__/js/images-loaded.min.js" type="text/javascript"></script>
<script type="text/javascript">
	/**
     * 附件 如果是图片时 双击可查看大图
     */
    $('.litebox_file').liteBox({
      revealSpeed: 400,
      background: 'rgba(0,0,0,.8)',
      overlayClose: true,
      escKey: true,
      navKey: true,
      errorMessage: '图片加载失败.'
    });
</script>