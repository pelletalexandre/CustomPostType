<?php

/**
 * Permet de définir un cpt comme enfant, et ainsi lister tous les enfants du parent
 *
 */

if( !class_exists( 'CPT_Field_Post_Children' ) ) {


    class CPT_Field_Post_Children extends CPT_Field
    {

        //Le type des posts liés        
        //public $child_post_type = "";
        
        
        public $value_libelle;
        
        public function __construct ( $arr_data, $cpt )
        {
            parent::__construct ( $arr_data, $cpt );
            
            $this->arr_script = array (
									   array ( "fichier"	=>	"post_children.js", "systeme"	=>	false)
									   );
        }
        
        
        /**
         * Affichage des enfants du post courant sous la forme d'un tableau avec des boutons :
         *
         * - modifier
         * - supprimer
         *
         */
        public function display ( $post_id )
        {
            //die('display');
            //Récupération de tous les child post
			$post_children = array ();
            $this->loadAllValues ( $post_id, $post_children );
            
			//var_dump($post_children);die();
            //public $children_cpt_display;
        
            /*
            $this->loadValue ( $post_id );
            $this->loadDataTaxonomy ();
            */                     
            $html = $this->createStartTag ();
            
			
			//Bouton d'ajout d'un nouveau child
            if ( $this->children_button_add == true )
            {
                $html .= '<div><a href="post-new.php?post_type='.$this->children_post_type.'&parent_id='.$post_id.'" class="button button-primary" >Ajouter un produit</a></div>';
            }
			

            //Liste des posts sous forme de table
            if ( $this->children_cpt_display == "table" )
            {
                $html .= '<table class="form-table">';
                
                for ( $i=0; $i<count ($post_children); $i++ )
                {
                    $html .= $this->addPostChild ( $post_children[$i], $post_id );
                }			
                $html .= "</table>"; 
                
                
                $html .= $this->initJavascript ( );
            }
            //Liste des posts sous la forme de checkbox
            else if ( $this->children_cpt_display == "checkbox" )
            
            
            
            
            $html .= $this->createEndTag ();
            
            return $html;
        }
        
        
		
		protected function addPostChild ( $obj, $post_id )
		{
			
			$h = 	'<tr data-child-post="'.$obj->ID.'">'.
					'<td rowspan="2">'.
					'<img src="'.$obj->produit_image->url_thumbnail.'" />'.
					'</td><td>'.$obj->post_title.'</td>'.
					'<td><a href="post.php?post='.$obj->ID.'&action=edit&parent_id='.$post_id.'">Modifier</a></td>'.
					'</tr>';
					
			$h .= 	'<tr data-child-post="'.$obj->ID.'">'.
					'<td>'.$obj->produit_description.'</td>'.
					'<td><a href="javascript:delete_post(\''.admin_url('admin-ajax.php').'\', \''.$this->children_post_type.'\', '.$obj->ID.', \'un produit\' );" >Supprimer</a></td>'.
					'</tr>';
			
			return $h;
		}
		
		/**
         * Création des tags de démarrage nécessaires pour l'affichage dans un tableau
         */
        protected function createStartTag ( )
        {
            return '';//'<tr class="form-field form-required" id="tr-'.$this->post_meta.'" colspan="2" ><td>';
        }
        
        /**
         * Création des tags de fin nécessaires pour l'affichage dans un tableau
         */
        protected function createEndTag ( )
        {
            $end = '';//'</tr>';
			
			/*if ( $this->blank_line_after == true )
			{
				$end .= "<tr><td>&nbsp;</td></tr>";
			}*/
			
			return $end;
        }
		
        protected function initJavascript ( )
        {
            $h = '
            <script>   
            	
				
				
                jQuery ( document ).ready ( function ()
                {
					//alert("")
            		
		
            	});

        	</script>';
    
            return $h;
        }
         
        
        /**
         *
         */
        public function loadAllValues ( $post_id, &$post_children )
        {
			
			//global $enseigne;
			//$post_children = $enseigne->wp_produit->cpt->loadAllWithParent($post_id);

            $post_children = $this->cpt->loadAllWithParent($post_id);
			
			/*
			
            //Réalisation de la requète des sites
            $args = array(
                'post_type' => $this->children_post_type,
                'post_status' => 'publish',
                'post_parent'   => $post_id,
                'nopaging'	=> true,
                'posts_per_page' => -1
                );
            $query = new WP_Query( $args );
        
            if ( $query->have_posts() ) 
            {        
                while ( $query->have_posts() ) 
                {
                    $query->the_post();  
        
					var_dump(get_attached_media( 'image' , $query->post->ID )); die();
                    $o = new stdClass();
                    $o->ID =  $query->post->ID;
                    $o->label = $query->post->post_title;
					$o->description = get_post_meta($query->post->ID, 'produit_description', true );
					//$o->photo_url = wp_get_attachment_url( $query->post->ID  );
                    $post_children [] = $o;
                }
            }*/
        }
		
        /**
         * On ne devrait pas avoir besoin de cette fonction car logiquement,
         * l'enregistrement est réalisé dans le post anfant
         */
        public function saveValue ( $post_id )
        {
            /* 
            //La valeur récupérée est un tableau qui contient les term_id sélectionnés
            if ( $this->validate (  ) )
            {
                //On enregistre un post_meta dans tous les cas.
                update_post_meta ( $post_id, $this->post_meta."_id", $_POST[$this->post_meta."_id"] );
				update_post_meta ( $post_id, $this->post_meta, $_POST[$this->post_meta."_id"] );
                //update_post_meta ( $post_id, $this->post_meta."_name", $_POST[$this->post_meta."_name"] );   
            
            }
            else
            {                
                delete_post_meta ( $post_id, $this->post_meta."_id" );
				delete_post_meta ( $post_id, $this->post_meta );
                //delete_post_meta ( $post_id, $this->post_meta."_name" );
            }
            */
        }
        
        
        /**
         * Logiquement, on ne devrait pas avoir besoin de cette fonction,
         * l'affichage individuel n'est pas géré ici.
         */
        public function loadValue ( $post_id )
        {
            /*
            //On charge via la méthode parent
            parent::loadValue ( $post_id );

            //On charge la deuxième attendue
            //$this->value_libelle = get_post_meta ( $post_id, $this->post_meta."_name", true );
			if ( $this->value=="" )
			{
				$this->value_libelle = "";
			}
			else
			{
				$post = get_post ( $this->value );
				//echo "#".$this->value."#";
				//var_dump($post);die();
				$this->value_libelle = $post->post_title;
				//var_dump($this->value_libelle);
			}
			*/
			
        }
		
		
		public function loadValueJson ( $post_id )
        {
            /*
			$post = get_post ( $this->value );
			
            $o = new stdClass ();
			$o->{"id"} = get_post_meta($post_id, $this->post_meta."_id", true );
			$o->{"name"} = get_post_meta($post_id, $post->post_title, true );
            
			return $o;
			*/
			
			global $enseigne;
			
			if ( $enseigne->wp_produit->cpt != null )
				return $enseigne->wp_produit->cpt->loadAllWithParent($post_id);
			
			return '';
            
        }
        
		
		
		
        
        public function validate ( )
        {
            $b_ret = true;
            
            return $b_ret;
        }

    };
}

?>