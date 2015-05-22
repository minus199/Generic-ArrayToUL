    /**
     * Created by minus on 5/22/15.
     */
    var UI = function () {};

    UI.prototype.getDroppableOptions = function () {
        return {
            activate: function (ev, ui) {
                $("#tempContainer").css({'border-width': '1px', 'border-style': 'dashed', 'border-color': 'black'});
            },
            deactivate: function (event, ui) {
                $("#tempContainer").css({'border-width': '0px', 'border-style': 'dashed', 'border-color': 'black'});
            },
            drop: function (event, ui) {
                /* extracted text from draggable */
                var output = [];
                $.each(ui.draggable.parents().prev("li"), function(){ output.push($(this).text()); });
                var tableName = output.reverse().join("-");
                tableName = tableName ? "table_" + tableName : "table_root";

                /* Append/update li */
                var textToFind = ui.draggable.clone().children().remove().end().text();
                var matchedByText = $(this).find("li:containsExact(" + textToFind + ")");

                var $liElement;
                if (matchedByText.length) {
                    $liElement = matchedByText.eq(0);
                    var $liSpan = $liElement.find("span").eq(0);
                    $liSpan.text($liSpan.text() == 0 ? "[1]" : "[" + (parseInt($liSpan.text().replace(/\D/g, '')) + 1) + "]");
                } else {
                    $liElement = $("<li/>", {text: ui.draggable.text(), class: "top_level " + tableName});
                    $liElement.append($("<span/>"));

                    $liElement.append(createQueryBuilderButtons());

                    $(this).append($liElement);
                    $(this).switchClass('containerHover', "containerNonHover", 3000, "easeInOutQuad");
                }

                $("." + tableName).toggle("highlight", "slow").toggle("highlight", "slow")
            },
            over: function (e, ui) {
                $(this).addClass("containerNonHover").switchClass("containerNonHover", 'containerHover', 3000, "easeInOutQuad");
            },
            out: function (e, ui) {
                $(this).switchClass('containerHover', "containerNonHover", 3000, "easeInOutQuad");
            },
            tolerance: "pointer",
            accept: '.regular-item'
        };
    };

    UI.prototype.getDraggableOptions = function () {
        return {
            revert: true,
            cursor: 'move',
            start: function (event, ui) {
                $(this).toggleClass('hovering-item');
            },
            stop: function (event, ui) {
                $(this).toggleClass('hovering-item');
            }
        }
    };

    UI.prototype.getSortableOptions = function () {
        return {
            placeholder: "ui-state-highlight"
        };
    };

    UI.prototype.draggableDroppableSortable = function() {
        var that = this;
        $(".regular-item").draggable(
            that.getDraggableOptions()
        );

        $("#tempContainer")
            .droppable(that.getDroppableOptions())
            .sortable(that.getSortableOptions());

        $( "#sortable" ).disableSelection();
    };

    UI.prototype.toggleSorted = function(){
        var container = $("#tempContainer");

        if (!window.unsorted){
            window.unsorted = container.clone();
            window.unsorted.attr("id", "clonedTemp");
        } else {
            container = this.undoSorting(container);
            return;
        }

        var output = {};
        $.each(container.find("li"), function(){
            var that = ($(this));

            if(output[that.attr("class")] == undefined){
                output[that.attr("class")] = [];
            }

            output[that.attr("class")].push(that);
        });

        container.empty();

        $.each(output, function(title,group){
            container.append($("<li/>", {text: title, class: 'top_level'}));

            var $ul = $("<ul/>");
            $.each (group, function(index, item){
                item.toggleClass("top_level").toggleClass("temp_sub_item");
                $ul.append(item);
            });

            container.append($ul);
        });

        container.sortable(this.getSortableOptions());
    };

    UI.prototype.undoSorting = function($container){
        var that = this;
        window.unsorted.attr("id", "tempContainer");
        var newContainer = $container.replaceWith(window.unsorted);
        $("#tempContainer")
           .droppable(that.getDroppableOptions())
           .sortable(that.getSortableOptions());
        window.unsorted = null;

        return newContainer;
    };