<?php

    
if( !class_exists( 'CPT_Field_Select' ) ) {
	
	class CPT_Field_Select extends CPT_Field
    {
        
        public function display ( $post_id )
        {
			 $this->loadValue ( $post_id );
			
			
			if ( $this->taxonomy != '' )
				$this->loadDataTaxonomy ();
            else if ( $this->database != null )
				$this->loadDataDatabase ();
			
            $html = $this->createStartTag ();
            
            if ( $this->taxonomy_hierarchical == false )
			{
				$html .= $this->displayFlat ();
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
		
		protected function displayFlat ( )
		{
			$html = "";
			$html .= '<select name="'.$this->id.'" id="search_id-'.$this->id.'" >';
            $html .= (($this->first_option == '')?'':$this->first_option);
            
			if ( $this->taxonomy != '' )
			{
				for ( $i=0; $i<count($this->data_taxonomy); $i++ )
				{
					$html .= '<option value="'.$this->data_taxonomy[$i]->term_id.'" '.(($this->value==$this->data_taxonomy[$i]->term_id)?'selected="selected"':'').'>'.$this->data_taxonomy[$i]->name.'</option>';
				}
			}
			
			else if ( $this->database != null )
			{
				//var_dump($this->value);
				//var_dump($this->data_database);die();
				for ( $i=0; $i<count($this->data_database); $i++ )
				{
					$html .= '<option value="'.$this->data_database[$i]->id.'" '.(($this->value==$this->data_database[$i]->id)?'selected="selected"':'').'>'.$this->data_database[$i]->libelle.'</option>';
				}
			}
		
            $html .= '</select>';
			
			return $html;
		}
        
        public function saveValue ( $post_id )
        {
			//var_dump($this->taxonomy);
			//var_dump($_POST);die();
            if ( $this->validate (  ) )
            {
                parent::saveValue ( $post_id );
            
				if ( $this->taxonomy != '' )
					wp_set_object_terms ( $post_id, array(intval($_POST[$this->post_meta])), $this->taxonomy );
					
				//die(get_post_meta($post_id, 'offre_departement', true));
            }
            else
            {
                delete_post_meta ( $post_id, $this->post_meta );
                
				if ( $this->taxonomy != '' )
				{
					$terms = get_the_terms( $post_id, $this->taxonomy );
					if ( $terms && ! is_wp_error( $terms ) )
					{
						wp_remove_object_terms ( $post_id, $terms[0]->term_id, $this->taxonomy );
					}
				}
                
            }
            
            
        }
        
        
        
        public function loadValue ( $post_id )
        {
			if ( $this->taxonomy != '' )
			{
				$terms = get_the_terms( $post_id, $this->taxonomy );
				if ( $terms && ! is_wp_error( $terms ) )
				{
					$this->value = $terms[0]->term_id;
				}
				else
				{
					$this->value = -1;    
				}
			}
			else if ( $this->database != null )
			{
				parent::loadValue ( $post_id );				
			}
			
        }
        
		
		public function loadValueJson ( $post_id )
        {
			$o = new stdClass ();

			if ( $this->database != null )
			{
				$this->loadValue ( $post_id );
				
				
				$this->loadDataDatabase ( $this->database['id']['field']." = ".$this->value );
				
				$o->{"id"} = $this->value;
				$o->{"name"} = $this->data_database[0]->libelle;
			}
            
			return $o;
        }
		
		public function getRestValue ( $object )
        {
			//var_dump($object);
			if ( $this->taxonomy != '' )
			{
				$terms = get_the_terms( $object['id'], $this->taxonomy );
				if ( $terms && ! is_wp_error( $terms ) )
				{
					$this->value = $terms[0]->name;
				}
			}
			else if ( $this->database != null )
			{
				$this->value = parent::loadValue ( $post_id );
			}
            return $this->value ;
        }
		
        
        public function validate ( )
        {
            $b_ret = true;
            
            //Le champ ne doit pas être vide
            if ( $this->can_be_empty == false )
            {
                $this->value = $_POST[$this->post_meta];
                
                if ( $this->value == -1 )
                {
                    $this->error = true;
                    $this->error_message = "Vous devez séléctionner une valeur pour le champ '".$this->libelle."'.";
                    
                    $b_ret = false; 
                }
            }
            
            return $b_ret;
        }

    };
}

?>