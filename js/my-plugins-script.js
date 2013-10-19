/**
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
	
});