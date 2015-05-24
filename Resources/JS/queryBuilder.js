/**
 * Created by minus on 5/22/15.
 * ~loading: 0
 */
/* Query Builder */

var QueryBuilder = function (){};
    QueryBuilder.prototype.queryBuilder = function (){
        var o = {};
        $.each($("#tempContainer").children("li"), function(){
            var output = {};

            $.each($(this).next("ul").children("li"), function(){
                var criteria = $(this).find("input[type='radio']:checked").val();
                output['criteria'] = criteria ? criteria : "FROM";

                var current = $(this).clone().children().remove().end().text().split(": ");
                output[current[0]] = current[1];
            });

            o[$(this).text()] = output;
        });

        return o;
    };

    QueryBuilder.prototype.createQueryBuilderButtons = function ($allowMulti, sorted){
        var alts = ['SELECT', 'WHERE', 'AND', 'OR'];
        var output = $("<div/>", {class: "radioContainer" + (sorted !== undefined ? "Sorted" : "")});

        $.each(alts, function(i,x){
            var $radio = $("<input/>", {type: 'radio', value: x, title: x});
            $radio.tooltip({
                'data-toggle': "tooltip",
                'data-placement': "bottom",
                delay: { "show": 100, "hide": 200 }
            });

            if ($allowMulti !== undefined){
                $radio.attr('name', 'query_radio_input_' + $(".radioContainer").length);
            }

            output.append($radio);
        });

        return output;
    };
