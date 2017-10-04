<?php

if( !class_exists( 'CPT_Field_Checkbox_Post' ) ) {

    class CPT_Field_Checkbox_Post extends CPT_Field
    {
        
        public function display ( $post_id )
        {
            /*
$this->cpt_checkbox_display_type = $arr_data['extra']['post']['display_type'];

					$this->cpt_checkbox_display_field = $arr_data['extra']['post']['display_field'];

					$this->cpt_checkbox_name = $arr_data['extra']['post']['cpt_name'];

					$this->cpt_checkbox_hierarchical = $arr_data['extra']['post']['hierarchical'];
            */

            
            $this->loadValue ( $post_id );
            $this->loadDataPost ( $this->cpt_checkbox_name, $this->cpt_checkbox_display_field );

            $html = $this->createStartTag ();
            
            if ( $this->cpt_checkbox_hierarchical == false )
			{
				if ( $this->cpt_checkbox_display_type == "" || $this->cpt_checkbox_display_type == "INLINE")
				{
					$html .= $this->displayInline ();	
				}
				else if ( $this->cpt_checkbox_display_type == "LIST")
				{
					$html .= $this->displayList ( $post_id );	
				}
				else if ( $this->cpt_checkbox_display_type == "RADIO")
				{
					$html .= $this->displayList ( $post_id );	
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
                //var_dump($this->data_post[$i]);
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
        
		
		protected function displayList ( $post_id )
		{
			$html = "<ul class='ul-field-checkbox'>";

            for ( $i=0; $i<count($this->data_post); $i++ )
            {
                $checked = "";
                if ( count($this->value) > 0 )
                {
                    if ( in_array($this->data_post[$i]->post_id, $this->value) )
                    {
                        $checked = "checked";
                    }
                }                
                
                $html .= '<li><input type="checkbox" name="'.$this->post_meta.'[]" value="'.$this->data_post[$i]->post_id.'" '.($checked==""?"":'checked="checked"').' " >'.$this->data_post[$i]->post_title.'</option>';
                
                //Affichage de la zone de texte pour saisir l'ordre du post lors de l'affichage
                if ( $this->cpt_checkbox_ordering == true )
                {   
                    //var_dump($this->post_meta.'_'.$this->data_post[$i]->post_id);
                    $val = get_post_meta ( $post_id, $this->post_meta.'_'.$this->data_post[$i]->post_id, true );
                    //var_dump($val);die();
                    $order_value = ($val=='')?0:$val;
                    $html .= ' / Ordre <input style="width:50px;display:inline;" type="text" value="'.$order_value.'" id="" name="'.$this->post_meta.'_'.$this->data_post[$i]->post_id.''.'"/>';//_order_cpt_checkbox -> pas certain d'en avoir besoin
                }
                    
                
                $html .= '</li>';
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
            
                //On doit sauvegarder l'ordre des posts, le cas échéant
                if ( $this->cpt_checkbox_ordering == true )
                {
                    foreach ( $_POST as $key=>$val )
                    {
                        //On ne cherche que les variables qui traitent de l'ordre des posts associés
                        if ( substr ( $key, 0, strlen ( $this->post_meta )+1) == $this->post_meta.'_' )
                        {
                            //var_dump ( $key );
                            //var_dump ( $val );
                            //$cpt_post_id = substr ( $key, strlen ( $this->post_meta ) + 1 );
                            //var_dump($cpt_post_id);
                            //On sauvegarde pour le post en cours, la valeur de l'ordre pour le post associé
                            update_post_meta ( $post_id, $key, $val );
                        }
                    }
                    //var_dump($_POST);die();
                }
                
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
                //echo "<br>tmp=''";
                $this->value = array ();   
            }

            //Il faut ordonner les valeurs
            if ( $this->cpt_checkbox_ordering == true )
            {
                $arr = array ();
                //On parcourt les valeurs et on récupère l'ordre associé à chacune
                foreach ( $this->value as $item_id )
                {
                    $o = new stdClass();
                    $o->id = $item_id;
                    $o->order = get_post_meta($post_id, $this->post_meta.'_'.$o->id, true);
                    $arr[] = $o;
                }
                //On trie les valeurs en fonction de l'ordre
                usort($arr, array($this, "cmp"));

                //On réaffecte les valeurs dans le bon ordre
                $this->value = array ();
                foreach ( $arr as $item )
                {
                    $this->value[] = $item->id;
                }
            }

            
        }

        protected function cmp($a, $b)
        {
            return strcmp($a->order, $b->order);
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