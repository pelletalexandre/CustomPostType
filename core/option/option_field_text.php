<?php

if( !class_exists( 'Option_Field_Input_Text' ) ) {

    class Option_Field_Input_Text extends Option_Field 
    {
        public function display ( )
        {
            $this->loadValue ( );
            $html = "";
            
            $html .= $this->createStartTag ();
            $html .= '<input type="text"  value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
            $html .= $this->createEndTag ();
            
            return $html;
        }
    };
}


if( !class_exists( 'Option_Field_Input_Float' ) ) {

    class Option_Field_Input_Float extends Option_Field
    {
        public function display ( )
        {
            $this->loadValue ( );
            $html = "";
            
            $html .= $this->createStartTag ();
            $html .= '<input type="text"  value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
            $html .= $this->createEndTag ();
            
            
            $html .= "
            <script>
            
            jQuery(document).ready (function ()
            {
                jQuery('#".$this->id."').keypress(function(event)
                {
                    if ((event.which != 46 || jQuery(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57))
                    {
                        event.preventDefault();
                    }
                });
            });
            </script>";
            
            return $html;
        }
    };
}



if( !class_exists( 'Option_Field_Visual_Editor' ) ) {
    
    class Option_Field_Visual_Editor extends Option_Field
    {
        public function display ( )
        {
            $this->loadValue ( );
            $html = "";
            
            ob_start();
            
            $html .= $this->createStartTag ();
            wp_editor( $this->value, $this->id );
            /*$html .= $this->createStartTag ();
            $html .= '<input type="text"  value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
            $html .= $this->createEndTag ();
            */
            $this->createEndTag ();
            $html = ob_get_clean ();
            return $html;
        }
        
        /**
         * Chargement de la valeur du champ en base de données
         *
         */
        public function loadValue (  )
        {
            $this->value =  get_option ( $this->id, "" );
            $this->value = stripcslashes ( $this->value );
            
            $this->value = apply_filters('the_content', $this->value);
            $this->value = str_replace(']]>', ']]&gt;', $this->value);
        }
        
        
        /**
         * Enregistrement de la valeur du champ en base de données
         *
         */
        public function saveValue ( )
        {
            if ( $this->validate (  ) && $this->special_validation () )
            {
                update_option ( $this->id, wp_kses_post($this->value) );
            }
            else
            {
                delete_option ( $this->id );
            }
        }
        
        
        //wp_kses_post
    };
}


?>