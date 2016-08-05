function tab(btn,main,cur_class){
	btn.click(function(){
		var index = btn.index(this);
		btn.removeClass(cur_class).eq(index).addClass(cur_class);
		main.hide().eq(index).show();
	})
}