(function () {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame']
                                   || window[vendors[x] + 'CancelRequestAnimationFrame'];
    }

    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function (callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function () { callback(currTime + timeToCall); },
              timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };

    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function (id) {
            clearTimeout(id);
        };
}());

(function () {
    window.GameTimer = function (fn, timeout) {
        this.__fn = fn;
        this.__timeout = timeout;
        this.__running = false;
        this.__lastTime = Date.now();
        this.__stopcallback = null;
    };

    window.GameTimer.prototype.__runer = function () {
        if (Date.now() - this.__lastTime >= this.__timeout) {
            this.__lastTime = Date.now();
            this.__fn.call(this);
        }
        if (this.__running) {
            window.requestAnimationFrame(this.__runer.bind(this));
        }
        else {
            if (typeof this.__stopcallback === 'function') {
                window.setTimeout(this.__stopcallback,100);
            }
        }
    };

    window.GameTimer.prototype.start = function () {
        this.__running = true;
        this.__runer();
    };
    window.GameTimer.prototype.stop = function (callback) {
        this.__running = false;
        this.__stopcallback = callback;
    };

})();