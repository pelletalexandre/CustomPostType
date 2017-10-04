var media_manager=
{
	media:
	{
		documents:null,
		photos:null
	}
};
//alert('');
var image_to_edit = 0;
var document_to_edit = 0;

function delete_attachment_application ( post_id, post_meta, display_miniature )
{
	
	if ( window.confirm ('Etes-vous certain de vouloir supprimer ce document?') )
	{
		jQuery ( '#' + post_meta ).val ( '' );
		
		if ( display_miniature == true )
		{
			jQuery ( '#' + post_meta + "_image" ).attr('src', '').hide();
			/*
			jQuery ('li[post_id='+post_id+']').remove();
			
			//Mettre à jour le hidden
			var docs = "";
			jQuery('li[id^=attachment-document]').each ( function ()
			{
				if ( docs != "" )
					docs+=",";
				docs += jQuery(this).attr('post_id');
			});
			
			jQuery ( '#' + post_meta ).val ( docs );
			*/
		}
		else
		{
			
			jQuery ( '#' + post_meta + "_lib" ).html('').hide();
			
		}
		jQuery('#wp-media-document-delete-' + post_meta).hide ();
		jQuery('#wp-media-document-add-' + post_meta).show ();
	}
}





function update_attachment_application ( post_id )
{
	document_to_edit = post_id;
	media_manager.media.documents.frame().open();
}


function add_attachment_application ( data, appendTo, post_meta )
{
	alert("ici")
	if ( data == null )
		return;
	var h = '<li class="attachment" id="attachment-document-'+data.id+'" post_id="'+data.id+'">';
	
	h += '<div class="attachment-preview type-'+data.type+' subtype-'+data.subtype+' landscape">';

	h += '<div class="hover">';
	h += '<ul>';
	
	h += '<li><a id="delete_app_'+data.id+'" title="Retirer le document" href="javascript:void(0);"><img src="'+img_delete+'"/></a></li>';
	
	h += '<li><a id="update_app_'+data.id+'" target="_blank" title="Editer le document" href="'+data.url+'"><img src="'+img_edit+'"/></a></li>';
	
	h += '</ul></div>';

	h += '<img src="'+data.icon+'" class="icon" draggable="false" />';
	h += '<div class="filename"><div>'+data.filename+'</div></div>';
	h += '</div></li>';
	
	jQuery(h).appendTo(appendTo);
	jQuery('#delete_app_'+data.id).on('click', function (){delete_attachment_application(data.id, post_meta)});
	//jQuery('#update_app_'+data.id).on('click', function (){update_attachment_application(data.id)});
}
function add_attachment_image ( data, appendTo, post_meta )
{
	
	if ( data == null )
		return;
		
	if ( data.width < 640 )
	{
		alert("L'image que vous avez choisie est trop petite.\n" + "Il s'agit de "+data.title);
		return false;
	}
	
	var h = '<li class="attachment" id="attachment-image-'+data.id+'" post_id="'+data.id+'">';
	h += '<div class="attachment-preview type-'+data.type+' subtype-'+data.subtype+' '+data.orientation+'">';
	
	h += '<div class="hover" style="z-index:1000px !important;">';
	h += '<ul>';
	h += '<li><a id="delete_image_'+data.id+'" title="Retirer la photo" href="javascript:void(0);"><img src="'+img_delete+'"/></a></li>';

	h += '<li><a id="update_image_'+data.id+'" target="_blank" title="Editer la photo" href="'+data.url+'"><img src="'+img_edit+'"/></a></li>';
	

	h += '</ul></div>'
	
	h += '<div class="thumbnail">';
	h += '<div class="centered">';
	h += '<img src="'+data.sizes['medium'].url+'" draggable="false" />';
	h += '</div></div></div></li>';

	jQuery(h).appendTo(appendTo);
	jQuery('#delete_image_'+data.id).on('click', function (){delete_attachment_image(data.id, post_meta)});
	
	
	//jQuery('#update_image_'+data.id).on('click', function (){update_attachment_image(data.id)});
//	return h;
}

var current_media_post_meta = "";
var current_media_display_miniature = false;

media_manager.media.documents = 
{
	input:'',//'devis_documents',
	div:'',//devis-liste-documents',
	//post_meta:'',
	//display_miniature: false,
	template:'',
	type:'',
	
	frame: function( id_frame, a_post_meta, a_display_miniature ) 
	{
		if ( a_post_meta )
			post_meta=	a_post_meta;
		if ( a_display_miniature)
			display_miniature= a_display_miniature;
		if ( this._frame )
			return this._frame;

		this._frame = wp.media(
		{
			
			id:			id_frame/*my-frame'*/,				   
			//frame:     	'post',
			//state:     	'gallery-library',/*library ne fonctionne pas*/
			title:     	wp.media.view.l10n.editGalleryTitle,
			//editing:   	false,
			multiple:  	false,
			library:
			{
				type:type
			},
			button:
			{
				text:'Valider'
			}
		});
		
		this._frame.on( 'init', function() 
		{
			//alert("load");
		});
		this._frame.on( 'open', function() 
		{

			if ( document_to_edit > 0 )
			{
				var selection = media_manager.media.documents.frame().state().get('selection');

				//wp.media.button.text='valider';
				attachment = wp.media.attachment(document_to_edit);
				attachment.fetch();
				selection.reset( attachment ? [ attachment ] : [] );
			}
			
			document_to_edit = 0;

			
		});
		this._frame.on( 'select', function() 
		{
			//console.log ( this );
			
			// get selected images
			selection = media_manager.media.documents.frame(id_frame).state().get('selection');
			//console.log ( selection);
			if( selection )
			{
				var i = 0;
				
				selection.each(function(attachment)
				{
					//console.log ( attachment );
			    	// counter
			    	i++;
			    	

					if ( this.display_miniature == true )
					{
						if ( attachment.attributes.type == "image" )
						{
							//add_attachment_image(attachment.attributes, "#list-attachment-document", this.post_meta );
							if ( attachment.attributes.sizes.thumbnail )
								jQuery('#' + this.post_meta+"_image").attr('src', attachment.attributes.sizes.thumbnail.url ).show();
							else
								jQuery('#' + this.post_meta+"_image").attr('src', attachment.attributes.sizes.full.url ).show();
						
						}
						else if ( attachment.attributes.type == "application" )
						{
							add_attachment_application(attachment.attributes, "#list-attachment-document", this.post_meta );
						}
					}
					else
					{
						//console.log ( attachment.attributes.sizes.thumbnail.url);
						jQuery('#' + this.post_meta+"_lib").html( attachment.attributes.filename ).show();
						
					}
					jQuery(this.id_but_add).hide ();//'#wp-media-document-add'
					jQuery(this.id_but_del).show ();//'#wp-media-document-delete'
					
					
					var docs = jQuery ('#' + this.post_meta).val();
					if ( docs != "" )
						docs += ',';
					docs += attachment.attributes.id;
					
					jQuery ('#' + this.post_meta).val( docs );
					
					
					
					
			    });
			    // selection.each(function(attachment){
			}
			// if( selection )
		});
		
		return this._frame;
	},

	init: function ( a_id_frame, a_input, a_div, a_post_meta, a_display_miniature, a_type ) 
	{
		input = a_input;
		div = a_div;
		//this.post_meta = a_post_meta;
		//display_miniature = a_display_miniature;
		type = a_type;
		id_frame = a_id_frame;
		id_but_add = '#wp-media-document-add-' + id_frame;
		id_but_del = '#wp-media-document-delete-' + id_frame;
		
		
		//jQuery(id_but_add).click( function( event ) 
		//{
			//event.preventDefault();
		/*
			media_manager.media.documents.frame ( 	jQuery(this).attr('data-frame-id' ),
													jQuery(this).attr('data-post-meta' ),
													((jQuery(this).attr('data-display-miniature')=="1")?true:false)
												).open();
*/
		//});
		
		
		/*
		//if ( this.display_miniature == true )
		{
			jQuery(id_but_del).click( function( event ) 
			{
				event.preventDefault();
				var p_meta = jQuery(this).attr('data-post-meta');
				var dip_mini = (jQuery(this).attr('data-display-miniature')=="1")?true:false;
				
				delete_attachment_application ( jQuery('#'+p_meta).val(), p_meta, dip_mini );
	
			});
		}
		*/
	},
	add: function ( )
	{
		
	},
	remove: function ( )
	{
		
	}
};


/*
function delete_attachment_application_old ( post_id, post_meta, display_miniature )
{
	
	if ( window.confirm ('Etes-vous certain de vouloir supprimer ce document?') )
	{
		if ( display_miniature == true )
		{
			jQuery ('li[post_id='+post_id+']').remove();
			
			//Mettre à jour le hidden
			var docs = "";
			jQuery('li[id^=attachment-document]').each ( function ()
			{
				if ( docs != "" )
					docs+=",";
				docs += jQuery(this).attr('post_id');
			});
			
			jQuery ( '#' + post_meta ).val ( docs );
		}
		else
		{
			jQuery ( '#' + post_meta ).val ( '' );
			jQuery ( '#' + post_meta + "_lib" ).html('').hide();
			jQuery('#wp-media-document-delete').hide ();
			jQuery('#wp-media-document-add').show ();
		}
	}
}
*/
