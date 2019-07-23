$(function(){
    $(".mod-tools a").mouseenter(function(){
        var self=$(this);
        self.addClass("active");
        self.stop().animate({
            "width":"100px"
        });
    });
    $(".mod-tools a").mouseleave(function(){
        var self=$(this);
        self.removeClass("active");
        self.stop().animate({
            "width":"0"
        });
    });
    $(".mod-list .active").attr("data-class","active").animate({"width":"200px"});
    $(".mod-list a").mouseenter(function(){
        var self=$(this),
            now = $(".mod-list a[class=active]");
        if(self.hasClass("active")){
            return
        }
        now.removeClass().attr("data-class","active");
        setTimeout(function(){
            now.stop().animate({"width":"105px"});
        },10);
        self.addClass("active");
        self.stop().animate({
            "width":"200px"
        });
    });
    $(".mod-list a").mouseleave(function(){
        var self=$(this),
            now = $(".mod-list a[data-class=active]");
        if(self == now){
            alter("")
        }
        self.removeClass("active");
        self.stop().animate({
            "width":"105px"
        });
        now.addClass("active");
        setTimeout(function(){
            now.stop().animate({"width":"200px"});
        },10);
    });
    // $("#tools").affix({
    //     offset: {
    //         top: 200,
    //         bottom: 300
    //     }
    // });
    $(".case-hover").click(function(){
       var panel_id = $(this).attr("panel_id")
       if (panel_id && panel_id.length > 0) {
         panel_id = (panel_id*1) + 1
         $('#slider-case').anythingSlider(panel_id)
       };
       showModal("modalCase");
    });
    $(".modal-close").click(function(){
        var id=$(this).parents(".modal").attr("id");
        hideModal(id);
    });
    $(".J-tips").on("focus","input",function(){
        var $this = $(this),
            $tips = $this.parents(".li-input").next(".li-error");
        $tips.hide();
    });
});
function showModal(id){
    $("body").append('<div class="modal-backdrop fade active"></div>');
    $("#"+id).addClass("active").show();
}
function hideModal(id){
    $("#"+id).removeClass("active").hide();
    $(".modal-backdrop").remove();
}
