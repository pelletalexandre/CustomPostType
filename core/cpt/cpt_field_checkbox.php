<?php

if( !class_exists( 'CPT_Field_Checkbox' ) ) {

    class CPT_Field_Checkbox extends CPT_Field
    {
        
        
        public function display ( $post_id )
        {
            $this->loadValue ( $post_id );
            $this->loadDataTaxonomy ();
                                   
            $html = $this->createStartTag ();
            
            if ( $this->taxonomy_hierarchical == false )
			{
				if ( $this->display_type == "" || $this->display_type == "INLINE")
				{
					$html .= $this->displayInline ();	
				}
				else if ( $this->display_type == "LIST")
				{
					$html .= $this->displayList ();	
				}
				
			}
			else
			{
				$html .= $this->displayHierarchical ();
			}
            $html .= $this->createEndTag ();
            
            return $html;
        }
        
		protected function displayHierarchical ( )
		{
			$html = "";
			$options = "";
			
			$html .= '<select name="'.$this->id.'" id="search_id-'.$this->id.'" >';
            $html .= (($this->first_option == '')?'':$this->first_option);
            
            for ( $i=0; $i<count($this->data_taxonomy); $i++ )
            {
				if ( $this->data_taxonomy[$i]->parent == 0 )
				{
					if ( $options != "" )
					{
						$options .= '</optgroup>';
					}
					$options .= '<optgroup label="'.$this->data_taxonomy[$i]->name.'">';
				}
				else
				{
					$options .= '<option value="'.$this->data_taxonomy[$i]->term_id.'" '.(($this->value==$this->data_taxonomy[$i]->term_id)?'selected="selected"':'').'>'.$this->data_taxonomy[$i]->name.'</option>';
				}
            }
			$options .= '</optgroup>';
		
            $html .= $options.'</select>';
			
			return $html;
		}
		
		protected function displayInline ( )
		{
			$html = "";

            for ( $i=0; $i<count($this->data_taxonomy); $i++ )
            {
                $checked = "";
                if ( count($this->value) > 0 )
                {
                    if ( in_array_object ( $this->value, $this->data_taxonomy[$i], 'term_id', 'term_id' ) )
                    {
                        $checked = "checked";
                    }
                }                
                
                $html .= '<input type="checkbox" name="'.$this->post_meta.'[]" value="'.$this->data_taxonomy[$i]->term_id.'" '.($checked==""?"":'checked="checked"').' " >'.$this->data_taxonomy[$i]->name.'</option>';
            }
			
			return $html;
		}
        
		
		protected function displayList ( )
		{
			$html = "<ul class='ul-field-checkbox'>";

            for ( $i=0; $i<count($this->data_taxonomy); $i++ )
            {
                $checked = "";
                if ( count($this->value) > 0 )
                {
                    if ( in_array_object ( $this->value, $this->data_taxonomy[$i], 'term_id', 'term_id' ) )
                    {
                        $checked = "checked";
                    }
                }                
                
                $html .= '<li><input type="checkbox" name="'.$this->post_meta.'[]" value="'.$this->data_taxonomy[$i]->term_id.'" '.($checked==""?"":'checked="checked"').' " >'.$this->data_taxonomy[$i]->name.'</option></li>';
            }
			
			$html .= "</ul>";
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
                wp_set_object_terms ( $post_id, array_map( 'intval', $_POST[$this->post_meta] ), $this->taxonomy );
                
            }
            else
            {                
                delete_post_meta ( $post_id, $this->post_meta );
                wp_set_object_terms ( $post_id, "", $this->taxonomy );
                
                /*$terms = get_the_terms( $post_id, $this->taxonomy );
                if ( $terms && ! is_wp_error( $terms ) )
                {
                    wp_remove_object_terms ( $post_id, $terms[0]->term_id, $this->taxonomy );
                }*/
                
            }
            
            
            
        }
        
        
        
        public function loadValue ( $post_id )
        {
            $terms = get_the_terms( $post_id, $this->taxonomy );
            if ( $terms && ! is_wp_error( $terms ) )
            {
                $this->value = $terms;
            }
            else
            {
                $this->value = array ();    
            }
        }
		
		
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