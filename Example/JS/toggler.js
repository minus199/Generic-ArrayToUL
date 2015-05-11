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
        $(this).css({cursor: 'pointer', 'text-decoration': 'underline'});
        $(this).next("ul").toggle();
    });

    $container.find("[ref='title_sub_depth']").click(function () {
        $(this).next("ul").toggle();
    });

    var button = $container.find("#toggleTree");
    button.click(function () {
        if ($(this).attr("ref") == "collapsedTrue") {
            toggleProjectTree($container);
            $(this).val("-").attr("ref", "collapsedFalse")
        } else {
            toggleProjectTree($container, true);
            $(this).attr("ref", "collapsedTrue").val("+");
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