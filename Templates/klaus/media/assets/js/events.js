var lang = {};
lang[0]="";
lang[1]="";
lang[2]="";
lang[3]="";
//don't know what's lang
var MuEvents = {};
MuEvents.text = [
    [lang[0], lang[1]],
    [lang[2], lang[3]]
];
MuEvents.sked = [
    ['Suvivor', 0, '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'],
    ['Crywolf', 0, '20:00'],
    ['Red Dragon', 0, '00:15', '06:00', '12:20', '18:00'],
    ['Golden invasion', 0, '00:02', '02:02', '04:02', '06:02', '08:02', '10:02', '12:02', '14:02', '16:02', '18:02', '20:02', '22:02'],
    ['Lunar Rabbit invasion', 0, '00:02', '02:02', '04:02', '06:02', '08:02', '10:02', '12:02', '14:02', '16:02', '18:02', '20:02', '22:02'],
    ['Fortune Pouch invasion', 0, '00:02', '02:02', '04:02', '06:02', '08:02', '10:02', '12:02', '14:02', '16:02', '18:02', '20:02', '22:02'],
    ['Blood Castle', 0, '00:00', '02:00', '04:00', '06:00', '08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'],
    ['Chaos Castle', 0, '01:00', '03:00', '05:00', '07:00', '09:00', '11:00', '13:00', '15:00', '17:00', '19:00', '21:00', '23:00'],
    ['Devil Square', 0, '01:30', '05:30', '09:30', '13:30','17:30', '21:30'],
    ['Illusion Temple', 0, '00:15', '03:15','07:15', '11:15', '15:15', '18:15', '21:15']];
MuEvents.init = function (e) {


    if (typeof e == "string") var g = new Date(new Date().toString().replace(/\d+:\d+:\d+/g, e));
    var f = (typeof e == "number" ? e : (g.getHours() * 60 + g.getMinutes()) * 60 + g.getSeconds()),
        q = MuEvents.sked,
        j = [];
    for (var a = 0; a < q.length; a++) {
        var n = q[a];
        for (var k = 2; k < q[a].length; k++) {
            var b = 0,
                p = q[a][k].split(":"),
                o = (p[0] * 60 + p[1] * 1) * 60,
                c = q[a][2].split(":");
            if (q[a].length - 1 == k && (o - f) < 0) b = 1;
            var r = b ? (1440 * 60 - f) + ((c[0] * 60 + c[1] * 1) * 60) : o - f;
            if (f <= o || b) {
                var l = Math.floor((r / 60) / 60),
                    l = l < 10 ? "0" + l : l,
                    d = Math.floor((r / 60) % 60),
                    d = d < 10 ? "0" + d : d,
                    u = r % 60,
                    u = u < 10 ? "0" + u : u;
                j.push('<li class="bTimeEvent"><img src="/klaus/Templates/klaus/img/icon-eventos.png"/>' + (l == 0 && !q[a][1] && d < 5 ? '' : '') + '<span><strong>Proximo Horário : ' + q[a][b ? 2 : k] + "</strong></span><p>" + n[0] + " : <span class='time_event'>" + (l + ":" + d + ":" + u) + "</span></p>" + (MuEvents.text[q[a][1]][+(l == 0 && d < (q[a][1] ? 1 : 5))]) + "</li>");
                break;
            };
        };
    };
	
    document.getElementById("events").innerHTML = j.join("");
    setTimeout(function () {
        MuEvents.init(f == 86400 ? 1 : ++f);
    }, 1000);
};