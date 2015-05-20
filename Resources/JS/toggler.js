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

            draggableDroppable();
        }
    );

    function draggableDroppable(){
        $(".regular-item").draggable(
            {
                revert: true,
                cursor:'move',
                start: function( event, ui ) { $(this).toggleClass('hovering-item'); },
                stop: function( event, ui ) { $(this).toggleClass('hovering-item'); }
            }
        );
        $("#tempContainer").droppable(
            {
                drop: function( event, ui ) {
                    var $newListItem = $("<li/>", {text: ui.draggable.text()});
                    $(this).append($newListItem);
                },
                tolerance: "pointer",
                accept: '.regular-item'
            }
        ).sortable();
    }

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
                $(this).next("ul").fadeToggle("fast", "linear", function(){
                    if(!$(this).next("ul").is(":visible"))
                        $(this).children("ul:visible").hide();
                });
            });

            $(this).next("ul").fadeOut("fast", "linear");
        });
    }

    function toggleProjectTree($container, $button) {
        if ($button.attr("class") == "collapsedTrue"){
            $container.first().children().find("ul:hidden").fadeIn("fast", "linear");
            $button.attr("class", "collapsedFalse").val("-");
        } else {
            $container.first().children().find("ul:visible").fadeOut("fast", "linear");
            $button.attr("class", "collapsedTrue").val("+");
        }
    }