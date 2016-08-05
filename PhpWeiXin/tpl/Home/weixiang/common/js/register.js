var phoneReg = new RegExp("^[1][3,4,5,8][0-9]{9}$");
var secs = 60 * 2;
var wait = secs * 1000;

$(document).ready(function () {

    /*************************注册过程开始*****************************/

    $("#Phone").blur(function () {

        if (phoneCheckForRight()) {
            $("#Phone").removeClass("yz_style");
            $("#phoneStatus > span").addClass("vhidden");
            $("#phoneStatus > span").eq(0).removeClass("vhidden");
        }
        else {
            $("#Phone").addClass("yz_style");
            $("#phoneStatus > span").addClass("vhidden");
            $("#phoneStatus > span").eq(1).removeClass("vhidden");
            $("#phoneStatus > span").eq(2).removeClass("vhidden");
        }
    });

    $("#repassword").blur(function () {
        if (pwdCheckForSame()) {
            $("#repasswordStatus > span").addClass("vhidden");
            $("#repasswordStatus > span").eq(0).removeClass("vhidden");
        }
        else {
            $("#repasswordStatus > span").addClass("vhidden");
            $("#repasswordStatus > span").eq(1).removeClass("vhidden");
            $("#repasswordStatus > span").eq(2).removeClass("vhidden");
        }
    });

    $("#Password").keyup(function () {
        var pwd = $(this).val();
        checklevel(pwd);
    });

    $("#randCodeImg").click(function () {
        $("#refreshImg").click();
    })

    $("#refreshImg").click(function () {
        $("#randCodeImg").attr("src", "/CreateSafeCode/39/70/" + Math.random());
    });

    $("#sendRegister").click(function () {
        var check = pwdCheckForSame() && phoneCheckForRight() && safeCodeCheckForRight();
        if (!check) {
            whtr.dialog.alert("页面内容填写不正确");
            return false;
        }

        return true;
    });

    function pwdCheckForSame() {
        return $("#Password").val() == $("#repassword").val();
    }

    function phoneCheckForRight() {

        var phone = $("#Phone").val();

        if (phone.length < 6)
            return false;

        return phone != undefined && phone.length == 11 && phoneReg.test(phone);

    }

    function safeCodeCheckForRight() {
        return $("#RandCode").val() != "";
    }
    //
    function checkstr(str) {

        if (str >= 48 && str <= 57)//数字
        {
            return 1;
        }
        else if (str >= 65 && str <= 90)//大写字母
        {
            return 2;
        }
        else if (str >= 97 && str <= 122)//小写字母
        {
            return 3;
        }
        else//特殊字符
        {
            return 4;
        }
    }
    //
    function checkl(string) {
        n = false;
        s = false;
        t = false;
        l_num = 0;
        if (string.length < 6) {
            l_num = 1;
        }
        else {
            for (i = 0; i < string.length; i++) {
                asc = checkstr(string.charCodeAt(i));
                if (asc == 1 && n == false) { l_num += 1; n = true; }
                if ((asc == 2 || asc == 3) && s == false) { l_num += 1; s = true; }
                if (asc == 4 && t == false) { l_num += 1; t = true; }
            }
        }
        return l_num;
    }
    //表示层获取验证码
    function checklevel(psw) {

        thelev = checkl(psw)
        $(".fc999 span").hide();
        $(".fc999 span").removeClass("vhidden");
        switch (thelev) {
            default:
            case 1:
                $(".fc999 .ruo1").show();

                break;
            case 2:
                $(".fc999 .zhong2").show();
                break;
            case 3:
                $(".fc999 .qiang3").show();
                break;
        }
        $("#PwdSafeLevel").val(thelev);
    }

    /*************************注册过程开始*****************************/

    /*************************注册结果开始*****************************/


    $("#submitSafeCode").click(function () {
        var randCode = $("#RandCode").val();

        if (randCode == undefined || randCode == "") {
            whtr.dialog.alert("请填写验证码");
            return false;
        }

        return true;
    });

    var countdown = $("#Countdown").val();

    if (countdown > 0) {
        $("#showButton").hide();
        $("#refresh").removeClass("vhidden");
        $("#refresh").show();
        Timer(countdown);
    }

    $("#getSafeCode").click(function () {
        var phone = $("#Phone").val();
        if (phone == undefined || phone == "" || phone.length == 0) {
            return;
        }

        $.ajax({
            type: "post",
            url: "/CustomerAuth/SendSafeCodeForRegister",
            async: false,
            data: { phone: phone },
            success: function (result) {
                if (result.State == -1) {
                    whtr.dialog.alert("发送失败，号码不正确!");
                    return;
                }
                else if (result.State == -3) {
                    whtr.dialog.alert("发送失败，号码不存在!");
                    return;
                }
                else if (result.State == 1) {
                    $("#showButton").hide();

                    $("#refresh").removeClass("vhidden");
                    $("#refresh").show();
                    Timer(wait);
                }
            }
        });
    });

    $("#speakRandcode").click(function () {
        var phone = $("#Phone").val();
        if (phone == undefined || phone == "" || phone.length == 0) {
            return;
        }

        $.ajax({
            type: "post",
            url: "/CustomerAuth/SpeakSafeCodeForRegister",
            async: false,
            data: { phone: phone },
            success: function (result) {
                if (result.State == -1) {
                    whtr.dialog.alert("发送失败，号码不正确!");
                    return;
                }
                else if (result.State == -3) {
                    whtr.dialog.alert("发送失败，号码不存在!");
                    return;
                }
                else if (result.State == 1) {
                    $("#showButton").hide();

                    $("#refresh").removeClass("vhidden");
                    $("#refresh").show();
                    Timer(wait);
                }
            }
        });
    })
});
/*************************注册结果结束*****************************/

function Timer(countDown) {
    for (i = 1; i <= (countDown / 100) ; i++) {
        setTimeout("timerRefresh(" + i + "," + countDown + ")", i * 100);
    }
}

function timerRefresh(sec, countDown) {
    if (sec == (countDown / 100)) {
        $("#refresh").hide();
        $("#showButton").show();
    } else {
        var wut = (countDown / 100) - sec;
        $("#refresh").val(parseInt(wut / 10, 10) + "秒后可以重新获取");
    }
}