<?php

if( !class_exists( 'CPT_Field_Radio_Custom_Values' ) ) {

    class CPT_Field_Radio_Custom_Values extends CPT_Field
    {
        public function __construct ( $arr_data, $cpt )
		{
			parent::__construct  ( $arr_data, $cpt );
		}
        
        public function display ( $post_id, $front=false, $value="" )
        {
            $html = '';
            
            $this->loadValue ( $post_id );
			                
			//Backoffice
            if ( !$front )
            {
                $html .= $this->createStartTag ();
								
                $style = "cpt-ul cpt-ul-list";                
                if ( $this->display_type == "LIST" )
                {
                    $style = 'cpt-ul cpt-ul-list'; 
                }
                else if ( $this->display_type == "COL" )
                {
                    $style = 'cpt-ul cpt-ul-column'; 
                }
				
				$html .= $this->createList ( $this->radio_custom_values, $this->name, $this->value, $style );	
                
                
                $html .= $this->createEndTag ();
                
                
                //On gère l'eventualité où un champs serait lié
                if ( $this->linked_field == true )
                {
                    $html .= $this->initLinkedField ( );
                }
                
            }
            /*else
            {
                $style = "cpt-ul cpt-ul-list";
                
                if ( $this->display_type == "LIST" )
                {
                    $style = 'cpt-ul cpt-ul-list'; 
                }
                else if ( $this->display_type == "COL" )
                {
                    $style = 'cpt-ul cpt-ul-column'; 
                }

				$html .= '<div class="form-group">';
                $html .= '<label class="label-titre-field">'.$this->libelle.(($this->can_be_empty == false)?' (*)':'').'</label>';
                $html .= '<ul class="'.$style.'">';
                
                for ( $i=0; $i<count ($this->data_taxonomy); $i++ )
                {
                    $html .= '<li>';
                                       
                    $html .= '<input type="radio" id="radio_'.$this->data_taxonomy[$i]->term_id.'" data-slug="'.$this->data_taxonomy[$i]->slug.'" value="'.$this->data_taxonomy[$i]->term_id.'" name="'.$this->name.'" '.(($value==$this->data_taxonomy[$i]->term_id)?'checked="checked"':'').' />';//

                    $html .= '<label for="radio_'.$this->data_taxonomy[$i]->term_id.'" class="label-li">'.$this->data_taxonomy[$i]->name.'</label>';

                    $html .= '</li>';
                }
                
                $html .= '</ul>';
				$html .= '</div>';
            }
            */
            return $html;
        }
        
        public function createList ( $data, $name, $value, $style )
		{
			$html = '<ul class="'.$style.'">';
            
            foreach ( $data as $key=>$val)
            {
                $html .= '<li>';

                $html .= '<input type="radio"  value="'.$key.'" name="'.$name.'" '.(($value==$key)?'checked="checked"':'').' />';//data-slug="'.$key.'"
                $html .= '<label for="radio_'.$key.'" class="label-li">'.$val.'</label>';

                $html .= '</li>';
            }

			$html .= '</ul>';
			
			return $html;
		}
        /*
        public function initLinkedField ( )
        {
            $h = "<script>";
            
            for ( $i=0; $i<count($this->linked_field_params); $i++ )
            {
                $param = $this->linked_field_params [$i];
                
                $field_id = $param['field_id'];
                $conditiions_show = $param['conditions']['show'];
                $conditiions_hide = $param['conditions']['hide'];
                $show_slug = $param['conditions']['show']['slug'];
                $hide_slug = $param['conditions']['hide']['slug'];
                
                
                $h .= '
                jQuery(document).ready ( function()
                {
                    //Gestion des clics pour hide / show
                    jQuery("input[name=\''.$this->name.'\']").click(function()
                    {
                        var data_slug = jQuery(this).attr(\'data-slug\');
                        //console.log ( data_slug );
                        
                        //SHOW
                        if ( data_slug == "'.$conditiions_show['slug'].'")
                        {
                            jQuery("#tr-'.$field_id.'").show ();                        
                        }
                        
                        //HIDE
                        if ( data_slug == "'.$conditiions_hide['slug'].'")
                        {
                            jQuery("#tr-'.$field_id.'").hide ();                        
                        }
                                                
                    });
                    
                    //Gestion de l état de visibilité au chargement
                    
                    //Si aucun radio n est coché, on masque les champs liés
                    if ( jQuery("input[name=\''.$this->name.'\']").is(\':checked\') == false )
                    {
                        jQuery("#tr-'.$field_id.'").hide ();
                    }
                    else
                    {
                        //On masque les champs liés si la valeur data-slug pour le radio séléctionné est hide
                        if ( jQuery("input[name=\''.$this->name.'\']:checked").attr(\'data-slug\') == "'.$hide_slug.'")
                        {
                            jQuery("#tr-'.$field_id.'").hide ();    
                        }
                    }
                    
                });
                    ';
                
            }
            
            $h .= "</script>";
            return $h;
        }*/
        
        public function saveValue ( $post_id )
        {
            
            if ( $this->validate (  ) )
            {            //echo 'validate: '.$_POST[$this->post_meta].' '.$post_id.' '.$this->post_meta;
                //parent::saveValue ( $post_id );
                update_post_meta( $post_id, $this->post_meta, $_POST[$this->post_meta] );
            }
            else
            {
                delete_post_meta ( $post_id, $this->post_meta );            
            }          
        }

        public function loadValue ( $post_id )
        {
            $this->value = get_post_meta ( $post_id, $this->post_meta, true );

            if ( $this->value == "" )
            {
                $this->value = $this->radio_custom_value_default;    
            }
        }
        
        /*
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
                $this->error_message .= "<li>Le champ '".$this->libelle."' doit avoir une séléction</li>";
                $b_ret = false;
            }
            
            return $b_ret;
        }
		
		public function printMail ( )
		{
			$term = get_term( $_POST[$this->post_meta], $this->taxonomy );
			
			$str_mail = "<div style='margin-bottom:15px;'>";
			
			$str_mail .= "<label><b>".$this->libelle." : </b></label>";
			$str_mail .= "<br/>";
			$str_mail .= "<label>".$term->name."</label>";
			
			$str_mail .= "</div>";
			return $str_mail;
		}
    }
}

?>