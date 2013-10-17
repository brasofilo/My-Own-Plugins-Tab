/*
 * Script actions 
 * 
 * @plugin My Own Plugins Tab
 */


jQuery(document).ready( function($){
	
    /* Remove separator */
    $('.mopt-icon').each(function(){
	var par_text = $(this).parent().html();
	var rep = par_text.replace('|','');
        $(this).parent().html(rep);
    });

    /* Send form with return in text field */
    $(window).keydown(function(event)
    {
        if(event.keyCode == 13) 
        {
            mopt_post_ajax();
            return false;
        }
    });
    
    /* Toggle settings */
    $('#mopt-pluginconflink').click(function(e)
    { 
        e.preventDefault(); 
        if( $('#mopt_config_row').is(':visible') )
            $(this).text(mopt_ajax_vars.open_btn);
        else
            $(this).text(mopt_ajax_vars.close_btn);
        
        $('#mopt_config_row').slideToggle(); 
    });
    
    /* Ajax */
    $('#mopt_config_submit').click( mopt_post_ajax );
    function mopt_post_ajax() 
    {
        var mopt_post_data = {
            "action" : "mopt_save_config",
            "nonce" : mopt_ajax_vars._nonce,
            "mopt_config-authors" : $('#mopt_config-authors').val(),
            "mopt_config-others" : $('#mopt_config-others').val(), 
            "mopt_config-icon" : $('#mopt_config-icon').val()
        };
        //console.dir(mopt_post_data);
        $.post( mopt_ajax_vars.ajaxurl , mopt_post_data, function( response ) 
        {
            // ERROR HANDLING
            if( !response.success )
            {
                $( '#mopt-message' )
                    .addClass('ui-state-error ui-corner-all');
            
                // No data came back, maybe a security error
                if( !response.data ) 
                {
                    $('#mopt-message')
                        .text( 'undefined error; this should not appear!' )
                        .show('fast')
                        .animate({opacity: 1.0}, 3000);//.hide('slow');
                }  
                else 
                {
                    $( '#mopt-message' )
                        .text( response.data.error )
                        .show('fast')
                        .animate({opacity: 1.0}, 3000);//.hide('slow');
                }
                return false;
            }
            location.href = location.href;
            $( '#mopt-message' )
                .addClass('ui-state-highlight ui-corner-all');

            $('#mopt-message')
                    .text( response.data )
                    .show('fast')
                    .animate({opacity: 1.0}, 3000)
                    .hide('slow');
        });

        return false;
    }
	
});