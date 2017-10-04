<?php 

if( !class_exists( 'CPT_Field_Visual_Editor' ) ) {
    
    class CPT_Field_Visual_Editor extends CPT_Field
    {
        public function display ( $post_id )
        {
            $this->loadValue ( $post_id );
            $html = "";
            
            ob_start();
            
            echo $this->createStartTag ();

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
        public function loadValue ( $post_id )
        {
            $this->value =  get_post_meta ( $post_id, $this->post_meta, true );
            $this->value = stripcslashes ( $this->value );
            
            $this->value = apply_filters('the_content', $this->value);
            $this->value = str_replace(']]>', ']]&gt;', $this->value);
        }
        
        
        /**
         * Enregistrement de la valeur du champ en base de données
         *
         */ 
        public function saveValue ( $post_id )
        {
            if ( $this->validate (  ) && $this->special_validation () )
            {
                update_post_meta ( $post_id, $this->post_meta, $_POST[wp_kses_post($this->post_meta)]);
            }
            else
            {
                delete_post_meta ( $post_id, $this->post_meta );
            }
        }
        
        
        //wp_kses_post
    };
}