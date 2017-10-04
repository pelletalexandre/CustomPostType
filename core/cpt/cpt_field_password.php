<?php

if( !class_exists( 'CPT_Field_Input_Password' ) ) {

    class CPT_Field_Input_Password extends CPT_Field
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
                /*
                
			$this->password_option->readable = false;
			$this->password_option->switch_readability =
			-
                */
                
                $default_value = $this->value;
                $type = "password";
                
                //var_dump($this->password_option);die();
                
                if ( $this->password_option->default_value == true && $this->value == "" )
                {
                    $default_value = wp_generate_password( $length=25, $include_standard_special_chars=true );
                }
                
                if ( $this->password_option->readable_default == true )
                {
                    $type = "text";
                }
                
                $html .= $this->createStartTag ();
                
                $html .= '<input type="'.$type.'"  value="'.$default_value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
                
                if ( $this->password_option->readable == true )
                {
                    
                }
                
                if ( $this->password_option->switch_readability == true )
                {
                    //Ajout du bouton pour afficher/masquer le mot de passe
                }
                
                
                $html .= $this->createEndTag ();
								
                
                
            }
            else
            {
                
            }
            
            return $html;
        }
        
       
       /*
        public function saveValue ( $post_id )
        {
            if ( $this->validate (  ) )
            {            
                wp_set_object_terms ( $post_id, array(intval($_POST[$this->post_meta])), $this->taxonomy );
            }
            else
            {
                $terms = get_the_terms( $post_id, $this->taxonomy );
                if ( $terms && ! is_wp_error( $terms ) )
                {
                    wp_remove_object_terms ( $post_id, $terms[0]->term_id, $this->taxonomy );
                }                
            }          
        }
        */
/*
        public function loadValue ( $post_id )
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
        */
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
        /*
        public function validate ( )
        {
            $b_ret = true;
            
            //Le champ ne doit pas être vide
            if ( $this->can_be_empty == false )
            {
                if ( !isset ( $_POST[$this->post_meta] ) )
                {
                    $this->error = true;
                    $this->error_message = "<li>Vous devez séléctionner une valeur pour le champ '".$this->libelle."'.</li>";
                    
                    $b_ret = false; false;
                }
            }
            
            return $b_ret;
        }
		*/
        /*
		public function printMail ( )
		{
			$term = get_term( $_POST[$this->post_meta], $this->taxonomy );
			
			$str_mail = "<div style='margin-bottom:15px;'>";
			
			$str_mail .= "<label><b>".$this->libelle." : </b></label>";
			$str_mail .= "<br/>";
			$str_mail .= "<label>".$term->name."</label>";
			
			$str_mail .= "</div>";
			return $str_mail;
		}*/
    }
}

?>