<?php

if( !class_exists( 'Option_Field_Input_Email' ) ) {

    class Option_Field_Input_Email extends Option_Field_Input_Text
    {
        public function display ( )
        {
            $this->loadValue ( );
            $html = "";
            
            $html .= $this->createStartTag ();
            $html .= '<input type="email"  value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
            $html .= $this->createEndTag ();
            
            return $html;
        }
        
        public function validate ( )
		{
			//On passe la validation par défaut.
			$ret = parent::validate ( );
			
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
    };
}

?>