/**
 * xiufei.lu
 */
$(document).ready(function(){
    var initMiniCharts=function () {
        if (!jQuery().easyPieChart) {
            return;
        }
        // IE8 Fix: function.bind polyfill
        if (Metronic.isIE8() && !Function.prototype.bind) {
            Function.prototype.bind = function (oThis) {
                if (typeof this !== "function") {
                    // closest thing possible to the ECMAScript 5 internal IsCallable function
                    throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
                }

                var aArgs = Array.prototype.slice.call(arguments, 1),
                    fToBind = this,
                    fNOP = function () {},
                    fBound = function () {
                        return fToBind.apply(this instanceof fNOP && oThis ? this : oThis,
                            aArgs.concat(Array.prototype.slice.call(arguments)));
                    };

                fNOP.prototype = this.prototype;
                fBound.prototype = new fNOP();

                return fBound;
            };
        }

        $('.easy-pie-chart .number.transactions').easyPieChart({
            animate: 1000,
            size: 75,
            lineWidth: 3,
            barColor: Metronic.getBrandColor('yellow')
        });

        $('.easy-pie-chart .number.visits').easyPieChart({
            animate: 1000,
            size: 75,
            lineWidth: 3,
            barColor: Metronic.getBrandColor('green')
        });

        $('.easy-pie-chart .number.bounce').easyPieChart({
            animate: 1000,
            size: 75,
            lineWidth: 3,
            barColor: Metronic.getBrandColor('red')
        });

        $('.easy-pie-chart-reload').click(function () {
            $('.easy-pie-chart .number').each(function () {
                var newValue = Math.floor(100 * Math.random());
                $(this).data('easyPieChart').update(newValue);
                $('span', this).text(newValue);
            });
        });


    };

    initMiniCharts();
});
