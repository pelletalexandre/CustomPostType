<?php

if( !class_exists( 'CPT_Field_Select_Post' ) ) {

    class CPT_Field_Select_Post extends CPT_Field
    {
        
        
        public function display ( $post_id )
        {
            /*

					$this->cpt_select_display_field = $arr_data['extra']['post']['display_field'];

					$this->cpt_select_name = $arr_data['extra']['post']['cpt_name'];

            */

            
            
            $this->loadValue ( $post_id );
            $this->loadDataPost ( $this->cpt_select_name, $this->cpt_select_display_field );

            $html = $this->createStartTag ();
            
			$options = "";
			
			$html .= '<select name="'.$this->id.'" id="search_id-'.$this->id.'" >';
            $html .= (($this->cpt_first_option == '')?'':$this->cpt_first_option);
            
            for ( $i=0; $i<count($this->data_post); $i++ )
            {
                $options .= '<option value="'.$this->data_post[$i]->post_id.'" '.(($this->value==$this->data_post[$i]->post_id)?'selected="selected"':'').'>'.$this->data_post[$i]->post_title.'</option>';
            }
            

            $html .= $options.'</select>';
            
			
			return $html;

            $html .= $this->createEndTag ();
            
            return $html;
        }
        
        
		
        public function saveValue ( $post_id )
        {
            
            //La valeur récupérée est un tableau qui contient les term_id sélectionnés
            if ( $this->validate (  ) )
            {
                //On enregistre un post_meta dans tous les cas.
                parent::saveValue ( $post_id ); 
            
                //On associe les taxonomies au post
                //wp_set_object_terms ( $post_id, array_map( 'intval', $_POST[$this->post_meta] ), $this->taxonomy );
                
            }
            else
            {                
                delete_post_meta ( $post_id, $this->post_meta );
                //wp_set_object_terms ( $post_id, "", $this->taxonomy );
                
                /*$terms = get_the_terms( $post_id, $this->taxonomy );
                if ( $terms && ! is_wp_error( $terms ) )
                {
                    wp_remove_object_terms ( $post_id, $terms[0]->term_id, $this->taxonomy );
                }*/
                
            }
            
            
            
        }
        
        
        
        public function loadValue ( $post_id )
        {
            $this->value = get_post_meta ( $post_id, $this->post_meta, true );

            if ( $this->value == "" )
            {
                $this->value = array ();    
            }
        }
		
		/*
		public function loadValueJson ( $post_id )
        {
			$ret = array();
            $terms = get_the_terms( $post_id, $this->taxonomy );
			
			
            if ( $terms && ! is_wp_error( $terms ) )
            {
                for ( $i=0; $i<count($terms); $i ++)
				{
					$o = new stdClass ();
					$ret [] = $o;
					
					$o->libelle = $terms[$i]->name;
					$o->slug = $terms[$i]->slug;
				}
            }
            else
            {
                
            }
			
			return $ret;
        }
        
		
		public function getRestValue ( $object )
        {
            $terms = get_the_terms( $object['id'], $this->taxonomy );
            if ( $terms && ! is_wp_error( $terms ) )
            {
                $this->value = $terms[0]->name;
            }
            return $this->value ;
        }
		*/
        
        public function validate ( )
        {
            $b_ret = true;
            
            //Le champ ne doit pas être vide
            
            if ( $this->can_be_empty == false && !isset ( $_POST[$this->post_meta] ) )
            {
                $this->error = true;
                $this->error_message .= "<li>Le champ '".$this->libelle." doit avoir au moins un choix de coché</li>";
                $b_ret = false;
            }
            
            return $b_ret;
        }

    };
}

?>

