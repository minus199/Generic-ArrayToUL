    /**
     * Created by minus on 5/11/15.
     */
    var $ = jQuery;

    window.onload = function () {
        $("document").ready(
            function () {
                $.expr[":"].containsExact = function (obj, index, meta, stack) {
                    obj = $(obj).clone().children().remove().end();
                    return (obj.textContent || obj.innerText || $(obj).text() || "") == meta[3];
                };

                $.fn.ignore = function (sel) {
                    return this.clone().find(sel || ">*").remove().end();
                };

                window.togglerJS = new Toggler();

                window.togglerJS.init();
            }
        );
    };

