/**
 * Created by minus on 5/11/15.
 */
    var $ = jQuery;

    window.onload = function() {
        $("document").ready(
            function () {
                $.expr[":"].containsExact = function (obj, index, meta, stack) {
                    obj = $(obj).clone().children().remove().end();
                    return (obj.textContent || obj.innerText || $(obj).text() || "") == meta[3];
                };

                $.fn.ignore = function (sel) {
                    return this.clone().find(sel || ">*").remove().end();
                };

                var $container = $("#recursive");

                makeItToggle($container);
                $("#toggleTree").click(function () {
                    toggleProjectTree($container, $(this));
                });

                draggableDroppable();

                $("#tempContainer").on('mouseenter', "input[type='radio']", showBox);
                $("#tempContainer").on('mouseleave', "input[type='radio']", function(){console.log("out")});
            }
        );
    }



    function draggableDroppable(){
        $(".regular-item").draggable(
            {
                revert: true,
                cursor:'move',
                start: function( event, ui ) {
                    $(this).toggleClass('hovering-item'); },
                stop: function( event, ui ) {
                    $(this).toggleClass('hovering-item'); }
            }
        );

        $("#tempContainer").droppable(
            {
                activate: function (ev, ui){
                    $("#tempContainer").css({'border-width': '1px', 'border-style': 'dashed', 'border-color': 'black'});
                },
                deactivate: function( event, ui ) {
                    $("#tempContainer").css({'border-width': '0px', 'border-style': 'dashed', 'border-color': 'black'});
                },
                drop: function( event, ui ) {
                    /* extracted text from draggable */
                    var tableName = ui.draggable.parents().prev("li").last().text();

                    


                    /* Append/update li */
                    var textToFind = ui.draggable.clone().children().remove().end().text();
                    var matchedByText = $(this).find("li:containsExact(" + textToFind + ")");

                    var $liElement;
                    if (matchedByText.length){
                        $liElement = matchedByText.eq(0);
                        var $liSpan = $liElement.find("span").eq(0);
                        $liSpan.text($liSpan.text() == 0 ? "[1]" : "[" + (parseInt($liSpan.text().replace(/\D/g,'')) + 1) + "]");
                    }else{
                        $liElement = $("<li/>", {text: ui.draggable.text()});
                        $liElement.append($("<span/>"));

                        $liElement.append(createQueryBuilderButtons());

                        $(this).append($liElement);
                        $(this). switchClass('containerHover', "containerNonHover", 3000, "easeInOutQuad" );
                    }

                },
                over: function (e, ui){
                    $(this).addClass("containerNonHover"). switchClass("containerNonHover", 'containerHover', 3000, "easeInOutQuad" );
                },
                out: function (e, ui){
                    $(this). switchClass('containerHover', "containerNonHover", 3000, "easeInOutQuad" );
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




