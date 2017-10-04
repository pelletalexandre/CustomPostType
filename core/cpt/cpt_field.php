<?php

if( !class_exists( 'CPT_Field' ) ) {
    class CPT_Field
    {
        const TYPE_INPUT_TEXT = 0;
        const TYPE_SELECT = 1;
        const TYPE_RADIO = 2;
        const TYPE_CHECKBOX = 3;
        const TYPE_INPUT_URL = 4;
        const TYPE_INPUT_HIDDEN = 5;
        const TYPE_INPUT_EMAIL = 6;
        const TYPE_TEXTAREA = 7;
        const TYPE_FEATURED_IMAGE = 8;
        const TYPE_INPUT_DATE = 9;
		const TYPE_MEDIA = 10;
		const TYPE_VISUAL_EDITOR = 11; 
		const TYPE_POST_LINK = 12;
		const TYPE_INPUT_PASSWORD = 13;
		const TYPE_POST_CHILDREN = 14;
		const TYPE_POST_PARENT = 15;
		const TYPE_INPUT_NUMBER = 16;
		const TYPE_TAXONOMY = 17;
		const TYPE_INPUT_PHONE = 18;
		const TYPE_CHECKBOX_POST = 19;
		//Choix d'une page wordpress
		const TYPE_PAGE_LINK = 20;
		const TYPE_SELECT_POST = 21;
		const TYPE_RADIO_CUSTOM_VALUES = 22;
        
        //cpt associé
        public $cpt = null;
        
        //Libellé du champ
        public $libelle = 'le libelle du champ';
        
        //Type du champ
        public $type = CPT_Field::TYPE_INPUT_TEXT;
        
        //La taxonomy associée, au besoin
        public $taxonomy = "";
		public $taxonomy_hierarchical = false;
		public $taxonomy_orderby = "name";
        
        //L'identifiant dans le formulaire
        public $id = "";
        
        //Le name dans le formulaire
        public $name = "";
        
        //La valeur du champ dans le formulaire
        public $value = "";
		//Valeur par défaut à affecter au champ
		public $default_value = "";
        
        //Nom du post meta associé
        public $post_meta = "";
    
        //Le placeholder pour un champ texte
        public $placeholder = "";
        
		//Le champ est-il enable
		public $enable = true;
		
        //Le bloc dans lequel ils sont placés
        public $bloc = "";
        
        //Le premier élément d'une liste déroulante
        public $first_option = "";
        
        //Est ce que la variable peut-être vide?
        public $can_be_empty = false;
		
		//Des conditions sont posées concernant le champ  qui peut être vide
		public $can_be_empty_option = null;
		
		//Type d'affichage, dépend des types de champs
		public $display_type = "";
		public $display_type_taxonomy = "COL";
        
        //Gestion des erreurs.
        public $error_message = "";
        public $error = false;
        
        //Condition de validation du champ
        public $validation = array ();
        
        //Les datas de la taxonomy
        public $data_taxonomy = null;
		
		//La liste des taxonomies affectées au post type
		public $post_taxonomies = null;
        
		//La liste des pages WP disponibles
		public $data_page = null;

		///////////////////////
		//Les données issues d'un post type
		public $data_post = null;

		//Le nom du cpt dont on veut afficher les valeurs
		public $cpt_checkbox_name = null;

		//Les valeurs du cpt à afficher dans les checkbox
		public $cpt_checkbox_hierarchical = false;

		//Faut-il permettre d'ordonner les post?
		public $cpt_checkbox_ordering = false;

		//Le champ du cpt à afficher dans les checkbox
		public $cpt_checkbox_display_field = null;

		//Le type d'affichage
		public $cpt_checkbox_display_type = null;

		///////////////////////
		//Le nom du cpt dont on veut afficher les valeurs
		public $cpt_select_name = null;

		//Le champ du cpt à afficher dans les checkbox
		public $cpt_select_display_field = null;

		//L'option par défaut du select des post
		public $cpt_first_option = "";
		
		///////////////////////
		public $radio_custom_values = null;
		public $radio_custom_value_default = "";
		///////////////////////


		//Longueur max d'un textarea
		public $textarea_max_length = -1;
        
		//Des champs sont-ils liés?
		public $linked_field = false;
		public $linked_field_params = null;
		
        //Doit-on exposer cette variable à l'api REST
        public $rest_api = false;
		public $rest_get = false;
		public $rest_update = false;
		
		//Gestion des posts enfants.
		public $children_post_type;
		public $children_cpt_display;
		public $children_button_add = false;
		
		
		//Gestion des type number
		public $number_min = -1;
		public $number_max = -1;
		public $number_step = -1;
		
		//Gestion des types liés à une base de données
		public $database = null;
		public $data_database = null;
		
        //Featured Image
		public $featured_image = false;
		//public $arr_url_image = array ();
        public $arr_img_size = array ();
		
		//Javascript à charger
		public $arr_script = array (  );
		
		//Css à charger
		public $arr_style = array (  );
		
		public $blank_line_after = false;
		public $content_after = "";
		
		//Media
		public $display_media_miniature = false;
		public $media_type_document = "ALL";
		public $media_libelle_bouton_ajout = "Ajouter un document";
        public $media_libelle_bouton_supression = "Supprimer le document";
		public $media_sizes = array();
		
		//Password
		public $password_option = null;
		
		//Le champ est-il éditable en front
		public $editable_front = false;
		
		//Le type d'affichage = ""
		public $linked_post_display = "autocomplete";
		public $data_linked_post = null;
		//Le type des posts liés        
        public $linked_post_type = "";
		//Option par défaut dans les listes déroulantes
        public $linked_post_first_option = "";
        
        /**
         * Constructeur
         *
         * @param   $arr_data   array   Tableau qui contient les données de paramètrage de l'objet.
         * @param   $cpt        CPT     L'objet CPT associé au champ - non utilisé pour l'instant
         *
         */
        public function __construct ( $arr_data, $cpt )
        {

            $this->cpt = $cpt;
            $this->id = $arr_data['id'];
            $this->name = $arr_data['id'];
            $this->libelle = $arr_data['label'];
            $this->post_meta = $this->id;
            $this->value = "";
            $this->bloc = $arr_data['bloc'];
            $this->type = $arr_data['type'];
            
            if ( isset ( $arr_data['extra'] ) )
            {
                if ( isset ( $arr_data['extra']['placeholder'] ) )
                {
                    $this->placeholder = $arr_data['extra']['placeholder'];
                }
                if ( isset ( $arr_data['extra']['taxonomy'] ) )
                {
					if ( isset ( $arr_data['extra']['taxonomy']['id'] ) )
					{
						$this->taxonomy = $arr_data['extra']['taxonomy']['id'];
					}
					if ( isset ( $arr_data['extra']['taxonomy']['first_option'] ) )
					{
						$this->first_option = $arr_data['extra']['taxonomy']['first_option'];
					}
					if ( isset ( $arr_data['extra']['taxonomy']['hierarchical'] ) )
					{
						$this->taxonomy_hierarchical = $arr_data['extra']['taxonomy']['hierarchical'];
					}
					if ( isset ( $arr_data['extra']['taxonomy']['orderby'] ) )
					{						
						$this->taxonomy_orderby = $arr_data['extra']['taxonomy']['orderby'];
						//die ( $this->taxonomy_orderby );
					}
                }
				if ( isset ( $arr_data['extra']['database'] ) )
                {
					$this->database = $arr_data['extra']['database'];
                }
                if ( isset ( $arr_data['extra']['can_be_empty'] ) )
                {
                    $this->can_be_empty = $arr_data['extra']['can_be_empty'];
                }
				if ( isset ( $arr_data['extra']['can_be_empty_option'] ) )
                {
                    $this->can_be_empty_option = $arr_data['extra']['can_be_empty_option'];
                }
				
				
				if ( isset ( $arr_data['extra']['default_value'] ) )
                {
                    $this->default_value = $arr_data['extra']['default_value'];
                }
				
				if ( isset ( $arr_data['extra']['textarea'] ) && isset ( $arr_data['extra']['textarea']['max_length'] ) )
				{
					$this->textarea_max_length = $arr_data['extra']['textarea']['max_length'];
					//die($this->textarea_max_length);
				}
				
				if ( isset ( $arr_data['extra']['enable'] ) )
                {
                    $this->enable = $arr_data['extra']['enable'];
                }
                if ( isset ( $arr_data['extra']['validation'] ) )
                {
                    $this->validation = $arr_data['extra']['validation'];
                }
                
                if ( isset ( $arr_data['extra']['featured-image'] ) )
                {
                    $this->featured_image = true;
                    //$this->arr_url_image = array ();
                    $this->arr_img_size = $arr_data['extra']['featured-image'];
                }
				
				if ( isset ( $arr_data['extra']['javascript'] ) )
                {
                    $this->arr_script = $arr_data['extra']['javascript'];
                }
				if ( isset ( $arr_data['extra']['css'] ) )
                {
                    $this->arr_style = $arr_data['extra']['css'];
                }
				
				//Champs liés
				if ( isset ( $arr_data['extra']['linked_fields'] ) )
				{
					$this->linked_field = true;
					$this->linked_field_params = $arr_data['extra']['linked_fields'];
				}
				//Post Liés
				if ( isset ( $arr_data['extra']['linked_cpt'] ) )
				{
					if ( isset ( $arr_data['extra']['linked_cpt']['display'] ) )
					{
						$this->linked_post_display = $arr_data['extra']['linked_cpt']['display'];
					}
					if ( isset ( $arr_data['extra']['linked_cpt']['cpt'] ) )
					{
						$this->linked_post_type = $arr_data['extra']['linked_cpt']['cpt'];
					}
					if ( isset ( $arr_data['extra']['linked_cpt']['first_option'] ) )
					{
						$this->linked_post_first_option = $arr_data['extra']['linked_cpt']['first_option'];
					}
					
				}
				//
				//Posts Enfants
				if ( isset ( $arr_data['extra']['children_cpt'] ))
				{
					$this->children_post_type = $arr_data['extra']['children_cpt']['cpt'];
					$this->children_cpt_display = $arr_data['extra']['children_cpt']['display'];
					$this->children_button_add = $arr_data['extra']['children_cpt']['button_add'];
					
				}
				//Type d'affichage
				if ( isset ( $arr_data['extra']['display_type'] ) )
				{
					$this->display_type = $arr_data['extra']['display_type'];
				}
				if ( isset ( $arr_data['extra']['display_type_taxonomy'] ) )
				{
					$this->display_type_taxonomy = $arr_data['extra']['display_type_taxonomy'];
				}
				
				//Ligne vide après
				if ( isset ( $arr_data['extra']['blank_line_after'] ) )
				{
					$this->blank_line_after = true;
				}
				if ( isset ( $arr_data['extra']['content_after'] ) )
				{
					$this->content_after = $arr_data['extra']['content_after'];
				}
				
				//Media
				if ( isset ( $arr_data['extra']['media']) )
				{					
					$this->display_media_miniature = $arr_data['extra']['media']['display_miniature'];
					
					if ( isset ( $arr_data['extra']['media']['type_document']) )
					{
						$this->media_type_document = $arr_data['extra']['media']['type_document'];
					}
					
					if ( isset ( $arr_data['extra']['media']['libelle_bouton_ajout']) )
					{						
						$this->media_libelle_bouton_ajout = $arr_data['extra']['media']['libelle_bouton_ajout'];
					}
					if ( isset ( $arr_data['extra']['media']['libelle_bouton_supression']) )
					{						
						$this->media_libelle_bouton_supression = $arr_data['extra']['media']['libelle_bouton_supression'];
					}
					if ( isset ( $arr_data['extra']['media']['sizes']) )
					{						
						$this->media_sizes = $arr_data['extra']['media']['sizes'];
					}
				}
				//Type Number
				if ( isset ( $arr_data['extra']['input_number']) )
				{
					if ( isset ( $arr_data['extra']['input_number']['min']) )
					{
						$this->number_min = $arr_data['extra']['input_number']['min'];
					}
					if ( isset ( $arr_data['extra']['input_number']['max']) )
					{
						$this->number_max = $arr_data['extra']['input_number']['max'];
					}
					if ( isset ( $arr_data['extra']['input_number']['step']) )
					{
						$this->number_step = $arr_data['extra']['input_number']['step'];
					}					
				}
				//Password
				if ( isset ( $arr_data['extra']['password']) )
				{
					$this->initPasswordOption ( $arr_data );									
				}
				
				//Le champ est editable en front?
				if ( isset ( $arr_data['extra']['editable_front']))
				{
					$this->editable_front = $arr_data['extra']['editable_front'];
				}

				//Liste de post dans des checkbox
				if ( isset ( $arr_data['extra']['checkbox_post'] ) )
				{
					$this->cpt_checkbox_display_type = $arr_data['extra']['checkbox_post']['display_type'];

					$this->cpt_checkbox_display_field = $arr_data['extra']['checkbox_post']['display_field'];

					$this->cpt_checkbox_name = $arr_data['extra']['checkbox_post']['cpt_name'];

					$this->cpt_checkbox_hierarchical = $arr_data['extra']['checkbox_post']['hierarchical'];

					$this->cpt_checkbox_ordering = $arr_data['extra']['checkbox_post']['ordering'];

				}	

				//Liste de post dans un select
				if ( isset ( $arr_data['extra']['select_post'] ) )
				{
					$this->cpt_select_display_field = $arr_data['extra']['select_post']['display_field'];

					$this->cpt_select_name = $arr_data['extra']['select_post']['cpt_name'];

					$this->cpt_first_option = $arr_data['extra']['select_post']['first_option'];
				}		

				//Radio avec valeur custom
				if ( isset ( $arr_data['extra']['radio_custom_values'] ) )
				{
					$this->radio_custom_values = $arr_data['extra']['radio_custom_values']['values'];
					$this->radio_custom_value_default = $arr_data['extra']['radio_custom_values']['default_value'];
				}		
            }
            
			if ( isset ( $arr_data['rest-api'] ) )
			{
				$this->rest_api = true;
				
				if ( isset ( $arr_data['rest-api']['get'] ) )
				{
					$this->rest_get = $arr_data['rest-api']['get'];
				}
				if ( isset ( $arr_data['rest-api']['update'] ) )
				{
					$this->rest_update = $arr_data['rest-api']['update'];
				}
			}
            
            if ( $this->cpt->rest_api == true && $this->rest_api == true )
            {
                //die();
                $this->initRestApi ( );
            }
			
            
        }
        
        public function initPasswordOption ( $arr_data )
		{
			$this->password_option = new stdClass ();
			
			$this->password_option->default_value = false;
			$this->password_option->readable = false;
			$this->password_option->switch_readability = false;
			$this->password_option->readable_default = false;
			
			if ( isset ( $arr_data['extra']['password']['default_value']) )
			{
				$this->password_option->default_value = $arr_data['extra']['password']['default_value'];
			}
			if ( isset ( $arr_data['extra']['password']['readable']) )
			{
				$this->password_option->readable = $arr_data['extra']['password']['readable'];
			}
			if ( isset ( $arr_data['extra']['password']['switch_reability']) )
			{
				$this->password_option->switch_reability = $arr_data['extra']['password']['switch_readability'];
			}
			if ( isset ( $arr_data['extra']['password']['switch_readability']) )
			{
				$this->password_option->readable_default = $arr_data['extra']['password']['readable_default'];
			}
			
		}
		public function displayAfter ()
		{
			$after = "";
			if ( $this->blank_line_after == true )
			{
				$after .= "<br/><br/>";
			}
			if ( $this->content_after != "" )
			{
				$after.= $this->content_after;
			}
			
			return $after;
		}
		
        /**
         * Création des tags de démarrage nécessaires pour l'affichage dans un tableau
         */
        protected function createStartTag ( )
        {
            return '<tr class="form-field form-required" id="tr-'.$this->post_meta.'">
			<th valign="top" scope="row">'.$this->libelle.(($this->can_be_empty == false)?' (*)':'').'</th>
			<td>';
        }
        
        /**
         * Création des tags de fin nécessaires pour l'affichage dans un tableau
         */
        protected function createEndTag ( )
        {
            $end = '</td></tr>';
			
			/*if ( $this->blank_line_after == true )
			{
				$end .= "<tr><td>&nbsp;</td></tr>";
			}*/
			
			return $end;
        }
        
        
        /**
         * Chargement de la valeur du champ en base de données
         *
         * @param   $post_id    int     Identifiant du post pour lequel on veut la valeur.
         * 
         */
        public function loadValue ( $post_id )
        {
            $this->value =  get_post_meta ( $post_id, $this->post_meta, true );
			
			if ( $this->value == "" && $this->default_value != "" )
			{
				$this->value = $this->default_value;
			}
				
        }
         
		/**
         * Chargement de la valeur du champ en base de données au format JSON
         *
         * @param   $post_id    int     Identifiant du post pour lequel on veut la valeur.
         * 
         */
		public function loadValueJson ( $post_id )
        {
            return $this->value;   
        }
        
        /**
         * Enregistrement de la valeur du champ en base de données
         *
         * @param   $post_id    int     Indentifiant du post pour lequel on souhaite enregistrer la valeur
         *
         */
        public function saveValue ( $post_id )
        {
            if ( $this->validate (  ) && $this->special_validation () )
            {
				update_post_meta ( $post_id, $this->post_meta, $_POST[$this->post_meta] );      
            }
            else
            {
                delete_post_meta ( $post_id, $this->post_meta );
            }
        }
		
		
		public function printMail ( )
		{
			$str_mail = "<div style='margin-bottom:15px;'>";
			
			$str_mail .= "<label><b>".$this->libelle." : </b></label>";
			$str_mail .= "<br/>";
			$str_mail .= "<label>".$_POST[$this->post_meta]."</label>";
			
			$str_mail .= "</div>";
			return $str_mail;
		}
        
        
        /**
         * Valide la saisie du champ avant de le sauvegarder. Un message d'erreur est généré en cas de non validation.
         * C'est la version de base, donc on ne vérifie que si la saisie n'est pas vide.
         *
         * @return  bool    true si la validation est ok, false sinon
         */
        public function validate ( )
        {
            $b_ret = true;
            $this->value = $_POST[$this->post_meta];
            $can_be_empty_option = false;
			   
			//var_dump($this->post_meta);
			//var_dump($this->can_be_empty);
            //Le champ ne doit pas être vide
            if ( $this->can_be_empty == false )
            {
				
				//On teste si le champs a une condition pour être empty
				if ( $this->can_be_empty_option != null )
				{
					echo "la";
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
            }
			
			//var_dump($this->value);
			if ( $can_be_empty_option == false && $this->value == "" && $this->can_be_empty == false )
			{
				//echo "ror";
				$this->error = true;
				$this->error_message .= "<li>Le champ '".$this->libelle."' ne peut pas être vide</li>";
				
				$b_ret = false;
			}
            
            return $b_ret;
        }

        
        /**
         * Validation évoluée qui permet de définir dans les paramètres le type d'opération à réaliser pour passer la validation.
         *
         * @return  bool    true si la validation est ok, false sinon
         */
        function special_validation ( )
        {
            $b_ret = true;
            $this->value = $_POST[$this->post_meta];
            
            for ( $i=0; $i<count($this->validation); $i++ )
            {
                $validation = $this->validation[$i];
                //La valeur du champ doit-être différente de celle de la clause de validation.
                if ( $validation['operator'] == "DIFF" )
                {
                    if ( $this->value == $validation['value'] )
                    {
                        $this->error = true;
                        $this->error_message .= "<li>".$validation['error_message']."</li>";
                        $b_ret = false;
                    }
                }
            }
            return $b_ret;
        }
        
		
		public function canBeEmptyOption ()
		{
			$can_be_empty_option = false;
			
			//Le champ ne doit pas être vide
            if ( $this->can_be_empty == false )
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
            }
			
			return $can_be_empty_option;
		}
        
		
		/**
		 * Chargement de la liste des taxonomies disponibles pour le post type
		 *
		 */
		public function loadPostTaxonomy ( )
		{
			$this->post_taxonomies = get_object_taxonomies ( 'cpt_enseigne', 'objects' );
		}
		
        /**
         * Chargement de la taxonomie associée au champ
         *
         */
        public function loadDataTaxonomy ( )
        {
			if ( $this->taxonomy_hierarchical == false )
			{
				//$this->taxonomy_orderby = 'term_id';
				//die(''.$this->taxonomy_orderby);
				$this->data_taxonomy = get_terms ( $this->taxonomy , array('hide_empty'=>0, 'parent' => 0, 'order'=>'ASC','orderby' => $this->taxonomy_orderby ));
			}
			else
			{
				$this->data_taxonomy = get_terms ( $this->taxonomy , array('hide_empty'=>0, 'orderby'=>'term_group'));
				
				$taxo = get_terms ( $this->taxonomy , array('hide_empty'=>0, 'parent' => 0, 'order'=>'ASC','orderby' => $this->taxonomy_orderby ));
				$this->obj_taxonomy_hierarchical = array();
				
				for ( $z=0;$z<count ( $taxo );$z++ )
				{
					$o = new stdClass ();
					$o->taxo = $taxo[$z];
					$o->taxo_cat = get_terms ( $this->taxonomy, array ( 'hide_empty'=>0, 'parent' => $taxo[$z]->term_id ) );
					$this->obj_taxonomy_hierarchical[] = $o;
					
				}
			}
        }
		
		
		/**
		 * Chargement des données issues d'une table
		 *
		 */
		public function loadDataDatabase ( $where="" )
		{
			global $wpdb;
			
			$fields = $this->database['id']['field'];
			if ( isset ( $this->database['id']['alias'] ) )
			{
				$fields .= " as ".$this->database['id']['alias'];
			}
			$fields .= ", ";
			
			
			$obj_concat = $this->database['concat'];
			if ( count($obj_concat) == 1 )
			{
				$fields .= $obj_concat[1]['value'];
			}
			else
			{
				$fields .= " concat (";
				$arr = array();
				for ( $i=0;$i<count($obj_concat); $i++ )
				{
					$o = $obj_concat[$i];
					
					if ( $o['type'] == "field" )
					{
						$arr[] = $o['value'];
					}
					else if ( $o['type'] == "string" )
					{
						$arr[] = "'".$o['value']."'";
					}
				}
				
				$fields.= implode(',', $arr );
				
				$fields .= ") as libelle ";
			}
			
			$table = $this->database['table']['libelle'];
			if ( $this->database['table']['wp_prefix'] == true )
			{
				$table = $wpdb->prefix.$table;
			}
			//die($table);
			$query = "SELECT ".$fields." FROM ".$table."";
			
			if ( isset($this->database['clause']) && $this->database['clause'] != '' )
			{
				$query .= " WHERE ".$this->database['clause'];
			}
			
			if ( $where != '' )
			{
				$query .= " WHERE ".$where;
			}
			
			$this->data_database = $wpdb->get_results( $query, OBJECT );
			//var_dump($this->data_database);
			//die();
		}
		
		/**
		 * Chargement des données d'un post type à afficher
		 */
		public function loadDataPost ( $cpt_name, $fields, $arg=array() )
		{
			$this->data_post = array ();

			//Réalisation de la requète
			$args = array(
				'post_type' => $cpt_name,
				'post_status' => 'publish',  
				'nopaging'	=> true,
				'posts_per_page' => -1
				);
			$query = new WP_Query( $args );

			if ( $query->have_posts() ) 
			{        
				while ( $query->have_posts() ) 
				{
					$query->the_post();  
		
					$o = new stdClass();
					$o->post_id =  $query->post->ID;
					$o->post_title = $query->post->post_title;

//var_dump($this->cpt_checkbox_display_field );die();
					if ( count ( $this->cpt_checkbox_display_field ) > 0 && $this->cpt_checkbox_display_field[0] != '')
					{
						$metas = get_post_meta ( $query->post->ID );

						foreach ( $this->cpt_checkbox_display_field as $field )
						{
							$o->{$field} = $metas[$field][0];
						}
					}
					
					$this->data_post[] = $o;
				}
			}
		}

		/**
		 * Chargement des pages wp disponibles
		 */
		public function loadDataPage (  )
		{
			$this->data_page = array ();

			//Réalisation de la requète
			$args = array(
				'post_type' => 'page',
				'post_status' => 'publish',  
				'nopaging'	=> true,
				'posts_per_page' => -1
				);
			$query = new WP_Query( $args );

			if ( $query->have_posts() ) 
			{        
				while ( $query->have_posts() ) 
				{
					$query->the_post();  
		
					$o = new stdClass();
					$o->post_id =  $query->post->ID;
					$o->post_title = $query->post->post_title;
					$o->post_link = get_permalink ( $query->post->ID );
					$this->data_page[] = $o;
				}
			}
		}


		/**
		 * Chargement des données d'un post liés
		 */

        public function loadDataLinkedPost ( )
		{
			$this->data_linked_post = array ();
			
			//Réalisation de la requète
			$args = array(
				'post_type' => $this->linked_post_type,
				'post_status' => 'publish',  
				'nopaging'	=> true,
				'posts_per_page' => -1
				);
			$query = new WP_Query( $args );
		
			if ( $query->have_posts() ) 
			{        
				while ( $query->have_posts() ) 
				{
					$query->the_post();  
		
					$o = new stdClass();
					$o->value = $query->post->ID;
					$o->id =  $query->post->ID;
					$o->label = $query->post->post_title;
					
					$this->data_linked_post[] = $o;
				}
			}
		}
        public function setEditableFront ( $editable )
		{
			$this->editable_front = $editable;
			return $this;
		}
        
        
        public function initRestApi ( )
        {
            //$this->loadValue ();
            //add_action( 'rest_api_init', array ( $this, 'registerFieldREST'), 10, 0 );
            
            
            register_rest_field ( $this->cpt->name,
                $this->post_meta,
                array(
                    'get_callback'    => ($this->rest_get)?array ( $this, 'getRestValue' ):null,
                    'update_callback' => ($this->rest_update)?array ( $this, 'updateRestValue' ):null,
                    'schema'          => null,
                )
            );
            
            /*
            $f = fopen(__DIR__."/temp.txt", "a+");
            fputs($f, "initRestApi\n");
            fputs($f, $this->cpt->name."\n");
            fputs($f, $this->post_meta."\n");
            fclose($f);
            */
        }
        
       
        public function getRestValue ( $object )
        {
            $this->loadValue ( $object[ 'id' ] );
            return $this->value ;
        }
        

        public function updateRestValue ( $value, $object, $field_name )
        {
			//die('cocou');
            /*if ( ! $value || ! is_string ( $value ) )
			{
				return;
			}*/
			/*ob_start ();
			var_dump($object);
			$f = fopen(__DIR__."/tempxx.txt", "a+");
            fputs($f, "initRestApi\n");
            fputs($f, $this->cpt->name."\n");
            fputs($f, $this->post_meta."\n");
			fputs($f, ob_get_clean()."\n");
			fputs($f, $value."\n");
            fclose($f);*/
			
			
			return update_post_meta ( $object->ID, $this->post_meta, strip_tags ( $value ) );      
			
        }

        
    };

}    




?>