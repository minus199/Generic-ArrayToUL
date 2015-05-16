/**
 * Created by minus on 5/11/15.
 */


var $ = jQuery;

$("document").ready(
    function(){
        makeItToggle($("#recursive"));
    }
);

function makeItToggle($container) {
    $.each($container.find("[ref='title_sub_depth']"), function () {



        $(this).css(
            {
                cursor: 'pointer',
                /*'text-shadow': '2px 4px 3px rgba(0,0,0,0.3)',*/
                /*'text-shadow': '4px 3px 0px #fff, 9px 8px 0px rgba(0,0,0,0.15)',*/
                /*'text-shadow': '0px 4px 3px rgba(0,0,0,0.4), 0px 8px 13px rgba(0,0,0,0.1),0px 18px 23px rgba(0,0,0,0.1)',*/
                color: 'rgba(0,0,0,0.6)',
                'font-size': 16,
                'text-shadow': '2px 8px 6px rgba(0,0,0,0.2), 0px -5px 35px rgba(255,255,255,0.3)'
                /*'text-decoration': 'underline'*/
            }
        );
        $(this).next("ul").toggle();
    });

    $container.find("[ref='title_sub_depth']").click(function () {
        var $ulToToggle = $(this).next("ul");
        $ulToToggle.toggle();

        /*if ($("#makespan").is(":visible") == true) { alert("visible"); }
              else { alert("not visible"); }*/


        if(!$ulToToggle.is(":visible")){
            console.log($ulToToggle.children().find("ul"));
            //$ulToToggle.children().find("ul:visible").hide();
            $ulToToggle.next().find("ul").hide();
        }

        //console.log($(this).next("ul").html());
    });

    var button = $("body").find("#toggleTree");
    button.click(function () {
        if ($(this).attr("class") == "collapsedTrue") {
            toggleProjectTree($container);
            $(this).val("-").attr("class", "collapsedFalse")
        } else {
            toggleProjectTree($container, true);
            $(this).attr("class", "collapsedTrue").val("+");
        }
    });
}

function toggleProjectTree($container, expend) {
    $.each($container.find("[ref='title_sub_depth']"), function () {
        if (expend !== undefined) {
            $(this).next("ul").show();
        } else {
            $(this).next("ul").hide();
        }
    });
}