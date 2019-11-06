<?php if (!defined('THINK_PATH')) exit();?><!-- HTML5媒体播放器 -->
<link href="__PUBLIC__/style/plyr/plyr.css" rel="stylesheet">
<style>
	.body_border tr td{border:1px solid #e7eaec}
	thead tr td{background:#F9FaFC}
    .conent0{line-height: 30px;}
    .allmap{width:100%;height:150px;display:none;}
    .business_table tbody tr td{border:none;padding:2px;}
	/*活动*/
    .ai-yellow{
        background-color: rgba(254,215,32,.7);
    }
    .ai-blue{
        background-color: rgba(43,161,225,.7);
    }
    .ai-green{
        background-color: rgba(121,196,85,.7);
    }
    .ai-red{
        background-color:rgba(230,88,69,.7);
    }
    .ai-orange{
       background-color:rgba(244,131,61,.7); 
    }
    .ai-purple{
        background-color:rgba(166,58,150,.7);
    }
    .ai-dark-blue{
        background-color: rgba(58,153,166,.7);
    }
    #vertical-timeline::before{
        width:0px !important;
    }

    .addMember-remove{
        position: relative;
        margin-right:-10px;
        top: -13px;
        right: 13px;
        display:none;
        font-size: 10px;
        line-height: 1;
        color: #fa7a7a;
        cursor: pointer;
        background-color: #fff;
        border-radius: 50%;
    }
    .taskboard-stage-header .dropdown-menu>li>a{
		padding: 6px 10px;
	    -webkit-transition: background-color .25s;
	    -o-transition: background-color .25s;
	    transition: background-color .25s;
	    display: block;
	    padding: 3px 20px;
	    clear: both;
	    font-weight: 400;
	    line-height: 1.6;
	    color: #76838f;
	    white-space: nowrap;
	}
	.dropdown-menu li .icon:first-child, .dropdown-menu li>a .icon:first-child{
		width: 1em; 
	    margin-right: .5em;
	    text-align: center;
	}

	#right-sidebar-task{
		width: 50% !important;
		right: -60%;
		background-color: #fff;
	    overflow: hidden;
	    position: fixed;
	    top: 0px;
	    z-index: 1009;
	    bottom: 0;
	    box-shadow: 0px 2px 1px #888888;
	}
	.agile-list li{
		margin-bottom: -1px;
		border:none;
	}
	.taskboard-stage{
		height:90% !important;
	}
	.taskboard-stages{
		overflow-y:hidden !important;
	}
    .bg-blue-600 label:before{
		background-color: #62a8ea!important;
	}
	.bg-green-600 label:before{
		background-color: #46be8a!important;
	}
	.bg-cyan-600 label:before{
		background-color: #57c7d4!important;
	}
	.bg-orange-600 label:before{
		background-color: #f2a654!important;
	}
	.bg-red-600 label:before{
		background-color: #f96868!important;
	}
	.bg-blue-grey-600 label:before{
		background-color: #526069!important;
	}
	.bg-purple-600 label:before{
		background-color: #926dde!important;
	}

	.bg-blue-600 label:after{
		background-color: #62a8ea!important;
	}
	.bg-green-600 label:after{
		background-color: #46be8a!important;
	}
	.bg-cyan-600 label:after{
		background-color: #57c7d4!important;
	}
	.bg-orange-600 label:after{
		background-color: #f2a654!important;
	}
	.bg-red-600 label:after{
		background-color: #f96868!important;
	}
	.bg-blue-grey-600 label:after{
		background-color: #526069!important;
	}
	.bg-purple-600 label:after{
		background-color: #926dde!important;
	}
	/*.radio label:before{
		width:24px;
		height:24px;
	}*/
	/* .radio label:after{
        left:6px;
        top: 4px;
        font-family: "FontAwesome";
            content: "\f00c";
            color: #fff;
    } */
    .taskboard-stage{
		height:85% !important;
	}
	.taskboard-stages{
		overflow-y:hidden !important;
	}

	.color-selector>li{
		position: relative;
	    display: inline-block;
	    width: 24px;
	    height: 24px;
	    margin: 0 5px 0 0;
	    border-radius: 100%;
	}
    .modal-backdrop{z-index:0;}
</style>
<ul class="nav nav-tabs" id="left_list">
    <li class="active">
        <a href="#tab1" data-toggle="tab" type="tab1">跟进记录</a>
    </li>
	<?php if($share_num != 1): ?><li class="">
            <a href="#tab2" data-toggle="tab" type="tab2">产品详情</a>
        </li>
        <li class="">
            <a href="#tab3" data-toggle="tab" type="tab3">联系人</a>
        </li>
        <li class="">
            <a href="#tab5" data-toggle="tab" type="tab5">销售合同</a>
        </li><?php endif; ?>
    <?php if($business_id == ''): ?><li class="">
            <a href="#tab11" data-toggle="tab" type="tab11">访客计划</a>
        </li><?php endif; ?>
	<?php if($share_num != 1): ?><li class="">
            <a href="#tab6" data-toggle="tab" type="tab6">财务</a>
        </li><?php endif; ?>
	<li class="">
        <a href="#tab9" data-toggle="tab" type="tab9">日程</a>
    </li>
    <?php if($business_id == ''): ?><li class="">
            <a href="#tab10" data-toggle="tab" type="tab10">任务</a>
        </li><?php endif; ?>
    <li class="">
        <a href="#tab7" data-toggle="tab" type="tab7">附件</a>
    </li>
    <li class="">
        <a href="#tab8" data-toggle="tab" type="tab8">操作记录</a>
    </li>
	
   <!--  <div class="nav pull-right">
        <?php if($is_business_code == 1): ?><span style="line-height: 30px;">（ 商机编号：<?php echo ($business[0]['code']); ?> ）</span><?php endif; ?>
    </div> -->
</ul>
<div class="tab-content">
    <div id="tab1" class="tab-pane fade in active">
        <div class="panel-body">
            <?php if($content != 'resource'): ?><div id="form-div">
                    <form id="add-form" action="<?php echo U('Log/add');?>" method="post">
    					<input type='hidden' name="r" value="rBusinessLog"/>
    					<input type='hidden' name="module" value="business"/> 
    					<input type='hidden' id="business_id" name="id" value="<?php echo ($business_id); ?>"/> 
    					<input type='hidden' name="role_id" value="<?php echo (session('role_id')); ?>"/>
    					<textarea name="content" placeholder="添加跟进记录" id="log-text" style="resize:none;" class="form-control" cols="30" rows="2"></textarea>
                        <table class="table business_table" style="border:none;margin-top:15px;display:none;" id="business_table">
                            <tr >
                                <td class="tdleft" style="width:120px;">跟进类型：</td>
                                <td style="width:120px;">
                                    <select name="status_id" id="status_id" class="form-control" onchange="selectStatus()">
                                        <option value="">--请选择--</option>
                                        <?php if(is_array($status_list)): $i = 0; $__LIST__ = $status_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </td>
                                <td>&nbsp;&nbsp;</td>
                                <td class="tdleft" style="width:120px;">快捷添加：</td>
                                <td style="width:300px;">
                                    <select id="replay_list" class="form-control select2" onchange="selectReply()" style="width:80%;float:left;">
                                        <option value="">--请选择--</option>
                                        <?php if(is_array($reply_list)): $i = 0; $__LIST__ = $reply_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['content']); ?>" rel="<?php echo ($vo['status_id']); ?>"><?php echo ($vo['str_content']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>&nbsp;&nbsp;
                                    <a href="javascript:void(0)" id="setting_reply_dialog" title="管理快捷跟进模板" style="line-height: 30px;margin-left:10px;"><i class="fa fa-cog" style="color:#999;"></i></a>
                                </td>
                                <td>&nbsp;&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="tdleft" style="width:120px;">下次联系时间：</td>
                                <td style="width:150px;">
                                    <input type="text" value="" id="nextstep_time_log" class="form-control Wdate" name="nextstep_time" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm', onpicked: time_change, oncleared: time_change})" autocomplete="off">
                                </td>
                                <td>&nbsp;&nbsp;</td>
                                <td class="tdleft" style="width:120px;">保存为跟进模板：</td>
                                <td style="width:120px;">
                                    <div class="checkbox checkbox-primary">
                                        <input type="hidden" name="type" value="2" />
                                        <input name="save_reply" class="select_list" id="save_reply" type="checkbox" value="1"/>
                                        <label for=""></label>
                                    </div>
                                </td>
                                <td>&nbsp;&nbsp;</td>
                            </tr>
                            <?php if ($business_id == '') { ?>
                                <tr id="tr_join_visitor_plan_log" style="display:none;">
                                    <td class="tdleft">加入访客计划：</td>
                                    <td>
                                        <div class="checkbox checkbox-primary pull-left">
                                            <input id="join_visitor_plan_log" name="join_visitor_plan" value="1" type="checkbox">
                                            <label for="join_visitor_plan_log">是</label>
                                        </div>
                                
                                    </td>
                                    <td class="tdleft"></td>
                                    <td>
                                    </td>
                                </tr>
                                <tr id="tr_visitor_plan_content" style="display:none;">
                                    <td class="tdleft">计划内容：</td>
                                    <td>
                                        <input type="text" class="form-control" id="visitor_plan_content" name="visitor_plan_content" placeholder="默认为跟进记录">
                                    </td>
                                    <td class="tdleft"></td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdleft">相关计划：</td>
                                    <td colspan="3">
                                        <?php if(is_array($visitor_plan_list_not_done)): $key = 0; $__LIST__ = $visitor_plan_list_not_done;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><div class="radio radio-info radio-inline" style="margin-left: 10px;" title="<?php echo ($vo["content"]); ?>">
                                                <input type="radio" name="visitor_plan_id" id="visitor_plan_<?php echo ($key); ?>" value="<?php echo ($vo["id"]); ?>">
                                                <label for="visitor_plan_<?php echo ($key); ?>">
                                                    <p style="overflow: hidden; text-overflow:ellipsis; white-space: nowrap; width: 120px;"><?php echo ($vo["content"]); ?></p>
                                                </label>
                                            </div><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (empty($business_id)): ?>
                                <tr>
                                    <td class="tdleft" style="width:120px;">关联商机：</td>
                                    <td colspan="5">
                                        <div class="text-left" id="product-radio" style="padding-top:8px;">
                                            <?php if(is_array($business)): $key = 0; $__LIST__ = $business;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><div class="radio radio-info radio-inline" style="margin-left: 10px;">
                                                    <input type="radio" name="id" class="radio_obj business_code" id="status_<?php echo ($key); ?>" code="<?php echo ($vo["code"]); ?>" value="<?php echo ($vo["business_id"]); ?>">
                                                    <label for="status_<?php echo ($key); ?>">&nbsp;<?php echo ($vo["code"]); ?>&nbsp;&nbsp;&nbsp;</label>&nbsp;&nbsp;
                                                </div><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tdleft" style="width:120px;">相关联系人：</td>
                                    <td colspan="5">
                                        <div class="text-left" id="product-radio" style="padding-top:8px;">
                                            <select name="contacts_id" class="form-control" style="width:200px;">
                                                <option value="">-- 选择联系人 --</option>
                                                <?php if(is_array($contacts_list)): $i = 0; $__LIST__ = $contacts_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['contacts_id']); ?>"><?php echo ($vo['name']); ?>（<?php echo ($vo['telephone']); ?>）</option><?php endforeach; endif; else: echo "" ;endif; ?>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
    					<div>
    						<div class="text-right" id="log-btn" style="padding-top:8px;display:none;"><button class="btn btn-primary" id="add_log" type="button">添加</button>&nbsp;</div><br>
    					</div>
                    </form>
                </div><?php endif; ?>
            <div id="log-list" style="margin-top: 10px;">
                <?php if(is_array($log_list)): $i = 0; $__LIST__ = $log_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="ibox-content gray-log" log-rel="<?php echo ($vo['log_id']); ?>" >
                        <div class="social-feed-separated clearfix">
                            <div class="social-feed-box">
        						<?php if($content != 'resource' && $vo['sign'] == '0' && $vo['role_id'] == session('role_id')): ?><div class="pull-right social-action dropdown">
                                        <span data-toggle="dropdown" class="dropdown-toggle">
                                            <i style="font-size:20px;cursor:pointer" class="fa fa-angle-down"></i>
                                        </span>
                                        <ul class="dropdown-menu m-t-xs" >
                                            <li><a rel="<?php echo ($vo['log_id']); ?>" href="javascript:void(0);" type="<?php echo ($vo['log_type']); ?>" onclick="del_confirm(this);"><?php echo L('DELETE');?></a></li>
                                        </ul>
                                    </div><?php endif; ?>
                                <?php if($vo['sign'] == 1): ?><div class="social-avatar">
                                        <?php if(empty($vo['owner']['thumb_path'])): ?><img alt="image" style="width:35px;height:35px;" class="img-circle" src="__PUBLIC__/img/avatar_default.png">
                                        <?php else: ?>                
                                            <img alt="image" style="width:35px;height:35px;" class="img-circle" src="<?php echo ($vo['owner']['thumb_path']); ?>"><?php endif; ?>
                                        <a class="role_info name-colors"  rel="<?php echo ($vo['owner']['role_id']); ?>" href="javascript:void(0);"><?php echo ($vo['owner']['full_name']); ?></a>&nbsp;&nbsp;
                                        <span class="text-muted">发布了一条签到记add_log录</span>&nbsp;&nbsp;&nbsp;
                                        <span class="text-muted" ><?php echo (date("Y-m-d H:i",$vo['create_date'])); ?></span>
                                        <div class="text-muted" style="padding:0 15px 0 50px;">
                                            <div class="conent0" style="width:100%;">
                                                <img style="width:15px;height:15px;vertical-align:text-bottom;" src="__PUBLIC__/img/location.png"/>
                                                <span style="color:#666"><?php echo ($vo['sign_info']['address']); ?></span>
                                                <input class="longitude" type="hidden" rel="<?php echo ($vo['sign_info']['y']); ?>"/>
                                                <!-- <a href="javascript:void(0);" style="font-size:12px;" class="map" >显示地图</a> -->
                                                <div id="allmap<?php echo ($vo['log_id']); ?>" rel="allmap<?php echo ($vo['log_id']); ?>" class="allmap"></div>
                                                <input class="latitude" type="hidden" rel="<?php echo ($vo['sign_info']['x']); ?>"/>
                                            </div>
                                            <div class="conent0">
                                                <span style="color:#000">签到说明：<?php echo ($vo['sign_info']['log']); ?></span>
                                            </div>
                                            <div class="conent0">
                                                <div style="color:#000">现场照片：</div>
                                                <?php if(is_array($vo['sign_img'])): $i = 0; $__LIST__ = $vo['sign_img'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="box-secondary" rel="<?php echo ($vo["img_id"]); ?>" style="width:100px;height:100px;float:left;margin-left:5px;">
                                                        <a href="<?php echo ($v["path"]); ?>" target="_self" class="litebox_file" data-litebox-group="group-<?php echo ($vo['log_id']); ?>">
                                                            <img src="<?php echo ($v["path"]); ?>" class="thumbnail thumb100" style="width:100%;height:100%;">
                                                        </a>
                                                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
                                                <div style="clear:both;"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif($vo['is_call'] == 1): ?>
                                    <div class="social-avatar">
                                        <?php if(empty($vo['owner']['thumb_path'])): ?><img alt="image" style="width:35px;height:35px;" class="img-circle" src="__PUBLIC__/img/avatar_default.png">
                                        <?php else: ?>                
                                            <img alt="image" style="width:35px;height:35px;" class="img-circle" src="<?php echo ($vo['owner']['thumb_path']); ?>"><?php endif; ?>
                                        <a class="role_info name-colors"  rel="<?php echo ($vo["owner"]["role_id"]); ?>" href="javascript:void(0);"><?php echo ($vo['owner']['full_name']); ?></a>&nbsp;&nbsp;
                                        <span class="text-muted">发布了一条通话记录</span>&nbsp;&nbsp;&nbsp;
                                        <span class="text-muted" ><?php echo (date("Y-m-d H:i",$vo["create_date"])); ?>&nbsp;&nbsp;</span>
                                    </div>
                                    <div class="social-body">
                                        <?php if(!empty($vo['file_path'])): ?><div style="width:300px;">
                                            <audio controls preload="none">
                                                <source src="<?php echo ($vo['file_path']); ?>" type="audio/wav">
                                                <source src="<?php echo ($vo['file_path']); ?>" type="audio/mp3">
                                            </audio>
                                        </div><?php endif; ?>
                                        <div class="log-relation">&nbsp;<span>通话状态 : <?php echo ($vo['call_type_name']); ?></span></div>
                                    </div>
                                <?php else: ?>
                                    <div class="social-avatar">
                                        <?php if(empty($vo['owner']['thumb_path'])): ?><img alt="image" style="width:35px;height:35px;" class="img-circle" src="__PUBLIC__/img/avatar_default.png">
                                        <?php else: ?>                
                                            <img alt="image" style="width:35px;height:35px;" class="img-circle" src="<?php echo ($vo['owner']['thumb_path']); ?>"><?php endif; ?>
                                        <a class="role_info name-colors"  rel="<?php echo ($vo["owner"]["role_id"]); ?>" href="javascript:void(0);"><?php echo ($vo['owner']['full_name']); ?></a>&nbsp;&nbsp;
                                        <span class="text-muted">发布了一条快速记录</span>&nbsp;&nbsp;&nbsp;
                                        <span class="text-muted" ><?php echo (date("Y-m-d H:i",$vo["create_date"])); ?>&nbsp;&nbsp;<a title="沟通类型" href="javascript:void(0);"><?php echo ($vo['status_name']); ?></a></span>
                                    </div>
                                    <div class="social-body">
                                        <span style="word-wrap:break-word;line-height: 21px;color: #000;"><?php echo ($vo['content']); ?></span>
                                        <?php if($vo['code']): ?><div class="log-relation"><i class="fa fa-bookmark"></i>&nbsp;<span>相关商机 : <?php echo ($vo['code']); ?></span></div><?php endif; ?>
                                        <div class="social-avatar" style="padding-top:10px;">
                                            <?php if($vo['nextstep_time']): ?><span class="text-muted pull-right">下次联系时间：<?php echo (date("Y-m-d H:i",$vo['nextstep_time'])); ?></span><?php endif; ?>
                                        </div>
                                    </div>
                                    <span class="text-muted pull-right" style="margin-right: 20px;">
                                        相关联系人： 
                                        <?php if($vo['contacts_id']): ?><a href="<?php echo U('contacts/view','id='.$vo['contacts_id']);?>" target="_blank"><?php echo ($vo['contacts_name']); ?>（<?php echo ($vo['contacts_phone']); ?>）</a>
                                        <?php else: ?>
                                            （无）<?php endif; ?>
                                    </span>
                                    <?php if($vo['finish_visitor_plan']): ?><div class="text-muted pull-right" style="margin-right: 20px;">
                                            <span class="pull-left">相关访客计划：</span>
                                            <p title="<?php echo ($vo['finish_visitor_plan']); ?>" style="overflow: hidden; text-overflow:ellipsis; white-space: nowrap; width: 120px; float:left;"><?php echo ($vo['finish_visitor_plan']); ?></p>
                                        </div><?php endif; endif; ?>
                            </div>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
    </div>
	<div id="tab9" class="tab-pane fade in">
        <div class="panel-body">
            <?php if($content != 'resource'): ?><div class="pull-right">
                    <a href="javascript:void(0);" id="add_event" type="button" class="btn btn-primary">
                        <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;添加</a>
                </div><?php endif; ?>
            <div style="clear:both;"></div>
        </div>
        <div class="panel-body">
            <div class="ibox">
                <?php if(empty($event_list)): ?><div style="text-align:center;color:#E4E4E4;font-size:22px;font-weight:700;padding:15px 0px;">
	<img src="__PUBLIC__/img/exclamation_mark.png" style="margin-top:-3px;">&nbsp;&nbsp;暂无数据
</div>
				<?php else: ?>
					<?php if(is_array($event_list)): $i = 0; $__LIST__ = $event_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div style="padding-bottom:20px;border-left:1px solid #ccc;margin: 5px 0 0 15px;">
							<?php if($vo['color']): ?><i class="fa fa-circle pull-left" style="margin-left:-5px;color:<?php echo ($vo['color']); ?>;font-size:12px;"></i>
							<?php else: ?>
								<i class="fa fa-circle pull-left" style="margin-left:-5px;color:#FB8F7A;font-size:12px;"></i><?php endif; ?>
							<div class="pull-left" style="margin-left:25px;color:#999">
								<div><?php echo (date("H:i",$vo['start_date'])); ?></div>
								<div><?php echo (date("Y/m/d",$vo['start_date'])); ?></div>
							</div>
							<div class="pull-left" style="margin-left:25px;color:#999">
								<div style="margin-top:11px;">~</div>
							</div>
							<div class="pull-left" style="margin-left:25px;color:#999">
								<div><?php echo (date("H:i",$vo['end_date'])); ?></div>
								<div><?php echo (date("Y/m/d",$vo['end_date'])); ?></div>
							</div> 
							<div class="pull-left " style="margin-left:25px;"><img src="<?php echo ($vo['img']); ?>" style="width:30px;height:30px;border-radius:50px;margin-top:5px;"></div>
							<div class="pull-left owner_info" style="margin-left:10px;width:60%">
								<div><?php echo ($vo['create_role_name']); ?> &nbsp;<?php if($vo['bus_num']): ?>(<?php echo ($vo['bus_num']); ?>)<?php endif; ?></div>
								<div>
                                    <a class="event_view" rel="<?php echo ($vo['event_id']); ?>" href="javascript:void(0);">
                                        <?php echo ($vo['subject']); ?>
                                    </a>
                                </div> 
							</div>
							<div style="clear:both"></div>
						</div><?php endforeach; endif; else: echo "" ;endif; ?>
                    <div id="event_loading" style="display: none; padding-bottom:20px;border-left:0px solid #ccc;margin: 5px 0 0 15px;">
                        <div>
                            <div class="sk-spinner sk-spinner-three-bounce">
                                <div class="sk-bounce1"></div>
                                <div class="sk-bounce2"></div>
                                <div class="sk-bounce3"></div>
                            </div>
                        </div>
                    </div>
                    <div id="event_model" style="display: none; padding-bottom:20px;border-left:1px solid #ccc;margin: 5px 0 0 15px;">
                        <i class="fa fa-circle pull-left" style="margin-left:-5px;font-size:12px;"></i>
                        <div class="start_date pull-left" style="margin-left:25px;color:#999">
                            <div></div>
                            <div></div>
                        </div>
                        <div class="pull-left" style="margin-left:25px;color:#999">
                            <div style="margin-top:11px;">~</div>
                        </div>
                        <div class="end_date pull-left" style="margin-left:25px;color:#999">
                            <div></div>
                            <div></div>
                        </div>
                        <div class="pull-left" style="margin-left:25px;">
                            <img src="" style="width:30px;height:30px;border-radius:50px;margin-top:5px;">
                        </div>
                        <div class="pull-left" style="margin-left:10px;width:60%">
                            <div><?php echo ($vo['create_role_name']); ?> &nbsp;<?php if($vo['bus_num']): ?>(<?php echo ($vo['bus_num']); ?>)<?php endif; ?>
                            </div>
                            <div>
                                <a class="event_view" rel="<?php echo ($vo['event_id']); ?>" href="javascript:void(0);">
                                    <?php echo ($vo['subject']); ?>
                                </a>
                            </div>
                        </div>
                        <div style="clear:both"></div>
                    </div><?php endif; ?>
            </div> 
        </div>
        <script>
            // 访客计划动态加载
            let event_page = 1;
            let event_load = true;
            $(window).scroll(function () {
                let w_height = $(window).height();
                let customer_id = '<?php echo ($customer_id); ?>';
                let business_id = '<?php echo ($business_id); ?>';
                let offset_top = $(window).scrollTop();
                let d_height = $(document).height();
                if (w_height + offset_top >= d_height) {
                    if ($('[href="#tab9"]').attr('aria-expanded') == 'true' && event_load) {
                        event_page++;
                        $('#event_loading').show();
                        $.ajax({
                            url: '<?php echo U("event/view_ajax");?>',
                            data: { customer_id: customer_id, business_id: business_id, p: event_page },
                            type: 'POST',
                            dataType: 'JSON',
                            success: function (res) {
                                if (res.status) {
                                    $.each(res.data, function (key, val) {
                                        let model = $('#event_model').clone(1);
                                        model.removeAttr('id');
                                        model.show();
                                        model.find('i').css('color', val.color);
                                        if (Number(val.start_date)) {
                                            let timestamp1 = Number(val.start_date) * 1000;
                                            model.find('.start_date div').eq(0).html(y_date('H:i', timestamp1));
                                            model.find('.start_date div').eq(1).html(y_date('Y/m/d', timestamp1));
                                        } else {
                                            model.find('.start_date div').eq(0).html('###');
                                            model.find('.start_date div').eq(1).html('###');
                                        }
                                        if (Number(val.end_date)) {
                                            let timestamp2 = Number(val.end_date) * 1000;
                                            model.find('.end_date div').eq(0).html(y_date('H:i', timestamp2));
                                            model.find('.end_date div').eq(1).html(y_date('Y/m/d', timestamp2));
                                        } else {
                                            model.find('.end_date div').eq(0).html('###');
                                            model.find('.end_date div').eq(1).html('###');
                                        }
                                        model.find('img').attr('src', val.img);
                                        model.find('.owner_info div').eq(0).html(val['create_role_name']);
                                        model.find('.owner_info div').eq(1).find('a').attr('rel', val['event_id']);
                                        model.find('.owner_info div').eq(1).find('a').html('rel', val['subject']);
                                        $('#event_loading').before(model);
                                    });
                                } else {
                                    event_load = false;
                                }
                                $('#event_loading').hide();
                            }
                        });
                    }
                }
            });
        </script>
    </div>
    <div id="tab2" class="tab-pane fade in">
        <div class="panel-body">
            <div class="ibox">
                <?php if(empty($business)): ?><div style="text-align:center;color:#E4E4E4;font-size:22px;font-weight:700;padding:15px 0px;">
	<img src="__PUBLIC__/img/exclamation_mark.png" style="margin-top:-3px;">&nbsp;&nbsp;暂无数据
</div>
                <?php else: ?>
                <?php if(is_array($business)): $i = 0; $__LIST__ = $business;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="pull-right" style="padding-bottom: 15px;clear: both;"> 
                        <?php if($business_id): ?><button style="margin-top:10px; " class="btn btn-primary btn-sm editproduct_view" rel=<?php echo ($vo['business_id']); ?>>编辑产品</button><?php endif; ?> 
                    </div>
                    <div class="row">
                        <table class="table select-table table-bordered">
                            <thead>
                                <tr style="background-color:#f9fafc;text-align:center;">
                                    <td style="background-color:#f9fafc;padding:14px;color:#999">产品名称</td>
                                    <td style="background-color:#f9fafc;padding:14px;color:#999">规格</td>
                                    <td style="background-color:#f9fafc;padding:14px;color:#999">价格(元)</td>
                                    <td style="background-color:#f9fafc;padding:14px;color:#999">折扣(%)</td>
                                    <td style="background-color:#f9fafc;padding:14px;color:#999">销售单价(元)</td>
                                    <td style="background-color:#f9fafc;padding:14px;color:#999">数量</td>
                                    <td style="background-color:#f9fafc;padding:14px;color:#999">单位</td>
                                    <td style="background-color:#f9fafc;padding:14px;color:#999">小计</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(is_array($vo['product'])): $i = 0; $__LIST__ = $vo['product'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                                    <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo ($v['name']); ?></td>
                                    <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo ($v['spec_list']); ?></td>
                                    <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo (number_format($v['ori_price'],2)); ?></td>
                                    <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo ($v['discount_rate']); ?></td>
                                    <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo (number_format($v['unit_price'],2)); ?></td>
                                    <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo ($v['amount']); ?></td>
                                    <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo ($v['unit']); ?></td>
                                    <td style="text-align:center;padding:14px;color:#666"><?php echo ($v['subtotal']); ?></td>
                                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table>
                        <div style="text-align:center;margin-top:15px;float:right;margin-bottom:15px;clear: both;">
                            <div class="pull-right">销售订单金额(元):<span style="color:#ffd290;">&nbsp;<?php echo (number_format($vo['final_price'],2)); ?>&nbsp;</span></div>&nbsp;&nbsp;
                            <div class="pull-right">整单折扣(%):<span style="color:#999;">&nbsp;<?php echo ($vo['final_discount_rate']); ?>&nbsp;</span></div>&nbsp;&nbsp;
                            <div class="pull-right">产品合计(元):<span style="color:#999;">&nbsp;<?php echo ($vo['total_subtotal_val']); ?>&nbsp;</span></div>&nbsp;&nbsp;
                            <div class="pull-right"><i class="fa fa-bookmark" style="color: #19aa8d;"></i>&nbsp;相关商机:<span style="color:#999;">&nbsp;<?php echo ($vo['code']); ?>&nbsp;</span></div>&nbsp;&nbsp;
                        </div>
                    </div>
                    <div class="row" style="height: 10px;border-top: 1px dashed #d3d3d6;margin-top: 10px;"></div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
            </div> 
        </div>
    </div>
    <div id="tab3" class="tab-pane fade in">
        <div class="panel-body">
            <div class="ibox">
    			<?php if($content != 'resource'): ?><div class="pull-right" style="padding-bottom: 15px"> 
                        <a class="btn btn-primary btn-sm" rel="<?php echo ($customer_id); ?>" rel1="<?php echo ($business_id); ?>" id="rel_contacts" style="<?php echo ($business_id? '':'display:none'); ?>" href="javascript:void(0);">关联联系人</a>
                        <?php if($business_id): ?><a class="btn btn-primary btn-sm" href="<?php echo U('contacts/add',array('redirect'=>'business','redirect_id'=>$business_id));?>">添加联系人</a>
                        <?php else: ?>
                            <a class="btn btn-primary btn-sm" href="<?php echo U('contacts/add',array('redirect'=>'customer','redirect_id'=>$customer_id));?>">添加联系人</a><?php endif; ?>
                    </div><?php endif; ?>
                <?php if(empty($contacts_list)): ?><div style="text-align:center;color:#E4E4E4;font-size:22px;font-weight:700;padding:15px 0px;">
	<img src="__PUBLIC__/img/exclamation_mark.png" style="margin-top:-3px;">&nbsp;&nbsp;暂无数据
</div>
                <?php else: ?>
                    <table class="table select-table table-bordered">
                        <tr class="tabTh" style="background-color:#f9fafc;">
                            <td style="border-right: 1px solid #e7eaec;text-align:center;background-color:#f9fafc;padding:14px;color:#999">姓名</td>
                            <td style="border-right: 1px solid #e7eaec;text-align:center;background-color:#f9fafc;padding:14px;color:#999">手机号</td>
                            <td style="border-right: 1px solid #e7eaec;text-align:center;background-color:#f9fafc;padding:14px;color:#999">邮箱</td>
                            <td style="border-right: 1px solid #e7eaec;text-align:center;background-color:#f9fafc;padding:14px;color:#999">QQ</td>
                            <td style="border-right: 1px solid #e7eaec;text-align:center;background-color:#f9fafc;padding:14px;color:#999">职位</td>
                            <td style="border-right: 1px solid #e7eaec;text-align:center;background-color:#f9fafc;padding:14px;color:#999">角色</td>
                            <td style="border-right: 1px solid #e7eaec;text-align:center;background-color:#f9fafc;padding:14px;color:#999">尊称</td>
                            <td></td>
                        </tr>
                        <tbody>
                            <?php if(is_array($contacts_list)): $i = 0; $__LIST__ = $contacts_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><a href="<?php echo U('contacts/view','id='.$vo['contacts_id']);?>"><?php echo ($vo['name']); ?><span class="text-danger" style="<?php echo ($mail_contacts_id == $vo['contacts_id'] ? '':'display:none'); ?>">(首要)</span></a></td>
                                <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo ($vo['telephone']); ?></td>
                                <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo ($vo['email']); ?></td>
                                <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo ($vo['qq_no']); ?></td>
                                <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo ($vo['post']); ?></td>
                                <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo ($vo['role']); ?></td>
                                <td style="text-align:center;padding:14px;color:#666;border-right: 1px solid #e7eaec;"><?php echo ($vo['saltname']); ?></td>
                                
                                <td class="dropdown">
                                <?php if($content != 'resource'): ?><span class="dropdown-toggle" data-toggle="dropdown" style="cursor:pointer;color:#8FA1B2">
                                        <i class="fa fa-angle-down fa-lg"></i>
                                    </span>
                                    <ul class="dropdown-menu">
                                        <?php if($business_id): ?><li><a rel="<?php echo U('contacts/relToBusiness',array('business_id'=>$business_id,'contacts_id'=>$vo['contacts_id'],'act_n'=>2));?>" href="javascript:void(0);" class="not_rel_contacts" >解除关联</a></li><?php endif; ?>
                                        <li><a href="<?php echo U('contacts/edit',array('id'=>$vo['contacts_id']));?>">编辑</a></li>
                                        <?php if($business_id == ''): if($vo['contacts_id'] != $mail_contacts_id): ?><li><a title="" href="<?php echo U('contacts/changeToFirstContact', 'id='.$vo['contacts_id'].'&customer_id='.$customer_id);?>">设为首要</a></li>
                                                <li><a title="删除联系人" href="javascript:void(0)" class="del_contacts" rel="<?php echo ($vo['contacts_id']); ?>" rel1="delete">删除</a></li>
                                            <?php else: ?>
                                                <li><a title="删除联系人" href="javascript:void(0)" class="del_contacts" rel="<?php echo ($vo['contacts_id']); ?>" rel1="mdelete" rel2="<?php echo ($customer_id); ?>">删除</a></li><?php endif; endif; ?>
                                    </ul><?php endif; ?>
                                </td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table><?php endif; ?>
            </div> 
        </div>
    </div>
	<div id="tab5" class="tab-pane fade in">
        <div class="panel-body">
            <div class="ibox">
			<?php if($contract_list): ?><table class="table select-table table-bordered">
                    <thead>
                        <td style="background:#F9FaFC;text-align:center;color:#999;">合同编号</td>
                        <!-- <td style="background:#F9FaFC;text-align:center;color:#999;">客户名称</td> -->
                        <td style="background:#F9FaFC;text-align:center;color:#999;">到期时间</td>
                        <td style="background:#F9FaFC;text-align:center;color:#999;">合同金额</td>
                        <td style="background:#F9FaFC;text-align:center;color:#999;">开票金额</td>
                        <td style="background:#F9FaFC;text-align:center;color:#999;">签约人</td>
                        <td style="background:#F9FaFC;text-align:center;color:#999;">状态</td>
                        <td style="background:#F9FaFC;text-align:center;color:#999;">商机编号</td>
                    </thead>
                    <tbody class="body_border">
						<?php if(is_array($contract_list)): $i = 0; $__LIST__ = $contract_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
								<td style="text-align:center;"><a href="<?php echo U('contract/view','id='.$v['contract_id']);?>"><?php echo ($v['number']); ?></a></td>
								<!-- <td style="text-align:center;"><?php echo ($v['customer_name']); ?></td> -->
								<td style="text-align:center;"><?php if(!empty($v["end_date"])): echo (date("Y-m-d",$v["end_date"])); endif; ?></td>
                                <td style="text-align:center;"><?php echo (number_format($v["price"],2)); echo L('YUAN');?></td>
								<td style="text-align:center;"><?php echo (number_format($v["invoice_price"],2)); echo L('YUAN');?></td>
								<td style="text-align:center;"><?php if(!empty($v["owner"]["user_name"])): ?><a class="role_info" rel="<?php echo ($v["owner"]["role_id"]); ?>" href="javascript:void(0)"><?php echo ($v["owner"]["user_name"]); ?></a><?php endif; ?></td>
								<td style="text-align:center;"><?php if($v['is_checked'] == '2'): ?><span class="fa fa-circle" style="color:#F5715F"></span>&nbsp;&nbsp;&nbsp;拒绝
									<?php elseif($v['is_checked'] == '1'): ?>
										<span class="fa fa-circle" style="color:#7CCA4E"></span>&nbsp;&nbsp;&nbsp;通过
									<?php else: ?>
										<span class="fa fa-circle" style="color:#F5CA00"></span>&nbsp;&nbsp;&nbsp;待审<?php endif; ?> 
								</td>
								<td style="text-align:center;"><?php echo ($v["business"]["code"]); ?></td> 
							</tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
			<?php else: ?>
				<?php if($contract_info['contract_id']){ ?>
					<div style="font-size:13px;font-weight:700;margin-top:15px;"><span style="border-left:5px solid #19AA8D;padding-left:10px;height:10px;"></span>基本信息</div>
					<div class="form-horizontal view-group " style="background:#F2F4F7;margin-top:15px;padding:10px 10px 10px">
						<div class="form-group">
							<label class="col-lg-2 control-label"><?php echo L('CONTRACT_NO');?></label>
							<div class="col-lg-4">
								<p class="form-p">
                                    <a href="<?php echo U('contract/view','id='.$contract_info['contract_id']);?>">
									<?php echo ($contract_info["number"]); ?>
                                    </a>
								</p>
							</div>
							<label class="col-lg-2 control-label"><?php echo L('SIGNING_TIME');?></label>
							<div class="col-lg-4">
								<div class="pull-left" style="margin-top:6px;color:#000;"><?php echo (date("Y-m-d",$contract_info["due_time"])); ?></div>	
								<div class="pull-right social-action dropdown" style="margin-top:6px;">
									<span data-toggle="dropdown" class="dropdown-toggle">
										<i style="font-size:20px;cursor:pointer" class="fa fa-angle-down"></i>
									</span>
									<ul class="dropdown-menu m-t-xs" >
										<li><a href="<?php echo U('contract/view','id='.$contract_info['contract_id']);?>">详情</a></li>
									</ul>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">签约<?php echo L('CUSTOMER');?></label>
							<div class="col-lg-4">
								<p class="form-p">
                                    <a href="<?php echo U('customer/view', array('id' => $contract_info['customer_id']));?>">
                                    <?php echo ($contract_info["customer_name"]); ?> 
                                    </a>
								</p>
							</div>
							<label class="col-lg-2 control-label">合同名称</label>
							<div class="col-lg-4">
								<p class="form-p">
									<?php echo ($contract_info["contract_name"]); ?>  
								</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 control-label">合同签约人</label>
							<div class="col-lg-4">
								<p class="form-p">
                                    <a class="role_info" rel="<?php echo ($contract_info['owner_role_id']); ?>" href="javascript:void(0)">
                                    <?php echo ($contract_info['owner_name']); ?>
                                    </a>
								</p>
							</div>
							<label class="col-lg-2 control-label">合同金额</label>
							<div class="col-lg-4">
								<p class="form-p">
									<?php echo (number_format($contract_info["price"],2)); ?>
								</p>
							</div>
						</div>
						<div style="display:none;" id="contract_content">
							<div class="form-group">
								<label class="col-lg-2 control-label">合同生效时间</label>
								<div class="col-lg-4">
									<p class="form-p">
										<?php echo (date("Y-m-d",$contract_info['start_date'])); ?>
									</p>
								</div>
								<label class="col-lg-2 control-label">合同到期时间</label>
								<div class="col-lg-4">
									<p class="form-p">
										<?php echo (date("Y-m-d",$contract_info['end_date'])); ?>
									</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-2 control-label">合同创建人</label>
								<div class="col-lg-4">
									<p class="form-p">
										<?php echo ($contract_info['creator_name']); ?>
									</p>
								</div>
								<label class="col-lg-2 control-label">合同创建时间</label>
								<div class="col-lg-4">
									<p class="form-p">
										<?php echo (date("Y-m-d",$contract_info["create_time"])); ?>
									</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-2 control-label">合同备注</label>
								<div class="col-lg-10">
									<p class="form-p">
										<?php echo ($contract_info["description"]); ?>
									</p>
								</div>
							</div>
						</div>
					</div>
					<div style="font-size:13px;font-weight:700;margin-top:20px;">
						<span style="border-left:5px solid #19AA8D;padding-left:10px;height:10px;"></span>审核信息
					</div>
					<div>
						<div class="pull-left">
                            <?php if($contract_info['creator_info']['img']){ ?>
                            <img src="<?php echo ($contract_info['creator_info']['img']); ?>" style="width:45px;height:45px;margin:10px 0 0 10px;border-radius:50px;">
                            <?php }else{ ?>
                                <img src="__PUBLIC__/img/moren.png" style="width:45px;height:45px;margin:10px 0 0 10px;border-radius:50px;" />
                            <?php } ?>
                        </div>
						<div class="pull-left" style="margin:10px 0 0 10px;">
							<div class="control-label" style="margin-top:2px;font-size:16px;color:#B4B1C2"><a class="role_info" rel="<?php echo ($contract_info['creator_info']['role_id']); ?>" href="javascript:void(0)"><?php echo ($contract_info['creator_info']['full_name']); ?></a></div>
							<div class="control-label" style="margin-top:2px;font-size:15px;"><?php echo (date("Y-m-d H:i",$contract_info["create_time"])); ?></div>
						</div>
                        <?php if($contract_examine){ ?>
                            <?php if(is_array($check_list)): $i = 0; $__LIST__ = $check_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="pull-left" style="margin-left:10px;margin-top:25px;">
                                    <span class="fa fa-circle" style="color:#B4B1C2;font-size:12px;"></span>
                                    <span class="fa fa-circle" style="color:#B4B1C2;font-size:12px;"></span>
                                    <span class="fa fa-circle" style="color:#B4B1C2;font-size:12px;"></span>
                                </div>
                                <div class="pull-left" style="margin-left:10px;">
                                    <?php if($vo['user_info']['thumb_path']){ ?>
                                        <img src="<?php echo ($vo['user_info']['thumb_path']); ?>" style="width:45px;height:45px;margin:10px 0 0 10px;border-radius:50px;" />
                                    <?php }else{ ?>
                                        <img src="__PUBLIC__/img/moren.png" style="width:45px;height:45px;margin:10px 0 0 10px;border-radius:50px;" />
                                    <?php } ?>
                                </div>
                                <div class="pull-left" style="margin:10px 0 0 10px;">
                                    <div class="control-label" style="margin-top:2px;font-size:14px;color:#B4B1C2">
                                        <a href="javascript:void(0)" rel="<?php echo ($vo['role_id']); ?>" class="role_info" style="color: #B4B1C2;"><?php echo ($vo['user_info']['full_name']); ?></a>
                                    </div>
                                    <div class="control-label check_badge" style="margin-top:2px;font-size:13px;">
                                        <?php if($contract_info['order_id'] && ($contract_info['order_id'] >= $vo['order_id'])){ ?>
                                            <span style="color:#19AA8D">通过</span>
                                        <?php }else { ?>
                                            <?php if($contract_info['is_checked'] == 1 || $contract_info['is_checked'] == 2){ ?>
                                                <span style="color:#F5715F">结束</span>
                                            <?php }else { ?>
                                                <span style="color:#F5CA00">待审</span>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div><?php endforeach; endif; else: echo "" ;endif; ?>
                        <?php }else { ?>
                            <div class="pull-left" style="margin-left:10px;margin-top:25px;">
                                <span class="fa fa-circle" style="color:#B4B1C2;font-size:12px;"></span>
                                <span class="fa fa-circle" style="color:#B4B1C2;font-size:12px;"></span>
                                <span class="fa fa-circle" style="color:#B4B1C2;font-size:12px;"></span>
                            </div>
                            <div class="pull-left" style="margin-left:10px;">
                                <?php if($contract_info['is_checked'] == 0){ ?>
                                    <img src="__PUBLIC__/img/moren.png" style="width:45px;height:45px;margin:10px 0 0 10px;border-radius:50px;" />
                                <?php }else { ?>
                                    <img src="<?php echo ($contract_info['examine_role_info']['thumb_path']); ?>" style="width:45px;height:45px;margin:10px 0 0 10px;border-radius:50px;" />
                                <?php } ?>
                            </div>
                            <div class="pull-left" style="margin:10px 0 0 10px;">
                                <div class="control-label" style="margin-top:2px;font-size:14px;color:#B4B1C2">
                                    <?php if($contract_info['is_checked'] != 0){ ?>
                                        <a href="javascript:void(0)" rel="<?php echo ($contract_info['examine_role_id']); ?>" class="role_info" style="color: #B4B1C2;"><?php echo ($contract_info['examine_role_info']['full_name']); ?></a>
                                    <?php }else { ?>
                                        合同审核员
                                    <?php } ?>
                                </div>
                                <div class="control-label check_badge" style="margin-top:2px;font-size:13px;">
                                    <?php if($contract_info['is_checked'] == 1){ ?>
                                        <span style="color:#19AA8D">通过</span>
                                    <?php }elseif($contract_info['is_checked'] == 2){ ?>
                                        <span style="color:#F5715F">拒绝</span>
                                    <?php }elseif($contract_info['is_checked'] == 3){ ?>
                                        <span style="color:#FF6600">审批中</span>
                                    <?php }else { ?>
                                        <span style="color:#F5CA00">待审</span>
                                    <?php } ?>
                                    <?php if(checkPerByAction('contract','check') && $contract_info['is_checked'] > 0){ ?>
                                        &nbsp;<a class="backbtn control" style="display:none;" href="<?php echo U('contract/revokecheck', 'id='.$contract_info['contract_id']);?>"><i class="icon-repeat"></i> 撤销</a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
						<div style="clear:both"></div>
					</div>
				<?php }else{ ?>
                    <?php if($is_business_code == 1 && empty($is_find)){ ?>
						<div class="pull-right" style="padding-bottom: 15px"> 
							<a class="btn btn-primary btn-sm" href="<?php echo U('contract/add','business_id='.$c_business_id);?>">添加合同</a>
						</div>
						<div style="text-align:center;color:#E4E4E4;font-size:22px;font-weight:700;padding:15px 0px;">
	<img src="__PUBLIC__/img/exclamation_mark.png" style="margin-top:-3px;">&nbsp;&nbsp;暂无数据
</div>
                    <?php } ?>
				<?php } endif; ?>
			</div>
		</div>
	</div>
	<div id="tab6" class="tab-pane fade in">
		<div class="panel-body">
			<div class="ibox">
                <div class="pull-right" style="padding-bottom: 15px">
                    <?php if($business_id == ''): if($invoice_info == ''): ?><a class="btn btn-primary btn-sm add_invoice" href="javascript:void(0);">添加发票</a>
                        <?php else: ?>
                            <a class="btn btn-primary btn-sm view_invoice" rel="<?php echo ($invoice_info['id']); ?>" href="javascript:void(0);">查看发票</a><?php endif; endif; ?>
                </div>
    			<?php if($receivingorder_list || $paymentorder_list): if($receivingorder_list): ?><table class="table select-table">
        					<thead>
        						<tr>
        							<td style="text-align:center;color:#999;">收款编号</td>
        							<td style="text-align:center;color:#999;">收款时间</td>
        							<td style="text-align:center;color:#999;">收款金额</td>
        							<td style="text-align:center;color:#999;">收款账户</td>
        							<td style="text-align:center;color:#999;">收款人</td>
        							<td style="text-align:center;color:#999;">状态</td>
        						</tr>                           
        					</thead>
        					<tbody class="body_border">
        						<?php if(is_array($receivingorder_list)): $i = 0; $__LIST__ = $receivingorder_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
        								<td style="text-align:center;"><a href="<?php echo U('finance/view','t=receivingorder&id='.$vo['receivingorder_id']);?>"><?php echo ($vo["name"]); ?></a></td>
        								<td style="text-align:center;"><?php echo (date("Y-m-d",$vo['pay_time'])); ?></td>
        								<td style="text-align:center;"><?php echo ($vo['money']); ?>元</td>
        								<td style="text-align:center;"><?php echo ($vo['receipt_account']); ?></td>
        								<td style="text-align:center;"><?php if(!empty($vo["owner"]["user_name"])): ?><a class="role_info" rel="<?php echo ($vo["owner"]["role_id"]); ?>" href="javascript:void(0)"><?php echo ($vo["owner"]["user_name"]); ?></a><?php endif; ?></td> 
        								<td style="text-align:center;"><?php if($vo['status'] == '2'): ?><span class="fa fa-circle" style="color:#F5715F"></span>&nbsp;&nbsp;&nbsp;拒绝
        									<?php elseif($vo['status'] == '1'): ?>
        										<span class="fa fa-circle" style="color:#7CCA4E"></span>&nbsp;&nbsp;&nbsp;通过
        									<?php else: ?>
        										<span class="fa fa-circle" style="color:#F5CA00"></span>&nbsp;&nbsp;&nbsp;待审<?php endif; ?> 
        								</td>
        							</tr><?php endforeach; endif; else: echo "" ;endif; ?>
        					</tbody>
        				</table><?php endif; ?>

                    <?php if($paymentorder_list): ?><table class="table select-table">
                            <thead>
                                <tr>
                                    <td style="text-align:center;color:#999;">付款编号</td>
                                    <td style="text-align:center;color:#999;">付款时间</td>
                                    <td style="text-align:center;color:#999;">付款金额</td>
                                    <td style="text-align:center;color:#999;">付款账户</td>
                                    <td style="text-align:center;color:#999;">付款人</td>
                                    <td style="text-align:center;color:#999;">状态</td>
                                </tr>                           
                            </thead>
                            <tbody class="body_border">
                                <?php if(is_array($paymentorder_list)): $i = 0; $__LIST__ = $paymentorder_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                        <td style="text-align:center;"><a href="<?php echo U('finance/view','t=payables&id='.$vo['payables_id']);?>"><?php echo ($vo["name"]); ?></a></td>
                                        <td style="text-align:center;"><?php echo (date("Y-m-d",$vo['pay_time'])); ?></td>
                                        <td style="text-align:center;"><?php echo ($vo['money']); ?>元</td>
                                        <td style="text-align:center;"><?php echo ($vo['receipt_account']); ?></td>
                                        <td style="text-align:center;"><?php if(!empty($vo["owner"]["user_name"])): ?><a class="role_info" rel="<?php echo ($vo["owner"]["role_id"]); ?>" href="javascript:void(0)"><?php echo ($vo["owner"]["user_name"]); ?></a><?php endif; ?></td> 
                                        <td style="text-align:center;"><?php if($vo['status'] == '2'): ?><span class="fa fa-circle" style="color:#F5715F"></span>&nbsp;&nbsp;&nbsp;拒绝
                                            <?php elseif($vo['status'] == '1'): ?>
                                                <span class="fa fa-circle" style="color:#7CCA4E"></span>&nbsp;&nbsp;&nbsp;通过
                                            <?php else: ?>
                                                <span class="fa fa-circle" style="color:#F5CA00"></span>&nbsp;&nbsp;&nbsp;待审<?php endif; ?> 
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                            </tbody>
                        </table><?php endif; ?>
    			<?php else: ?>
    				<div style="text-align:center;color:#E4E4E4;font-size:22px;font-weight:700;padding:15px 0px;">
	<img src="__PUBLIC__/img/exclamation_mark.png" style="margin-top:-3px;">&nbsp;&nbsp;暂无数据
</div><?php endif; ?>
			</div>
		</div>
	</div>
	<div class="tab-pane fade in" id="tab7">
	    <div class="panel-body">
    		<?php if($content != 'resource'): ?><div class="pull-right">
    				<a href="javascript:void(0);" type="button" class="add_file btn btn-primary" rel="<?php if (empty($business_id)){echo 'customer';}else{echo 'business';} ?>"><i class="fa fa-upload"></i>&nbsp;&nbsp;上传</a>
    			</div><?php endif; ?>
			<div style="clear:both;"></div>
		</div>
		<?php if($file_info == null): ?><div style="text-align:center;color:#E4E4E4;font-size:22px;font-weight:700;padding:15px 0px;">
	<img src="__PUBLIC__/img/exclamation_mark.png" style="margin-top:-3px;">&nbsp;&nbsp;暂无数据
</div>
		<?php else: ?>
			<table class="table product-table">
				<tr>
					<td>附件名称</td>
					<td><?php echo L('SIZE');?></td>
					<td>上传人</td>
					<td>上传时间</td>
					<?php if($content != 'resource'): ?><td>操作</td><?php endif; ?>
				</tr>
				<?php if(is_array($file_info)): $i = 0; $__LIST__ = $file_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						<td>
							<img src="__PUBLIC__/productImg/<?php echo ($vo['pic']); ?>" alt="">&nbsp;&nbsp;&nbsp;
							<a <?php if(in_array(getExtension($vo['name']),imgFormat())): ?>class="litebox_file" href="<?php echo ($vo['file_path']); ?>" title="点击查看大图" data-litebox-group="group-1"<?php else: ?>href="javascript:;" file="<?php echo ($vo["file_path"]); ?>" filename="<?php echo ($vo["name"]); ?>" onclick="filedown(this);"<?php endif; ?>><?php echo ($vo["name"]); ?></a>
						</td>
						<td>
							<?php echo ($vo["size"]); ?>KB
						</td>
						<td>
							<?php if(!empty($vo["owner"]["user_name"])): ?><a class="role_info" rel="<?php echo ($vo['owner']['role_id']); ?>" href="javascript:void(0)"><?php echo ($vo["owner"]["user_name"]); ?></a><?php endif; ?>
						</td>
						<td>
							<?php if(!empty($vo["create_date"])): echo (date("Y-m-d H:i",$vo["create_date"])); endif; ?>
						</td>
						<?php if($content != 'resource'): ?><td class="tdleft">
							<a href="javascript:void(0);" class="file_delete" rel="<?php echo ($vo['file_id']); ?>"><?php echo L('DELETE');?></a>
							<a href="javascript:void(0);" file="<?php echo ($vo["file_path"]); ?>" filename="<?php echo ($vo["name"]); ?>" onclick="filedown(this);">下载</a>
						</td><?php endif; ?>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</table><?php endif; ?>
	</div>
	<div class="tab-pane fade in" id="tab8">
	     <div id="tab-3" class="tab-pane">
			<div id="vertical-timeline" class="vertical-container light-timeline" style="width:100%;">
				<?php if(is_array($group_list)): $i = 0; $__LIST__ = $group_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i;?><div class="col-sm-12">
						<div class="pull-left">
							<span style="line-height:32px;margin-right: 10px;">
								<small><?php echo ($vo1['create_date']); ?>&nbsp;<?php echo ($vo1['week_name']); ?></small>
							</span>
						</div>
						<div class="pull-left">
							<?php if(is_array($vo1['action_list'])): $i = 0; $__LIST__ = $vo1['action_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="vertical-timeline-block" style="margin:0px;border-left:1px solid #ccc;margin-left:20px;">
									<i class="fa fa-circle pull-left" style="margin-left:-6px;color:#CAD7EF;margin-top:9px;font-size:12px;"></i>
									<div class="vertical-timeline-content" style="padding-top: 0px;margin-left:20px;">
										<div class="pull-left">
											<span class="pull-left" style="line-height:32px;margin-right: 10px;">
												<small><?php echo (date('H:i',$vo['create_time'])); ?></small>
											</span>
											<a class="role_info pull-left" rel="<?php echo ($vo['create_role_info']['role_id']); ?>" href="javascript:void(0)" style="margin-right:5px;">
												<img alt="image" class="img-circle" style="width:32px;height:32px;" <?php if($vo['create_role_info']['thumb_path']): ?>src="<?php echo ($vo['create_role_info']['thumb_path']); ?>"<?php else: ?>src="__PUBLIC__/img/avatar_default.png"<?php endif; ?>>
											</a>
											<span class="pull-left" style="line-height:32px;">
												<div class="pull-left"><?php echo ($vo['create_role_info']['full_name']); ?>&nbsp;&nbsp;</div>
												<div class="pull-left"><?php echo ($vo['duixiang']); ?></div>
												<div style="clear:both;"></div>
											</span>
											<?php if(is_array($vo['role_list'])): $i = 0; $__LIST__ = $vo['role_list'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i;?><a class="role_info" rel="<?php echo ($vo1['role_id']); ?>" href="javascript:void(0)" style="margin-right:5px;">
													<img alt="image" class="img-circle" style="width:32px;height:32px;" <?php if($vo1['thumb_path']): ?>src="<?php echo ($vo1['thumb_path']); ?>"<?php else: ?>src="__PUBLIC__/img/avatar_default.png"<?php endif; ?>>
												</a>
												<span style="line-height:32px;"><?php echo ($vo1['full_name']); ?></span><?php endforeach; endif; else: echo "" ;endif; ?>
										</div>
									</div>
								</div><?php endforeach; endif; else: echo "" ;endif; ?>
						</div>
					</div><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
		</div>
    </div>
    <?php if($business_id == ''): ?><div class="tab-pane fade in" id="tab10">
            <div class="panel-body">
                <?php if($content != 'resource'): ?><div class="pull-right">
                        <a href="javascript:void(0);" id="add_task" type="button" class="btn btn-primary">
                            <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;添加</a>
                    </div><?php endif; ?>
                <div style="clear:both;"></div>
            </div>
            <div id="task_empty" style="display: <?php echo empty($task) ? '' : 'none' ?>;">
                <div style="text-align:center;color:#E4E4E4;font-size:22px;font-weight:700;padding:15px 0px;">
	<img src="__PUBLIC__/img/exclamation_mark.png" style="margin-top:-3px;">&nbsp;&nbsp;暂无数据
</div>
            </div>
            <table id="customer_view_task_table" class="table product-table"  style="display: <?php echo empty($task) ? 'none' : '' ?>;">
                <tr>
                    <td width="min-width: 50px;">优先级</td>
                    <td>任务名称</td>
                    <td style="width: 200px;">完成进度</td>
                    <td style="min-width: 200px;">创建时间</td>
                </tr>
                <?php if(is_array($task)): $i = 0; $__LIST__ = $task;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                        <td>
                            <div style="border-radius: 50%; width: 10px; height: 10px; background: <?php echo ($vo['priority_color']); ?>;"></div>
                        </td>
                        <td>
                            <a class="task_view" href="javascript:void(0)" rel="<?php echo ($vo['task_id']); ?>" title="点击查看详情">
                                <?php echo ($vo['subject']); ?>
                            </a>
                        </td>
                        <td >
                            <div title="<?php echo ($vo['done']); ?>%" class="progress progress-mini" style="background: #ccc; margin-top: 3px;">
                                <div style="width: <?php echo ($vo['done']); ?>%;" class="progress-bar <?php echo ($vo['done_class']); ?>"></div>
                            </div>
                        </td>
                        <td>
                            <span><?php echo ($vo['create_date']); ?></span>
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </table>
        </div><?php endif; ?>
    <?php if($business_id == ''): ?><div class="tab-pane fade in" id="tab11">
            <div id="visitor_plan_empty" style="display: <?php echo empty($visitor_plan_list) ? '' : 'none' ?>;">
                <div style="text-align:center;color:#E4E4E4;font-size:22px;font-weight:700;padding:15px 0px;">
	<img src="__PUBLIC__/img/exclamation_mark.png" style="margin-top:-3px;">&nbsp;&nbsp;暂无数据
</div>
            </div>
            <table id="customer_view_visitor_plan_table" class="table product-table" style="display: <?php echo empty($visitor_plan_list) ? 'none' : '' ?>;">
                <tr>
                    <td width="min-width: 50px;">状态</td>
                    <td>计划内容</td>
                    <td>计划时间</td>
                    <td>延期时间</td>
                    <td>完成类型</td>
                    <td>操作</td>
                </tr>
                <tr id="visitor_plan_tr_model" style="display: none;">
                    <td class="status">
                        <div style="width: 12px; height: 12px; border-radius: 50%; float: left; margin: 3px 3px 0 0;"></div>
                        <span></span>
                    </td>
                    <td class="content">
                        <p style="overflow: hidden; text-overflow:ellipsis; white-space: nowrap; width: 220px; margin-top: 10px;"></p>
                    </td>
                    <td class="plan_time">
                    </td>
                    <td class="delay_time">
                    </td>
                    <td class="finish_type">
                        <a href="javascript:void(0);" class="log_history" module="" module-id=""></a>
                    </td>
                    <td class="operation">
                        <a class="visitor_plan_edit" rel="" href="javascript:void(0);">延期</a>
                        <a class="visitor_plan_delete" rel="" href="javascript:void(0);">取消</a>
                    </td>
                </tr>
                <?php if(is_array($visitor_plan_list)): $i = 0; $__LIST__ = $visitor_plan_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr id="visitor_plan_<?php echo ($val['id']); ?>">
                        <td class="status">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: <?php echo ($val['status_color']); ?>; float: left; margin: 3px 3px 0 0;"></div>
                            <span>
                                <?php echo ($val['status']); ?>
                            </span>
                        </td>
                        <td class="content" title="<?php echo ($val['content']); ?>">
                            <p style="overflow: hidden; text-overflow:ellipsis; white-space: nowrap; width: 220px; margin-top: 10px;"><?php echo ($val['content']); ?></p>
                        </td>
                        <td class="plan_time">
                            <?php echo ($val['plan_time']); ?>
                        </td>
                        <td class="delay_time">
                            <?php echo ($val['delay_time']); ?>
                        </td>
                        <td class="finish_type">
                            <?php if($val['module_id'] != 0): ?><a href="javascript:void(0);" class="log_history" module="<?php echo ($val['module']); ?>" module-id="<?php echo ($val['module_id']); ?>"><?php echo ($val['finish_type_name']); ?></a>
                            <?php else: ?>
                                -<?php endif; ?>
                        </td>
                        <td class="operation">
                            <?php if (in_array((int) $val['status_id'], array(0, 1, 2))) { ?>
                                <a class="visitor_plan_edit" rel="<?php echo ($val['id']); ?>" href="javascript:void(0);">延期</a>
                                <a class="visitor_plan_delete" rel="<?php echo ($val['id']); ?>" href="javascript:void(0);">取消</a>
                            <?php } ?>
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tfoot>
                    <tr id="visitor_plan_loading" style="display: none;">
                        <td colspan="5">
                            <div class="spiner-example">
                                <div class="sk-spinner sk-spinner-three-bounce">
                                    <div class="sk-bounce1"></div>
                                    <div class="sk-bounce2"></div>
                                    <div class="sk-bounce3"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div><?php endif; ?>
</div>
<div style="display:none" id="dialog-role-info" title="<?php echo L('EMPLOYEE_INFORMATION');?>">
    <div class="spiner-example">
        <div class="sk-spinner sk-spinner-three-bounce">
            <div class="sk-bounce1"></div>
            <div class="sk-bounce2"></div>
            <div class="sk-bounce3"></div>
        </div>
    </div>
</div>
<div style="display:none" id="dialog-contacts" title="关联联系人">
    <div class="spiner-example">
        <div class="sk-spinner sk-spinner-three-bounce">
            <div class="sk-bounce1"></div>
            <div class="sk-bounce2"></div>
            <div class="sk-bounce3"></div>
        </div>
    </div>
</div>
<div class="" style="display:none;" id="dialog-file" title="<?php echo L('ADD_ATTACHMENT');?>">
    <div class="spiner-example">
        <div class="sk-spinner sk-spinner-three-bounce">
            <div class="sk-bounce1"></div>
            <div class="sk-bounce2"></div>
            <div class="sk-bounce3"></div>
        </div>
    </div>
</div>
<div style="display:none" id="dialog-setting_reply" title="管理快捷沟通">
    <div class="spiner-example">
        <div class="sk-spinner sk-spinner-three-bounce">
            <div class="sk-bounce1"></div>
            <div class="sk-bounce2"></div>
            <div class="sk-bounce3"></div>
        </div>
    </div>
</div>

<div class="modal inmodal fade" id="addInvoice" aria-hidden="true" aria-labelledby="addInvoice" role="dialog" style="overflow:auto; border:1px solid #000000;">
    <div class="modal-dialog modal-md" style="width:700px;">
        <div class="modal-content" id="add_invoice_dialog">

        </div>
    </div>
</div>
<div class="modal inmodal fade" id="editInvoice" aria-hidden="true" aria-labelledby="editInvoice" role="dialog" style="overflow:auto; border:1px solid #000000;">
    <div class="modal-dialog modal-md" style="width:700px;">
        <div class="modal-content" id="edit_invoice_dialog">
            
        </div>
    </div>
</div>
<div class="modal inmodal fade" id="addNewTask" aria-hidden="true" aria-labelledby="addNewTask" role="dialog" style="overflow:auto; border:1px solid #000000;">
    <div class="modal-dialog" style="width:750px;">
        <div class="modal-content" id="task_modal">

        </div>
    </div>
</div>
<div class="modal inmodal fade" id="viewInvoice" aria-hidden="true" aria-labelledby="viewInvoice" role="dialog" style="overflow:auto; border:1px solid #000000;">
    <div class="modal-dialog modal-md" style="width:700px;">
        <div class="modal-content form-horizontal" id="view_invoice_dialog">
            
        </div>
    </div>
</div>
<div class="modal inmodal fade" id="viewNewEvent" aria-hidden="true" aria-labelledby="viewNewEvent" role="dialog" style="overflow:auto; border:1px solid #000000;">
    <div class="modal-dialog modal-md">
        <div class="modal-content form-horizontal" id="view_event">

        </div>
    </div>
</div>
<div class="modal inmodal fade" id="addNewEvent" aria-hidden="true" aria-labelledby="addNewEvent" role="dialog" style="overflow:auto; border:1px solid #000000;">
    <div class="modal-dialog modal-md">
        <div class="modal-content" id="add_modal">

        </div>
    </div>
</div>
<div class="modal inmodal fade" id="editNewEvent" aria-hidden="true" aria-labelledby="editNewEvent" tabindex="9999" role="dialog" style="overflow:auto; border:1px solid #000000;">
    <div class="modal-dialog modal-md">
        <div class="modal-content" id="edit_event">
            <div class="sk-spinner sk-spinner-three-bounce">
                <div class="sk-bounce1"></div>
                <div class="sk-bounce2"></div>
                <div class="sk-bounce3"></div>
            </div>
        </div>
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
                value="<?php echo ($event_info['remind_info']['visitor_plan']['plan_time']); ?>">
        </div>
    </div>
</div>
<div style="display:none" id="dialog-log_history" title="跟进记录">
    <div class="spiner-example">
        <div class="sk-spinner sk-spinner-three-bounce">
            <div class="sk-bounce1"></div>
            <div class="sk-bounce2"></div>
            <div class="sk-bounce3"></div>
        </div>
    </div>
</div>
<div id="dialog_visitor_plan" style="display: none;" title="计划延期">
</div>
<script src="__PUBLIC__/style/plyr/plyr.js"></script>
<script>plyr.setup();</script>

<script type="text/javascript" src="http://api.map.baidu.com/getscript?v=2.0&ak=grWGxlWOpGc1D0kVToxUgD6bwwjo35Tr"></script>
<link href="__PUBLIC__/css/litebox.css" rel="stylesheet" type="text/css">
<script src="__PUBLIC__/js/litebox.min.js" type="text/javascript"></script>
<script src="__PUBLIC__/js/images-loaded.min.js" type="text/javascript"></script>
<script>

    var detail_html = '<div class="spiner-example">\
                <div class="sk-spinner sk-spinner-fading-circle">\
                    <div class="sk-circle1 sk-circle"></div>\
                    <div class="sk-circle2 sk-circle"></div>\
                    <div class="sk-circle3 sk-circle"></div>\
                    <div class="sk-circle4 sk-circle"></div>\
                    <div class="sk-circle5 sk-circle"></div>\
                    <div class="sk-circle6 sk-circle"></div>\
                    <div class="sk-circle7 sk-circle"></div>\
                    <div class="sk-circle8 sk-circle"></div>\
                    <div class="sk-circle9 sk-circle"></div>\
                    <div class="sk-circle10 sk-circle"></div>\
                    <div class="sk-circle11 sk-circle"></div>\
                    <div class="sk-circle12 sk-circle"></div>\
                </div>\
            </div>';
   //鼠标点击空白处，隐藏右侧滑出
    document.onmousedown = function (event) {
        if ($(".litebox-overlay").length > 0) return;
        if ($("#dialog_open").val() == 1) return;
        if (event.target.className == 'right-sidebar-toggle-task') return;

        var div = document.getElementById("right-sidebar-task");
        var x = event.clientX;
        var y = event.clientY;
        var divx1 = div.offsetLeft;
        var divy1 = div.offsetTop;
        var divx2 = div.offsetLeft + div.offsetWidth;
        var divy2 = div.offsetTop + div.offsetHeight;
        if (x < divx1 || x > divx2 || y < divy1 || y > divy2) {
            if ($("#right-sidebar-task").css('right') == '0px') {
                $("#right-sidebar-task").animate({ right: '-60%' }, 400);
            }
        }
    }
    $(document).on('click', '.task_view', function () {
        var task_id = $(this).attr('rel');
        $(".emoji_container").remove();
        if ($("#right-sidebar-task").css('right') != '0px') {
            $("#right-sidebar-task").animate({ right: '0px' }, 600);
        }
        $('#task-content').html(detail_html);
        $('#sidebar-container').load("<?php echo U('task/view');?>&task_id=" + task_id);
    });
    //添加任务
    $(document).on('click', '#add_task', function () {
        var url = "<?php echo U('task/add');?>&customer_id=<?php echo ($customer_id); ?>";
        $('#addNewTask').modal('show');
        $('#task_modal').load(url);
    });

    //快捷沟通
    $(".select2").select2();

    $("#dialog-setting_reply").dialog({
        autoOpen: false,
        // modal: true,
        width: 550,
        maxHeight: 450,
        position: ["center",100],
        close:function(){
            selectStatus();//更新
            $(this).html("");
        },
        buttons: {
            "确定": function () {
                $('#status_form1').submit();
                $(this).dialog("close");
            },
            "取消": function () {
                selectStatus();//更新
                $(this).dialog("close");
            }
        }
    });

    $(function(){
        $("#setting_reply_dialog").click(function(){
            $('#dialog-setting_reply').dialog('open');
            $('#dialog-setting_reply').load('<?php echo U("setting/replyList");?>');
        });
    })

    function selectStatus(){
        var status_id = $("#status_id option:selected").val();
        var temp = '<option value="">--请选择--</option>';
        $.ajax({
            type:'post',
            url: "<?php echo U('setting/getReplyByStatus');?>",
            data: {status_id: status_id},
            async: false,
            success: function (data) {
                $.each(data.data, function(k, v){
                    temp += '<option value="'+v.content+'">'+v.str_content+'</option>';
                });
            },
            dataType: 'json'
        });
        $('#replay_list').html(temp);
    }

    function selectReply(){
        var replay_content = $("#replay_list option:selected").val();
        var status_id = $("#replay_list option:selected").attr('rel');
        //修改跟进类型
        $("#status_id option[value="+status_id+"]").attr('selected',true);
        //判断是否替换
        var log_content = $('#log-text').val();
        if (log_content !== '') {
            swal({
                title: '',
                text: '已填写沟通日志内容，确定要替换吗？',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "确认",
                cancelButtonText: "取消",
                closeOnConfirm: true,
                closeOnCancel:  true}, function(isConfirm){
                if (isConfirm) {
                    $('#log-text').val(replay_content);
                } else {
                    return false;
                } 
            });
        } else {
            $('#log-text').val(replay_content);
        }
    }

    $('#log-list').on('click','.map',function(){
        $(this).next('.allmap').slideToggle('show');
        var a =$(this).siblings('.longitude').attr('rel');
        var b =$(this).siblings('.latitude').attr('rel');
        var vals = $(this).next('.allmap').attr('rel');
        var url = "<?php echo U('index/gettranslocation');?>"+"&longtitude="+a+"&latitude="+b;
        $.get(url, function(data){
            var x = data.data.x;
            var y = data.data.y;
            setTimeout("mapInit("+y+","+x+","+vals+")",1000);
        })
    });

    function mapInit(x,y,mapID){
        var map = new BMap.Map(mapID);
        var point = new BMap.Point(y, x);
        map.centerAndZoom(point, 15);
        var marker = new BMap.Marker(point);  // 创建标注
        map.addOverlay(marker);               // 将标注添加到地图中
        map.enableScrollWheelZoom(true);
    };
    /**
    * 页面加载时执行Tab start
     */
    $(function(){
        var thisId = window.location.hash;
        var atype = thisId.substr(1);
        // $('#left_list a[type="'+atype+'"]').trigger('click');
        $('#left_list a[type="'+atype+'"]').tab('show');
     });
    /**
    * 页面加载时执行Tab end
     */
    $('#left_list a').click(function (e) {
        // $(this).tab('show');
        var maodian = '#'+$(this).attr('type');
        url_jump(maodian);
		var bid = $('#bid').val();
		if(bid == ''){
			$('#customer-relation').removeClass('all_business');
			$('#customer-relation').addClass('all_business_a');
		}
    });

    function url_jump(maodian){
        $('#maodian').val(maodian);
        var customer_id = "<?php echo ($customer_id); ?>";
        var module_business_id = $('#module_business_id').val();
        var bid = $('#bid').val();
        if(module_business_id){
            var url = "<?php echo U('business/view','id=');?>"+module_business_id+maodian;
        }else{
            if(bid){
                var url = "<?php echo U('customer/view','id=');?>"+customer_id+'&bid='+bid+maodian;
            }else{
                var url = "<?php echo U('customer/view','id=');?>"+customer_id+maodian;
            }
        }
       
        // $("body").scrollTop(0);
        // window.location.href = 'http://'+window.location.host+url;
        window.history.replaceState({},0,'http://'+window.location.host+url);
    }
    var business_id = "<?php echo ($business_id); ?>";

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
	$("#dialog-file").dialog({
		autoOpen: false,
		modal: true,
		width: 800,
		maxHeight: 400,
		position: ["center",100],
		buttons: {
			"确定": function () {
				location.reload();
			},
			"取消": function () {
                $(this).html('');//清空缓存
				$(this).dialog("close");
			}
		}
	});
	$(".add_file").click(function(){
        var module = $(this).attr('rel');
        $('#dialog-file').dialog('open');
        if(module == 'customer'){
            $('#dialog-file').load('<?php echo U("file/add","r=RCustomerFile&module=customer&id=".$customer_id);?>');
        }else if(module == 'business'){
            $('#dialog-file').load('<?php echo U("file/add","r=RBusinessFile&module=business&id=".$business_id);?>');
        }
	});

    $(".role_info").click(function(){
        $role_id = $(this).attr('rel');
        $('#dialog-role-info').dialog('open');
        $('#dialog-role-info').load('<?php echo U("user/dialoginfo","id=");?>'+$role_id);
    });
    
    $("#dialog-role-info").dialog({
        autoOpen: false,
        modal: true,
        width: 600,
        maxHeight: 550,
        position: ["center",100]
    });

	$('.radio_obj').on('click',function(){
		var is_checked = $(this).attr('rel');
		if(is_checked == 1){
			$(this).attr('rel','');
			$(this).prop('checked', false);
		}else{
			var full_name = $(this).attr('class');
            var name_arr = full_name.split(' ');
            var second_name = name_arr[1];
            $('.' + second_name).attr('rel', '');
			$(this).attr('rel',1);
		}
	});
    /*单选框取消选择*/
    $('#product-radio ins').on('click',function(){
        var checked = $(this).parent().hasClass('hahha');

        if(!checked){
            $(this).parent().addClass('checked');
            $(this).parent().addClass('hahha');
            $(this).prev().prop('checked',true);
        }else{
            $(this).parent().removeClass('checked');
            $(this).parent().removeClass('hahha');
            $(this).prev().prop('checked',false);
        }
    });
    /*关联联系人*/
    $("#dialog-contacts").dialog({
        autoOpen: false,
        modal: true,
        minWidth: 850,
        maxHeight: 600,
        position: ["center",100],
        buttons: {
            "确定": function () {
                var _this = this;
                var contacts_ids = new Array();
                $('.contacts_ch:checked').each(function(k,v){ 
                    contacts_ids.push($(v).val());
                }).val();
                if(contacts_ids.length == 0){
                    alert_crm('请至少选择一个联系人！');
                }else{
                    $.get("<?php echo U('contacts/relToBusiness');?>&act_n=1&contacts_id="+contacts_ids+"&business_id="+business_id, {},function(ret){
                        if (ret[0] == 'success') {
                            var temp_business_id = business_id;
                            $('.product-content').html(detail_html);
                            //product_detail($('.product-list[rel='+temp_business_id+']').find('.product-a'),"a[href='#tab-3']");
                            $('.product-content').load("<?php echo U('business/view_ajax');?>", {id: temp_business_id,type:1,module:'customer'}, function(){
                                $("a[href='#tab-3']").trigger('click');
                            });
                            $(_this).dialog("close");
                        } else {
                            alert_crm(ret[1]);
                        }
                    })
                    
                }
            },
            "取消": function () {
                $(this).html('');
                $(this).dialog("close");
            }
        },
        close: function() {
        $(this).empty();
        }
    });

    $("#rel_contacts").click(function(){
        $('#dialog-contacts').html('');
        // var customer_id = $(this).attr('rel');
        // var business_id = $(this).attr('rel1');
        $('#dialog-contacts').load('<?php echo U("contacts/checklistdialog",array("id"=>$customer_id,"business_id"=>$business_id));?>'); 
        $('#dialog-contacts').dialog('open');
    });
    //解绑联系人
    $(".not_rel_contacts").click(function(){
        var _this = this;
        swal({
            title: '确定要解除绑定吗？',
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确认",
            cancelButtonText: "取消",
            closeOnConfirm: true,
            closeOnCancel:  true}, function(isConfirm){
            if (isConfirm) {
                $.get($(_this).attr('rel'),{},function(ret){
                    if (ret[0] == 'success') {
                        var temp_business_id = business_id;
                        $('.product-content').html(detail_html);
                        //product_detail($('.product-list[rel='+temp_business_id+']').find('.product-a'),"a[href='#tab-3']");
                        $('.product-content').load("<?php echo U('business/view_ajax');?>", {id: temp_business_id,type:1,module:'customer'}, function(){
                            $("a[href='#tab-3']").trigger('click');
                        });
                    } else {
                        alert_crm(ret[1]);
                    }
                })
            } else {
                return false;
            } 
        });
    })
    /*跟进记录*/
    function btnHide(){
        $('#log-text').attr('rows',1);
        $('#log-btn').hide();
        $('#business_table').hide();
        $('#log-text').val('');
    }
    $('#log-text').focus(function(){
        $(this).attr('rows',4);
        $('#log-btn').show();
        $('#business_table').show();
        $('#add_log').prop('disabled',false);
    });
    $('#log-text').focusout(function(){
        var code_id = $("input[name='id']:checked").val();
        if($(this).val() == '' && code_id == ''){
            btnHide();
        }
    });
    $('#quxiao').click(function(){
        btnHide();
        return false;
    });
    /*ajax 提交记录*/
    
    $('#add_log').click(function(){
        var log_content = $.trim($('#log-text').val());
        if(log_content == ''){
            alert_crm('沟通日志内容不能为空！');
            return false;
        }
        //跟进类型
        // var status_id = $('#status_id option:selected').val();
        // if (status_id == '') {
        //     alert_crm('跟进类型不能为空！');
        //     $('#add_log').prop('disabled',false);
        // }

        var radio_id = $('#product-radio input:radio:checked').val();
        var code = $('#product-radio input:radio:checked').attr('code');
        var html_code = "<div class='log-relation'><i class='fa fa-bookmark'></i>&nbsp;<span>相关商机:"+code+"</span></div>";
        //console.log(radio_id);
        var log_type = 'rBusinessLog';
        
        var status_name = '';
        if ($('#status_id option:selected').text() !== '--请选择--') {
            status_name = $('#status_id option:selected').text();
        }
        var nextstep_time_log = '';
        if ($('#nextstep_time_log').val() !== '') {
            nextstep_time_log = $('#nextstep_time_log').val();
        }
        
        if(radio_id == null || radio_id == 0){
            var html_code = '';
            $("#business_id").val(business_id);

            if(business_id == null || business_id == 0){
                $("[name='r']").val('RCustomerLog');
                $("[name='module']").val('customer');
                $("#business_id").val("<?php echo ($customer_id); ?>");
                log_type = 'rCustomerLog';
            }
        }

        $(this).prop('disabled',true);
        $.post("<?php echo U('Log/add');?>", $("#add-form").serialize(), function(data){
            if(data.status == 1){
                var content = $('#log-text').val().replace('&nbsp','&NBSP');
                var log_html = "<div class='ibox-content gray-bg' log-rel='"+data.data.log_id+"' style='margin-bottom: 18px'><div class='social-feed-separated clearfix'><div class='social-feed-box'><div class='pull-right social-action dropdown'><span data-toggle='dropdown' class='dropdown-toggle'><i style='font-size:20px;cursor:pointer' class='fa fa-angle-down'></i></span><ul class='dropdown-menu m-t-xs' ><li><a  rel='"+data.data.log_id+"' href='javascript:void(0);' type='"+log_type+"' onclick='del_confirm(this);'><?php echo L('DELETE');?></a></li></ul></div><div class='social-avatar'><img alt='image' style='width:35px;height:35px;' class='img-circle' src='"+data.data.owner.thumb_path+"'><a class='role_info name-colors'  rel='"+data.data.owner.role_id+"' href='javascript:void(0)'>"+data.data.owner.full_name+"</a>&nbsp;&nbsp;<span class='text-muted'>添加了一条快速记录</span>&nbsp;&nbsp;<span class='text-muted' >"+data.data.date+"&nbsp;&nbsp;<a title='沟通类型' href='javascript:void(0);'>"+status_name+"</a></span></div><div class='social-body'><span style='word-wrap:break-word;line-height: 21px;color: #000;'>"+content+"</span>"+html_code+"</div>";
                if (nextstep_time_log !== '1970-01-01 08:00') {
                    log_html += "<div class='social-avatar' style='padding-top:10px;''><span class='text-muted pull-right'>下次联系时间："+nextstep_time_log+"</span></div>";
                }
                log_html += "</div></div></div>";
                $('#log-list').prepend(log_html);
                rtal('添加成功');
                $('#customer-relation').click();
                btnHide();
            }else{
                alert_crm('添加失败, 请重试');
            }
            $("[name='r']").val('rBusinessLog');
            $("[name='module']").val('business');
            $("#business_id").val(business_id);
        });
    });
    /*删除日志*/
    function del_confirm(e){
        swal({
            title: "确定要删除沟通日志吗？",
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
                var log_id = $(e).attr('rel');
                var type = $(e).attr('type');
                $.get("<?php echo U('log/delete');?>", {r:type, id:log_id}, function(data){
                    if(data != 0){
                        swal({
                            title: "删除成功！",
                            text: "",
                            type: "success"
                        });
                        $("[log-rel='"+log_id+"']").remove();
                    }else{
                        swal({
                            title: "操作失败！",
                            text:data.info,
                            type: "error"
                        })
                        return false;
                    }
                });
            } else {
                swal("已取消","您取消了删除操作！","error");
            }
        });
    };
    //删除联系人
    $('.del_contacts').click(function(){
        var contacts_id = $(this).attr('rel');
        var type = $(this).attr('rel1');
        var customer_id = $(this).attr('rel2');
        swal({
            title: "您确定要删除联系人吗？",
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
                if(type == 'mdelete'){
                    //删除首要联系人
                    $.ajax({
                        type:'get',
                        url: "<?php echo U('contacts/mdelete','r=rContactsCustomer&module_id=');?>"+customer_id+'&id='+contacts_id,
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
                }else{
                    $.ajax({
                        type:'get',
                        url: "<?php echo U('contacts/delete','id=');?>"+contacts_id,
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
                }
            } else {
                swal("已取消","您取消了删除操作！","error");
            }
        });
    });
    //删除附件
    $('.file_delete').click(function(){
        var file_id = $(this).attr('rel');
        swal({
            title: "您确定要删除附件信息吗？",
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
                var url;
                if ('<?php echo ($business_id); ?>' == '') {
                    url = "<?php echo U('file/delete','r=RCustomerFile&id=');?>" + file_id;
                } else {
                    url = "<?php echo U('file/delete','r=RBusinessFile&id=');?>" + file_id;
                }
                $.ajax({
                    type:'get',
                    url: url,
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

    $(".editproduct_view").click(function(){
    	var business_id = $(this).attr('rel');
    	var customer_id = "<?php echo ($customer['customer_id']); ?>";
    	$('#dialog-editproduct').dialog('open');
    	$('#dialog-editproduct').load('<?php echo U("product/mutildialog_product","business_id=");?>'+business_id);
    });

    //发票
    $('.add_invoice').click(function(){
        var customer_id = "<?php echo ($customer_id); ?>";
        $('#addInvoice').modal('show');
        var url = "<?php echo U('invoice/addData','customer_id=');?>"+customer_id;
        $('#add_invoice_dialog').load(url);
    });
    $('.view_invoice').click(function(){
        var invoice_id = $(this).attr('rel');
        $('#viewInvoice').modal('show');
        var url = "<?php echo U('invoice/viewData','invoice_id=');?>"+invoice_id;
        $('#view_invoice_dialog').load(url);
    });

    
    $('#add_event').click(function () {
        var business_id = '<?php echo ($business_id); ?>';
        if (business_id) {
            var module = 'business',
            module_id = business_id;
        } else {
            var module = 'customer',
            module_id = '<?php echo ($customer_id); ?>';
        }
        $('#addNewEvent').modal('show');
        var url = "<?php echo U('event/add');?>&module="+ module + "&module_id=" + module_id;
        $('#add_modal').load(url);
    });
    $('.event_view').on('click', function(){
        var event_id = $(this).attr('rel');
        $('#viewNewEvent').modal('show');
        var url = "<?php echo U('event/view','event_id=');?>" + event_id;
        $('#view_event').load(url);
    });

    // 访客计划
    $('.visitor_plan_edit').on('click', function () {
        $('#dialog_visitor_plan').dialog('open');
        let id = $(this).attr('rel');
        let time = $(this).parents('tr').find('.plan_time').html();
        time = $.trim(time);
        let html = $('#visitor_plan_model').clone(1);
        html.find('[name="id"]').val(id);
        html.find('[name="plan_time"]').val(time);
        html.show();
        $('#dialog_visitor_plan').html('');
        $('#dialog_visitor_plan').append(html);
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
                        _dialog.dialog('close');
                        if (res.status == 1) {
                            swal({
                                title: res.msg,
                                type: 'success'
                            }, function () {
                                let plan_time = $('#visitor_plan_'+ data.id).find('.plan_time').html();
                                plan_time = $.trim(plan_time);
                                $('#visitor_plan_' + data.id).find('.plan_time').html(data.plan_time);
                                $('#visitor_plan_' + data.id).find('.delay_time').html(plan_time);
                                $('#visitor_plan_' + data.id).find('.status span').html('延期');
                            });
                        } else {
                            swal(res.msg, '', 'error');
                            swal({
                                title: res.msg,
                                type: 'error'
                            }, function () {
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

    // 取消计划
    $('.visitor_plan_delete').on('click', function () {
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
                            $('#visitor_plan_' + id).remove();
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
    
    // 访客计划 
    $('#nextstep_time_log').on('input', function () {
        time_change();
    })
    function time_change() {
        if ($('#nextstep_time_log').val() != '') {
            $("#tr_join_visitor_plan_log").show(200);
        } else {
            $("#tr_join_visitor_plan_log").hide(200);
            $("#join_visitor_plan_log").prop('checked', false);
            $('#tr_visitor_plan_content').hide(200);
        }
    }
    $("#join_visitor_plan_log").on('change', function () {
        if ($(this).is(':checked')) {
            $('#tr_visitor_plan_content').show(200);
        } else {
            $('#tr_visitor_plan_content').hide(200);
        }
    });
    
    // 访客计划动态加载
    let visitor_plan_page = 1;
    let visitor_plan_load = true;
    $(window).scroll(function () {
        let w_height = $(window).height();
        let customer_id = '<?php echo ($customer_id); ?>';
        let offset_top = $(window).scrollTop();
        let d_height = $(document).height();
        if (w_height + offset_top >= d_height) {
            if ($('[href="#tab11"]').attr('aria-expanded') == 'true' && visitor_plan_load) {
                visitor_plan_page++;
                $('#visitor_plan_loading').show();
                $.ajax({
                    url: '<?php echo U("remind/visitor_plan");?>',
                    data: { customer_id: customer_id, p: visitor_plan_page },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function (res) {
                        if (res.status) {
                            $.each(res.data, function (key, val) {
                                let model = $('#visitor_plan_tr_model').clone(1);
                                model.attr('id', 'visitor_plan_' + val.id);
                                model.find('.status div').css('background', val.status_color);
                                model.find('.status span').html(val.status);
                                model.find('.content').attr('title', val.content);
                                model.find('.content p').html(val.content);
                                model.find('.plan_time').html(val.plan_time);
                                model.find('.delay_time').html(val.delay_time);
                                if ($.inArray(val.status_id, [0, 1, 2])) {
                                    model.find('.operation a').attr('rel', val.id);
                                } else {
                                    model.find('.operation a').remove();
                                }
                                if (val.module_id != 0) {
                                    model.find('.finish_type a').attr('module', val.module);
                                    model.find('.finish_type a').attr('module-id', val.module_id);
                                    model.find('.finish_type a').html(val.finish_type_name);
                                } else {
                                    model.find('.finish_type').html('-');
                                }
                                $('#tab11 table').append(model);
                                model.show(200);
                                console.log(model.find('.status sapn'));
                            });
                        } else {
                            visitor_plan_load = false;
                        }
                        $('#visitor_plan_loading').hide();
                    }
                });
            }
        }
    });
    $("#dialog-log_history").dialog({
        autoOpen: false,
        modal: true,
        width: 650,
        maxHeight: 450,
        position: ["center", 100],
        close: function () {
            $(this).html("");
        },
        // buttons: {
        //     "确定": function () {
        //         $(this).dialog("close");
        //     },
        //     "取消": function () {
        //         $(this).dialog("close");
        //     }
        // }
    });
    $(".log_history").click(function () {
        let _module = $(this).attr('module');
        let _module_id = $(this).attr('module-id');
        $('#dialog-log_history').dialog('open');
        $('#dialog-log_history').load('<?php echo U("log/commun_list","module=");?>' + _module + '&module_id=' + _module_id);
    });
</script>
<div id="right-sidebar-task">
    <!--the css for jquery.mCustomScrollbar-->
    <link rel="stylesheet" href="__PUBLIC__/emoji/css/jquery.mCustomScrollbar.min.css" />
    <!--the css for this plugin-->
    <link rel="stylesheet" href="__PUBLIC__/emoji/css/jquery.emoji.css" />
    <!--(Optional) the js for jquery.mCustomScrollbar's addon-->
    <script src="__PUBLIC__/emoji/js/jquery.mousewheel-3.0.6.min.js"></script>
    <!--the js for jquery.mCustomScrollbar-->
    <script src="__PUBLIC__/emoji/js/jquery.mCustomScrollbar.min.js"></script>
    <!--the js for this plugin-->
    <script src="__PUBLIC__/emoji/js/jquery.emoji.js"></script>

    <div class="sidebar-container" id="sidebar-container">

    </div>
</div>