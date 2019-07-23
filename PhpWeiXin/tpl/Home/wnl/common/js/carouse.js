$.loadImage = function(url) {
  // Define a "worker" function that should eventually resolve or reject the deferred object.
  var loadImage = function(deferred) {
    var image = new Image();
   
    // Set up event handlers to know when the image has loaded
    // or fails to load due to an error or abort.
    image.onload = loaded;
    image.onerror = errored; // URL returns 404, etc
    image.onabort = errored; // IE may call this if user clicks "Stop"
   
    // Setting the src property begins loading the image.
    image.src = url;
   
    function loaded() {
      //unbindEvents();
      // Calling resolve means the image loaded sucessfully and is ready to use.
      deferred.resolve(image);
    }
    function errored() {
      ///unbindEvents();
      // Calling reject means we failed to load the image (e.g. 404, server offline, etc).
      deferred.reject(image);
    }
    function unbindEvents() {
      // Ensures the event callbacks only get called once.
      image.onload = null;
      image.onerror = null;
      image.onabort = null;
    }
  };
 
  // Create the deferred object that will contain the loaded image.
  // We don't want callers to have access to the resolve() and reject() methods,
  // so convert to "read-only" by calling `promise()`.
  return $.Deferred(loadImage).promise();
};

$.slideImg = function(btn,con){
    var $btn = $(btn),
    length = $(btn).find('span').length,
    $con = $(con),
    curImg = 0,
    num = curImg,
    timer,
    init = function(){
        $btn.find('span:eq(0)').attr("id","cur-ic");
        $('.banner-word').css({'left':'-600px',"opacity":"0","display":"none"});
        $('.banner-phone').css({'right':'-363px',"opacity":"0","display":"none"});
        
        $con.find('.banner-img').hide().eq(0).fadeIn(1000,iconShowPlay);
        function iconShowPlay(){
            $('.banner-phone').eq(0).animate({'right':"20px","opacity":"1"},500).css("display","block");
            $('.banner-word').eq(0).animate({'left':"0","opacity":"1"},500).css("display","block");
        }
    },
    play = function(){
        // if (!!window.ActiveXObject) {
        //     if (document.getElementById("loadimg01").readyState == 'complete') {
        //         $('#loading').hide();
        //     }
        // } else {
        //     if (document.getElementById("loadimg01").complete == true) {
        //         $('#loading').hide();
        //     }
        // }
        stopPlay();
        curImg++;
        if(curImg == length){
            curImg = 0;
        }
        $btn.find('span').attr("id","").eq(curImg).attr("id","cur-ic");
        $('.banner-word').css({'left':'-600px',"opacity":"0","display":"none"});
        $('.banner-phone').css({'right':'-363px',"opacity":"0","display":"none"});
        $con.find('.banner-img').hide().eq(curImg).fadeIn(1000,iconPlay);
        function iconPlay(){
            $('.banner-word').eq(curImg).animate({'left':"0","opacity":"1"},500).css("display","block");
            $('.banner-phone').eq(curImg).animate({'right':"20px","opacity":"1"},500).css("display","block");
        }
    },
    autoPlay = function(){
        stopPlay();
        timer = setTimeout(function(){
        play();
        autoPlay();
        },3000);
    },
    stopPlay = function(){
        clearTimeout(timer);
    };
    showImgPre = function(){
        stopPlay();
        curImg--;
        if(curImg == -1){
            curImg = 3;
        }
        $("#carouse-btn").find("span").attr("id","").end().find('span').eq(curImg).attr("id","cur-ic");
        $('.banner-word').css({'left':"-600px","opacity":"0","display":"none"});
        $('.banner-phone').css({'right':'-363px',"opacity":"0","display":"none"});
        $('.banner-img').hide().eq(curImg).fadeIn(1000,clickIconPlay);
        function clickIconPlay(){
            $('.banner-word').eq(curImg).animate({'left':"0","opacity":"1"},500).css("display","block");
            $('.banner-phone').eq(curImg).animate({'right':"20px","opacity":"1"},500).css("display","block");
        }
    }
    init();
    autoPlay();
    // hover
    $con.parent().hover(function(){
        stopPlay();
    },function(){
        autoPlay();
    });
    // click
    $btn.find('span').click(function(){
        $btn.find('span').attr("id","");
        $(this).attr("id","cur-ic");
        curImg = $(this).index() -1 ;
        play();
        return false;
    });
    timer2 = null;
    $('#pre').click(function(){
        if (timer2) {
            clearTimeout(timer2);
        };
        timer2 = setTimeout(showImgPre, 200);
        return false;
    });
    $('#next').click(function(){
        if(timer2){
            clearTimeout(timer2);
        }
        timer2 = setTimeout(play,200);
        return false;
    });
}
