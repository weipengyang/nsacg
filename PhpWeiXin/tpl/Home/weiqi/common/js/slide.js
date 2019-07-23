function slide(box,btn,btn_class,time){//box:进行卷入动画的包裹框，btn:按钮列表，btn_class:按钮选中状态的样式名，time:动画执行的间隔时间
	var i = 0,timeAnim;
	wid = parseInt(box.width());
	
	btn.click(function(){ //点击按钮滚动
		var eq = btn.index(this);
		i = eq;
		anim(i,box,btn,wid,btn_class);
	})
	
	timeAnim = setInterval(function(){//自动滚动
		i++;
		if(i>=btn.length){
			i=0;
		}
		anim(i,box,btn,wid,btn_class);
	},time);
	
	box.mouseover(function(){//在banner范围内清除定时滚动效果
		clearInterval(timeAnim)
	})
	box.mouseout(function(){
		timeAnim = setInterval(function(){
			i++;
			if(i>=btn.length){
				i=0;
			}
			anim(i,box,btn,wid,btn_class);
		},time);
	})
	
	btn.mouseover(function(){//在按钮范围内 清除定时滚动效果
		clearInterval(timeAnim)
	})
	btn.mouseout(function(){
		timeAnim = setInterval(function(){
			i++;
			if(i>=btn.length){
				i=0;
			}
			anim(i,box,btn,wid,btn_class);
		},time);
	})
	
	function anim(index,box,btn,wid,btn_class){//图片轮播
		btn.removeClass(btn_class).eq(index).addClass(btn_class);
		if(!box.is(":animated")){
			box.animate({scrollLeft:index*wid},500);
		}
	}
}