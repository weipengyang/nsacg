
$("#flash a[banner]").each(function (index) {
    $(this).attr("id", "flash" + (index + 1))
    if (index == 0)
        $(this).show();
    else
        $(this).hide();
})
$(".flash_bar div").each(function (index) {
    $(this).attr("id", "f" + (index + 1))
    $(this).attr("funid", (index + 1))

    if (index == 0)
        $(this).attr("class", "dq");
    else
        $(this).attr("class", "no");
})
/*Ð¡µãµã»÷*/
$(".flash_bar div").click(function () {
    var funId = $(this).attr("funid")
    changeflash(funId);
})

var ImgCount = $("a[id^='flash']").length;
var currentindex = 1;
$("#flashBg").css("background-color", $("#flash1").attr("name"));

function changeflash(i) {
    currentindex = i;
    for (j = 1; j <= ImgCount; j++) {
        if (j == i) {
            $("#flash" + j).fadeIn("normal");
            $("#flash" + j).css("display", "block");
            $("#f" + j).removeClass();
            $("#f" + j).addClass("dq");
            $("#flashBg").css("background-color", $("#flash" + j).attr("name"));
        } else {
            $("#flash" + j).css("display", "none");
            $("#f" + j).removeClass();
            $("#f" + j).addClass("no");
        }
    }
}
function startAm() {
    timerID = setInterval("timer_tick()", 3000);
}
function stopAm() {
    clearInterval(timerID);
}
function timer_tick() {
    currentindex = currentindex >= ImgCount ? 1 : (parseInt(currentindex) + 1);
    changeflash(currentindex);
}
$(document).ready(function () {
    $(".flash_bar div").mouseover(function () {
        stopAm();
    }).mouseout(function () {
        startAm();
    });
    startAm();
});