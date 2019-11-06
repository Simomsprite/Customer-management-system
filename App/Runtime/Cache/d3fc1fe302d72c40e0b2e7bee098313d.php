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
    <h1 class="abe-header-title">线索</h1>
    <a href="<?php echo U('Leadsm/add');?>" class="next-btn"><i class="iconfont mrg_5">&#xe63f;</i>添加线索</a>
  </header>

  <div class="ku-search">
    <div class="ksinfo" id="kspop">
      <form id="customer_search" action="" method="get">
        <input type="hidden" name="m" value="leadsm"/>
        <input type="hidden" name="a" value="index"/>
        <input type="hidden" name="by" value="<?php echo ($by); ?>"/>
        <input type="hidden" name="name[condition]" value="contains"/>
        <input type="hidden" name="name[form_type]" value="text">
        <input type="text" class="txt" name="name[value]" placeholder="线索名/联系人/联系号码" onkeydown='if(event.keyCode==13) {$("#short_search_btn").trigger("click");return false;}'   <?php if($_GET['name']['condition'] == 'contains'): ?>value="<?php echo ($_GET['name']['value']); ?>"<?php endif; ?> />
        <button class="pbtn pb-blue" type="submit" id="short_search_btn"><em>搜索</em></button>
      </form>
    </div>
  </div>

  <div class="pbtab pbtab4 mbg_10">
    <a href="<?php echo U('Leadsm/index');?>" <?php if($by != 'public'): ?>class="active"<?php endif; ?> id="leads">线索</a>
    <a href="<?php echo U('leadsm/index','by=public');?>"  <?php if($by == 'public'): ?>class="active"<?php endif; ?> id="leadspool">线索池</a></div>
    <input type="hidden" id="by" name="<?php echo ($by); ?>" value="<?php echo ($by); ?>"/>
  <div class="zcmlbox">
    <ul class="zcml kcpp">
      <?php if(is_array($leadslist)): $i = 0; $__LIST__ = $leadslist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
          <div class="tbt"><em class="tip"></em>
            <span class="abe-fl">
              <input type="hidden" id="leads_id" name="<?php echo ($vo["leads_id"]); ?>" value="<?php echo ($vo["leads_id"]); ?>"/>
              <a href="<?php echo U('leadsm/view',array('id'=>$vo['leads_id'],'by'=>$by));?>" class="vi-blue"><?php if($vo["name"] != ''): echo ($vo["name"]); else: ?>线索操作<?php endif; ?></a></span>
            <!--<a href="<?php echo U('leadsm/view',array('id'=>$vo['leads_id'],'by'=>$by));?>" class="abe-fr vi-blue"><i class="iconfont mrg_5">&#xe6a7;</i>联系人:<?php echo ($vo["contacts_name"]); echo ($vo["saltname"]); ?></a></div>-->
          <a href="<?php echo U('leadsm/view',array('id'=>$vo['leads_id'],'by'=>$by));?>" class="abe-fr vi-blue"><i class="iconfont mrg_5 ">&#xe6a7;</i>联系人:<?php echo ($vo["contacts_name"]); echo ($vo["saltname"]); ?></a></div>
          <a href="<?php echo U('Leadsm/leadsdetail',array('leads_id'=>$vo['leads_id']));?>"> <div class="item"><span class="">手机号码</span>
            <div class="info"><?php echo ($vo["mobile"]); ?></div>
          </div>
          <div class="item"><span class="">创建人</span>
            <div class="info"><?php echo ($vo["creator"]["user_name"]); ?></div>
          </div>
          <div class="item"><span class="">创建时间</span>
            <div class="info"><?php echo (date('Y-m-d H:i',$vo['create_time'])); ?></div>
          </div>
          <div class="item"><span class="">下次联系时间</span>
            <div class="info"><?php if($vo['nextstep_time'] > 0): echo (date('Y-m-d H:i',$vo['nextstep_time'])); else: endif; ?></div>
          </div>
          <div class="item"><span class="">下次联系内容</span>
            <div class="info"><?php echo ($vo["nextstep"]); ?></div>
          </div>
          </a>
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
/*$("#leads").click(function(){
    $(this).addClass("class","active");
    $("#leadspool").attr("class","");
});
$("#leadspool").click(function(){
    $(this).attr("class","active");
    $("#leads").attr("class","");
});*/

$("#leadspool").click(function(){
   if ($($(this)).href==String(window.location))
       $(this).addClass("active");
});
</script>
</body>
</html>