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
  <header class="abe-header am-header-default"> <a href="javascript:history.go(-1)" class="return iconfont">&#xe612;</a>
    <h1 class="abe-header-title">客户列表</h1>
      <a href="<?php echo U('customerm/add',array('by'=>$content));?>" class="next-btn"><i class="iconfont mrg_5">&#xe63f;</i>添加客户</a>
  </header>
  <div class="ku-search">
    <div class="ksinfo" id="kspop">
      <form id="customer_search" action="" method="get">
        <input type="hidden" name="m" value="customerm"/>
        <input type="hidden" name="a" value="index"/>
        <input type="hidden" name="field" value="name"/>
        <input type="hidden" name="condition" value="contains"/>
        <input type="hidden" name="content" value="<?php echo ($_GET['content']); ?>"/>
        <input type="hidden" name="by" value="<?php echo ($content); ?>"/>
        <input type="hidden" name="scene_id" value="<?php echo ($_GET['scene_id']); ?>"/>
      <input type="text" class="txt" name="search" placeholder="客户名/联系人/联系电话" onkeydown='if(event.keyCode==13) {$("#short_search_btn").trigger("click");return false;}' class="form-control input-sm" <?php if($_GET['field'] == 'name'): ?>value="<?php echo ($_GET['search']); ?>"<?php endif; ?>/>
      <button class="pbtn pb-blue" type="submit" id="short_search_btn"><em>搜索</em></button>
      </form>
    </div>
  </div>
  <div class="pbtab pbtab4 mbg_10">
    <a href="<?php echo U('Customerm/index');?>" <?php if($content != 'resource'): ?>class="active"<?php endif; ?>>客户</a>
    <a href="<?php echo U('Customerm/index','content=resource');?>" <?php if($content == 'resource'): ?>class="active"<?php endif; ?>>客户池</a></div>
    <input type="hidden" id="cont" name="<?php echo ($content); ?>" value="<?php echo ($content); ?>">
  <!--<div class="blank40">

    <div id="search">
      <form class="form-inline pull-left" id="customer_search" action="" method="get">
        <input type="hidden" name="m" value="customerm"/>
        <input type="hidden" name="a" value="index"/>
        <input type="hidden" name="field" value="name"/>
        <input type="hidden" name="condition" value="contains"/>
        <input type="hidden" name="content" value="<?php echo ($_GET['content']); ?>"/>
        <input type="hidden" name="by" value="<?php echo ($content); ?>"/>
        <input type="hidden" name="scene_id" value="<?php echo ($_GET['scene_id']); ?>"/>
        <input type="text" name="search" id="short_search" class="text" placeholder="客户名/联系人/联系电话" onkeydown='if(event.keyCode==13) {$("#short_search_btn").trigger("click");return false;}' class="form-control input-sm" <?php if($_GET['field'] == 'name'): ?>value="<?php echo ($_GET['search']); ?>"<?php endif; ?>/>
        <button type="submit" id="short_search_btn" class="submit" style="color: deeppink">搜索</button>
      </form>
    </div>-->


  <div class="zcmlbox">
    <ul class="zcml kcpp">
      <?php if(is_array($customerlist)): $i = 0; $__LIST__ = $customerlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
        <div class="tbt"><em class="tip"></em><span class="abe-fl">
          <input type="hidden" id="customer_id" name="<?php echo ($vo['customer_id']); ?>" value="<?php echo ($vo['customer_id']); ?>">
         <!-- <a href="<?php echo U('Customerm/indexctm',array('customer_id'=>$vo['customer_id']));?>" class="vi-blue"> <?php echo ($vo["name"]); ?></a></span>-->
          <a href="<?php echo U('customerm/view', array('id'=>$vo['customer_id'],'content'=>$content));?>" class="vi-blue"> <?php echo ($vo["name"]); ?></a></span>
          <!--<a href="<?php echo U('customerm/view', array('id'=>$vo['customer_id'],'content'=>$content));?>" class="abe-fr vi-blue"><i class="iconfont mrg_5">&#xe6a7;</i>联系人:<?php echo ($vo["contacts_name"]); ?></a></div>-->
        <a href="<?php echo U('Customerm/indexcon',array('contacts_id'=>$vo['contacts_id'],'content'=>$content));?>" class="abe-fr vi-blue">
          <i class="iconfont mrg_5">&#xe6a7;</i>联系人:<?php echo ($vo["contacts_name"]); ?></a></div>
        <a href="<?php echo U('Customerm/indexctm',array('customer_id'=>$vo['customer_id']));?>"><div class="item"><span class="">联系电话</span>
          <div class="info"><?php echo ($vo["crm_vwlnfx"]); ?></div>
        </div>
        <div class="item"><span class="">客户负责人</span>
          <div class="info"><?php echo ($vo["owner_role_name"]); ?></div>
        </div>
        <div class="item"><span class="">客户状态</span>
          <div class="info"><?php echo ($vo["customer_status"]); ?></div>
        </div>
        <div class="item"><span class="">行业</span>
          <div class="info"><?php echo ($vo["industry"]); ?></div>
        </div>
        <div class="item"><span class="">客户地址</span>
          <div class="info"><?php echo ($vo["address"]); ?></div>
        </div>
          <div class="item"><span class="">客户信息来源</span>
            <div class="info"><?php echo ($vo["origin"]); ?></div>
          </div></a>
          <!--<a href="<?php echo U('Customerm/delete',array('customer_id'=>$vo['customer_id']));?>" onclick="return confirmd()" class="abe-fr pbtn pb-red pbtn-sxm">删除</a>-->
       <!-- <a href="javascript:showtab(3,'是否删除该条数据')" class="abe-fr pbtn pb-red pbtn-sxm">删除</a>
        <a href="<?php echo U('Customerm/edit',array('customer_id'=>$vo['customer_id']));?>" class="abe-fr pbtn pbtn-sxm mrg_10">编辑</a>-->
      </li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
  </div>
  <div class="footer-menu footer-new">
    <a href="<?php echo U('Indexm/index');?>" id="Indexm"><i class="iconfont">&#xe60a;</i>首页</a>
    <a href="<?php echo U('Leadsm/index');?>" id="Leadsm"><i class="iconfont" >&#xe601;</i>线索</a>
    <a href="<?php echo U('Customerm/index');?>" id="Customerm"><i class="iconfont" >&#xe6a7;</i>客户</a>
    <a href="#" id="chat" name="chat" id=""><i class="iconfont">&#xe633;</i>聊天</a>
    <a href="<?php echo U('Userm/index');?>" id="Userm"><i class="iconfont">&#xe63b;</i>我的</a>
</div>
<script>
$(function () {
    var i = "<?php echo (MODULE_NAME); ?>";
    console.log(i);
    if (i == 'Indexm'){
        $('#Indexm').addClass("active");
    }else if(i == 'Leadsm'){
        $('#Leadsm').addClass("active");
    }else if(i == 'Customerm'){
        $('#Customerm').addClass("active");
    }else if(i == 'Userm'){
        $('#Userm').addClass("active");
    }
})

$('#chat').click(function () {
    var user_id = $("#chat1").val();
    var name = $("#chat2").val();
    $.ajax({
        url : "<?php echo U('Indexm/chatReg');?>",
        type: "POST",
        data:{user_id,name},
        success: function(msg) {
            if (msg) {
                console.log("五哥最帅");
            } else {
                console.log("五个最胖");
            }
        }
    });
});

$('#addFriend').click(function(){
    var user_id = $("#chat1").val();
    $.ajax({
        url:"<?php echo U('Indexm/chatAddFriends');?>",
        type: "POST",
        data:{user_id},
        success:function (msg) {
            showtab(msg);
        }
    });
});

</script>
</div>
<script>
    function closetab(){
        var content=$("#cont").val();
        var customer_id=$("#customer_id").val()
        $.ajax({
            url:"<?php echo U('Customerm/delete');?>",
            type:"POST",
            data:{'content':content,'customer_id':customer_id},
            async: true,
            success:function(){
                var str="<?php echo U('Customerm/index');?>"+'&content='+content;
                window.location.href=str;
            }
        });
    }


</script>
</body>

</html>