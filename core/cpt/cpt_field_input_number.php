<?php

if( !class_exists( 'CPT_Field_Input_Number' ) )
{

    class CPT_Field_Input_Number extends CPT_Field_Input_Text
    {
        public function display ( $post_id, $front=false, $value="", $css_div="" )
        {
            //var_dump($this);die();
            $this->loadValue ( $post_id );
            $html = "";
            
            $min = ((intval($this->number_min)==-1)?'':' min="'.$this->number_min.'" ');
            $max = ($this->number_max!=-1)?' max="'.$this->number_max.'" ':'';
            $step = ($this->number_step!=-1)?' step="'.$this->number_step.'" ':'';
            
            
			if ( !$front )
			{
                
                
				$html .= $this->createStartTag ();
				$html .= '<input type="number"  '.$min.$max.$step.' value="'.$this->value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';
				$html .= $this->createEndTag ();
			}
			else
			{
				$editable = (($this->editable_front==false)?'disabled="disabled"':'');
				$can_be_empty = $this->can_be_empty;
				if ( $this->editable_front==false )$can_be_empty=true;
				
				$html .= '<div class="form-group '.$css_div.'">';
				$html .= '<label class="label-titre-field">'.$this->libelle.(($can_be_empty == false)?' (*)':'').'</label>';
				$html .= '<input type="text" '.$editable.' '.$min.$max.$step.' value="'.$value.'" id="'.$this->id.'" name="'.$this->id.'" placeholder="'.$this->placeholder.'"/>';				
				$html .= '</div>';
			}
			
            
            return $html;
        }
    }
}

?>