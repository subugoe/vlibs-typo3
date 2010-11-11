(function() {
    var GBS_HOST = "http://books.google.com/";
    var GBS_LANG = "en";
    var h = true,
    j = null,
    k = false,
    l,
    m = this,
    r = function(a, b, c) {
        a = a.split(".");
        c = c || m; ! (a[0] in c) && c.execScript && c.execScript("var " + a[0]);
        for (var d; a.length && (d = a.shift());) if (!a.length && b !== undefined) c[d] = b;
        else c = c[d] ? c[d] : c[d] = {}
    },
    s = function() {},
    aa = function(a) {
        var b = typeof a;
        if (b == "object") if (a) {
            if (a instanceof Array || !(a instanceof Object) && Object.prototype.toString.call(a) == "[object Array]" || typeof a.length == "number" && typeof a.splice != "undefined" && typeof a.propertyIsEnumerable != "undefined" && !a.propertyIsEnumerable("splice")) return "array";
            if (! (a instanceof Object) && (Object.prototype.toString.call(a) == "[object Function]" || typeof a.call != "undefined" && typeof a.propertyIsEnumerable != "undefined" && !a.propertyIsEnumerable("call"))) return "function"
        } else return "null";
        else if (b == "function" && typeof a.call == "undefined") return "object";
        return b
    },
    t = function(a) {
        return aa(a) == "array"
    },
    u = function(a) {
        var b = aa(a);
        return b == "array" || b == "object" && typeof a.length == "number"
    },
    v = function(a) {
        return typeof a == "string"
    },
    ba = function(a) {
        return aa(a) == "function"
    },
    ca = function(a) {
        a = aa(a);
        return a == "object" || a == "array" || a == "function"
    },
    y = function(a) {
        return a[da] || (a[da] = ++ea)
    },
    da = "closure_uid_" + Math.floor(Math.random() * 2147483648).toString(36),
    ea = 0,
    fa = function(a, b) {
        var c = b || m;
        if (arguments.length > 2) {
            var d = Array.prototype.slice.call(arguments, 2);
            return function() {
                var f = Array.prototype.slice.call(arguments);
                Array.prototype.unshift.apply(f, d);
                return a.apply(c, f)
            }
        } else return function() {
            return a.apply(c, arguments)
        }
    },
    ga = function(a) {
        var b = Array.prototype.slice.call(arguments, 1);
        return function() {
            var c = Array.prototype.slice.call(arguments);
            c.unshift.apply(c, b);
            return a.apply(this, c)
        }
    },
    ha = Date.now ||
    function() {
        return + new Date
    },
    z = function(a, b) {
        function c() {}
        c.prototype = b.prototype;
        a.P = b.prototype;
        a.prototype = new c
    };
    var ia = function(a) {
        for (var b = 1; b < arguments.length; b++) {
            var c = String(arguments[b]).replace(/\$/g, "$$$$");
            a = a.replace(/\%s/, c)
        }
        return a
    },
    ja = /^[a-zA-Z0-9\-_.!~*'()]*$/,
    ka = function(a) {
        a = String(a);
        if (!ja.test(a)) return encodeURIComponent(a);
        return a
    },
    qa = function(a, b) {
        if (b) return a.replace(la, "&").replace(ma, "<").replace(na, ">").replace(oa, "\"");
        else {
            if (!pa.test(a)) return a;
            if (a.indexOf("&") != -1) a = a.replace(la, "&");
            if (a.indexOf("<") != -1) a = a.replace(ma, "<");
            if (a.indexOf(">") != -1) a = a.replace(na, ">");
            if (a.indexOf('"') != -1) a = a.replace(oa, "\"");
            return a
        }
    },
    la = /&/g,
    ma = /</g,
    na = />/g,
    oa = /\"/g,
    pa = /[&<>\"]/,
    sa = function(a, b) {
        for (var c = 0, d = String(a).replace(/^[\s\xa0]+|[\s\xa0]+$/g, "").split("."), f = String(b).replace(/^[\s\xa0]+|[\s\xa0]+$/g, "").split("."), e = Math.max(d.length, f.length), g = 0; c == 0 && g < e; g++) {
            var i = d[g] || "",
            n = f[g] || "",
            o = RegExp("(\\d*)(\\D*)", "g"),
            x = RegExp("(\\d*)(\\D*)", "g");
            do {
                var q = o.exec(i) || ["", "", ""],
                p = x.exec(n) || ["", "", ""];
                if (q[0].length == 0 && p[0].length == 0) break;
                c = ra(q[1].length == 0 ? 0: parseInt(q[1], 10), p[1].length == 0 ? 0: parseInt(p[1], 10)) || ra(q[2].length == 0, p[2].length == 0) || ra(q[2], p[2])
            }
            while (c == 0)
        }
        return c
    },
    ra = function(a, b) {
        if (a < b) return - 1;
        else if (a > b) return 1;
        return 0
    };
    var A = Array.prototype,
    ta = A.indexOf ?
    function(a, b, c) {
        return A.indexOf.call(a, b, c)
    }: function(a, b, c) {
        c = c == j ? 0: c < 0 ? Math.max(0, a.length + c) : c;
        if (v(a)) {
            if (!v(b) || b.length != 1) return - 1;
            return a.indexOf(b, c)
        }
        for (c = c; c < a.length; c++) if (c in a && a[c] === b) return c;
        return - 1
    },
    ua = A.forEach ?
    function(a, b, c) {
        A.forEach.call(a, b, c)
    }: function(a, b, c) {
        for (var d = a.length, f = v(a) ? a.split("") : a, e = 0; e < d; e++) e in f && b.call(c, f[e], e, a)
    },
    va = function() {
        return A.concat.apply(A, arguments)
    },
    wa = function(a) {
        if (t(a)) return va(a);
        else {
            for (var b = [], c = 0, d = a.length; c < d; c++) b[c] = a[c];
            return b
        }
    },
    xa = function(a) {
        for (var b = 1; b < arguments.length; b++) {
            var c = arguments[b],
            d;
            if (t(c) || (d = u(c)) && c.hasOwnProperty("callee")) a.push.apply(a, c);
            else if (d) for (var f = a.length, e = c.length, g = 0; g < e; g++) a[f + g] = c[g];
            else a.push(c)
        }
    },
    ya = function(a, b, c) {
        return arguments.length <= 2 ? A.slice.call(a, b) : A.slice.call(a, b, c)
    };
    var za = function(a, b) {
        this.x = a !== undefined ? a: 0;
        this.y = b !== undefined ? b: 0
    };
    za.prototype.l = function() {
        return new za(this.x, this.y)
    };
    var B = function(a, b) {
        this.width = a;
        this.height = b
    };
    B.prototype.l = function() {
        return new B(this.width, this.height)
    };
    B.prototype.floor = function() {
        this.width = Math.floor(this.width);
        this.height = Math.floor(this.height);
        return this
    };
    B.prototype.round = function() {
        this.width = Math.round(this.width);
        this.height = Math.round(this.height);
        return this
    };
    var Aa = function(a, b, c) {
        for (var d in a) b.call(c, a[d], d, a)
    },
    Ba = function(a) {
        var b = [],
        c = 0,
        d;
        for (d in a) b[c++] = a[d];
        return b
    },
    Ca = function(a) {
        var b = [],
        c = 0,
        d;
        for (d in a) b[c++] = d;
        return b
    },
    Da = ["constructor", "hasOwnProperty", "isPrototypeOf", "propertyIsEnumerable", "toLocaleString", "toString", "valueOf"],
    Ea = function(a) {
        for (var b, c, d = 1; d < arguments.length; d++) {
            c = arguments[d];
            for (b in c) a[b] = c[b];
            for (var f = 0; f < Da.length; f++) {
                b = Da[f];
                if (Object.prototype.hasOwnProperty.call(c, b)) a[b] = c[b]
            }
        }
    };
    var C,
    Fa,
    Ga,
    Ha,
    Ia,
    Ja,
    Ka = function() {
        return m.navigator ? m.navigator.userAgent: j
    },
    La = function() {
        return m.navigator
    };
    Ia = Ha = Ga = Fa = C = k;
    var D;
    if (D = Ka()) {
        var Ma = La();
        C = D.indexOf("Opera") == 0;
        Fa = !C && D.indexOf("MSIE") != -1;
        Ha = (Ga = !C && D.indexOf("WebKit") != -1) && D.indexOf("Mobile") != -1;
        Ia = !C && !Ga && Ma.product == "Gecko"
    }
    var Na = C,
    E = Fa,
    Oa = Ia,
    Pa = Ga,
    Qa = Ha,
    Ra = La();
    Ja = (Ra && Ra.platform || "").indexOf("Mac") != -1;
    var Sa = !!La() && (La().appVersion || "").indexOf("X11") != -1,
    Ta;
    a: {
        var Ua = "",
        F;
        if (Na && m.opera) {
            var Va = m.opera.version;
            Ua = typeof Va == "function" ? Va() : Va
        } else {
            if (Oa) F = /rv\:([^\);]+)(\)|;)/;
            else if (E) F = /MSIE\s+([^\);]+)(\)|;)/;
            else if (Pa) F = /WebKit\/(\S+)/;
            if (F) {
                var Wa = F.exec(Ka());
                Ua = Wa ? Wa[1] : ""
            }
        }
        if (E) {
            var Xa,
            Ya = m.document;
            Xa = Ya ? Ya.documentMode: undefined;
            if (Xa > parseFloat(Ua)) {
                Ta = String(Xa);
                break a
            }
        }
        Ta = Ua
    }
    var Za = Ta,
    $a = {},
    G = function(a) {
        return $a[a] || ($a[a] = sa(Za, a) >= 0)
    };
    var ab = !E || G("9");
    E && G("9");
    var bb = function(a) {
        var b;
        b = (b = a.className) && typeof b.split == "function" ? b.split(/\s+/) : [];
        var c;
        c = ya(arguments, 1);
        for (var d = 0, f = 0; f < c.length; f++) if (! (ta(b, c[f]) >= 0)) {
            b.push(c[f]);
            d++
        }
        c = d == c.length;
        a.className = b.join(" ");
        return c
    };
    var db = function(a, b) {
        Aa(b,
        function(c, d) {
            if (d == "style") a.style.cssText = c;
            else if (d == "class") a.className = c;
            else if (d == "for") a.htmlFor = c;
            else if (d in cb) a.setAttribute(cb[d], c);
            else a[d] = c
        })
    },
    cb = {
        cellpadding: "cellPadding",
        cellspacing: "cellSpacing",
        colspan: "colSpan",
        rowspan: "rowSpan",
        valign: "vAlign",
        height: "height",
        width: "width",
        usemap: "useMap",
        frameborder: "frameBorder",
        type: "type"
    },
    eb = function(a) {
        var b = a.document;
        if (Pa && !G("500") && !Qa) {
            if (typeof a.innerHeight == "undefined") a = window;
            b = a.innerHeight;
            var c = a.document.documentElement.scrollHeight;
            if (a == a.top) if (c < b) b -= 15;
            return new B(a.innerWidth, b)
        }
        a = b.compatMode == "CSS1Compat";
        if (Na && !G("9.50")) a = k;
        a = a ? b.documentElement: b.body;
        return new B(a.clientWidth, a.clientHeight)
    },
    gb = function() {
        var a = arguments,
        b = a[0],
        c = a[1];
        if (!ab && c && (c.name || c.type)) {
            b = ["<", b];
            c.name && b.push(' name="', qa(c.name), '"');
            if (c.type) {
                b.push(' type="', qa(c.type), '"');
                var d = {};
                Ea(d, c);
                c = d;
                delete c.type
            }
            b.push(">");
            b = b.join("")
        }
        b = document.createElement(b);
        if (c) if (v(c)) b.className = c;
        else t(c) ? bb.apply(j, [b].concat(c)) : db(b, c);
        a.length > 2 && fb(document, b, a, 2);
        return b
    },
    fb = function(a, b, c, d) {
        function f(g) {
            if (g) b.appendChild(v(g) ? a.createTextNode(g) : g)
        }
        for (d = d; d < c.length; d++) {
            var e = c[d];
            u(e) && !(ca(e) && e.nodeType > 0) ? ua(hb(e) ? wa(e) : e, f) : f(e)
        }
    },
    H = function(a) {
        return document.createElement(a)
    },
    ib = function(a) {
        return a && a.parentNode ? a.parentNode.removeChild(a) : j
    },
    hb = function(a) {
        if (a && typeof a.length == "number") if (ca(a)) return typeof a.item == "function" || typeof a.item == "string";
        else if (ba(a)) return typeof a.item == "function";
        return k
    };
    var jb = function() {},
    lb = function(a, b, c) {
        switch (typeof b) {
        case "string":
            kb(a, b, c);
            break;
        case "number":
            c.push(isFinite(b) && !isNaN(b) ? b: "null");
            break;
        case "boolean":
            c.push(b);
            break;
        case "undefined":
            c.push("null");
            break;
        case "object":
            if (b == j) {
                c.push("null");
                break
            }
            if (t(b)) {
                var d = b.length;
                c.push("[");
                for (var f = "", e = 0; e < d; e++) {
                    c.push(f);
                    lb(a, b[e], c);
                    f = ","
                }
                c.push("]");
                break
            }
            c.push("{");
            d = "";
            for (f in b) if (b.hasOwnProperty(f)) {
                e = b[f];
                if (typeof e != "function") {
                    c.push(d);
                    kb(a, f, c);
                    c.push(":");
                    lb(a, e, c);
                    d = ","
                }
            }
            c.push("}");
            break;
        case "function":
            break;
        default:
            throw Error("Unknown type: " + typeof b);
        }
    },
    mb = {
        '"': '\\"',
        "\\": "\\\\",
        "/": "\\/",
        "\u0008": "\\b",
        "\u000c": "\\f",
        "\n": "\\n",
        "\r": "\\r",
        "\t": "\\t",
        "\u000b": "\\u000b"
    },
    nb = /\uffff/.test("\uffff") ? /[\\\"\x00-\x1f\x7f-\uffff]/g: /[\\\"\x00-\x1f\x7f-\xff]/g,
    kb = function(a, b, c) {
        c.push('"', b.replace(nb,
        function(d) {
            if (d in mb) return mb[d];
            var f = d.charCodeAt(0),
            e = "\\u";
            if (f < 16) e += "000";
            else if (f < 256) e += "00";
            else if (f < 4096) e += "0";
            return mb[d] = e + f.toString(16)
        }), '"')
    };
    var I = function(a, b, c) {
        v(b) ? ob(a, c, b) : Aa(b, ga(ob, a))
    },
    ob = function(a, b, c) {
        a.style[pb(c)] = b
    },
    rb = function(a, b, c) {
        var d,
        f = Oa && (Ja || Sa) && G("1.9");
        if (b instanceof za) {
            d = b.x;
            b = b.y
        } else {
            d = b;
            b = c
        }
        a.style.left = qb(d, f);
        a.style.top = qb(b, f)
    },
    sb = function(a, b, c) {
        if (b instanceof B) {
            c = b.height;
            b = b.width
        } else {
            if (c == undefined) throw Error("missing height argument");
            c = c
        }
        a.style.width = qb(b, h);
        a.style.height = qb(c, h)
    },
    qb = function(a, b) {
        if (typeof a == "number") a = (b ? Math.round(a) : a) + "px";
        return a
    },
    tb = {},
    pb = function(a) {
        return tb[a] || (tb[a] = String(a).replace(/\-([a-z])/g,
        function(b, c) {
            return c.toUpperCase()
        }))
    },
    ub = function(a, b) {
        var c = a.style;
        if ("opacity" in c) c.opacity = b;
        else if ("MozOpacity" in c) c.MozOpacity = b;
        else if ("filter" in c) c.filter = b === "" ? "": "alpha(opacity=" + b * 100 + ")"
    };
    var vb = "StopIteration" in m ? m.StopIteration: Error("StopIteration"),
    wb = function() {};
    wb.prototype.next = function() {
        throw vb;
    };
    wb.prototype.la = function() {
        return this
    };
    var xb = function(a) {
        if (typeof a.t == "function") return a.t();
        if (v(a)) return a.split("");
        if (u(a)) {
            for (var b = [], c = a.length, d = 0; d < c; d++) b.push(a[d]);
            return b
        }
        return Ba(a)
    },
    yb = function(a, b, c) {
        if (typeof a.forEach == "function") a.forEach(b, c);
        else if (u(a) || v(a)) ua(a, b, c);
        else {
            var d;
            if (typeof a.v == "function") d = a.v();
            else if (typeof a.t != "function") if (u(a) || v(a)) {
                d = [];
                for (var f = a.length, e = 0; e < f; e++) d.push(e);
                d = d
            } else d = Ca(a);
            else d = void 0;
            f = xb(a);
            e = f.length;
            for (var g = 0; g < e; g++) b.call(c, f[g], d && d[g], a)
        }
    };
    var J = function(a) {
        this.h = {};
        this.c = [];
        var b = arguments.length;
        if (b > 1) {
            if (b % 2) throw Error("Uneven number of arguments");
            for (var c = 0; c < b; c += 2) this.p(arguments[c], arguments[c + 1])
        } else if (a) {
            if (a instanceof J) {
                b = a.v();
                c = a.t()
            } else {
                b = Ca(a);
                c = Ba(a)
            }
            for (var d = 0; d < b.length; d++) this.p(b[d], c[d])
        }
    };
    l = J.prototype;
    l.a = 0;
    l.Q = 0;
    l.t = function() {
        zb(this);
        for (var a = [], b = 0; b < this.c.length; b++) a.push(this.h[this.c[b]]);
        return a
    };
    l.v = function() {
        zb(this);
        return this.c.concat()
    };
    l.m = function(a) {
        return K(this.h, a)
    };
    l.remove = function(a) {
        if (K(this.h, a)) {
            delete this.h[a];
            this.a--;
            this.Q++;
            this.c.length > 2 * this.a && zb(this);
            return h
        }
        return k
    };
    var zb = function(a) {
        if (a.a != a.c.length) {
            for (var b = 0, c = 0; b < a.c.length;) {
                var d = a.c[b];
                if (K(a.h, d)) a.c[c++] = d;
                b++
            }
            a.c.length = c
        }
        if (a.a != a.c.length) {
            var f = {};
            for (c = b = 0; b < a.c.length;) {
                d = a.c[b];
                if (!K(f, d)) {
                    a.c[c++] = d;
                    f[d] = 1
                }
                b++
            }
            a.c.length = c
        }
    };
    J.prototype.o = function(a, b) {
        if (K(this.h, a)) return this.h[a];
        return b
    };
    J.prototype.p = function(a, b) {
        if (!K(this.h, a)) {
            this.a++;
            this.c.push(a);
            this.Q++
        }
        this.h[a] = b
    };
    J.prototype.l = function() {
        return new J(this)
    };
    J.prototype.la = function(a) {
        zb(this);
        var b = 0,
        c = this.c,
        d = this.h,
        f = this.Q,
        e = this,
        g = new wb;
        g.next = function() {
            for (;;) {
                if (f != e.Q) throw Error("The map has changed since the iterator was created");
                if (b >= c.length) throw vb;
                var i = c[b++];
                return a ? i: d[i]
            }
        };
        return g
    };
    var K = function(a, b) {
        return Object.prototype.hasOwnProperty.call(a, b)
    };
    var Ab = RegExp("^(?:([^:/?#.]+):)?(?://(?:([^/?#]*)@)?([\\w\\d\\-\\u0100-\\uffff.%]*)(?::([0-9]+))?)?([^?#]+)?(?:\\?([^#]*))?(?:#(.*))?$");
    var L = function(a, b) {
        var c;
        if (a instanceof L) {
            this.C(b == j ? a.g: b);
            Bb(this, a.k);
            Cb(this, a.K);
            Db(this, a.q);
            Eb(this, a.u);
            Fb(this, a.w);
            Gb(this, a.e.l());
            Hb(this, a.F)
        } else if (a && (c = String(a).match(Ab))) {
            this.C( !! b);
            Bb(this, c[1] || "", h);
            Cb(this, c[2] || "", h);
            Db(this, c[3] || "", h);
            Eb(this, c[4]);
            Fb(this, c[5] || "", h);
            Gb(this, c[6] || "", h);
            Hb(this, c[7] || "", h)
        } else {
            this.C( !! b);
            this.e = new M(j, this, this.g)
        }
    };
    l = L.prototype;
    l.k = "";
    l.K = "";
    l.q = "";
    l.u = j;
    l.w = "";
    l.F = "";
    l.ra = k;
    l.g = k;
    l.toString = function() {
        if (this.d) return this.d;
        var a = [];
        this.k && a.push(N(this.k, Ib), ":");
        if (this.q) {
            a.push("//");
            this.K && a.push(N(this.K, Ib), "@");
            var b;
            b = this.q;
            b = v(b) ? encodeURIComponent(b) : j;
            a.push(b);
            this.u != j && a.push(":", String(this.u))
        }
        if (this.w) {
            this.q && this.w.charAt(0) != "/" && a.push("/");
            a.push(N(this.w, Jb))
        } (b = String(this.e)) && a.push("?", b);
        this.F && a.push("#", N(this.F, Kb));
        return this.d = a.join("")
    };
    l.l = function() {
        var a = this.k,
        b = this.K,
        c = this.q,
        d = this.u,
        f = this.w,
        e = this.e.l(),
        g = this.F,
        i = new L(j, this.g);
        a && Bb(i, a);
        b && Cb(i, b);
        c && Db(i, c);
        d && Eb(i, d);
        f && Fb(i, f);
        e && Gb(i, e);
        g && Hb(i, g);
        return i
    };
    var Bb = function(a, b, c) {
        O(a);
        delete a.d;
        a.k = c ? b ? decodeURIComponent(b) : "": b;
        if (a.k) a.k = a.k.replace(/:$/, "");
        return a
    },
    Cb = function(a, b, c) {
        O(a);
        delete a.d;
        a.K = c ? b ? decodeURIComponent(b) : "": b;
        return a
    },
    Db = function(a, b, c) {
        O(a);
        delete a.d;
        a.q = c ? b ? decodeURIComponent(b) : "": b;
        return a
    },
    Eb = function(a, b) {
        O(a);
        delete a.d;
        if (b) {
            b = Number(b);
            if (isNaN(b) || b < 0) throw Error("Bad port number " + b);
            a.u = b
        } else a.u = j;
        return a
    },
    Fb = function(a, b, c) {
        O(a);
        delete a.d;
        a.w = c ? b ? decodeURIComponent(b) : "": b;
        return a
    },
    Gb = function(a, b, c) {
        O(a);
        delete a.d;
        if (b instanceof M) {
            a.e = b;
            a.e.J = a;
            a.e.C(a.g)
        } else {
            c || (b = N(b, Lb));
            a.e = new M(b, a, a.g)
        }
        return a
    },
    P = function(a, b, c) {
        O(a);
        delete a.d;
        a.e.p(b, c);
        return a
    },
    Mb = function(a, b, c) {
        O(a);
        delete a.d;
        t(c) || (c = [String(c)]);
        var d = a.e;
        b = b;
        c = c;
        Q(d);
        R(d);
        b = S(d, b);
        if (d.m(b)) {
            var f = d.b.o(b);
            if (t(f)) d.a -= f.length;
            else d.a--
        }
        if (c.length > 0) {
            d.b.p(b, c);
            d.a += c.length
        }
        return a
    },
    Hb = function(a, b, c) {
        O(a);
        delete a.d;
        a.F = c ? b ? decodeURIComponent(b) : "": b;
        return a
    },
    O = function(a) {
        if (a.ra) throw Error("Tried to modify a read-only Uri");
    };
    L.prototype.C = function(a) {
        this.g = a;
        this.e && this.e.C(a)
    };
    var Nb = /^[a-zA-Z0-9\-_.!~*'():\/;?]*$/,
    N = function(a, b) {
        var c = j;
        if (v(a)) {
            c = a;
            Nb.test(c) || (c = encodeURI(a));
            if (c.search(b) >= 0) c = c.replace(b, Ob)
        }
        return c
    },
    Ob = function(a) {
        a = a.charCodeAt(0);
        return "%" + (a >> 4 & 15).toString(16) + (a & 15).toString(16)
    },
    Ib = /[#\/\?@]/g,
    Jb = /[\#\?]/g,
    Lb = /[\#\?@]/g,
    Kb = /#/g,
    M = function(a, b, c) {
        this.j = a || j;
        this.J = b || j;
        this.g = !!c
    },
    Q = function(a) {
        if (!a.b) {
            a.b = new J;
            if (a.j) for (var b = a.j.split("&"), c = 0; c < b.length; c++) {
                var d = b[c].indexOf("="),
                f = j,
                e = j;
                if (d >= 0) {
                    f = b[c].substring(0, d);
                    e = b[c].substring(d + 1)
                } else f = b[c];
                f = decodeURIComponent(f.replace(/\+/g, " "));
                f = S(a, f);
                a.add(f, e ? decodeURIComponent(e.replace(/\+/g, " ")) : "")
            }
        }
    };
    l = M.prototype;
    l.b = j;
    l.a = j;
    l.add = function(a, b) {
        Q(this);
        R(this);
        a = S(this, a);
        if (this.m(a)) {
            var c = this.b.o(a);
            t(c) ? c.push(b) : this.b.p(a, [c, b])
        } else this.b.p(a, b);
        this.a++;
        return this
    };
    l.remove = function(a) {
        Q(this);
        a = S(this, a);
        if (this.b.m(a)) {
            R(this);
            var b = this.b.o(a);
            if (t(b)) this.a -= b.length;
            else this.a--;
            return this.b.remove(a)
        }
        return k
    };
    l.m = function(a) {
        Q(this);
        a = S(this, a);
        return this.b.m(a)
    };
    l.v = function() {
        Q(this);
        for (var a = this.b.t(), b = this.b.v(), c = [], d = 0; d < b.length; d++) {
            var f = a[d];
            if (t(f)) for (var e = 0; e < f.length; e++) c.push(b[d]);
            else c.push(b[d])
        }
        return c
    };
    l.t = function(a) {
        Q(this);
        if (a) {
            a = S(this, a);
            if (this.m(a)) {
                var b = this.b.o(a);
                if (t(b)) return b;
                else {
                    a = [];
                    a.push(b)
                }
            } else a = []
        } else {
            b = this.b.t();
            a = [];
            for (var c = 0; c < b.length; c++) {
                var d = b[c];
                t(d) ? xa(a, d) : a.push(d)
            }
        }
        return a
    };
    l.p = function(a, b) {
        Q(this);
        R(this);
        a = S(this, a);
        if (this.m(a)) {
            var c = this.b.o(a);
            if (t(c)) this.a -= c.length;
            else this.a--
        }
        this.b.p(a, b);
        this.a++;
        return this
    };
    l.o = function(a, b) {
        Q(this);
        a = S(this, a);
        if (this.m(a)) {
            var c = this.b.o(a);
            return t(c) ? c[0] : c
        } else return b
    };
    l.toString = function() {
        if (this.j) return this.j;
        if (!this.b) return "";
        for (var a = [], b = 0, c = this.b.v(), d = 0; d < c.length; d++) {
            var f = c[d],
            e = ka(f);
            f = this.b.o(f);
            if (t(f)) for (var g = 0; g < f.length; g++) {
                b > 0 && a.push("&");
                a.push(e);
                f[g] !== "" && a.push("=", ka(f[g]));
                b++
            } else {
                b > 0 && a.push("&");
                a.push(e);
                f !== "" && a.push("=", ka(f));
                b++
            }
        }
        return this.j = a.join("")
    };
    var R = function(a) {
        delete a.S;
        delete a.j;
        a.J && delete a.J.d
    };
    M.prototype.l = function() {
        var a = new M;
        if (this.S) a.S = this.S;
        if (this.j) a.j = this.j;
        if (this.b) a.b = this.b.l();
        return a
    };
    var S = function(a, b) {
        var c = String(b);
        if (a.g) c = c.toLowerCase();
        return c
    };
    M.prototype.C = function(a) {
        if (a && !this.g) {
            Q(this);
            R(this);
            yb(this.b,
            function(b, c) {
                var d = c.toLowerCase();
                if (c != d) {
                    this.remove(c);
                    this.add(d, b)
                }
            },
            this)
        }
        this.g = a
    };
    var Pb; ! E || G("9");
    E && G("8");
    var T = function() {};
    T.prototype.aa = k;
    T.prototype.L = function() {
        if (!this.aa) {
            this.aa = h;
            this.n()
        }
    };
    T.prototype.n = function() {};
    var U = function(a, b) {
        this.type = a;
        this.currentTarget = this.target = b
    };
    z(U, T);
    U.prototype.n = function() {
        delete this.type;
        delete this.target;
        delete this.currentTarget
    };
    U.prototype.A = k;
    U.prototype.O = h;
    var V = function(a, b) {
        a && this.M(a, b)
    };
    z(V, U);
    l = V.prototype;
    l.target = j;
    l.relatedTarget = j;
    l.offsetX = 0;
    l.offsetY = 0;
    l.clientX = 0;
    l.clientY = 0;
    l.screenX = 0;
    l.screenY = 0;
    l.button = 0;
    l.keyCode = 0;
    l.charCode = 0;
    l.ctrlKey = k;
    l.altKey = k;
    l.shiftKey = k;
    l.metaKey = k;
    l.ta = k;
    l.ba = j;
    l.M = function(a, b) {
        var c = this.type = a.type;
        this.target = a.target || a.srcElement;
        this.currentTarget = b;
        var d = a.relatedTarget;
        if (d) {
            if (Oa) try {
                d = d.nodeName && d
            } catch(f) {
                d = j
            }
        } else if (c == "mouseover") d = a.fromElement;
        else if (c == "mouseout") d = a.toElement;
        this.relatedTarget = d;
        this.offsetX = a.offsetX !== undefined ? a.offsetX: a.layerX;
        this.offsetY = a.offsetY !== undefined ? a.offsetY: a.layerY;
        this.clientX = a.clientX !== undefined ? a.clientX: a.pageX;
        this.clientY = a.clientY !== undefined ? a.clientY: a.pageY;
        this.screenX = a.screenX || 0;
        this.screenY = a.screenY || 0;
        this.button = a.button;
        this.keyCode = a.keyCode || 0;
        this.charCode = a.charCode || (c == "keypress" ? a.keyCode: 0);
        this.ctrlKey = a.ctrlKey;
        this.altKey = a.altKey;
        this.shiftKey = a.shiftKey;
        this.metaKey = a.metaKey;
        this.ta = Ja ? a.metaKey: a.ctrlKey;
        this.state = a.state;
        this.ba = a;
        delete this.O;
        delete this.A
    };
    l.n = function() {
        V.P.n.call(this);
        this.relatedTarget = this.currentTarget = this.target = this.ba = j
    };
    var W = function(a, b) {
        this.fa = b;
        this.r = [];
        if (a > this.fa) throw Error("[goog.structs.SimplePool] Initial cannot be greater than max");
        for (var c = 0; c < a; c++) this.r.push(this.i ? this.i() : {})
    };
    z(W, T);
    W.prototype.i = j;
    W.prototype.$ = j;
    W.prototype.s = function() {
        if (this.r.length) return this.r.pop();
        return this.i ? this.i() : {}
    };
    var Rb = function(a, b) {
        a.r.length < a.fa ? a.r.push(b) : Qb(a, b)
    },
    Qb = function(a, b) {
        if (a.$) a.$(b);
        else if (ca(b)) if (ba(b.L)) b.L();
        else for (var c in b) delete b[c]
    };
    W.prototype.n = function() {
        W.P.n.call(this);
        for (var a = this.r; a.length;) Qb(this, a.pop());
        delete this.r
    };
    var Sb;
    var Tb = (Sb = "ScriptEngine" in m && m.ScriptEngine() == "JScript") ? m.ScriptEngineMajorVersion() + "." + m.ScriptEngineMinorVersion() + "." + m.ScriptEngineBuildVersion() : "0";
    var Ub = function() {},
    Vb = 0;
    l = Ub.prototype;
    l.key = 0;
    l.B = k;
    l.R = k;
    l.M = function(a, b, c, d, f, e) {
        if (ba(a)) this.da = h;
        else if (a && a.handleEvent && ba(a.handleEvent)) this.da = k;
        else throw Error("Invalid listener argument");
        this.H = a;
        this.ia = b;
        this.src = c;
        this.type = d;
        this.capture = !!f;
        this.U = e;
        this.R = k;
        this.key = ++Vb;
        this.B = k
    };
    l.handleEvent = function(a) {
        if (this.da) return this.H.call(this.U || this.src, a);
        return this.H.handleEvent.call(this.H, a)
    };
    var Wb,
    Xb,
    Yb,
    Zb,
    $b,
    ac,
    bc,
    cc,
    dc,
    ec,
    fc; (function() {
        function a() {
            return {
                a: 0,
                f: 0
            }
        }
        function b() {
            return []
        }
        function c() {
            var p = function(w) {
                return g.call(p.src, p.key, w)
            };
            return p
        }
        function d() {
            return new Ub
        }
        function f() {
            return new V
        }
        var e = Sb && !(sa(Tb, "5.7") >= 0),
        g;
        ac = function(p) {
            g = p
        };
        if (e) {
            Wb = function() {
                return i.s()
            };
            Xb = function(p) {
                Rb(i, p)
            };
            Yb = function() {
                return n.s()
            };
            Zb = function(p) {
                Rb(n, p)
            };
            $b = function() {
                return o.s()
            };
            bc = function() {
                Rb(o, c())
            };
            cc = function() {
                return x.s()
            };
            dc = function(p) {
                Rb(x, p)
            };
            ec = function() {
                return q.s()
            };
            fc = function(p) {
                Rb(q, p)
            };
            var i = new W(0, 600);
            i.i = a;
            var n = new W(0, 600);
            n.i = b;
            var o = new W(0, 600);
            o.i = c;
            var x = new W(0, 600);
            x.i = d;
            var q = new W(0, 600);
            q.i = f
        } else {
            Wb = a;
            Xb = s;
            Yb = b;
            Zb = s;
            $b = c;
            bc = s;
            cc = d;
            dc = s;
            ec = f;
            fc = s
        }
    })();
    var X = {},
    Y = {},
    Z = {},
    gc = {},
    hc = function(a, b, c, d, f) {
        if (b) if (t(b)) {
            for (var e = 0; e < b.length; e++) hc(a, b[e], c, d, f);
            return j
        } else {
            d = !!d;
            var g = Y;
            b in g || (g[b] = Wb());
            g = g[b];
            if (! (d in g)) {
                g[d] = Wb();
                g.a++
            }
            g = g[d];
            var i = y(a),
            n;
            g.f++;
            if (g[i]) {
                n = g[i];
                for (e = 0; e < n.length; e++) {
                    g = n[e];
                    if (g.H == c && g.U == f) {
                        if (g.B) break;
                        return n[e].key
                    }
                }
            } else {
                n = g[i] = Yb();
                g.a++
            }
            e = $b();
            e.src = a;
            g = cc();
            g.M(c, e, a, b, d, f);
            c = g.key;
            e.key = c;
            n.push(g);
            X[c] = g;
            Z[i] || (Z[i] = Yb());
            Z[i].push(g);
            if (a.addEventListener) {
                if (a == m || !a.Z) a.addEventListener(b, e, d)
            } else a.attachEvent(ic(b), e);
            return c
        } else throw Error("Invalid event type");
    },
    jc = function(a, b, c, d, f) {
        if (t(b)) {
            for (var e = 0; e < b.length; e++) jc(a, b[e], c, d, f);
            return j
        }
        a = hc(a, b, c, d, f);
        X[a].R = h;
        return a
    },
    kc = function(a, b, c, d, f) {
        if (t(b)) {
            for (var e = 0; e < b.length; e++) kc(a, b[e], c, d, f);
            return j
        }
        d = !!d;
        a: {
            e = Y;
            if (b in e) {
                e = e[b];
                if (d in e) {
                    e = e[d];
                    a = y(a);
                    if (e[a]) {
                        a = e[a];
                        break a
                    }
                }
            }
            a = j
        }
        if (!a) return k;
        for (e = 0; e < a.length; e++) if (a[e].H == c && a[e].capture == d && a[e].U == f) return lc(a[e].key);
        return k
    },
    lc = function(a) {
        if (!X[a]) return k;
        var b = X[a];
        if (b.B) return k;
        var c = b.src,
        d = b.type,
        f = b.ia,
        e = b.capture;
        if (c.removeEventListener) {
            if (c == m || !c.Z) c.removeEventListener(d, f, e)
        } else c.detachEvent && c.detachEvent(ic(d), f);
        c = y(c);
        f = Y[d][e][c];
        if (Z[c]) {
            var g = Z[c],
            i = ta(g, b);
            i >= 0 && A.splice.call(g, i, 1);
            g.length == 0 && delete Z[c]
        }
        b.B = h;
        f.ga = h;
        mc(d, e, c, f);
        delete X[a];
        return h
    },
    mc = function(a, b, c, d) {
        if (!d.N) if (d.ga) {
            for (var f = 0, e = 0; f < d.length; f++) if (d[f].B) {
                var g = d[f].ia;
                g.src = j;
                bc(g);
                dc(d[f])
            } else {
                if (f != e) d[e] = d[f];
                e++
            }
            d.length = e;
            d.ga = k;
            if (e == 0) {
                Zb(d);
                delete Y[a][b][c];
                Y[a][b].a--;
                if (Y[a][b].a == 0) {
                    Xb(Y[a][b]);
                    delete Y[a][b];
                    Y[a].a--
                }
                if (Y[a].a == 0) {
                    Xb(Y[a]);
                    delete Y[a]
                }
            }
        }
    },
    nc = function(a, b, c) {
        var d = 0,
        f = a == j,
        e = b == j,
        g = c == j;
        c = !!c;
        if (f) Aa(Z,
        function(n) {
            for (var o = n.length - 1; o >= 0; o--) {
                var x = n[o];
                if ((e || b == x.type) && (g || c == x.capture)) {
                    lc(x.key);
                    d++
                }
            }
        });
        else {
            a = y(a);
            if (Z[a]) {
                a = Z[a];
                for (f = a.length - 1; f >= 0; f--) {
                    var i = a[f];
                    if ((e || b == i.type) && (g || c == i.capture)) {
                        lc(i.key);
                        d++
                    }
                }
            }
        }
        return d
    },
    ic = function(a) {
        if (a in gc) return gc[a];
        return gc[a] = "on" + a
    },
    pc = function(a, b, c, d, f) {
        var e = 1;
        b = y(b);
        if (a[b]) {
            a.f--;
            a = a[b];
            if (a.N) a.N++;
            else a.N = 1;
            try {
                for (var g = a.length, i = 0; i < g; i++) {
                    var n = a[i];
                    if (n && !n.B) e &= oc(n, f) !== k
                }
            } finally {
                a.N--;
                mc(c, d, b, a)
            }
        }
        return Boolean(e)
    },
    oc = function(a, b) {
        var c = a.handleEvent(b);
        a.R && lc(a.key);
        return c
    };
    ac(function(a, b) {
        if (!X[a]) return h;
        var c = X[a],
        d = c.type,
        f = Y;
        if (! (d in f)) return h;
        f = f[d];
        var e,
        g;
        if (Pb === undefined) Pb = E && !m.addEventListener;
        if (Pb) {
            var i;
            if (! (i = b)) a: {
                i = "window.event".split(".");
                for (var n = m; e = i.shift();) if (n[e]) n = n[e];
                else {
                    i = j;
                    break a
                }
                i = n
            }
            e = i;
            i = h in f;
            n = k in f;
            if (i) {
                if (e.keyCode < 0 || e.returnValue != undefined) return h;
                a: {
                    var o = k;
                    if (e.keyCode == 0) try {
                        e.keyCode = -1;
                        break a
                    } catch(x) {
                        o = h
                    }
                    if (o || e.returnValue == undefined) e.returnValue = h
                }
            }
            o = ec();
            o.M(e, this);
            e = h;
            try {
                if (i) {
                    for (var q = Yb(), p = o.currentTarget; p; p = p.parentNode) q.push(p);
                    g = f[h];
                    g.f = g.a;
                    for (var w = q.length - 1; ! o.A && w >= 0 && g.f; w--) {
                        o.currentTarget = q[w];
                        e &= pc(g, q[w], d, h, o)
                    }
                    if (n) {
                        g = f[k];
                        g.f = g.a;
                        for (w = 0; ! o.A && w < q.length && g.f; w++) {
                            o.currentTarget = q[w];
                            e &= pc(g, q[w], d, k, o)
                        }
                    }
                } else e = oc(c, o)
            } finally {
                if (q) {
                    q.length = 0;
                    Zb(q)
                }
                o.L();
                fc(o)
            }
            return e
        }
        d = new V(b, this);
        try {
            e = oc(c, d)
        } finally {
            d.L()
        }
        return e
    });
    var qc = function(a, b) {
        this.V = b || "en"
    },
    rc = function(a) {
        var b = H("img");
        b.src = ia("http://books.google.com/intl/%s/googlebooks/images/gbs_preview_button1.gif", a.V);
        b.border = 0;
        I(b, "cursor", "pointer");
        return b
    },
    sc = function(a, b, c) {
        this.V = c || "en";
        c = H("a");
        c.href = b;
        a.appendChild(c);
        a = rc(this);
        c.appendChild(a)
    };
    z(sc, qc);
    var tc = function(a, b, c) {
        this.V = c || "en";
        c = rc(this);
        a.appendChild(c);
        I(a, "cursor", "pointer");
        hc(a, "click", b)
    };
    z(tc, qc);
    var vc = function(a) {
        var b = document.getElementsByTagName("body")[0],
        c = H("div");
        ub(c, 0.5);
        I(c, {
            backgroundColor: "#333",
            position: "absolute",
            zIndex: 200
        });
        this.ma = c;
        var d = eb(window);
        sb(c, b.scrollWidth, Math.max(b.scrollHeight, d.height));
        rb(c, 0, 0);
        b.appendChild(c);
        this.I = H("div");
        I(this.I, {
            position: "absolute",
            zIndex: 201
        });
        b.appendChild(this.I);
        this.D = H("div");
        sb(this.D, 618, 500);
        I(this.D, {
            backgroundColor: "#333",
            position: "absolute",
            zIndex: 202
        });
        rb(this.D, 3, 3);
        ub(this.D, 0.3);
        this.I.appendChild(this.D);
        this.z = H("div");
        rb(this.z, 0, 0);
        I(this.z, {
            position: "absolute",
            padding: "8px",
            border: "1px solid #2c4462",
            backgroundColor: "#b4cffe",
            zIndex: 203
        });
        b = H("div");
        I(b, {
            backgroundColor: "#d8e8fd",
            fontSize: "16px",
            fontFamily: "Arial, sans-serif",
            fontWeight: "bold",
            padding: "2px 2px 2px 5px"
        });
        this.z.appendChild(b);
        c = H("img");
        c.src = "http://books.google.com/googlebooks/images/dialog_close_x.gif";
        c.width = 15;
        c.height = 15;
        I(c, {
            cursor: "pointer",
            position: "absolute",
            right: "11px",
            top: "11px"
        });
        jc(c, "click", fa(this.close, this));
        b.appendChild(c);
        c = H("div");
        c.innerHTML = " ";
        b.appendChild(c);
        this.Y = H("div");
        this.z.appendChild(this.Y);
        sb(this.Y, 600, 456);
        this.I.appendChild(this.z);
        uc(this.Y, a);
        b = eb(window);
        a = Math.max(0, (b.height - 500) / 2);
        c = !Pa && document.compatMode == "CSS1Compat" ? document.documentElement: document.body;
        a = Math.floor(a + (new za(c.scrollLeft, c.scrollTop)).y);
        b = Math.max(0, (b.width - 618) / 2);
        b = Math.floor(b);
        rb(this.I, b, a)
    };
    vc.prototype.close = function() {
        ua([this.z, this.ma, this.D], ib)
    };
    var wc = function(a, b) {
        this.J = new L(a);
        this.na = b ? b: "callback";
        this.X = 5E3
    },
    xc = 0;
    wc.prototype.send = function(a, b, c, d) {
        if (!document.documentElement.firstChild) {
            c && c(a);
            return j
        }
        d = d || "_" + (xc++).toString(36) + ha().toString(36);
        m._callbacks_ || (m._callbacks_ = {});
        var f = H("script"),
        e = j;
        if (this.X > 0) e = m.setTimeout(yc(d, f, a, c), this.X);
        c = this.J.l();
        for (var g in a) if (!a.hasOwnProperty || a.hasOwnProperty(g)) Mb(c, g, a[g]);
        if (b) {
            m._callbacks_[d] = zc(d, f, b, e);
            Mb(c, this.na, "_callbacks_." + d)
        }
        db(f, {
            type: "text/javascript",
            id: d,
            charset: "UTF-8",
            src: c.toString()
        });
        document.getElementsByTagName("head")[0].appendChild(f);
        return {
            ua: d,
            X: e
        }
    };
    var yc = function(a, b, c, d) {
        return function() {
            Ac(a, b, k);
            d && d(c)
        }
    },
    zc = function(a, b, c, d) {
        return function() {
            m.clearTimeout(d);
            Ac(a, b, h);
            c.apply(undefined, arguments)
        }
    },
    Ac = function(a, b, c) {
        m.setTimeout(function() {
            ib(b)
        },
        0);
        if (m._callbacks_[a]) if (c) delete m._callbacks_[a];
        else m._callbacks_[a] = s
    };
    var Bc = function() {};
    z(Bc, T);
    l = Bc.prototype;
    l.Z = h;
    l.W = j;
    l.addEventListener = function(a, b, c, d) {
        hc(this, a, b, c, d)
    };
    l.removeEventListener = function(a, b, c, d) {
        kc(this, a, b, c, d)
    };
    l.dispatchEvent = function(a) {
        a = a;
        if (v(a)) a = new U(a, this);
        else if (a instanceof U) a.target = a.target || this;
        else {
            var b = a;
            a = new U(a.type, this);
            Ea(a, b)
        }
        b = 1;
        var c,
        d = a.type,
        f = Y;
        if (d in f) {
            f = f[d];
            d = h in f;
            var e;
            if (d) {
                c = [];
                for (e = this; e; e = e.W) c.push(e);
                e = f[h];
                e.f = e.a;
                for (var g = c.length - 1; ! a.A && g >= 0 && e.f; g--) {
                    a.currentTarget = c[g];
                    b &= pc(e, c[g], a.type, h, a) && a.O != k
                }
            }
            if (k in f) {
                e = f[k];
                e.f = e.a;
                if (d) for (g = 0; ! a.A && g < c.length && e.f; g++) {
                    a.currentTarget = c[g];
                    b &= pc(e, c[g], a.type, k, a) && a.O != k
                } else for (c = this; ! a.A && c && e.f; c = c.W) {
                    a.currentTarget = c;
                    b &= pc(e, c, a.type, k, a) && a.O != k
                }
            }
            a = Boolean(b)
        } else a = h;
        return a
    };
    l.n = function() {
        Bc.P.n.call(this);
        nc(this);
        this.W = j
    };
    var Cc = function(a) {
        this.url = a;
        this.ja = j;
        this.ea = h
    };
    z(Cc, Bc);
    Cc.prototype.T = function() {
        if (this.ea) this.ea = k;
        else throw {};
    };
    Cc.prototype.s = function() {
        return this.ja
    };
    var $ = function(a) {
        Cc.call(this, a);
        this.sa = new wc(a);
        this.G = h
    };
    z($, Cc);
    $.prototype.T = function(a, b) {
        $.P.T.call(this, a, b);
        this.G = k;
        this.sa.send({},
        fa(this.qa, this, a), fa(this.pa, this, b))
    };
    $.prototype.qa = function(a, b) {
        if (!this.G) {
            this.ja = b;
            this.dispatchEvent("success");
            a && a(this.s());
            this.G = h
        }
    };
    $.prototype.pa = function(a) {
        if (!this.G) {
            this.dispatchEvent("error");
            a && a();
            this.G = h
        }
    };
    var Ec = function(a, b, c, d) {
        t(a) || (a = [a]);
        this.ca = a;
        this.ka = b;
        this.ha = c;
        b = new L(Dc);
        P(b, "bibkeys", a.join(","));
        P(b, "hl", GBS_LANG);
        P(b, "source", d || "previewlib"); (new $(b)).T(fa(this.oa, this))
    },
    Dc = (GBS_HOST || "http://books.google.com/") + "books?jscmd=viewapi";
    Ec.prototype.oa = function(a) {
        for (var b = 0; b < this.ca.length; b++) {
            var c = a[this.ca[b]];
            if (c) {
                var d = c.preview_url,
                f;
                if (f = d) {
                    f = c.preview;
                    c = c.embeddable;
                    c !== undefined || (c = h);
                    f = (f == "full" || f == "partial") && c
                }
                if (f) {
                    this.ka && this.ka(d);
                    return
                }
            }
        }
        this.ha && this.ha()
    };
    r("GBS_insertPreviewButtonLink",
    function(a, b) {
        var c = ga(Fc, (b || {}).alternativeUrl);
        Gc(a, c, "GBS_insertPreviewButtonLink")
    },
    void 0);
    r("GBS_insertPreviewButtonPopup",
    function(a) {
        Gc(a, Hc, "GBS_insertPreviewButtonPopup")
    },
    void 0);
    r("GBS_insertEmbeddedViewer",
    function(a, b, c) {
        Gc(a, ga(Ic, b, c), "GBS_insertEmbeddedViewer")
    },
    void 0);
    var Gc = function(a, b, c) {
        var d = Jc();
        new Ec(a,
        function(f) {
            b(d, f)
        },
        j, c)
    },
    Fc = function(a, b, c) {
        if (a) a = a;
        else {
            a = new L(c);
            if (Kc) {
                c = new L(GBS_HOST);
                Bb(a, c.k);
                Db(a, c.q);
                Eb(a, c.u);
                Fb(a, "/books/p/" + Kc)
            }
            P(a, "hl", Lc || "en");
            a = a.toString()
        }
        new sc(b, a, Lc)
    },
    Hc = function(a, b) {
        var c = ga(Mc, b);
        new tc(a, c, Lc)
    },
    Ic = function(a, b, c, d) {
        var f = H("div");
        c.appendChild(f);
        sb(f, a, b);
        uc(f, d)
    },
    uc = function(a, b) {
        var c = gb("iframe", {
            frameBorder: "0",
            width: "100%",
            height: "100%"
        });
        a.appendChild(c);
        var d = new L(b);
        P(d, "output", "embed");
        if (Nc) {
            var f = [];
            lb(new jb, Nc, f);
            Hb(d, ka(f.join("")))
        }
        c.src = d.toString()
    },
    Mc = function(a) {
        new vc(a)
    },
    Lc = "en";
    r("GBS_setLanguage",
    function(a) {
        Lc = a
    },
    void 0);
    r("GBS_setViewerOptions",
    function(a) {
        Nc = a
    },
    void 0);
    var Kc = j;
    r("GBS_setCobrandName",
    function(a) {
        Kc = a
    },
    void 0);
    var Nc = {},
    Jc = function() {
        var a = "__GBS_Button" + Oc++;
        document.write(ia('<span id="%s"></span>', a));
        return v(a) ? document.getElementById(a) : a
    },
    Oc = 0;
    hc(window, "unload",
    function() {
        nc()
    });
})();
