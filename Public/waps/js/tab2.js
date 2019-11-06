;(function($){
//abel.five-tabstyle v1.0 by 风一样的胖叔叔
         $.fn.extend({
                   "tabs":function()
						{   
						// tab 初始化
						// 逐个给tab按钮默认第一个加选中class	
						   $(this).find(".tab-title li").first().addClass("actived");
						// 逐个给tab内容默认显示第一个
						   $(this).find(".tab-content .item").hide();
						   $(this).find(".tab-content .item").first().show();
						//点击切换功能	
						   $(this).find(".tab-title li").each(function(index){  
								  $(this).click(function(){
								  $(this).siblings("li").removeClass("actived"); 
								  $(this).addClass("actived"); 
								  var tb_item = $(this).parents(".tab-box").find(".tab-content .item");
								  tb_item.hide();
								  tb_item.eq(index).show(); 
							   });
						 });
						 },
			  		"tabs2":function()
						{   
						// 选项卡嵌套解决方案	
						// tab 初始化
						// 逐个给tab按钮默认第一个加选中class	
						   $(this).find(".tab-title-chid li").first().addClass("actived");
						// 逐个给tab内容默认显示第一个
						   $(this).find(".tab-content-chid .item-chid").hide();
						   $(this).find(".tab-content-chid .item-chid").first().show();
						//点击切换功能	
						   $(this).find(".tab-title-chid li").each(function(index){  
								  $(this).click(function(){
								  $(this).siblings("li").removeClass("actived"); 
								  $(this).addClass("actived"); 
								  var tb_item = $(this).parents(".tab-box-chid").find(".tab-content-chid .item-chid");
								  tb_item.hide();
								  tb_item.eq(index).show(); 
							   });
						 });
						 }
                   });              
})(jQuery);
