    /**
     * Created by minus on 5/22/15.
     * ~loading: 1
     */
    var UI = function () { this.container = $("#tempContainer"); };

    UI.prototype.getDroppableOptions = function () {
        var that = this;
        return {
            activate: function (ev, ui) {
                that.container.css({'border-width': '1px', 'border-style': 'dashed', 'border-color': 'black'});
            },
            deactivate: function (event, ui) {
                that.container.css({'border-width': '0px', 'border-style': 'dashed', 'border-color': 'black'});
            },
            drop: function (event, ui) {
                $('#sortToggle:hidden').toggle();

                /* extracted text from draggable */
                var output = [];
                $.each(ui.draggable.parents().prev("li"), function(){ output.push($(this).text()); });
                var tableName = output.reverse().join("|");
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
                    $liElement = $("<li/>", {text: ui.draggable.text(), class: "top_level", ref: tableName});
                    $liElement.append($("<span/>"));

                    $liElement.append(TogglerJS.queryBuilder.createQueryBuilderButtons());

                    $(this).append($liElement);
                    $(this).switchClass('containerHover', "containerNonHover", 3000, "easeInOutQuad");
                }

                that.container = $("#tempContainer");
                $("[ref='" + tableName + "']").toggle("highlight", "slow").toggle("highlight", "slow")
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
        /* undoSorting will unset window.unsorted(which is used to backup current state of the list */
        /* So if null, current state in unsorted */
        /* If not null, list is currently sorted */
        console.log(this.container);


        if (window.unsorted){
            this.container = this.undoSorting(this.container);
            return false;
        }

        this.container = this.sort(this.container);
        return true;
    };

    UI.prototype.sort = function($container){
        /* TODO: Move unsorted to this */
        window.unsorted = $container.clone();
        window.unsorted.attr("id", "clonedTemp");

        /* Sort by group */
        var output = {};
        $.each(this.container.find("li[ref]"), function(){
            var tableName = $(this).attr("ref").replace("table_", "");
            if(output[tableName] === undefined){
                output[tableName] = [];
            }

            output[tableName].push($(this));
        });

        /* Empty the container */
        $container.empty();

        var that = this;
        /* Re-build the container */
        $.each(output, function(title,group){
            var $code = $("<code/>", {text: title});
            var $li = $("<li/>", {class: 'top_level'});

            $li.append($code);
            $container.append($li);

            var $ul = $("<ul/>");
            $.each (group, function(index, item){
                $ul.append(item);
                item.switchClass("top_level", "temp_sub_item", 2000);

                item.children("div").detach();
                item.append(TogglerJS.queryBuilder.createQueryBuilderButtons(false, true));
            });

            $container.find("ul")
                .sortable(that.getSortableOptions())
                .droppable(that.getDroppableOptions());

            $container.append($ul);
        });

        return $container;
    };

    UI.prototype.undoSorting = function($container){
        var $newContainer = window.unsorted.clone().prop("id", $container.prop("id"));
        $container.replaceWith($newContainer);

        $newContainer
            .sortable(this.getSortableOptions())
            .droppable(this.getDroppableOptions());

        window.unsorted = null;
        return $newContainer;
    };

    UI.prototype.reset = function(){
        this.container.empty();
        window.unsorted = null;
    };