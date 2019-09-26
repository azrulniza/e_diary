$(function () {
    $("section.content").on("click", ".paginator a", function (event) {
        event.preventDefault();
        var url = $(this).attr("href");
        $.ajax({
            url: url,
            type: 'GET'
        })
                .done(function (data) {
                    $("section.content").html(data);

                    location.hash = url;
                });
    });

    $("section.content").on("change", "select.autosubmit", function () {
        var form = $(this).parents('form');
        var url = $(form).attr('action');

        $.ajax({
            url: url,
            type: 'GET',
            data: $(form).serialize()
        })
                .done(function (data) {
                    $("section.content").html(data);

                    location.hash = "";
                });
    });
    
    // make all required input has red asterisk
    $("form div.input.required").each(function(){
   
        $(this).find("> label").append("<span class='mark-required'> * </span>");
   
    });
    
    // make input with error has bootstrap error class
    $("form div.input.error").each(function(){
   
        $(this).addClass("has-error")
                .find(".error-message").addClass("help-block")
        //   console.log("required");
   
    });

});