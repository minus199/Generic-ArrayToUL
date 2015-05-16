/**
 * Created by minus on 5/11/15.
 */
    var $ = jQuery;

    $("document").ready(
        function () {
            var $container = $("#recursive");

            makeItToggle($container);
            $("#toggleTree").click(function () {
                toggleProjectTree($container, $(this));
            });
        }
    );

    function makeItToggle($container) {
        $.each($container.find("[ref='title_sub_depth']"), function () {
            $(this).css(
                {
                    cursor: 'pointer',
                    'font-size': 13,
                    'text-shadow': '2px 8px 6px rgba(0,0,0,0.2), 0px -5px 35px rgba(255,130,255,0.3)'
                }
            );

            $(this).click(function () {
                $(this).next("ul").toggle();
            });

            $(this).next("ul").hide();
        });
    }

    function toggleProjectTree($container, $button) {
        if ($button.attr("class") == "collapsedTrue"){
            $container.first().children().find("ul:hidden").show();
            $button.attr("class", "collapsedFalse").val("-");
        } else {
            $container.first().children().find("ul:visible").hide();
            $button.attr("class", "collapsedTrue").val("+");
        }
    }