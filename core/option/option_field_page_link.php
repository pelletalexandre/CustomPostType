<?php
//Propose le choix d'une ou plusieurs pages
if( !class_exists( 'Option_Field_Page_Link' ) ) 
{

    class Option_Field_Page_Link extends Option_Field
    {       
        public $url = "";
        /* 
        public function __construct()
        {
            die('construct');
        }
        */
        public function display (  )
        {
            $this->loadValue (  );
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
        
		

        
        
        
        public function loadValue (  )
        {
            parent::loadValue (  );

            $this->url = get_permalink ( $this->value );
        }
		

        public function saveValue ( )
        {
            if ( $this->validate (  ) && $this->special_validation () )
            {
                update_option ( $this->id, $this->value );
                update_option ( $this->id.'_url', get_permalink ( $this->value ) );
            }
            else
            {
                delete_option ( $this->id );
                delete_option ( $this->id );
            }
        }
        
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