<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<title></title>
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/reset.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/index.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/crm.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/calendar.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/swiper.min.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/mobiscroll.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/mobiscroll_date.css">
	<link href="__PUBLIC__/style/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="__PUBLIC__/css/font-awesome.min.css" rel="stylesheet">
	<script src="__PUBLIC__/waps/js/jquery-1.7.1.min.js"></script>
	<script src="__PUBLIC__/waps/js/jquery.circliful.js"></script>
	<script src="__PUBLIC__/waps/js/echarts.common.min.js"></script>
	<script src="__PUBLIC__/js/jquery.base64.js"></script>
	<script src="__PUBLIC__/js/jquery.md5.js"></script>
	<script src="__PUBLIC__/waps/js/PCASClass.js"></script>
	<script type="text/javascript" src="__PUBLIC__/waps/js/tab2.js"></script>
	<script type="text/javascript" src="__PUBLIC__/waps/js/swiper3.06.jquery.min.js"></script>
</head>
<script>
	function showtab(a,b){
        $("#success").hide();
        $("#error").hide();
        $("#warn").hide();

        if(a == 1){
            $("#title").html('成功');
            $("#success").show();
        }else if (a == 2){
            $("#title").html('错误');
            $("#error").show();
        }else if (a == 3){
            $("#title").html('提示');
            $("#warn").show();
        }
        if(b){
            $("#content").html(b);
        }
        $("#warning").show();
        $("#popup-bg").show();
    }

    function closestab() {
        $("#warning").hide();
        $("#popup-bg").hide();
    }


    function showtab1(a,b){
        $("#success1").hide();
        $("#error1").hide();
        $("#warn1").hide();

        if(a == 1){
            $("#title1").html('成功');
            $("#success1").show();
        }else if (a == 2){
            $("#title1").html('错误');
            $("#error1").show();
        }else if (a == 3){
            $("#title1").html('提示');
            $("#warn1").show();
        }
        if(b){
            $("#content1").html(b);
        }
        $("#warning1").show();
        $("#popup-bg1").show();
    }

    function closestab1() {
        $("#warning1").hide();
        $("#popup-bg1").hide();
    }

</script>
<body>
<div id="tab">
	<div class="warning" id="warning" style="display:none;">
		<a href="javascript:void(0);" onclick="closestab()" class="close iconfont">&#xe6cb;</a>
		<div class="tit-ico" id="success" style="display: none"><i class="iconfont ok">&#xe6a1;</i></div>
		<div class="tit-ico" id="error" style="display: none"><i class="iconfont error">&#xe6dc;</i></div>
		<div class="tit-ico" id="warn" style="display: none"><i class="iconfont noun">&#xe690;</i></div>
		<div class="tit" id="title">提示</div>
		<div class="winfo" id="content"></div>
		<div><input type="button" onclick="closetab()" class="pbtn pb-gray pbtn-sm" value="确定"></div>
	</div>
	<div class="popup-bg" id="popup-bg" style="display:none;"></div>

	<div class="warning" id="warning1" style="display:none;">
		<a href="javascript:void(0);" onclick="closestab1()" class="close iconfont">&#xe6cb;</a>
		<div class="tit-ico" id="success1" style="display: none"><i class="iconfont ok">&#xe6a1;</i></div>
		<div class="tit-ico" id="error1" style="display: none"><i class="iconfont error">&#xe6dc;</i></div>
		<div class="tit-ico" id="warn1" style="display: none"><i class="iconfont noun">&#xe690;</i></div>
		<div class="tit" id="title1">提示</div>
		<div class="winfo" id="content1"></div>
		<div><input type="button" onclick="closestab1()" class="pbtn pb-gray pbtn-sm" value="确定"></div>
	</div>
	<div class="popup-bg" id="popup-bg1" style="display:none;"></div>
</div>




<div class="wrap-box">
    <header class="abe-header am-header-default"> <a href="javascript:history.go(-1);" class="return iconfont">&#xe612;</a>
        <h1 class="abe-header-title">线索操作</h1>
    </header>
<!--线索详情-->
    <div class="clue">
        <input type="hidden" id="by" name="<?php echo ($bys); ?>" value="<?php echo ($bys); ?>"/>
        <ul class="clue-head">
            <input type="hidden" id="leads_id" name="<?php echo ($leads_id); ?>" value="<?php echo ($leads_id); ?>"/>
            <li class="pd0">
               <div class="info"> <a href="<?php echo U('Leadsm/leadsdetail',array('leads_id'=>$leads_id));?>" class="abe-block vi-blue"><i class="iconfont abe-ft24 mrg_10">&#xe608;</i><?php if($leads['name']) : echo ($leads['name']); else : echo ($leads["contacts_name"]); endif ;?></a><em class="arrow iconfont">&#xe607;</em></div>
            </li>
            <li><span class="tit">负责人:</span><div class="info"><?php echo ($leads["owner"]["user_name"]); ?></div></li>
            <li><span class="tit">联系人姓名:</span><div class="info"><?php echo ($leads["contacts_name"]); ?></div></li>
            <li><span class="tit">最后修改时间:</span><div class="info"><?php if($leads['update_time'] > 0): echo (date('Y-m-d H:i:s',$leads["update_time"])); endif; ?></div></li>
            <li><span class="tit">电话:</span><div class="info"><a href="tel:<?php echo ($leads['mobile']); ?>" class="abe-block"><em class="iconfont ztpos vi-blue abe-ft24 mrg_10">&#xe606;</em><?php echo ($leads['mobile']); ?></a></div></li>
        </ul>
        <div class="tab-box clue-tab">
            <ul class="tab-title">
                <li><a href="#" class="active">沟通日志</a></li>
                <!--<li><a href="#" class="active">附件</a></li>-->
            </ul>
            <div class="clue-faq tab-content">
                <div class="clue-item item" style="display: ">
                <?php if(is_array($leads['log'])): $i = 0; $__LIST__ = $leads['log'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><ul class="bop-list">
                    <li class="uhbox">
                        <em class="uhead">
                        <?php if(empty($vo['owner']['thumb_path'])): ?><img src="__PUBLIC__/img/avatar_default.png">
                            <?php else: ?>
                            <img src="<?php echo ($vo['owner']['thumb_path']); ?>"><?php endif; ?>
                        </em>
                        <div><span class="pdr_10"><?php echo ($vo['owner']['full_name']); ?></span><span><?php echo (date("Y-m-d  H:i",$vo["create_date"])); ?></span></div></li>
                    <li><span class="abe-gray3">联系时间：</span><?php if(empty($vo['nextstep_time'])): ?>未记录<?php else: echo (date("Y-m-d  H:i:s",$vo["nextstep_time"])); endif; ?></li>
                    <li class="vi-blue"><?php echo ($vo['content']); ?></li>
                    <li><span class="abe-gray3">下次联系时间：</span><?php if(empty($vo['nextstep_time'])): ?>未记录<?php else: echo (date("Y-m-d  H:i:s",$vo["nextstep_time"])); endif; ?></li>
                    <li><span class="abe-gray3">沟通类型：</span><?php echo ($vo['status_name']); ?></li>
                </ul><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <!--<div class="clue-item item">
                    <ul class="file">
                        <li><em class="thum iconfont">&#xe62c;</em><div><span class="filename abe-fl vi-blue">logo.png</span><span class="abe-fr abe-gray3">18.8KB</span></div></li>
                        <li><em class="thum iconfont">&#xe602;</em><div><span class="filename abe-fl vi-blue">我是一个兵.exl</span><span class="abe-fr abe-gray3">18.8KB</span></div></li>
                        <li><em class="thum iconfont">&#xe62d;</em><div><span class="filename abe-fl vi-blue">我是一个兵.doc</span><span class="abe-fr abe-gray3">18.8KB</span></div></li>
                        <li><em class="thum iconfont">&#xe7a2;</em><div><span class="filename abe-fl vi-blue">我是一个兵.ppt</span><span class="abe-fr abe-gray3">18.8KB</span></div></li>
                    </ul>
                </div>-->
            </div>
        </div>
    </div>
    <a class="faq-btn iconfont">&#xe60b;</a>
    <div class="faq-btn-box ad-faq">
        <a href="<?php echo U('Leadsm/add_log',array('leads_id'=>$leads_id));?>">
            <i class="iconfont icolor1">&#xe6b3;</i>添加日志</a><a href="javascript:void(0);" id="change">
        <i class="iconfont icolor2">&#xe8a4;</i>线索转换</a>
        <?php if($bys != 'public'): ?><a href="<?php echo U('Leadsm/edit',array('leads_id'=>$leads_id));?>"><i class="iconfont icolor3">&#xe6f8;</i>编辑</a>
            <?php else: ?><a href="javascript:showtab(3,'是否领取该条线索数据');"><i class="iconfont icolor3">&#xe6f8;</i>领取</a><?php endif; ?>
        <a href="javascript:showtab(3,'是否删除该条数据');"><i class="iconfont icolor5">&#xe603;</i>删除</a>
    </div>
    <div class="popup-bg" id="heimu" style="display: none;"></div>
    <script>
        function closetab(){
            var by=$('#by').val();
            var leads_id=$('#leads_id').val();
            $.ajax({
                url : "<?php echo U('Leadsm/delete');?>",
                type: "POST",
                data:{"by": by, "leads_id" : leads_id },
                async: true,
                success:function(){
                    var str="<?php echo U('Leadsm/index');?>"+'&by='+by;
                    window.location.href=str;
                }
            });
        }

        function closetab(){
            var by=$('#by').val();
            var leads_id=$('#leads_id').val();
            $.ajax({
                url : "<?php echo U('Leadsm/receive');?>",
                type:"POST",
                data:{"by": by,"id":leads_id},
                async:true,
                success:function(){
                    var str="<?php echo U('Leadsm/index');?>"+'&by='+by;
                    window.location.href=str;
                }
            });

        }

        $(function(){
            $("#heimu").css({top:"-100%"});
            $(".faq-btn").click(function(){
                $(".faq-btn-box").animate({bottom:"0"});
                $("#heimu").show();
                $("#heimu").animate({top:"0"},300);
            });
            $("#heimu").click(function(){
                $(this).animate({top:"-100%"},300);
                $(".faq-btn-box").animate({bottom:"-200px"});
            });
            $(".faq-btn-box a").click(function(){
                $(".faq-btn-box").animate({bottom:"-200px"});
                $("#heimu").animate({top:"-100%"},300);
            });
            $(".clue-tab").tabs();

        });

        $("#change").click(function () {
            var leads_id = "<?php echo ($leads['leads_id']); ?>";
            var item = "<?php echo ($role['0']['role_id']); ?>";
            $.ajax({
                type:'get',
                url:'index.php?m=leads&a=change_customer&id='+leads_id+'&role_id='+item,
                async:false,
                success:function(data){
                    if(data.status == 1){
                        showtab('1',"转换成功！", "您已经成功将该线索转换为客户！");
                    }else{
                        showtab('2',data.info);
                    }
                },
                dataType:'json'
            });
        })

    </script>
</div>
</body>
</html>