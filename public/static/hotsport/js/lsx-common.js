var lsx = {
    tableInitClick: function(arr) {
        arr.forEach(e => {
            if (e.dbClick) {
                $(e.dbClick).off("dblclick").on("dblclick", e.callback);
            } else if (e.click) {
                $(e.click).off("click").on("click", e.callback);
                $(e.click).off("dblclick").on("dblclick", function() {
                    return false;
                })
            }
        });
    },
}