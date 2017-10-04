<?php


if( !class_exists( 'CPT_Field_Post_Link' ) ) {


    class CPT_Field_Post_Link extends CPT_Field
    {

        
		
        
        //Le libelle pour autocomplétion
        public $arr_label;
        public $arr_all;
        
        public $value_libelle;
        
        public function __construct ( $arr_data, $cpt )
        {
            parent::__construct ( $arr_data, $cpt );
            
            $this->arr_script = array (
									   array ( "fichier"	=>	"jquery-ui-autocomplete", "systeme"	=>	true)
									   );
			
			/*$this->arr_style = array (
										"jquery-ui.min.css"  
									  );
									  */
        }
        
        
        public function display ( $post_id )
        {
            $this->loadValue ( $post_id );
            //$this->loadDataTaxonomy ();
                                   
            $html = $this->createStartTag ();
            
			if ( $this->linked_post_display == "autocomplete" )
			{
				//Liste des posts
				$this->arr_label =  array ( );
				$this->arr_all =  array ( );
				load_liste_post ( $this->linked_post_type, $this->arr_label, $this->arr_all );
				
				
				$html .= $this->initJavascript ( );
				
				$html .= '<input type="text" id="'.$this->post_meta.'_lib" name="'.$this->post_meta.'_lib"> 
					<input type="hidden" id="'.$this->post_meta.'_id" name="'.$this->post_meta.'_id" />
					<input type="hidden" id="'.$this->post_meta.'_name" name="'.$this->post_meta.'_name" />
					<label class="field-help" ><?php echo ""; ?></label>';
			}
            else if ( $this->linked_post_display == "select" )
			{
				
				$this->loadDataLinkedPost();
				$html .= "";
				$html .= '<select name="'.$this->id.'" id="search_id-'.$this->id.'" >';
				$html .= (($this->linked_post_first_option == '')?'':$this->linked_post_first_option);
				
				for ( $i=0; $i<count($this->data_linked_post); $i++ )
				{
					$html .= '<option value="'.$this->data_linked_post[$i]->id.'" '.(($this->value==$this->data_linked_post[$i]->id)?'selected="selected"':'').'>'.$this->data_linked_post[$i]->label.'</option>';
				}
			
				$html .= '</select>';
			}
            
            $html .= $this->createEndTag ();
            
            return $html;
        }
        
        
        protected function initJavascript ( )
        {
            //Le champ value correspond à l'identifiant du post lié
            $h = '
            <script>   
            	var arr_label_'.$this->post_meta.' = '.json_encode ( $this->arr_label ).'  ;
                var arr_all_'.$this->post_meta.' = '.json_encode ( $this->arr_all ).' ;
                
                jQuery ( document ).ready ( function ()
                {
		
            		//Initialisation
                    jQuery( "#'.$this->post_meta.'_lib" ).autocomplete(
                    {
                        source: arr_label_'.$this->post_meta.',
                        select : function (event, ui)
                        {    
                            jQuery("#'.$this->post_meta.'_id").val ( arr_all_'.$this->post_meta.'[ui.item.label].value );
                            jQuery("#'.$this->post_meta.'_name").val ( ui.item.label );
                        }   ,
                        create : function (event, ui)
                        {
							//Ne fonctionne pas pour séléctionner un élément au démarrage su deux éléments ont la même racine.
                            //jQuery("#'.$this->post_meta.'_lib").autocomplete("search","'.$this->value_libelle.'");
							//var menu=jQuery("#'.$this->post_meta.'_lib").autocomplete("widget");
                            //jQuery(menu[0].children[0]).click();

							
							jQuery( "#'.$this->post_meta.'_lib" ).autocomplete({}).val("'.$this->value_libelle.'")/*.data("autocomplete")*/.trigger("select");
                            
							if ( "'.$this->value_libelle.'" != "" )
							{
								jQuery("#'.$this->post_meta.'_id").val ( arr_all_'.$this->post_meta.'["'.$this->value_libelle.'"].value );
								jQuery("#'.$this->post_meta.'_name").val ( "'.$this->value_libelle.'" );
							}
							
							
							
                        }
                    });
		
            	});

        	</script>';
    
            return $h;
        }
        
		
        
        public function saveValue ( $post_id )
        {
            
            //La valeur récupérer est un tableau qui contient les term_id sélectionnés
            if ( $this->validate (  ) )
            {
                //On enregistre un post_meta dans tous les cas.
                update_post_meta ( $post_id, $this->post_meta."_id", $_POST[$this->post_meta] );
				update_post_meta ( $post_id, $this->post_meta, $_POST[$this->post_meta] );
                //update_post_meta ( $post_id, $this->post_meta."_name", $_POST[$this->post_meta."_name"] );   
            
            }
            else
            {                
				delete_post_meta ( $post_id, $this->post_meta );
                //delete_post_meta ( $post_id, $this->post_meta."_name" );
            }
            
            
            
        }
        
        
        
        public function loadValue ( $post_id )
        {
            //On charge via la méthode parent
            parent::loadValue ( $post_id );

            //On charge la deuxième attendue
            //$this->value_libelle = get_post_meta ( $post_id, $this->post_meta."_name", true );
			if ( $this->value=="" )
			{
				$this->value_libelle = "";
			}
			else
			{
				$post = get_post ( $this->value );
				//echo "#".$this->value."#";
				//var_dump($post);die();
				$this->value_libelle = $post->post_title;
				//var_dump($this->value_libelle);
			}
            
        }
		
		
		public function loadValueJson ( $post_id )
        {
			$post = get_post ( $this->value );
			
            $o = new stdClass ();
			$o->{"id"} = get_post_meta($post_id, $this->post_meta."_id", true );
			$o->{"name"} = $post->post_title;//get_post_meta($post_id, $post->post_title, true );
            
			return $o;
        }
        
		
		
		
        
        public function validate ( )
        {
            $b_ret = true;
            $can_be_empty_option = false;
			
            //Le champ ne doit pas être vide
			$can_be_empty_option = $this->canBeEmptyOption ();
			
            /*if ( $this->can_be_empty == false )
            {
				//On teste si le champs a une condition pour être empty
				if ( $this->can_be_empty_option != null )
				{
					//La valeur du champ lié
					$condition_value = $_POST [ $this->can_be_empty_option['field'] ];
											   
					//Si c'est une taxonomy, on récupère le slug
					if ( $this->can_be_empty_option['type'] == CPT_Field::TYPE_RADIO )
					{
						$term = get_term ( $_POST [ $this->can_be_empty_option['field'] ], $this->can_be_empty_option['taxonomy'] );
						$condition_value = $term->slug;
					}
					
					if ( $this->can_be_empty_option['operator'] == "EQUAL" )
					{
						if ( $condition_value == $this->can_be_empty_option['value'] )
						{
							$can_be_empty_option = true;
						}
					}
				}				
            }*/
			
			if ( $can_be_empty_option == false && $_POST[$this->post_meta."_name"] == "" && $this->can_be_empty == false )
			{
				$this->error = true;
				$this->error_message .= "<li>Le champ '".$this->libelle."' ne peut pas être vide</li>";
				
				$b_ret = false;
			}
            /*if ( $this->can_be_empty == false && $_POST[$this->post_meta."_name"] == "" )
            {
                $this->error = true;
                $this->error_message .= "<li>Le champ '".$this->libelle." doit avoir une valeur</li>";
                $b_ret = false;
            }*/
            return $b_ret;
        }

    };
}

?>