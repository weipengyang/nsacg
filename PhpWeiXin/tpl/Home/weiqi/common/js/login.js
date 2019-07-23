	var input_obj = jQuery('.form_txt');
		var bg_img = [
				{
					name:'input_bg1'
				},
				{
					name:'input_bg2'
				}
			]

		input_obj.focus(function(){
			var index = input_obj.index(this);
			jQuery('.input_bg').eq(index).addClass(bg_img[index].name);
		});

		input_obj.blur(function(){
			var index = input_obj.index(this);
			jQuery('.input_bg').eq(index).removeClass(bg_img[index].name);
		});

		var re = 0;



		jQuery('.remember').click(function(){
			if(re == 0){
				jQuery('.remember').addClass('re_hover');
				re = 1;
			}else{
				jQuery('.remember').removeClass('re_hover');
				setCookie('username',"",60)
				setCookie('password',"",60)
				re = 0;
			}
		});


		function getCookie(c_name)
		{
			if (document.cookie.length>0)
			{
			  c_start=document.cookie.indexOf(c_name + "=")
			  if (c_start!=-1)
				{
				c_start=c_start + c_name.length+1
				c_end=document.cookie.indexOf(";",c_start)
				if (c_end==-1) c_end=document.cookie.length
				return unescape(document.cookie.substring(c_start,c_end))
				}
			}
			return ""
		}

		function setCookie(c_name,value,expiredays)
		{
		var exdate=new Date()
		exdate.setDate(exdate.getDate()+expiredays)
		document.cookie=c_name+ "=" +escape(value)+
		((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
		}


		jQuery('.form_btn').click(function(){
			if(re == 1){
				var user = input_obj.eq(0).val();
				var pass = input_obj.eq(1).val();
				setCookie('username',user,60);
				setCookie('password',pass,60);
			}else{
				if ($("input[name='username']").val() == "") {
					alert("请填写您登陆的用户名");
					return false;
				}
				if ($("input[name='password']").val() == "") {
					alert("请填写您登陆的密码");
					return false;
				}
			}
		});


	jQuery('#land').click(function(){
		username=getCookie('username');
		password=getCookie('password');
		if(password !="" && password!= null && password!="" && password!=null){
			re = 1
			jQuery('.remember').addClass('re_hover');
			input_obj.eq(0).attr('value',username);
			input_obj.eq(1).attr('value',password);
		}else{
			re = 0;
			jQuery('.remember').removeClass('re_hover');
			setCookie('username',"",60);
			setCookie('password',"",60);
		}
		jQuery('.form_k').show()
	});
	jQuery('#mask_close').click(function(){
		jQuery('.form_k').hide()
	});