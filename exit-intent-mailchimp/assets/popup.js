(function($){
    function showPopup(){
        if($('#eimp-popup').is(':visible')) return;
        $('#eimp-popup').fadeIn();
    }

    function closePopup(){
        $('#eimp-popup').fadeOut();
        sessionStorage.setItem('eimpShown', 'true');
    }

    $(document).on('mouseleave', function(e){
        if(e.clientY < 0 && !sessionStorage.getItem('eimpShown')){
            sessionStorage.setItem('eimpShown', 'true');
            showPopup();
        }
    });

    $(document).on('click', '#popactive', function(){
        showPopup();
    });

    $(document).on('click', '#eimp-close', function(){
        closePopup();
    });

    $(document).on('submit', '#eimp-form', function(e){
        e.preventDefault();
        var email = $('#eimp-email').val();
        $.post(eimpAjax.ajaxUrl, {
            action: 'eimp_subscribe',
            email: email,
            nonce: eimpAjax.nonce
        }, function(res){
            if(res.success){
                $('#eimp-form').html('<p>¡Gracias por suscribirte!</p>');
            } else {
                alert(res.data.message || 'Error');
            }
        });
    });
})(jQuery);
