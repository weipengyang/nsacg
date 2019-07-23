var one_posi = [
			{
				top:427,
				left:273
			},
			{
				top:427,
				left:658
			},
			{
				top:513,
				left:273
			},
			{
				top:513,
				left:658
			}
		];
		var one_p = [
			{
				top:90,
				left:-400
			},
			{
				top:90,
				left:1400
			},
			{
				top:640,
				left:-29
			},
			{
				top:640,
				left:1400
			}
		];
		var banner = jQuery('.banner');
		var dipan = jQuery('.one_di');
		var len = jQuery('.banner_list a').length;
		var dipan_len = dipan.length;
		var wid = parseInt(banner.width());
		var time,time1,tiem2,time3;
		var i = -1;
		time = setTimeout(function(){
			i++;
			if(i >= len){
				i = 0;
			}
			if(!banner.is(":animated")){
				banner.stop(true,true).animate({scrollLeft:i*wid},500,function(){
					clearInterval(time1)
					time1 = setInterval(function(){
						dazzle(i,banner);
						clearInterval(time1)
					},300)
				});
			}
			time3 = setTimeout(arguments.callee,8000)
		},1) 
		
		function dazzle(index,box){
			jQuery('.three_tit').css('top',-96+"px");
			jQuery('.three_text').css('opacity',0);
			jQuery('.t_posi6 img').css('margin-top',44+"px");
			jQuery('.dian img').css('margin-top',34+"px");
			jQuery('.t_posi6').css('top',240+'px');
			clearInterval(tiem2);
			for(var k=0; k<dipan_len; k++ ){
				dipan.eq(k).css({'top':one_p[k].top+"px",'left':one_p[k].left+"px"});
			}
			jQuery('.banner_anim_box').css("overflow","hidden");
			jQuery('.one_text').css('width',0+'px')
			jQuery('.iphone_img').css('margin-top',334+"px")
			jQuery('.light').css('top',-376+"px");
			jQuery('.two_pic').css('top',-606+'px')
			jQuery('.two_tit').hide();
			jQuery('.two_text').css('bottom',-392+"px");
			if(index == 0){
				for(var j=0; j<dipan_len; j++ ){
					dipan.eq(j).animate({top:one_posi[j].top+"px",left:one_posi[j].left+"px"},500,function(){
						if(!dipan.eq(0).is(":animated")){
							jQuery('.banner_anim_box').css("overflow","visible");
							jQuery('.one_text').animate({width:373+'px'},800)
							jQuery('.iphone_img').animate({marginTop:0+"px"},700,function(){
								jQuery('.light').animate({top:90+"px"},500);
							})
						}
					})
					
				}
			}
			if(index == 1){
				jQuery('.two_pic').animate({top:30+'px'},500,function(){
					jQuery('.two_tit').show(500);
					jQuery('.two_text').animate({'bottom':108+"px"},500);
				});
			}
			if(index == 2){
				jQuery('.three_tit').animate({'top':230+"px"},100,function(){
					jQuery('.three_tit').animate({'top':160+"px"},200);
					setTimeout(function(){
						jQuery('.three_text').animate({'opacity':1},500,function(){
							jQuery('.t_posi6 img').animate({marginTop:0+"px"},500,function(){
								jQuery('.dian img').animate({marginTop:0+"px"},500)
								tiem2 = setInterval(function(){
									jQuery('.t_posi6').animate({top:235+'px'},200,function(){
										jQuery('.t_posi6').animate({top:240+'px'},200)
									})
								},200)
							})
						});
					},400)
				});
			}
		};
		
		jQuery('.banner_box').hover(function(){
			jQuery('.ban_btn').show();
			jQuery('.ban_btn').animate({'opacity':1},500);
			clearTimeout(time3);
			clearTimeout(time);
		},function(){
			jQuery('.ban_btn').hide()
			jQuery('.ban_btn').animate({'opacity':0},500);
			time = setTimeout(function(){
				i++;
				if(i >= len){
					i = 0;
				}
				if(!banner.is(":animated")){
					banner.stop(true,true).animate({scrollLeft:i*wid},500,function(){
						clearInterval(time1)
						time1 = setInterval(function(){
							dazzle(i,banner);
							clearInterval(time1)
						},300)
					});
				}
				time3 = setTimeout(arguments.callee,8000)
			},8000) 
		})
		
		jQuery('.ban_btn').mouseover(function(){
			clearTimeout(time3);
			clearTimeout(time);
		});
		
		jQuery('.ban_btn').click(function(){
			var index = jQuery('.ban_btn').index(this);
			if(index == 0){
				i--;
				if(i < 0){
					i = 0;
				}
			}else{
				i++;
				if(i >= len){
					i = len-1;
				}
			}
			if(!banner.is(":animated")){
				banner.stop(true,true).animate({scrollLeft:i*wid},500,function(){
					clearInterval(time1)
					time1 = setInterval(function(){
						dazzle(i,banner);
						clearInterval(time1)
					},300)
				});
			}
		})