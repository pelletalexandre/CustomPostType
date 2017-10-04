<?php


if( !class_exists( 'CPT_Field_Featured_Image' ) ) {
    class CPT_Field_Featured_Image extends CPT_Field
    {
        /**
         * La fonction ne revoie rien car cela est déjà implémenté par WP
         *
         */ 
        public function display ( $post_id )
        {            
            return "";
        }
         
        /**
		 * Execution d'un script pour renommer la boite de l'image à la une
		 */
		public function executeJavascript ( )
		{
			$js = 'jQuery(document).ready ( function () { jQuery("#postimagediv>h3>span, #postimagediv>h2>span").html("'.$this->libelle.'"); }  );';
			
			return $js;
		}
        /**
         * La fonction ne charge rien car elle est déjà implémentée pas WP
         *
         */
        public function loadValue ( $post_id )
        {
            $arr_image = array();
            
            //Identifiant de la miniature
            $tumbnail_id = (int) get_post_thumbnail_id ( $post_id );

/*
            ob_start ();
            echo "tableau des tailles\n";
            var_dump($this->arr_img_size);
            $f = fopen(__DIR__."/temp3.txt", "a+");
            fputs($f, $tumbnail_id ."\n");
*/
            
            //On boucle sur les tailles d'images voulues
            foreach ( $this->arr_img_size as $size )
            {
                $thumb = wp_get_attachment_image_src ( $tumbnail_id, $size );
                
                $arr_image [ $size ] = $thumb[0];
                
                //fputs($f, $size ."\n");
            }
            
            
/*        	var_dump($arr_image);
            fputs($f, ob_get_clean() ."\n");
            fclose($f);
*/
            $this->value =  $arr_image;
        }
        
        /**
         * La fonction ne sauvegarde rien car elle est déjà implémentée pas WP
         *
         */
        public function saveValue ( $post_id )
        {
        }

        
        /**
         * Seule méthode qu'il faut implémenter pour renvoyer un tableau d'url des documents
         *
         * @param	$object		object		Object de l'api rest
         *
         * @return	array	Le tableau des urls de documents
         */
        public function getRestValue ( $object )
        {
			$arr_image = array();
            
            //Identifiant de la miniature
            $tumbnail_id = (int) get_post_thumbnail_id ( $object['id'] );

/*
            ob_start ();
            echo "tableau des tailles\n";
            var_dump($this->arr_img_size);
            $f = fopen(__DIR__."/temp3.txt", "a+");
            fputs($f, $tumbnail_id ."\n");
*/
            
            //On boucle sur les tailles d'images voulues
            foreach ( $this->arr_img_size as $size )
            {
                $thumb = wp_get_attachment_image_src ( $tumbnail_id, $size );
                
                $arr_image [ $size ] = $thumb[0];
                
                //fputs($f, $size ."\n");
            }
            
            /*
        	var_dump($arr_image);
            fputs($f, ob_get_clean() ."\n");
            fclose($f);
*/
			
            return $arr_image;
		
		
            
        }        
    };
}
    
if( !class_exists( 'CPT_Field_Input_Text' ) ) {

    class CPT_Field_Input_Text extends CPT_Field
    {
        public function display ( $post_id, $front=false, $value="", $css_div="" )
        {
			$html = '';
			if ( !$front )
			{
				$this->loadValue ( $post_id );
				
				
				$html .= $this->createStartTag ();
				$html .= '<input type="text"  value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
				$html .= $this->createEndTag ();
				
				//var_dump($this->blank_line_after);
			}
            else
			{				
				$editable = (($this->editable_front==false)?'disabled="disabled"':'');
				$can_be_empty = $this->can_be_empty;
				if ( $this->editable_front==false )$can_be_empty=true;
				
				$html .= '<div class="form-group '.$css_div.'">';
				$html .= '<label class="label-titre-field">'.$this->libelle.(($can_be_empty == false)?' *':'').'</label>';
				$html .= '<input type="text" '.$editable.' value="'.$value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'" '.(($this->enable==false)?'readonly':'').' />';
				$html .= '</div>';
				
			}
            return $html;
        }
    };
}

if( !class_exists( 'CPT_Field_Input_Date' ) ) {

	class CPT_Field_Input_Date extends CPT_Field
    {
		public function __construct ( $arr_data, $cpt )
		{
			parent::__construct  ( $arr_data, $cpt );
			
			
			$this->arr_script = array (
									   array ( "fichier"	=>	"input_date.js", "systeme"	=>	false),
									   array ( "fichier"	=>	"jquery-ui-datepicker", "systeme"	=>	true)
									   );
			
			$this->arr_style = array (
										"jquery-ui.min.css"  
									  );
		}
        public function display ( $post_id, $front=false, $value="", $css_div="" )
        {
            
            $html = "";
			
			if ( !$front ) 
			{
				$this->loadValue ( $post_id );
                //die('<input type="text"  value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>');
				$html .= $this->createStartTag ();
				$html .= '<input type="text"  value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
				$html .= $this->createEndTag ();
			}
			else
			{
				$editable = (($this->editable_front==false)?'disabled="disabled"':'');
				$can_be_empty = $this->can_be_empty;
				if ( $this->editable_front==false )$can_be_empty=true;
				
				$html .= '<div class="form-group '.$css_div.'">';
				$html .= '<label class="label-titre-field">'.$this->libelle.(($can_be_empty == false)?' (*)':'').'</label>';
				$html .= '<input type="text" '.$editable.' value="'.$value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
				$html .= '</div>';
			}
			
			$html .= $this->displayAfter ();
            
            return $html;
        }
        
		public function executeJavascript ( )
		{
			$js = "initDatePicker ( '".$this->id."', '".$this->value."' );";
			
			return $js;
		}
		
		public function saveValue ( $post_id )
        {
			if ( $this->validate (  ) && $this->special_validation () )
            {
				update_post_meta ( $post_id, $this->post_meta, $_POST[$this->post_meta] );
				update_post_meta ( $post_id, $this->post_meta.'_julien', JourJulien($_POST[$this->post_meta]) );      
            }
            else
            {
                delete_post_meta ( $post_id, $this->post_meta );
            }
        }
		
		
        
    };
}



    

if( !class_exists( 'CPT_Field_Input_Url' ) ) {

    class CPT_Field_Input_Url extends CPT_Field_Input_Text
    {
        public function display ( $post_id, $front=false, $value='', $css_div="" )
        {
            $this->loadValue ( $post_id );
            $html = "";
            
            $html .= $this->createStartTag ();
            $html .= '<input type="url"  value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
            $html .= $this->createEndTag ();
            
            return $html;
        }
    }
}


if( !class_exists( 'CPT_Field_Input_Email' ) ) {
    
    class CPT_Field_Input_Email extends CPT_Field_Input_Text
    {
        public function display ( $post_id, $front=false, $value="", $css_div="" )
        {
            $this->loadValue ( $post_id );
            $html = "";
            
			if ( !$front )
			{
				$html .= $this->createStartTag ();
				$html .= '<input type="email"  value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
				$html .= $this->createEndTag ();
			}
			else
			{ 
				$editable = (($this->editable_front==false)?'disabled="disabled"':'');
				$can_be_empty = $this->can_be_empty;
				if ( $this->editable_front==false )$can_be_empty=true;
				
				$html .= '<div class="form-group '.$css_div.'">';
				$html .= '<label class="label-titre-field">'.$this->libelle.(($can_be_empty == false)?' (*)':'').'</label>';
				$html .= '<input type="text" '.$editable.' value="'.$value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';				
				$html .= '</div>';
			}
			
            
            return $html;
        }
		
		public function validate ( )
		{
			//On passe la validation par défaut.
			$ret = parent::validate ( );
			
            //var_dump($this->can_be_empty);
            //var_dump($ret);die();

            //On peut être vide et on est vide, donc, on return true.
            if ( $this->can_be_empty == true && $this->value == "" )
            {
                return true;
            }


			if ( $ret == true )
			{
				if ( is_email ( $this->value ) )
				{
					$ret = true;
				}
				else
				{
					$this->error = true;
                    $this->error_message .= "<li>Le champ '".$this->libelle."' n'est pas une adresse mail valide.</li>";
                    
                    $ret = false;
				}
			}
			
			return $ret;
		}
    }
}
//"#^0[1-9]([-. ]?[0-9]{2}){4}$#" 



if( !class_exists( 'CPT_Field_Input_Phone' ) ) {
    
    class CPT_Field_Input_Phone extends CPT_Field_Input_Text
    {
        public function display ( $post_id, $front=false, $value="", $css_div="" )
        {
            $this->loadValue ( $post_id );
            $html = "";
            
			if ( !$front )
			{
				$html .= $this->createStartTag ();
				$html .= '<input type="text"  value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
				$html .= $this->createEndTag ();
			}
			else
			{
				$editable = (($this->editable_front==false)?'disabled="disabled"':'');
				$can_be_empty = $this->can_be_empty;
				if ( $this->editable_front==false )$can_be_empty=true;
				
				$html .= '<div class="form-group '.$css_div.'">';
				$html .= '<label class="label-titre-field">'.$this->libelle.(($can_be_empty == false)?' (*)':'').'</label>';
				$html .= '<input type="text" '.$editable.' value="'.$value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';				
				$html .= '</div>';
			}
			
            
            return $html;
        }
		
		public function validate ( )
		{
			//On passe la validation par défaut.
			$ret = parent::validate ( );
			
            //On peut être vide et on est vide, donc, on return true.
            if ( $this->can_be_empty == true && $this->value == "" )
            {
                return true;
            }
            
			if ( $ret == true )
			{
				$matches = null;
				$returnValue = preg_match('#^0[1-9]([-. ]?[0-9]{2}){4}$#', $this->value, $matches);

				if ( $returnValue == 1 )
				{
					$ret = true;
				}
				else
				{
					$this->error = true;
                    $this->error_message .= "<li>Le champ '".$this->libelle."' n'est pas un numéro de téléphone valide. ex: 0610131415</li>";
                    
                    $ret = false;
				}
			}
			
			return $ret;
		}
    }
}


if( !class_exists( 'CPT_Field_Input_Hidden' ) ) {

    class CPT_Field_Input_Hidden extends CPT_Field_Input_Text
    {
        public function display ( $post_id, $front=false, $value='', $css_div="" )
        {
            $this->loadValue ( $post_id );
            $html = "";
            
            $html .= '<input type="hidden"  value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" />';
            
            return $html;
        }
    }
}

    
if( !class_exists( 'CPT_Field_Textarea' ) ) {

	class CPT_Field_Textarea extends CPT_Field_Input_Text
    {
        public function display ( $post_id, $front=false, $value="", $css_div=""  )
        {
			$html = '';
			//textarea_max_length
			//maxlength
			$max = "";
			if ( $this->textarea_max_length != -1 )
			{
				$max = ' maxlength="'.$this->textarea_max_length.'" ';
			}
			if ( !$front )
			{
				$this->loadValue ( $post_id );
				
				$html = $this->createStartTag ();
				$html .= '<textarea id="'.$this->id.'" name="'.$this->id .'" '.$max.'>'.$this->value.'</textarea>';
				$html .= $this->createEndTag ();
			}
			else
			{
				$html .= '<div class="form-group">';
				$html .= '<label class="label-titre-field">'.$this->libelle.(($this->can_be_empty == false)?' (*)':'').'</label>';
				$html .= '<textarea id="'.$this->id.'" name="'.$this->id .'" '.$max.'>'.$value.'</textarea>';
				$html .= '</div>';
			}

            
            return $html;
        }
    }
}

    
if( !class_exists( 'CPT_Field_Taxonomy' ) ) {

	class CPT_Field_Taxonomy extends CPT_Field
    {
        public function display ( $post_id )
        {
			$this->loadValue ( $post_id );
			
			$this->loadPostTaxonomy();
			
            $html = $this->createStartTag ();
            
			$html .= $this->displayFlat ();

            $html .= $this->createEndTag ();
            
            return $html;
        }
        
		
		
		protected function displayFlat ( )
		{
			$html = "";
			$html .= '<select name="'.$this->id.'" id="search_id-'.$this->id.'" >';
            $html .= (($this->first_option == '')?'':$this->first_option);
            
			foreach ( $this->post_taxonomies as $post_taxonomy )
			{
				$html .= '<option value="'.$post_taxonomy->name.'" '.(($this->value==$post_taxonomy->name)?'selected="selected"':'').'>'.$post_taxonomy->label.'</option>';
			}
			
            $html .= '</select>';
			
			return $html;
		}
        
        public function saveValue ( $post_id )
        {
            if ( $this->validate (  ) )
            {
                parent::saveValue ( $post_id );
				
            }
            else
            {
                delete_post_meta ( $post_id, $this->post_meta );
            }
        }
        
        
        
        public function loadValue ( $post_id )
        {
			parent::loadValue ( $post_id );		
			
        }
        
		
		public function loadValueJson ( $post_id )
        {
			return parent::loadValueJson ( $post_id );
        }
		
		public function getRestValue ( $object )
        {
			return parent::getRestValue ( $object );
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
    }
}
?>