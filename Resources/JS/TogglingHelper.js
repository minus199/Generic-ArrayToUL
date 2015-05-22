    /**
     * Created by minus on 5/22/15.
     */

    var Toggler = function () {

    };

    Toggler.prototype.init = function () {
        this.makeItToggle($("#recursive"));

        window.uiHelper = new UI();
        window.uiHelper.draggableDroppableSortable();

        this.appendEventListeners();
    };

    Toggler.prototype.appendEventListeners = function () {
        var that = this;
        $("#toggleTree").click(function () {
            that.toggleProjectTree($("#recursive"), $(this));
        });

        $("#tempContainer")
            .on('mouseenter', "input[type='radio']", showBox)
            .on('mouseleave', "input[type='radio']", hideBox);
    };

    Toggler.prototype.makeItToggle = function ($container) {
        $.each($container.find("[ref='title_sub_depth']"), function () {
            $(this).css(
                {
                    cursor: 'pointer',
                    'font-size': 13,
                    'text-shadow': '2px 8px 6px rgba(0,0,0,0.2), 0px -5px 35px rgba(255,130,255,0.3)'
                }
            );

            $(this).click(function () {
                $(this).next("ul").fadeToggle("fast", "linear", function () {
                    if (!$(this).next("ul").is(":visible"))
                        $(this).children("ul:visible").hide();
                });
            });

            $(this).next("ul").fadeOut("fast", "linear");
        });
    };

    Toggler.prototype.toggleProjectTree = function ($container, $button) {
        if ($button.attr("class") == "collapsedTrue") {
            $container.first().children().find("ul:hidden").fadeIn("fast", "linear");
            $button.attr("class", "collapsedFalse").val("-");
        } else {
            $container.first().children().find("ul:visible").fadeOut("fast", "linear");
            $button.attr("class", "collapsedTrue").val("+");
        }
    };