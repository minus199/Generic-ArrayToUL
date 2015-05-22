/**
 * Created by minus on 5/22/15.
 */
/* Query Builder */
    function queryBuilder(){
        var output = {};

        $.each($("#tempContainer>li"),
            function () {
                var criteria = [];
                $.each($(this).find("input[type='radio']:checked"), function(){  criteria.push($(this).val()) });

                var current = $(this).clone().children().remove().end().text().split(": ");
                output[current[0]] = {
                    value: current[1],
                    criteria: criteria
                };
            }
        );

        return output;
    }

    function createQueryBuilderButtons($allowMultie){
        var alts = ['SELECT', 'WHERE', 'AND', 'OR'];
        var output = $("<div/>", {class: "radioContainer"});

        $.each(alts, function(i,x){
            var currentSpan = $("<span/>", {class: 'tooltip'});

            var $label = $("<span/>", {text: x});
            currentSpan.append($label);

            var $radio = $("<input/>", {type: 'radio', value: x});
            if ($allowMultie === undefined){
                $radio.attr('name', 'query_radio_input');
            }
            currentSpan.append($radio);

            output.append(currentSpan);
        });

        return output;
    }

    function showBox(e){
        $(e.currentTarget).prev()
            .fadeIn()
            .offset({ left: e.pageX + 20, top: e.pageY + 20 });
    }

    function hideBox(e){
        $(e.currentTarget).prev().fadeOut();
    }