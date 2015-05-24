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
                window.queryBuilder = new QueryBuilder();

                getBootstrap();
                window.togglerJS.init();

                appendMenuButton();
            }
        );
    };

    function appendMenuButton() {
        var $sortToggleButton = $("<input/>", {
            type: 'button',
            'class': 'togglers',
            id: 'sortToggle',
            value: 'Toggle Sorting'
        }).hide().click(function () {
            var state = window.uiHelper.toggleSorted() ? 'Sort On' : 'Sort Off';
            $(this).val(state);
            $('#toArray').fadeToggle();
            $("#resetList").fadeToggle();
        });

        var $sortedToArray = $("<input/>", {type: 'button', 'class': 'togglers', id: 'toArray', value: 'To Array'});
        $sortedToArray
            .hide()
            .click(window.queryBuilder.queryBuilder);

        var $resetTempList = $("<input/>", {type: 'button', 'class': 'togglers', id: 'resetList', value: 'Clear List'});
        $resetTempList.hide().click(function() {
            window.uiHelper.reset();
        });

        var buttons = [$sortToggleButton, $sortedToArray, $resetTempList];
        var $buttonsTable = $("<table/>", {id: 'buttonsContainer'});

        $.each(buttons, function(index, button){
            $buttonsTable.append($("<tr/>").append($("<td/>").append(button)));
        });

        $("body").append($buttonsTable);
    }

    function getBootstrap() {
        var $head = $("head");
        $head.append($("<link/>", {
            rel: "stylesheet",
            href: "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"
        }));
        $head.append($("<link/>", {
            rel: "stylesheet",
            href: "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css"
        }));
        $head.append($("<script/>", {src: "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"}));
    }

