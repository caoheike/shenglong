function padLeft(a, e) {
    return a = "" + a,
    a.length >= e ? a : this.padLeft("0" + a, e)
}
function cardcolor(a) {
    switch (str = "",
    Math.floor(a / 13)) {
    case 0:
        str = "spare";
        break;
    case 1:
        str = "heart";
        break;
    case 2:
        str = "daimond";
        break;
    case 3:
        str = "club"
    }
    return "images/poker/" + str + padLeft(a % 13 + 1, 2) + ".png"
}
function roadcountdown(a) {
    setTimeout(function() {
        a > 0 ? (roadcountdown(a -= 1e3),
        $(".waiting .t3").html(a / 1e3)) : $(".times span").html(0)
    }, 1e3)
}
function opencount(a, e, t) {
    return !!a
}
var baccarat = {
    automode: !0,
    flag: !1,
    period: "",
    dataurl: "ajax/index.php?ajaxHandler=GetBac1AwardData",
    hisurl: "ajax/index.php?ajaxHandler=GetBac1HistData",
    init: function() {
        function a() {
            e(),
            m.drawImage(g, 0, -s),
            $(".highlight img").attr("src", $(".highlight").data("poker")),
            h[$(".highlight").attr("id").substr(-1)] = !0,
            4 == h.slice(0, 4).filter(opencount).length && $("#playercard .pokercard").fadeIn(),
            5 == h.slice(0, 5).filter(opencount).length && $("#bankercard .pokercard").fadeIn(),
            6 == h.filter(opencount).length && setTimeout(n, 5e3)
        }
        function e() {
            m.clearRect(c, -s, -c, s),
            m.drawImage(_, 0, -s)
        }
        function t() {
            baccarat.automode ? ($("#auto_mode").html("切换直接开牌"),
            baccarat.automode = !1,
            clearTimeout(d),
            $("#donttouch").hide()) : ($("#auto_mode").html("切换眯牌模式"),
            baccarat.automode = !0,
            $("#donttouch").show(),
            r())
        }
        function r() {
            0 == h[b] ? ($("#opencard" + b).click(),
            $(".btn_area_vertical .btn_open").click(),
            b++,
            d = setTimeout(function() {
                b < 6 && r()
            }, p)) : 1 == h[b] && (b++,
            r())
        }
        function o(a) {
            setTimeout(function() {
                a > 0 ? (o(a -= 1e3),
                $(".times span").html(a / 1e3)) : ($("#donttouch").show(),
                $("#auto_mode").hide(),
                clearTimeout(d),
                r(),
                $(".times span").html(0))
            }, 1e3)
        }
        function i(a) {
            setTimeout(function() {
                a > 0 ? (i(a -= 1e3),
                $(".resultclose").html("关闭 " + a / 1e3)) : $(".resultclose").click()
            }, 1e3)
        }
        function n() {
            "BankerWin" == f ? $("#banker_animate").show() : "PlayerWin" == f ? $("#player_animate").show() : $("#draw_animate").show(),
            baccarat.flag = !1,
            i(1e4)
        }
        function l(a) {
            $("#opencard" + a).fadeIn(),
            ++a < 4 ? setTimeout(function() {
                l(a)
            }, 500) : (setTimeout(function() {
                $("#opencard0").click()
            }, 500),
            baccarat.automode && setTimeout(r, 500),
            $("#auto_mode").show())
        }
        var c, s, d, u = document.getElementById("canvas"), h = [!1, !1, !1, !1, !1, !1], p = 1800, m = u.getContext("2d"), b = 0, f = "", g = new Image, _ = new Image;
        $(".waiting .t3").html(""),
        _.onload = function() {
            c = this.width,
            s = this.height,
            u.width = s,
            u.height = c,
            $("#currentpoker").width(s),
            $("#currentpoker").height(c),
            m.fillStyle = "rgba(255,255,255,0)",
            m.save(),
            m.rotate(Math.PI / 180 * 90),
            m.drawImage(_, 0, -s),
            m.lineWidth = 2,
            m.strokeStyle = "rgba(255,255,255,0)",
            m.fillStyle = "#CCC"
        }
        ,
        _.src = "images/poker/poker.png";
        for (var v = setTimeout(";"), w = 0; w < v; w++)
            clearTimeout(w);
        $.ajax({
            url: baccarat.dataurl,
            type: "GET",
            dataType: "json",
            success: function(a) {
				
                if ($("#banker_animate,#player_animate,#draw_animate").hide(),
                $(".loading").fadeOut(),
                f = a.result.Result[0],
                $("#top_periods .text2").html("当前期数: " + (1+a.current.periodNumber)),
                "" == baccarat.period && (baccarat.period = a.current.periodNumber,
                baccarat.pokerroad()),
                baccarat.period != a.current.periodNumber && (baccarat.period = a.current.periodNumber,
                baccarat.flag = !0),
                !baccarat.flag)
                    return $("#content").show(),
                    $("#game_content").hide(),
                    a.next.awardTimeInterval < 0 ? ($(".waiting .t2").hide(),
                    $(".waiting .t3").html('等待开牌中<span class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></span>'),
                    setTimeout(baccarat.init, 1e3)) : (baccarat.pokerroad(),
                    $(".waiting .t2").show(),
                    roadcountdown(a.next.awardTimeInterval + (a.next.delayTimeInterval * 1e3)),
                    setTimeout(baccarat.init, a.next.awardTimeInterval + (a.next.delayTimeInterval * 1e3))),
                    !1;
                $("#auto_mode").hide(),
                $("#content").hide(),
                $("#game_content").show(),
                baccarat.automode ? $("#donttouch").show() : $("#donttouch").hide(),
                o(4e4);
                var t = ""
                  , r = "";
                $.each(h, function(e, o) {
                    e % 2 == 0 ? void 0 != a.result.Player[Math.floor(e / 2)] ? r += '<li class="pokercard" id="opencard' + e + '" data-status="false" data-poker="' + cardcolor(a.result.Player[Math.floor(e / 2)]) + '"><img src="images/poker/poker.png" width="100%"></li>' : (h[e] = !0,
                    r += '<li class="pokercard" id="opencard' + e + '" data-status="true"><p>不补牌</p></li>') : void 0 != a.result.Banker[Math.floor(e / 2)] ? t += '<li class="pokercard" id="opencard' + e + '" data-status="false" data-poker="' + cardcolor(a.result.Banker[Math.floor(e / 2)]) + '"><img src="images/poker/poker.png" width="100%"></li>' : (h[e] = !0,
                    t += '<li class="pokercard" id="opencard' + e + '" data-status="true"><p>不补牌</p></li>')
                }),
                $("#bankercard").html(t),
                $("#playercard").html(r),
                w = 0,
                l(0),
                $(".pokercard").click(function() {
                    if (baccarat.rotate = 0,
                    "true" == $(this).attr("data-status"))
                        return !1;
                    $("canvas").removeAttr("style"),
                    $(".btn_area_horizontal").hide(),
                    $(".btn_area_vertical").show();
                    var t, r = $(this).parent("ul").attr("id");
                    if ("bankercard" == r && (t = "B"),
                    "playercard" == r && (t = "P"),
                    $(":animated").length > 0 || $(this).children("img").attr("src") == $(this).data("poker") || -1 == $.inArray(t, a.result.Bet))
                        return !1;
                    "matrix(1, 0, 0, 1, 0, 0)" == $("canvas").css("transform") || "none" == $("canvas").css("transform") ? $(".btn_area_vertical").show() : $(".btn_area_horizontal").show(),
                    $(".game_left").fadeIn(),
                    $(".pokercard").removeClass("highlight"),
                    $(this).addClass("highlight");
                    var o = $(this).data("poker");
                    e(),
                    g.src = o
                })
            },
            error: function() {
                alert("出错了，重新整理中..."),
                window.location.reload()
            }
        }),
        $(".resultclose").unbind("click").click(function() {
            baccarat.init()
        }),
        $(".btn_rotate").unbind("click").click(function() {
            $(".btn_area_vertical,.btn_area_horizontal").hide(),
            $("canvas").stop().animate({
                wordSpacing: baccarat.rotate += 90
            }, {
                step: function(a) {
                    $("canvas").css("transform", "rotate(-" + a + "deg)")
                },
                complete: function(a) {
                    baccarat.rotate %= 360,
                    $("canvas").css("wordSpacing", baccarat.rotate),
                    baccarat.rotate % 180 != 0 ? $(".btn_area_horizontal").show() : $(".btn_area_vertical").show()
                }
            })
        }),
        $(".btn_area_vertical .btn_open").unbind("click").click(function() {
            $(".btn_area_vertical").hide(),
            $(this).stop().animate({
                wordSpacing: c
            }, {
                duration: 1e3,
                step: function(a) {
                    console.log(a),
                    m.save();
                    var t = c - a;
                    e(),
                    m.fillStyle = "#f9f9f8",
                    baccarat.rotate % 360 != 0 ? (m.drawImage(g, -t, -s),
                    m.fillRect(c - t - 70, -220, 60, 200),
                    m.shadowBlur = 100,
                    m.shadowColor = "#000",
                    m.beginPath(),
                    m.moveTo(0, 0),
                    m.lineTo(0, -s),
                    m.lineTo(a / 2, -s),
                    m.lineTo(a / 2, 0),
                    m.lineTo(0, 0)) : (m.drawImage(g, t, -s),
                    m.fillRect(t + 20, 30 - s, 60, 200),
                    m.shadowBlur = 100,
                    m.shadowColor = "#000",
                    m.beginPath(),
                    m.moveTo(c, 0),
                    m.lineTo(c, -s),
                    m.lineTo(c - a / 2, -s),
                    m.lineTo(c - a / 2, 0),
                    m.lineTo(c, 0)),
                    m.stroke(),
                    m.fill(),
                    m.clip(),
                    m.clearRect(c, -s, -c, s),
                    m.restore()
                },
                complete: function(e) {
                    $(this).css("word-spacing", 0),
                    $(".btn_area_vertical").hide(),
                    a()
                }
            })
        }),
        $(".btn_area_vertical .btn_lift").unbind("draggable").draggable({
            cursor: "point",
            containment: "parent",
            helper: "clone",
            axis: "y",
            drag: function(a, t) {
                $(".btn_area_vertical").css("opacity", 0),
                e();
                var r = c * (1 - t.position.top / t.helper.context.offsetTop);
                m.save(),
                m.rotate(Math.PI / 180 * -90),
                baccarat.rotate % 360 != 0 ? (m.drawImage(g, s - r, -s + r),
                m.restore(),
                m.save(),
                m.beginPath(),
                m.shadowBlur = 50,
                m.shadowColor = "#000",
                m.moveTo(0, -s),
                m.lineTo(0, -s + r),
                m.lineTo(r, -s),
                m.lineTo(0, -s)) : (m.drawImage(g, -c + r, c - r),
                m.restore(),
                m.save(),
                m.beginPath(),
                m.shadowBlur = 50,
                m.shadowColor = "#000",
                m.moveTo(c, 0),
                m.lineTo(c, -r),
                m.lineTo(c - r, 0),
                m.lineTo(c, 0)),
                m.stroke(),
                m.fill(),
                m.clip(),
                m.clearRect(c, -s, -c, s),
                m.closePath(),
                m.restore()
            },
            stop: function(t, r) {
                0 == r.position.top ? ($(".btn_area_vertical").hide(),
                $("canvas").css({
                    transform: "rotate(-90deg)",
                    wordSpacing: 90
                }),
                a()) : ($(this).css("opacity", .7),
                e()),
                $(".btn_area_vertical").css("opacity", 1)
            }
        }),
        $(".btn_area_vertical .btn_up").unbind("draggable").draggable({
            cursor: "point",
            containment: "parent",
            helper: "clone",
            axis: "y",
            drag: function(a, t) {
                $(".btn_area_vertical").css("opacity", 0),
                m.save(),
                e();
                var r = c * (t.position.top / t.helper.context.offsetTop)
                  , o = c - r;
                m.fillStyle = "#f9f9f8",
                baccarat.rotate % 360 != 0 ? (m.drawImage(g, -r, -s),
                m.fillRect(c - r - 75, -220, 60, 200),
                m.shadowBlur = 100,
                m.shadowColor = "#000",
                m.beginPath(),
                m.moveTo(0, 0),
                m.lineTo(0, -s),
                m.lineTo(o / 2, -s),
                m.lineTo(o / 2, 0),
                m.lineTo(0, 0)) : (m.drawImage(g, r, -s),
                m.fillRect(r + 20, 30 - s, 60, 200),
                m.shadowBlur = 100,
                m.shadowColor = "#000",
                m.beginPath(),
                m.moveTo(c, 0),
                m.lineTo(c, -s),
                m.lineTo(c - o / 2, -s),
                m.lineTo(c - o / 2, 0),
                m.lineTo(c, 0)),
                m.stroke(),
                m.fill(),
                m.clip(),
                m.clearRect(c, -s, -c, s),
                m.restore()
            },
            stop: function(t, r) {
                0 == r.position.top ? ($(".btn_area_vertical").hide(),
                a()) : ($(this).css("opacity", .7),
                e()),
                $(".btn_area_vertical").css("opacity", 1)
            }
        }),
        $(".btn_area_horizontal .btn_up").unbind("draggable").draggable({
            cursor: "point",
            containment: "parent",
            helper: "clone",
            axis: "y",
            drag: function(a, t) {
                $(".btn_area_horizontal").css("opacity", 0),
                m.save(),
                e();
                var r = s * (1 - t.position.top / t.helper.context.offsetTop);
                m.fillStyle = "#f9f9f8",
                baccarat.rotate % 360 != 90 ? (m.drawImage(g, 0, 2 * -s + r),
                m.fillRect(c - 70, -s + r - 220, 50, 200),
                m.shadowBlur = 100,
                m.shadowColor = "#000",
                m.beginPath(),
                m.moveTo(0, -s),
                m.lineTo(c, -s),
                m.lineTo(c, r / 2 - s),
                m.lineTo(0, r / 2 - s),
                m.lineTo(0, -s)) : (m.drawImage(g, 0, -r),
                m.fillRect(20, 30 - r, 50, 200),
                m.shadowBlur = 100,
                m.shadowColor = "#000",
                m.beginPath(),
                m.moveTo(0, 0),
                m.lineTo(c, 0),
                m.lineTo(c, -r / 2),
                m.lineTo(0, -r / 2),
                m.lineTo(0, 0)),
                m.stroke(),
                m.fill(),
                m.clip(),
                m.clearRect(c, -s, -c, s),
                m.restore()
            },
            stop: function(t, r) {
                0 == r.position.top ? ($(".btn_area_horizontal").hide(),
                a()) : ($(this).css("opacity", .7),
                e()),
                $(".btn_area_horizontal").css("opacity", 1)
            }
        }),
        $(".btn_area_horizontal .btn_open").unbind("click").click(function() {
            $(".btn_area_horizontal").hide(),
            $(this).stop().animate({
                wordSpacing: s
            }, {
                step: function(a) {
                    m.save(),
                    m.clearRect(0, -s, -c, s),
                    m.drawImage(_, 0, -s);
                    var e = a;
                    m.fillStyle = "#f9f9f8",
                    baccarat.rotate % 360 != 90 ? (m.drawImage(g, 0, 2 * -s + e),
                    m.fillRect(c - 70, -s + e - 220, 50, 200),
                    m.shadowBlur = 100,
                    m.shadowColor = "#000",
                    m.beginPath(),
                    m.moveTo(0, -s),
                    m.lineTo(c, -s),
                    m.lineTo(c, e / 2 - s),
                    m.lineTo(0, e / 2 - s),
                    m.lineTo(0, -s)) : (m.drawImage(g, 0, -e),
                    m.fillRect(20, 30 - e, 50, 200),
                    m.shadowBlur = 100,
                    m.shadowColor = "#000",
                    m.beginPath(),
                    m.moveTo(0, 0),
                    m.lineTo(c, 0),
                    m.lineTo(c, -e / 2),
                    m.lineTo(0, -e / 2),
                    m.lineTo(0, 0)),
                    m.stroke(),
                    m.fill(),
                    m.clip(),
                    m.clearRect(c, -s, -c, s),
                    m.restore()
                },
                complete: function(e) {
                    $(this).css("word-spacing", 0),
                    $(".btn_area_horizontal").hide(),
                    a()
                }
            })
        }),
        $("#game .game_right a.btn_open").unbind("click").click(function() {
            t()
        })
    },
    pokerroad: function() {
        var a = 0;
        $.get(baccarat.hisurl, function(e) {
            for (var t = new Array, r = 0, o = 0, i = "", n = !1, l = "", c = "", s = 0, d = 0, u = !1, h = "", p = "", m = 0, b = 0, f = !1, g = "", _ = "", v = 0, w = 0, T = 0; T < $(".pokerroad tr:eq(0) td").length; T++)
                t[T] = 0;
            $(".paper1 table tr td,.pokerroad tr td").html(""),
            $.each(e, function(e, T) {
                var k = a % 6
                  , y = Math.floor(a / 6)
                  , q = "";
                if (-1 != $.inArray("BankerWin", T.Result) ? q += '<p class="bgcircle_red">庄</p>' : -1 != $.inArray("PlayerWin", T.Result) ? q += '<p class="bgcircle_blue">闲</p>' : -1 != $.inArray("Draw", T.Result) && (q += '<p class="bgcircle_green">和</p>'),
                -1 != $.inArray("PlayerPair", T.Result) && (q += '<div class="dot_blue"></div>'),
                -1 != $.inArray("BankerPair", T.Result) && (q += '<div class="dot_red"></div>'),
                $(".paper1 table tr:eq(" + k + ") td:eq(" + y + ")").html(q),
                q = "",
                "" == i)
                    i = T.Result[0];
                else if ("Draw" == T.Result[0]) {
                    var R = $(".dalu tr:eq(" + r + ") td:eq(" + o + ") .note_green");
                    1 == R.length && ("" == R.html() ? R.html(2) : R.html(parseInt(R.html()) + 1)),
                    $(".dalu tr:eq(" + r + ") td:eq(" + o + ") .note_green")
                } else
                    i != T.Result[0] ? (i = T.Result[0],
                    r = 0,
                    o = $(".dalu tr:eq(0) td:empty:first").index()) : "" == $(".dalu tr:eq(" + (r + 1) + ") td:eq(" + o + ")").html() ? r++ : o++;
                switch (T.Result[0]) {
                case "BankerWin":
                    q = '<p class="note_red"></p>';
                    break;
                case "PlayerWin":
                    q = '<p class="note_blue"></p>';
                    break;
                case "Draw":
                    q = $(".dalu tr:eq(" + r + ") td:eq(" + o + ") .note_green").length > 0 ? "" : '<p class="note_green"></p>'
                }
                if ($(".dalu tr:eq(" + r + ") td:eq(" + o + ")").append(q),
                0 == r && 0 == o && "Draw" == T.Result[0])
                    t[0]++;
                else if ("Draw" != T.Result[0]) {
                    var x = $(".dalu tr:eq(0) td:not(:empty):last").index();
                    t[-1 == x ? 0 : x]++
                }
                (1 == r && 1 == o || 0 == r && 2 == o) && (n = !0),
                (1 == r && 2 == o || 0 == r && 3 == o) && (u = !0),
                (1 == r && 3 == o || 0 == r && 4 == o) && (f = !0),
                n && "Draw" != T.Result[0] && (0 == r ? t[x - 2] == t[x - 1] ? (q = '<div class="hollow_circle_red"></div>',
                l = "r") : (q = '<div class="hollow_circle_blue"></div>',
                l = "b") : t[x] >= t[x - 1] + 2 || t[x - 1] >= t[x] ? (q = '<div class="hollow_circle_red"></div>',
                l = "r") : (q = '<div class="hollow_circle_blue"></div>',
                l = "b"),
                "" == c ? c = l : c == l ? "" == $(".dayanlu tr:eq(" + (s + 1) + ") td:eq(" + d + ")").html() ? s++ : d++ : (c = l,
                s = 0,
                d = $(".dayanlu tr:eq(0) td:empty:first").index()),
                $(".dayanlu tr:eq(" + s + ") td:eq(" + d + ")").html(q)),
                u && "Draw" != T.Result[0] && (0 == r ? t[x - 3] == t[x - 1] ? (q = '<div class="solid_circle_red"></div>',
                h = "r") : (q = '<div class="solid_circle_blue"></div>',
                h = "b") : t[x] >= t[x - 2] + 2 || t[x - 2] >= t[x] ? (q = '<div class="solid_circle_red"></div>',
                h = "r") : (q = '<div class="solid_circle_blue"></div>',
                h = "b"),
                "" == p ? p = h : p == h ? "" == $(".xiaolu tr:eq(" + (m + 1) + ") td:eq(" + b + ")").html() ? m++ : b++ : (p = h,
                m = 0,
                b = $(".xiaolu tr:eq(0) td:empty:first").index()),
                $(".xiaolu tr:eq(" + m + ") td:eq(" + b + ")").html(q)),
                f && "Draw" != T.Result[0] && (0 == r ? t[x - 4] == t[x - 1] ? (q = '<div class="slash_red"></div>',
                g = "r") : (q = '<div class="slash_blue"></div>',
                g = "b") : t[x] >= t[x - 3] + 2 || t[x - 3] >= t[x] ? (q = '<div class="slash_red"></div>',
                g = "r") : (q = '<div class="slash_blue"></div>',
                g = "b"),
                "" == _ ? _ = g : _ == g ? "" == $(".xiaoqianglu tr:eq(" + (v + 1) + ") td:eq(" + w + ")").html() ? v++ : w++ : (_ = g,
                v = 0,
                w = $(".xiaoqianglu tr:eq(0) td:empty:first").index()),
                $(".xiaoqianglu tr:eq(" + v + ") td:eq(" + w + ")").html(q)),
                a++
            })
        }, "json")
    }
};
$(document).ready(function(a) {
    var e = new Image
      , t = 0;
    e.onload = function() {
        ++t < 51 && (e.src = cardcolor(t))
    }
    ,
    e.src = cardcolor(t),
    baccarat.init()
});
