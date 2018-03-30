	$(function(){
		$("#main-menu li.dropdown").hover(function(){$(this).addClass("open");},function(){$(this).removeClass("open");});
		$("#main-menu a").each(function() {
			if ($(this)[0].href == String(window.location)) {$(this).parentsUntil("#main-menu>ul>li").addClass("active");}
		});
		$.post(GV.ROOT+'user/index/isLogin',{},function(data){
			if(data.code==1){
				if(data.data.user.avatar){}
				$("#main-menu-user span.user-nickname").text(data.data.user.user_nickname?data.data.user.user_nickname:data.data.user.user_login);$("#main-menu-user li.login").show();$("#main-menu-user li.offline").hide();
			}else {
				$("#main-menu-user li.login").hide();$("#main-menu-user li.offline").show();
			}
		});
        ;(function($){
			$.fn.totop=function(opt){
				var scrolling=false;
				return this.each(function(){
					var $this=$(this);
					$(window).scroll(function(){
						if(!scrolling){
							var sd=$(window).scrollTop();
							if(sd>100){
								$this.fadeIn();
							}else{
								$this.fadeOut();
							}
						}
					});
					$this.click(function(){scrolling=true;$('html, body').animate({scrollTop : 0}, 500,function(){scrolling=false;$this.fadeOut();});});
				});
			};
		})(jQuery); 
		$("#backtotop").totop();
		/* lx 新增鼠标点击特效 */
		var a_idx = 0;
		jQuery(document).ready(function($) {
		    $("body").click(function(e) {
		var a = new Array("富强", "民主", "文明", "和谐", "自由", "平等", "公正" ,"法治", "爱国", "敬业", "诚信", "友善");
		var $i = $("<span/>").text(a[a_idx]);
		a_idx = (a_idx + 1) % a.length;
		var x = e.pageX,y = e.pageY;
		        $i.css({"z-index": 999999999999999999999999999999999999999999999999999999999999999999999,"top": y - 20,"left": x,"position": "absolute","font-weight": "bold","color": "#ff6651"});
		        $("body").append($i);
		        $i.animate({"top": y - 180,"opacity": 0},1500,
		function() {
		            $i.remove();
		        });
		    });
		});
		/*lx新增end*/
	});