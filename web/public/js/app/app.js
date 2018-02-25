/**
 * Created by raulnet on 18/06/16.
 */
$(document).ready(function() {
    $('.btn-tools').on('click', function(e){
        $.ajax({
            type: "GET",
            url: $(this).data('url'),
            success: function (response) {
               console.log(response);
            }
        })
    });
});