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

    function createQueryBuilderButtons(){
        var alts = ['SELECT', 'WHERE', 'AND', 'OR'];
        var output = $("<div/>");

        $.each(alts, function(i,x){
            var currentSpan = $("<span/>", {class: 'tooltip'});

            var $label = $("<span/>", {text: x});
            currentSpan.append($label);

            var $radio = $("<input/>", {type: 'radio', /*name: 'query_radio_input',*/ value: x});

            /*<input id="radio1" type="radio" name="radio" value="1" checked="checked"><label for="radio1"><span><span></span></span>Option 1</label>*/
            currentSpan.append($radio);

            output.append(currentSpan);
        });

        return output;
    }

    function showBox(e){
        console.info("enter in");
        $(e.currentTarget).prev()
            .fadeIn()
            .offset({ left: e.pageX + 20, top: e.pageY + 20 });

        console.info("leave in");
    }

    function hideBox(){
        $(e.currentTarget).prev().fadeOut();
    }

    /* Query Builder */