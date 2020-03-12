$('#syncButton').on('click', function () {
    var path = $("#syncButton").attr("data-path");
    $.ajax({
        type: "POST",
        url: path,
        dataType: "json",
        success: function (response) {
            if (jQuery.isEmptyObject(response)) {
                $('#syncModal').modal();
            }
            else {
                $.each(response, function (key, value) {
                    $(`#${key}`).before(`<p class="maj p-2 bg-danger">${value}</p>`);
                });

                $('.maj').on('click', function () {
                    $(this).next().val($(this).text());
                });
            }
        },
        error: function (response) {
            console.log(response);
        }
    }); 
});



