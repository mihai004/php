$(document).ready(function(){

    $('.addToCart').click(function(e){
        var productID = $(this).attr("data-id");
        var userID = $('#userID').attr("value");
        $.post('cartFunctions.php', {
            'addToCart': "true",
            'productID': productID
        }, function(data) {
            if(userID > 1) {
                $("#response").html("The Book was successfully added");
                setTimeout(function () {
                    $("#response").html(" ");
                }, 2500);
            }else{
                $("#response").html("You need to log in first");
                setTimeout(function () {
                    $("#response").html(" ");
                }, 2500);
            }
        });
    });

    $('.addToCartPlus').click(function(e){
        var productID = $(this).attr("data-id");
        var quantity = $(this).attr("data-quantity");
        var quanId ='#'+quantity; // generate an id for the value
        $.post('cartFunctions.php', {
            'addToCart': "true",
            'productID': productID
        }, function(data) {
            $(quanId).html(data);
        });
        location.reload();
    });

    $('.removeFromCart').click(function(){
        var productID = $(this).attr("data-id");
         $.post('cartFunctions.php', {
            'removeFromCart': "true",
            'productID': productID
        }, function(data) {
        });
    });

    $('.removeFromCartMinus').click(function(){
        var productID = $(this).attr("data-id");
        var quantity = $(this).attr("data-quantity");
        var quanId ='#'+quantity;
        $.post('cartFunctions.php', {
            'removeFromCartMinus': "true",
            'productID': productID
        }, function(data) {
            if(data == ""){
                location.reload();
            }else{
                $(quanId).html(data);
            }
        });
        location.reload();
    });

    $('.clearCart').click(function(){
        $.post('cartFunctions.php', {
            'clearCart': "true",
        }, function(data) {
        });
    });

});