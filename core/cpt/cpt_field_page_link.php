<?php
//Propose le choix d'une ou plusieurs pages
if( !class_exists( 'CPT_Field_Page_Link' ) ) 
{

    class CPT_Field_Page_Link extends CPT_Field
    {       
        public $url = "";
        /* 
        public function __construct()
        {
            die('construct');
        }
        */
        public function display ( $post_id )
        {
            $this->loadValue ( $post_id );
            $this->loadDataPage (  );
            //var_dump($this->data_page);

            $html = $this->createStartTag ();
            
            $html .= $this->displaySelect ();
            /*if ( $this->cpt_checkbox_hierarchical == false )
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
			}*/
            $html .= $this->createEndTag ();
            
            return $html;
        }
        
        protected function displaySelect ()
        {
            $html = '';
            
            $html .= '<select name="'.$this->id.'" id="search_id-'.$this->id.'" >';
            $html .= '<option value="-1">Choisir une page</option>';
            //$html .= (($this->first_option == '')?'':$this->first_option);
            for ( $i=0; $i<count($this->data_page); $i++ )
            {
                $html .= '<option value="'.$this->data_page[$i]->post_id.'" '.(($this->value==$this->data_page[$i]->post_id)?'selected="selected"':'').'>'.$this->data_page[$i]->post_title.'</option>';
            }
		
            $html .= $options.'</select>';

            return $html;
        }

		protected function displayHierarchical ( )
		{
			$html = "";
			/*$options = "";
			
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
            */
			
			return $html;
		}
		
		protected function displayInline ( )
		{
			$html = "";

            //On parcourt chacun des post type pour créer une entrée dans une liste
            for ( $i=0; $i<count($this->data_post); $i++ )
            {
                var_dump($this->data_post[$i]);
                $checked = "";
                if ( count($this->value) > 0 )
                {
                    
                    if ( in_array($this->data_post[$i]->post_id, $this->value) )
                    {
                        $checked = "checked";
                    }
                }                
                
                $html .= '<input type="checkbox" name="'.$this->post_meta.'[]" value="'.$this->data_post[$i]->post_id.'" '.($checked==""?"":'checked="checked"').' " >'.$this->data_post[$i]->post_title.'</option>';
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
            if ( $this->validate (  ) && $this->special_validation () )
            {
				update_post_meta ( $post_id, $this->post_meta, $_POST[$this->post_meta] );      
            }
            else
            {
                delete_post_meta ( $post_id, $this->post_meta );
            }

            
            /*
            //La valeur récupérée est un tableau qui contient les term_id sélectionnés
            if ( $this->validate (  ) )
            {
                //On enregistre un post_meta dans tous les cas.
                parent::saveValue ( $post_id );
            
                //On associe les taxonomies au post
                
            }
            else
            {                
                delete_post_meta ( $post_id, $this->post_meta );
                
               
                
            }
            */
            
            
        }
        
        
        
        public function loadValue ( $post_id )
        {
            parent::loadValue ( $post_id );

            $this->url = get_permalink ( $post_id );
			
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
            $this->value = $_POST[$this->post_meta];

            //Le champ ne doit pas valoir -1
            
            if ( $this->can_be_empty == false && $this->value == -1 )
            {
                $this->error = true;
                $this->error_message .= "<li>Le champ '".$this->libelle."' doit avoir une valeur séléctionnée.</li>";
                $b_ret = false;
            }
            
            return $b_ret;
        }

    };
}

?>