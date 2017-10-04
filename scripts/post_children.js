function delete_post ( ajaxurl, post_type, post_id, libelle )
    {
        if ( confirm ("Vous êtes sur le point de supprimer "+libelle+".") )
        {
            var data = 
            {
                action: 'ajax_delete_child_post',
                post_type: post_type,
                post_id: post_id
            };
            
            
            jQuery('[data-child-post="'+post_id+'"] a').hide();
            
            jQuery.post(ajaxurl, data, function ( response ) 
            {
            })
            .done ( function ( data )
            {
                //On récupère les identifiants
               //console.log ( data );
                
                jQuery('[data-child-post="'+post_id+'"]').fadeOut(500)
                
                //jQuery('#loading').hide();
                
            })
            .fail(function( jqxhr, textStatus, error )
            {
                var err = textStatus + ", " + error;
                console.log( "Request Failed: " + err );
                //jQuery('#loading').hide();
            });
            
            
        }
    }