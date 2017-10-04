<?php

/*
   Version: 1.0
   Author: PELLET Alexandre
   Author URI: http://www.dynamik-informatik.com
   License: 
   */

   
if( !class_exists( 'CPT_Field_Media_File' ) ) {

	class CPT_Field_Media_File extends CPT_Field
    {
        public $url = "";
		public $attachment = null;
		
		public function __construct ( $arr_data, $cpt )
		{
			parent::__construct  ( $arr_data, $cpt );
			
			
			//wp_enqueue_media ();
			
			$this->arr_script = array (
									   array ( "fichier"	=>	"media.manager.js", "systeme"	=>	false)
									   );
			
			$this->arr_style = array (
										"hack-media.css"  
									  );
			
		}
        public function display ( $post_id )
        {
			//Apparemment, si il n'est pas ici, cet appel fait planter le media manager pour les autres posts
			wp_enqueue_media ();
			
            $this->loadValue ( $post_id );
			
			$attachment = wp_prepare_attachment_for_js($this->value);
			
			$html = "";
			
			/*$html .= "
			<script>
				//var attachment_"+$this->post_meta+" = ".json_encode($attachment).";
			</script>
			";
            */
			
			if ( $this->media_type_document == "ALL" )
			{
				$this->media_type_document = "";
			}
			
			
            $html .= $this->createStartTag ();
			$html .= '<input type="hidden" id="'.$this->id.'" name="'.$this->id.'" value="'.$this->value.'" />';
			
			$style_visibility_label = '';
			$style_visibility_image = '';
			$style_visibility_button_add = '';
			$style_visibility_button_delete = '';
			$label_value = "";
			
			if ( $this->display_media_miniature == true )
			{
				if ( $this->value == '' )
				{
					$style_visibility_image = 'display:none;';
					$style_visibility_button_delete = 'display:none;';
				}
				else
				{
					$style_visibility_button_add = 'display:none;';
					$label_value = $attachment['filename'];
				}
				
				if ( isset ( $attachment['sizes']['thumbnail'] ) )
					$html .= '<img style="'.$style_visibility_image.'" id="'.$this->id.'_image" src="'.$attachment['sizes']['thumbnail']['url'].'" />';
				else
					$html .= '<img style="'.$style_visibility_image.'" id="'.$this->id.'_image" src="'.$attachment['sizes']['full']['url'].'" />';
			}
			else
			{
				if ( $this->value == '' )
				{
					$style_visibility_label = 'display:none;';
					$style_visibility_button_delete = 'display:none;';
				}
				else
				{
					$style_visibility_button_add = 'display:none;';
					$label_value = $attachment['filename'];
				}
				
				$html .= '<label style="'.$style_visibility_label.'" id="'.$this->id.'_lib" >'.$label_value.'</label>';
			}

			$html .= 	'<input style="'.$style_visibility_button_add.'" '.
							'id="wp-media-document-add-'.$this->post_meta.'" '.
							'type="button" '.
							'data-frame-id="'.$this->post_meta.'" '.
							'data-post-meta="'.$this->post_meta.'" '.
							'data-display-miniature="'.$this->display_media_miniature.'" '.
							'class="button button-primary button-large" '.
							'value="'.$this->media_libelle_bouton_ajout.'"/>
					
						<input style="'.$style_visibility_button_delete.'" '.
							'id="wp-media-document-delete-'.$this->post_meta.'" '.
							'type="button" '.
							'data-frame-id="'.$this->post_meta.'" '.
							'data-post-meta="'.$this->post_meta.'" '.
							'data-display-miniature="'.$this->display_media_miniature.'" '.
							'class="button button-primary button-large" '.
							'value="'.$this->media_libelle_bouton_supression.'"/>
					';
			
            $html .= $this->createEndTag ();
            
            return $html;
            
//			return "";
        }
        
		public function executeJavascript ( )
		{
			$display =  (($this->display_media_miniature==false)?'false':'true');
			
			$js = "jQuery(document).ready (function(){
				
				jQuery('#wp-media-document-add-".$this->post_meta."').click ( function(){
				
					media_manager.media.documents.init ( '".$this->post_meta."', '".$this->post_meta."', '".$this->post_meta."-liste', '".$this->post_meta."',".$display.", '".$this->media_type_document."' );
				
					media_manager.media.documents.frame ( 	jQuery(this).attr('data-frame-id' ),
													jQuery(this).attr('data-post-meta' ),
													((jQuery(this).attr('data-display-miniature')==\"1\")?true:false)
												).open();
				});
				\n\n
				
				jQuery('#wp-media-document-delete-".$this->post_meta."').click ( function(){
				
					var p_meta = jQuery(this).attr('data-post-meta');
					var dip_mini = (jQuery(this).attr('data-display-miniature')==\"1\")?true:false;
				
					delete_attachment_application ( jQuery('#'+p_meta).val(), p_meta, dip_mini );
				
				});
				\n\n
				
				
			
			});\n\n";
			
			//$js .= "media_manager.media.documents.init ( '".$this->post_meta."', '".$this->post_meta."', '".$this->post_meta."-liste', '".$this->post_meta."',".$display.", '".$this->media_type_document."' );\n\n";
			
			return $js;
		}
		
		/**
         * Seule méthode qu'il faut implémenter pour renvoyer un tableau d'url à REST
         *
         */
        public function getRestValue ( $object )
        {            
            $documents = get_post_meta ( $object['id'], $this->post_meta, true );
			
			$attachments = array ( );
			if ( $documents != "" )
			{
				$ids = explode ( ',', $documents );
			}
			else
			{
				$ids = array();
			}
	
            
            //On boucle sur les tailles d'images voulues
            foreach ( $ids as $doc )
            {
				$attachments [] = wp_get_attachment_url ( $doc );
            }

            return $attachments;
        } 
        
        
        public function loadValue ( $post_id )
        {
            //Identifiant de l'attachment
            parent::loadValue ( $post_id );
            
            //Il faut charger les données de l'attachment pour avoir son nom et son url
            $this->url = wp_get_attachment_url( $this->value  );
			$this->attachment = wp_get_attachment_metadata ( $this->value );
        }
        
        public function loadValueJson ( $post_id )
        {
            $o = new stdClass ();
			$o->{"id"} = $this->value;
			$o->{"url"} = wp_get_attachment_url( $this->value  );
			$t = wp_get_attachment_image_src( $this->value,'thumbnail' );
			$o->{"url_thumbnail"} = $t[0];

			$o->{"sizes"} = new stdClass();
			for ( $i=0; $i<count ($this->media_sizes); $i++ )
			{
				$o->{"sizes"}->{$this->media_sizes[$i]} = wp_get_attachment_image_src( $this->value, $this->media_sizes[$i] );
			}
			
			//$o->{"attachment"} = wp_get_attachment_metadata( $this->value  );
            
			return $o;
        }
		
		
		//METHODE COMMENTEE CAR INEFICACE ET INUTILE
		/*public function saveValue ( $post_id )
        {
			$this->value = $_POST[$this->post_meta];
			var_dump($this->post_meta);
			var_dump($this->value);
			var_dump ($this->validate());
//die();
			if ( $this->validate () )
			{
				update_post_meta ( $post_id, $this->post_meta, $_POST[$this->post_meta] );  
			}
			
			if ( $this->value != "" )
			{
				//$arr_ids = explode( ',', $this->value );
				
				//Pour affecter le document au post parent
				//for ( $i == 0; $i<count($arr_ids); $i++ )
				//{
				//	$media = array(
				//					'ID'           => $arr_ids[$i],
				//					'post_parent'   => $post_id
				//				);
				//	wp_update_post( $media );
				//}
				//
			}
			
			//update_post_meta ( $post_id, $this->post_meta, $_POST[$this->post_meta] );  
        }*/
		/*
		public function validate ()
		{
			$ret = parent::validate ( );
			
            //var_dump($this->can_be_empty);
            //var_dump($ret);die();

            //On peut être vide et on est vide, donc, on return true.
            if ( $this->can_be_empty == true && $this->value == "" )
            {
                return true;
            }

			if ( $ret == false )
			{
				$this->error = true;
                $this->error_message .= "<li>Le champ '".$this->libelle."' ne peut pas être vide.</li>";
			}


			
			
			return $ret;
		}
		*/
		
    };
}

?>