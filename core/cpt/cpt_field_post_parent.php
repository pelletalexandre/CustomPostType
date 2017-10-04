<?php

if( !class_exists( 'CPT_Field_Post_Parent' ) ) {

    class CPT_Field_Post_Parent extends CPT_Field_Input_Text
    {
        public function display ( $post_id, $front=false, $value='', $css_div="" )
        {
            //$this->loadValue ( $post_id );
            $html = "";
            
            $html .= '<input type="hidden"  value="" id="'.$this->id.'" name="'.$this->id.'" />';
            
            return $html;
        }
        
        public function executeJavascript ( )
		{
            //Il faut affecter la valeur du post_parent passée en paramètre
            $js = 'var post_parent_id=parseInt("'.$_GET['parent_id'].'");';
            $js .= 'jQuery("#'.$this->id.'").val ( post_parent_id );';
			//$js .= "media_manager.media.documents.init ( '".$this->post_meta."', '".$this->post_meta."', '".$this->post_meta."-liste', '".$this->post_meta."',".$display.", '".$this->media_type_document."' );";
			
			return $js;
		}
        
        public function saveValue ( $post_id )
        {
			
            if ( isset ( $_POST['post_parent_id'] ) )
            {
				//echo $_POST['post_parent_id'];
				
				
                remove_action ('save_post', array ( $this->cpt, "savePost" ) );
                wp_update_post(array('ID' => $post_id, 'post_parent' => $_POST[$this->id]));
                update_post_meta($post_id, 'post_parent_id', $_POST[$this->id]);
                add_action ('save_post', array ( $this->cpt, "savePost" ) );
				
				//die('after save ixi');
            }
            
        }
    }
}


?>