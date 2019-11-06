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
    <h1 class="abe-header-title">日程</h1>
    <a href="javascript:getdates();"  class="next-btn" id="addevent"><i class="iconfont mrg_5">&#xe63f;</i><?php echo L('NEW_SCHEDULE');?></a> </header>
  <div class="time" style="display: ">
    <div class="timebox">
      <div id="time-date" style="max-width:640px;"></div>
    </div>
  </div>
  <div class="schedule">
    <ul class="zcml kcpp pdt_10" id="ul">
        <div id="infone">
        <?php if(is_array($event)): $i = 0; $__LIST__ = $event;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo U('Eventm/view',array('event_id'=>$vo['event_id']));?>">
            <li id="infotwo">
                <input type="hidden" id="event_id" name="<?php echo ($vo["event_id"]); ?>" value="<?php echo ($vo["event_id"]); ?>"/>
        <div class="tbt"><em class="tip"></em><span class="abe-fl" >
            <i id="start_time"> <?php echo ($vo["start_date"]); ?></i>
          <em class="abe-space">-</em>
            <i id="end_time"> <?php echo ($vo["end_date"]); ?></i>
        </span><!--<a href="<?php echo U('Eventm/delete',array('event_id'=>$vo['event_id']));?>" onclick="return confirmd()" class="abe-fr pbtn pb-red pbtn-sxm">删除</a>-->
            <!--<a href="javascript:showtab(3,'是否删除该条数据')"  class="abe-fr pbtn pb-red pbtn-sxm">删除</a>
          <a href="<?php echo U('Eventm/edit',array('event_id'=>$vo['event_id']));?>" class="abe-fr pbtn pbtn-sxm mrg_10">编辑</a>-->
        </div>
        <a href="<?php echo U('Eventm/view',array('event_id'=>$vo['event_id']));?>" class="pdl_15 pdr_15 pdt_5 pdt_5 abe-block">
          <div id="description"> <?php echo ($vo["description"]); ?></div></a>
            </li></a><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
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
  <script src="__PUBLIC__/waps/js/calendar.js"></script>
</div>
<div class="popup-bg" style="display: none"></div>
<script>
    function getdates(){
        var days=$('.selected').text();
        var day=days.substr(0,2);
        console.log(day);
        var year=$('.calendar-display:first').text();
        if(!day){
            var nowDate=new Date();
            var days=nowDate.getDate();
            day=days;
        }
        var dates=year+'/'+day;
        /*var str="/index.php?m=Eventm&a=add&start_date="+dates;*/
        var str="<?php echo U('Eventm/add');?>"+'&start_date='+dates;
        /*str=str.replace("[c]",dates);*/
        window.location.href=str;
    }


   $(function(){
	   //日期
	   var date_width = $(".timebox").width();

		if(date_width>640)
		{
			$('#time-date').calendar({
			width: 640,
			height: 640,
		});
		}else
		{
			$('#time-date').calendar({
			width: date_width,
			height: date_width,
		});
		}
		$('#time-date').height(date_width);
	    $(".rw-tab").tabs();
	   //其他
   });


    $(function(){
       $('.days li').click(function () {
           if ($('#infotwo').length>0) {
               $('li').remove('#infotwo');
           }
          var day=$(this).text();
          var year=$('.calendar-display:first').text();
           $.ajax({
               url : "<?php echo U('Eventm/eveInfo');?>",
               type: "POST",
               data:{"year": year, "day" : day },
               async: true,
               success: function(msg) {
                   var data='';
                   if (msg!=''){
                       data=eval("("+msg+")");
                   }
                   var html="";
                   if (data){
                       for(var i=0;i<data.length;i++){
                       var ls = data[i];
                       html+='<a href="<?php echo U('eventm/view',array('event_id'=>"'+ls.event_id+'"));?>"><li id="infotwo"><input type="hidden" id="event_id" name="'+ls.event_id+'" value="'+ls.event_id+'"/><div class="tbt">'+'<em class="tip"></em>'+'<span class="abe-fl" >'+
                       '<i id="start_time">'+ls.start_date+'</i>'+
                       '<em class="abe-space">'+'&#45;'+'</em>'+
                       '<i id="end_time">'+ls.end_date +'</i>'+
                       '</span>'+''+''+
                       ''+''+''+
                       '</div>'+
                       '<a href="<?php echo U('eventm/view',array('event_id'=>"'+ls.event_id+'"));?>" class="pdl_15 pdr_15 pdt_5 pdt_5 abe-block">'+
                       '<div id="description">'+ls.description +'</div>'+'</a></li></a>';
                       }
                       $("#infone").html(html);
                   }
               }
           });
       });
    });

   $(function(){
       $.ajax({
           url: "<?php echo U('Eventm/evePoint');?>",
           success: function (msg) {
               var data = '';
               var month=$('.m').text();
               if (msg != '') {
                   data = eval("(" + msg + ")");
               }
               console.log("我好难啊");
               if (data) {
                   for (var i = 0; i < data.length; i++) {
                       var ls = data[i];
                       var m=ls.start_date;
                       var n=ls.end_date;

                       $('.days:eq(1) li').each(function(){
                           var dayone=$(this).text();
                           var daytwo=parseInt($(this).text())+1;
                           var year=$('.calendar-display:first').text();
                           var dateone=year+'/'+dayone;
                           var datetwo=year+'/'+daytwo;
                           var datesone=Math.round(Date.parse(dateone)/1000);
                           var datestwo=Math.round(Date.parse(dateone)/1000)+86399;
                           if ((m<=datesone && n>=datestwo) ||
                               (m>=datesone && n<=datestwo) ||
                               (m<=datesone && n>=datesone && n<=datestwo) ||
                               (m>=datesone && m<=datestwo && n>=datestwo)
                           ){
                               if(!$(this).attr('class') ) {
                                   $(this).addClass('rc-tag');
                               }
                               if ($(this).attr('class')==" now") {
                                   $(this).addClass('rc-tag');
                               }
                           }
                       });

                       $('.days:eq(2) li').each(function(){
                           var dayone=$(this).text();
                           var daytwo=parseInt($(this).text())+1;
                           var years=$('.calendar-display:first').text();
                           var month=parseInt(years.substr(5))+1;
                           var year=years.substr(0,5);
                           var dateone=year+month+'/'+dayone;
                           var datetwo=year+month+'/'+daytwo;
                           var datesone=Math.round(Date.parse(dateone)/1000);
                           var datestwo=Math.round(Date.parse(dateone)/1000)+86399;
                           if ((m<=datesone && n>=datestwo) ||
                               (m>=datesone && n<=datestwo) ||
                               (m<=datesone && n>=datesone && n<=datestwo) ||
                               (m>=datesone && m<=datestwo && n>=datestwo)
                           ){
                               if(!$(this).attr('class')) {
                                   $(this).addClass('rc-tag');
                               }
                               if ($(this).attr('class')==" now") {
                                   $(this).addClass('rc-tag');
                               }
                           }
                       });

                       $('.days:eq(0) li').each(function(){
                           var dayone=$(this).text();
                           var daytwo=parseInt($(this).text())+1;
                           var years=$('.calendar-display:first').text();
                           var month=parseInt(years.substr(5))-1;
                           var year=years.substr(0,5);
                           var dateone=year+month+'/'+dayone;
                           var datetwo=year+month+'/'+daytwo;
                           var datesone=Math.round(Date.parse(dateone)/1000);
                           var datestwo=Math.round(Date.parse(dateone)/1000)+86399;
                           if ((m<=datesone && n>=datestwo) ||
                               (m>=datesone && n<=datestwo) ||
                               (m<=datesone && n>=datesone && n<=datestwo) ||
                               (m>=datesone && m<=datestwo && n>=datestwo)
                           ){
                               if(!$(this).attr('class')) {
                                   $(this).addClass('rc-tag');
                               }
                               if ($(this).attr('class')==" now") {
                                   $(this).addClass('rc-tag');
                               }
                           }
                       });

                   }
               }
           }
       });
   });

   function closetab(){
       var event_id=$("#event_id").val()
       $.ajax({
           url:"<?php echo U('Eventm/delete');?>",
           type:"POST",
           data:{'event_id':event_id},
           async: true,
           success:function(){
               var str="<?php echo U('Eventm/index');?>";
               window.location.href=str;
           }
       });
   }



</script>
</body>
</html>