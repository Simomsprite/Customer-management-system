<?php if (!defined('THINK_PATH')) exit();?><script>
// spacer.gif

 $(document).ready(function(){
// 固定按钮

		var list = '';
		<?php if(is_array($history)): $i = 0; $__LIST__ = $history;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>var t_type = "<?php echo ($vo['type']); ?>";
			var t_name = '';
			if(t_type == "leads"){
				t_name = '<i>线</i>';
			}
			if(t_type == "customer"){
				t_name = '<i>客</i>';
			}
			if(t_type == "business"){
				t_name = '<i>商</i>';
			}
			if(t_type == "contract"){
				t_name = '<i>合</i>';
			}
			if(t_type == "examine"){
				t_name = '<i>审</i>';
			}
			if(t_type == "finance"){
				t_name = '<i>财</i>';
			}
			list += "<li class='($vo['type']}'>"+t_name+"<a href='<?php echo U($vo['type'].'/view', 'id='.$vo['id']);?>'><?php echo ($vo['title']); ?></a></li>";<?php endforeach; endif; else: echo "" ;endif; ?>
 	      $(document.body).prepend("<div style='position:fixed;left:0;top:35%;z-index:1000;'><div id='recordBtn' style='display:none;position:absolute;left:0px;top:0px;width:27px; height:27px;cursor: pointer;background: url(Public/img/record.png) left top no-repeat;'></div><div class='recordBox' style='z-index: 1000;display:block; position:absolute;left:-222px;top:26px;width:220px;min-height:30px; max-height:550px; background:#fff; border:1px solid #ccc'><span class='recordClose' style='position:absolute;right:15px; top:10px; display:block;width:9px; height:9px;cursor: pointer;  background:url(Public/img/record.png) -3px -29px;z-index:998;'></span><span class='fixed'></span><div class='title'><i></i>最近访问</div><ul style='list-style:none'>"+list+"</ul></div></div>");
 
     
// 设置样式

       $('.recordBox ul').css({'padding':'10px 10px'});
       $('.recordBox ul li').css({'line-height':'18px','display':'block','margin-bottom':'8px'});
       $('.recordBox ul li i').css({'display':'inline-block','float':'left','width':'18px','height':'18px','text-align':'center','border':'1px solid #ccc','border-radius':'100%','-webkit-border-radius':'100%','-moz-border-radius':'100%','-ms-border-radius':'100%','font-size':'12px','font-style':'normal','line-height':'18px','margin-right':'8px','color':'#585858','background':'#0088cc','color':'#ffffff'});
       $('.title').css({'position':'relative','font-size':'14px','font-family':'微软雅黑','height':'30px','line-height':'30px','border-bottom':'1px solid #ccc','background':'#e6e6e6','text-indent':'25px'});
       $('.title i').css({'position':'absolute','display':'block','width':'15px','height':'15px','background':'url(Public/img/record.png) left bottom no-repeat','left':'5px','top':'7px'});
       $('.fixed').css({'display':'block','width':'16px','height':'16px','background':'url(Public/img/record.png) left -50px no-repeat','position':'absolute','right':'8px','top':'6px','cursor':'pointer','z-index':'999'});


//隐藏展示
  $('#recordBtn').click(function(oEvent){
            var oleft = $('.recordBox').css('left');
            
            if(oleft == '-222px'){
            $('.recordBox').animate({left:'0px'},300);
            }else{
            $('.recordBox').animate({left:'-222px'},300);
             delCookie("yes");
             $('.fixed').show();
            }
            e = window.event || oEvent;
           if (e.stopPropagation)
           {
               e.stopPropagation();
           }else{
               e.cancelBubble = true;
           }
});

                                               //关闭按钮

                                              $('.recordClose').click(function(oEvent){
                                                                   delCookie("yes");
                                              	                   $('.recordBox').animate({left:'-222px'},300);
                                                                   $('.fixed').show();
                                                                   e = window.event || oEvent;
                                                                   if (e.stopPropagation)
                                                                   {
                                                                       e.stopPropagation();
                                                                   }else{
                                                                       e.cancelBubble = true;
                                                                   }
                                              });
                                              $('.recordClose').mouseenter(function(){
                                              	$(this).css('background-position','-3px -41px');
                                              });
                                              $('.recordClose').mouseleave(function(){
                                              	$(this).css('background-position','-3px -29px');
                                              });

          // 阻止冒泡
          $('.recordBox').click(function(oEvent){

                               $('.recordBox').animate({left:'0px'},300);
                               e = window.event || oEvent;
                               if (e.stopPropagation)
                               {
                                   e.stopPropagation();
                               }else{
                                   e.cancelBubble = true;
                               }
          });

         $(document).click(function () {   



                      var ofixed = getCookie('yes');
                      if (ofixed == '1') {
                          $('.recordBox').animate({left:'0px'},300); 
                        
                      } else{
                          $('.recordBox').animate({left:'-222px'},300);
                          $('.fixed').show();
                      }                  
                                                                                 
          });
               



$('.fixed').click(function(){

   SetCookie ("yes", "1") ; 
   
   $(this).hide();
});
var ofixed = getCookie('yes');
if (ofixed == '1') {
  $('.recordBox').animate({left:'0px'},10);
  $('.fixed').hide();
}

                     
});



//写cookies函数  cookie设置
function SetCookie(name,value)//两个参数，一个是cookie的名子，一个是值  
{  
    var Days = 30; //此 cookie 将被保存 30 天  
    var exp  = new Date();    //new Date("December 31, 9998");  
    exp.setTime(exp.getTime() + Days*24*60*60*1000);  
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();  
}  
function getCookie(name)//取cookies函数          
{  
    var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));  
     if(arr != null) return unescape(arr[2]); return null;  
  
}  
function delCookie(name)//删除cookie  
{  
    var exp = new Date();  
    exp.setTime(exp.getTime() - 1);  
    var cval=getCookie(name);  
    if(cval!=null) document.cookie= name + "="+cval+";expires="+exp.toGMTString();  
} 
</script>