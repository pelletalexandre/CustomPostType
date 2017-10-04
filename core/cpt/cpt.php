<?php

if( !class_exists( 'CPT' ) ) {
    class CPT
    {
        //Le nom du CPT
        public $name = "";
        
        //Liste des champs de type CPT_FIELD
        public $fields = array ();
        
        //Liste des metabox
        public $metaboxs = array ();
        
		//Liste des onglets
        public $onglets = array ();
        
		
		//REST API
		public $rest_api = false;
		
		//Données à supprimer de l'envoi rest
		public $arr_rest_delete;
		
		//Gestion du géocodage en BO
		public $geocodage = false;
		public $geocodage_params = null;
		
		//Post Enfant
		public $has_child_post = false;
		public $is_child_post = true;
		public $child_cpt = '';
		public $parent_cpt = '';
		
		
		//Le nom du post dans le cas ou l'on utilise celui-ci dans le custom post type
		public $post_title = "";
		
		
		//placeholder du titre
		public $placeholder_title = "Saisissez le titre ici";
		
		//Titre automatique
		public $title_auto = false;
						
		//Champs du titre automatique
		public $title_field = array();
		
		//Séparateur entre les champs du titre automatique
		public $title_separator = "";
		
        /**
         * Constructeur
         *
         * @param   $arr_param   array      Tableau associatif contenant les paramètres
         */
        public function __construct ( $arr_param )
        {
			
            $this->name = $arr_param['name'];
			
			if ( isset ( $arr_param['REST-API'] ) )
			{
				$this->rest_api = $arr_param['REST-API'];	
			}
			
			//Le CPT a-t-il besoin d'un géocodage?
			if ( isset ( $arr_param['geocodage'] ) )
			{
				$this->geocodage = true;
				$this->geocodage_params = $arr_param['geocodage'];
			}
			
			//Le CPT a-t-il des posts enfants
			if ( isset ( $arr_param['child_post']['has_child_post'] ) )
			{
				$this->has_child_post = $arr_param['child_post']['has_child_post'];
				$this->child_cpt = $arr_param['child_post']['child_cpt'];
			}
			
			//Le CPT est-il un post enfant
			if ( isset ( $arr_param['child_post']['is_child_post'] ) )
			{
				$this->is_child_post = $arr_param['child_post']['is_child_post'];
				$this->parent_cpt = $arr_param['child_post']['parent_cpt'];
			}
			
			//Faut-il modifier le placeholder du titre
			if ( isset ( $arr_param['placeholder_title'] ) )
			{
				$this->placeholder_title = $arr_param['placeholder_title'];
			}
			
			if ( isset ( $arr_param['title_auto'] ) )
			{
				$this->title_auto = true;
				$this->title_field = $arr_param['title_auto']['title_field'];
				$this->title_separator = $arr_param['title_auto']['title_separator'];
			}
			
        }
        
		
        /**
         * Initialisation du cpt en vue de l'affichage
         *
         * @param   $arr_fields         array(array)   Tableau de tableaux associatif contenant les paramètres des champs
         * @param	$arr_metabox		array			Tableau associatif contenant les paramètres de création des metaboxs
         * * @param	$arr_onglet			array			Tableau associatif contenant les paramètres de création des onglets
         * 
         */

        public function initAdmin ( $arr_fields, $arr_metabox, $arr_onglet )
		{
			$this->initFields ( $arr_fields );
			
			//$this->initOnglets ( $arr_onglet );		
			$this->initMetaBoxs ( $arr_metabox );
			
			//$this->displayOnglets ( );
			$this->displayMetaboxs ( );
            
            $this->removeMetaBoxTaxonomy ( );
            
            $this->enqueueScript ( );
            $this->enqueueStyle ( );
			
			//var_dump($this);die();
			if ( $this->geocodage == true )
			{
				
				$this->initGeocodage ( );
			}
			
			//Initialisation de méthodes ajax liées à la suppression de posts enfants
			if ( $this->has_child_post == true )
			{
				//echo "ixi";die();
				add_action( 'wp_ajax_nopriv_ajax_delete_child_post', array($this,'ajax_delete_child_post') );
				add_action( 'wp_ajax_ajax_delete_child_post', array($this,'ajax_delete_child_post') );
			}
			
			//C'est un post enfant, on doit mettre un filtre sur l'url de renvoi après un save_post
			if ( $this->is_child_post == true )
			{
				//Mainenant dans la function savePost
				//Ajout d'un filtre de redirection
				add_filter( 'redirect_post_location', array($this,'redirect_post_location') );
				
				
				//Ajout d'un champ caché contenant le post_parent
			}
			
			//Changer le placeholder du titre
			add_filter( 'enter_title_here', array($this, 'change_placeholder_title' ) );
			
			//Gestion des erreurs
			add_action( 'admin_notices', array ( $this, 'error_notice' ) );
			
			//Gestion de la sauvegarde
			add_action( 'save_post', array ( $this, 'savePost' ), 10, 2 );
			
			
		}
		

		/**
		 * Modification du placeholder du titre
		 */		
		function change_placeholder_title ( $title )
		{
			$screen = get_current_screen();
			
			if ( $this->name == $screen->post_type )
			{
				$title = $this->placeholder_title;
			}
		
			return $title;
		}

		/**
         * Initialisation du cpt en vue de l'utilisation dans l'api rest
         *
         * @param   $arr_fields         array(array)   Tableau de tableaux associatif contenant les paramètres des champs
         * @param   $arr_rest_delete    array           Tableau contenant les éléments à supprimer des retours de l'api rest    
         */
		public function initRest ( $arr_fields, $arr_rest_delete )
		{
			$this->initFields ( $arr_fields );
			
			$this->arr_rest_delete = $arr_rest_delete;
/*			
			$this->displayMetaboxs ( );
            
            $this->removeMetaBoxTaxonomy ( );
            
            $this->enqueueScript ( );
            $this->enqueueStyle ( );
            
            */
		}
        
		public function initOnglets ( $arr_onglet )
		{
			$this->onglets = array ( );
			
			for ( $i=0; $i<count($arr_onglet); $i++ )
			{
				$onglet = new Onglet ( $arr_onglet[$i] );
				$this->onglets [] = $onglet;
			}	
		}
		
        public function initMetaBoxs ( $arr_metabox )
        {
            $this->metaboxs = array ( );

			for ( $i=0; $i<count($arr_metabox); $i++ )
			{
				$metabox = new MetaBox ( $arr_metabox[$i] );
				$this->metaboxs [] = $metabox;
			}
			
        }
        
        /**
         * Initialisation des champs du custom post type
         *
         * @param	$arr_data	array	Tableau contenant tous les champs du custom post type
         */
        public function initFields (  $arr_data )
        {
            //$this->fields = $arr_field;
            $this->fields = array ();
            
            for ( $i=0; $i<count($arr_data); $i++ )
            {
                switch ( $arr_data[$i]['type'] )
                {
                    case CPT_Field::TYPE_INPUT_TEXT :
                        $this->fields [] = new CPT_Field_Input_Text ( $arr_data [$i], $this );
                        break;
                    
                    case CPT_Field::TYPE_INPUT_URL :
                        $this->fields [] = new CPT_Field_Input_Url ( $arr_data [$i], $this );
                        break;
                    
                    case CPT_Field::TYPE_INPUT_EMAIL :
                        $this->fields [] = new CPT_Field_Input_Email ( $arr_data [$i], $this );
                        break;
                    
                    case CPT_Field::TYPE_INPUT_HIDDEN :
                        $this->fields [] = new CPT_Field_Input_Hidden ( $arr_data [$i], $this );
                        break;

                    case CPT_Field::TYPE_SELECT :
						
                        $this->fields [] = new CPT_Field_Select ( $arr_data [$i], $this );
                        break;

                    case CPT_Field::TYPE_RADIO :
                        $this->fields [] = new CPT_Field_Radio ( $arr_data [$i], $this );
                        break;
					
					case CPT_Field::TYPE_CHECKBOX :
                        $this->fields [] = new CPT_Field_Checkbox ( $arr_data [$i], $this );
                        break;

                    case CPT_Field::TYPE_TEXTAREA :
                        $this->fields [] = new CPT_Field_Textarea ( $arr_data [$i], $this );
                        break;
					
					case CPT_Field::TYPE_FEATURED_IMAGE :
						$this->fields [] = new CPT_Field_Featured_Image ( $arr_data [$i], $this );
						break;
					
					case CPT_Field::TYPE_INPUT_DATE :
						
						$this->fields [] = new CPT_Field_Input_Date ( $arr_data [$i], $this );
						break;
					
					case CPT_Field::TYPE_MEDIA :
						$this->fields [] = new CPT_Field_Media_File ( $arr_data [$i], $this );
						break;

					case CPT_Field::TYPE_POST_LINK :
						$this->fields [] = new CPT_Field_Post_Link ( $arr_data [$i], $this );
						break;
					
					case CPT_Field::TYPE_INPUT_PASSWORD :
						$this->fields [] = new CPT_Field_Input_Password ( $arr_data [$i], $this );
						break;
					
					case CPT_Field::TYPE_POST_CHILDREN :
						$this->fields [] = new CPT_Field_Post_Children ( $arr_data [$i], $this );
						break;
					
					case CPT_Field::TYPE_POST_PARENT :
						$this->fields [] = new CPT_Field_Post_Parent ( $arr_data [$i], $this );
						break;
					
					case CPT_Field::TYPE_INPUT_NUMBER :
						$this->fields [] = new CPT_Field_Input_Number ( $arr_data [$i], $this );
						break;
					
					case CPT_Field::TYPE_TAXONOMY :
						$this->fields [] = new CPT_Field_Taxonomy ( $arr_data [$i], $this );
						break;
					
					case CPT_Field::TYPE_INPUT_PHONE :
						$this->fields [] = new CPT_Field_Input_Phone ( $arr_data [$i], $this );
						break;

					case CPT_Field::TYPE_CHECKBOX_POST :
						$this->fields [] = new CPT_Field_Checkbox_Post ( $arr_data [$i], $this );
						break;

					case CPT_Field::TYPE_PAGE_LINK :
						$this->fields [] = new CPT_Field_Page_Link ( $arr_data [$i], $this );
						break;

					case CPT_Field::TYPE_VISUAL_EDITOR :
						$this->fields [] = new CPT_Field_Visual_Editor ( $arr_data [$i], $this );
						break;

					case CPT_Field::TYPE_SELECT_POST :
						$this->fields [] = new CPT_field_Select_Post ( $arr_data [$i], $this );
						break;

					case CPT_Field::TYPE_RADIO_CUSTOM_VALUES :
						$this->fields [] = new CPT_Field_Radio_Custom_Values ( $arr_data [$i], $this );
						break;
                }
            }
            
        }
        
        
        /**
         * Affichage des champs du custom post type dans le formulaire
         *
         * @param   $bloc       string      le meta_bloc dans lequel le champ se trouve
         * @param   $post_id    int         id du post permettant de récupérer la valeur
         *
         */
        public function displayFields ( $bloc, $post_id )
        {
			
            $html = '';
            for ( $i=0; $i<count($this->fields); $i++ )
            {
                if ( $this->fields[$i]->bloc == $bloc )
                {
                    $html .= $this->fields[$i]->display ( $post_id );
					
					$html .= $this->fields[$i]->displayAfter ();
                }
            }
            
            return $html;
        }
        
        
        /**
         * Affichage des metabox
         *
         */
        public function displayMetaboxs ( )
        {
            for ( $i=0; $i<count($this->metaboxs); $i++ )
            {
                $this->metaboxs[$i]->display ( $this );
            }
        }
		
		
		/**
         * Affichage des onglets
         *
         */
        public function displayOnglets ( )
        {
            for ( $i=0; $i<count($this->onglets); $i++ )
            {
                $this->onglets[$i]->display ( $this );
            }
        }
        
		
        /**
         * Sauvegarde la valeur des champs dans la base de données, dans la function save_post
         *
         */
        public function saveFields ( $post_id, $error_title='' )
        {
			//var_dump($_POST);
            $error_message = "";
            for ( $i=0; $i<count($this->fields); $i++ )
            {
                $this->fields[$i]->saveValue ( $post_id );
                
                //Erreur de validation durant l'enregistrement
                if ( $this->fields[$i]->error == true )
                {
                    $error_message .= $this->fields[$i]->error_message;
                }
            }
            
            if ( $error_message != "" )
            {
                $error_message = "<ul>".$error_title.$error_message."</ul>";
            }
			else if ( $error_message == "" && $error_title != '' )
			{
				$error_message = "<ul>".$error_title."</ul>";
			}
            //die();
            return $error_message;
        }
        
		
		/**
		 * Retourne un string qui contient tous les champs, formatés pour être envoyés dans le corps d'un mail
		 *
		 */
		public function printMail ( )
		{
			$str_mail = "";
			
			for ( $i=0; $i<count($this->fields); $i++ )
            {
                $str_mail .= $this->fields[$i]->printMail (  );
            }
			
			return $str_mail;
		}
        
        /**
         * Charge les données du CPT pour l'id passé en paramètre
         *
         * @param   $post_id    int         identifiant du post à récupérer
         *
         * @return              stdClass    objet contenant toutes les valeurs du post
         */
        public function loadValues ( $post_id )
        {
			$o = new stdClass ();
			
			//On récupère le post pour charger le titre
			$post = get_post($post_id);
			$o->post_title = $post->post_title;
			$o->post_content = $post->post_content;
			
			$o->ID = $post->ID;
			$o->post_date = $post->post_date; 
			$o->post_date_lib = date("d/m/Y", strtotime($post->post_date)); 
			$o->post_permalink = get_permalink ( $post->ID );
			//var_dump($o);
			 
            for ( $i=0; $i<count($this->fields); $i++ )
            {
                //Valeur du champ
                $this->fields[$i]->loadValue ( $post_id );
                $o->{$this->fields[$i]->post_meta} = $this->fields[$i]->loadValueJson ( $post_id );//$this->fields[$i]->value;
                
                //Libellé associé si taxonomy
                if ( $this->fields[$i]->taxonomy != "" )
                {
                    $terms = get_the_terms( $post_id, $this->fields[$i]->taxonomy );
                    $o->{$this->fields[$i]->post_meta."_lib"} = $terms[0]->name;
					$o->{$this->fields[$i]->post_meta."_slug"} = $terms[0]->slug;
					$o->{$this->fields[$i]->post_meta."_term_id"} = $terms[0]->term_id;
                    
                    //$o->{$this->fields[$i]->post_meta."_lib"} = $taxos->{$this->fields[$i]->taxonomy}[0]->name;
                }
				if ( $this->fields[$i]->database != null )
				{
					$o->{$this->fields[$i]->post_meta} = $this->fields[$i]->loadValueJson ( $post_id );
				}

				//Ici pour ajouter les infos des societes

				//Ici pour ajouter le permalink des pages
            }
            
            return $o;
        }
        
		
		/**
		 * Fonction qui retourne le field en fonction de l'id donné
		 *
		 * @param		field_id	int		identifiant du field à retourner
		 * 
		 * @return 		Field
		 */
		public function findField ( $field_id )
		{
			for ( $i=0; $i<count($this->fields); $i++ )
			{
				if ( $this->fields[$i]->id == $field_id )
				{
					return $this->fields[$i];
				}
			}
		}
		
		
		
		
		/**
		 * Charge tous les CPT de ce type présents dans la base de données.
		 * On récupère donc tous les identifiants et on appelle loadValues sur chacun
		 *
		 * @return	Array	tableau contenant les stdClass des cpt chargés.
		 */
		public function loadAll ( )
		{
			global $wpdb;
			$query = "SELECT ID from ".$wpdb->prefix."posts WHERE post_type='".$this->name."' AND post_status='publish' ";
			$results = $wpdb->get_results( $query, OBJECT );
			
			$arr = array();
			for ( $x=0; $x<count($results);$x++ )
			{
				$arr [ ] = $this->loadValues ( $results[$x]->ID ) ;
			}
			
			return $arr;
		}
		
		
		/**
		 * Charge tous les CPT de ce type présents dans la base de données, dont le parent est parent_id
		 *
		 * On récupère donc tous les identifiants et on appelle loadValues sur chacun
		 *
		 * @return	Array	tableau contenant les stdClass des cpt chargés.
		 */
		public function loadAllWithParent ( $parent_id )
		{
			global $wpdb;
			$query = "SELECT ID from ".$wpdb->prefix."posts WHERE post_type='".$this->name."' AND post_status='publish' AND post_parent=".$parent_id." ORDER By ID ASC";
						
			$results = $wpdb->get_results( $query, OBJECT );
			
			$arr = array();
			for ( $x=0; $x<count($results);$x++ )
			{
				$arr [ ] = $this->loadValues ( $results[$x]->ID ) ;
			}
			
			return $arr;
		}
		
        
        /**
         * Charge les taxonomies utilisées par la cpt.
         *
         * @return      stdClass    Objet contenant les taxonomies. La clé est le nom de la taxonomie
         */
        public function loadDataTaxonomy ( )
        {
            $o = new stdClass ();
            for ( $i=0; $i<count($this->fields); $i++ )
            {
                if ( $this->fields[$i]->taxonomy != "" )
                {
                    $this->fields[$i]->loadDataTaxonomy ( );
                    $o->{$this->fields[$i]->taxonomy} = $this->fields[$i]->data_taxonomy;
                }
            }
            
            return $o;
        }
		
        
        /**
         * Suppression des métabox des taxonomy dans la page d'édition d'un cpt
         */
        public function removeMetaBoxTaxonomy ()
        {
			
			for ( $i=0; $i<count($this->fields); $i++ )
            {
                if ( $this->fields[$i]->taxonomy != "" )
                {
					
                    remove_meta_box('tagsdiv-'.$this->fields[$i]->taxonomy, $this->name, 'normal');
					remove_meta_box($this->fields[$i]->taxonomy."div", $this->name, 'normal');

                }
            }
        }
      
	  
		/**
		 * Ajoute les fichiers javascript nécessaires
		 */
		public function enqueueScript ( )
		{
			for ( $i=0; $i<count($this->fields); $i++ )
            {
                for ( $x=0; $x<count($this->fields[$i]->arr_script);$x++ )
				{
					if ( $this->fields[$i]->arr_script[$x]['systeme'] == false )
					{
						wp_enqueue_script (
										$this->fields[$i]->arr_script[$x]['fichier'],
										plugins_url(  ) . '/custom_post_type_dynamik/scripts/'.$this->fields[$i]->arr_script[$x]['fichier']
							);
					}
					else
					{
						wp_enqueue_script (
										$this->fields[$i]->arr_script[$x]['fichier']
							);
					}
				}
            }
		}
		

		/**
		 * Ajoute les fichiers css nécessaires
		 */
		public function enqueueStyle ( )
		{
			for ( $i=0; $i<count($this->fields); $i++ )
            {
                for ( $x=0; $x<count($this->fields[$i]->arr_style);$x++ )
				{
					wp_enqueue_style (
									$this->fields[$i]->arr_style[$x],
									plugin_dir_url( __FILE__ ) . '../../styles/'.$this->fields[$i]->arr_style[$x]
						);
				}
            }
			
			
			
			wp_enqueue_style (
									'input',
									plugin_dir_url( __FILE__ ) . '../../styles/input.css'
						);

		}

		
		/**
		 * Ajoute les fichiers javascript nécessaires
		 */
		public function inlineScript ( $id_metabox )
		{
			$js = "<script>";
			
			for ( $i=0; $i<count($this->fields); $i++ )
            {
				
				if ( method_exists ( $this->fields[$i] , "executeJavascript" ) && $this->fields[$i]->bloc == $id_metabox )
				{
					$js .= $this->fields[$i]->executeJavascript ();
				}

            }
			$js .= "</script>";
			
			return $js;
		}
        
		
		/**
		 * Méthode de filtre qui permet de rediriger vers une page particulière lorsque l'on sauvegarde un post enfant
		 */
		public function redirect_post_location ( $location )
		{
			
			
			if ( $this->name == get_post_type() )
			{
				if ( /*isset( $_POST['save'] ) && */isset( $_POST['post_status'] ) && isset($_POST['post_parent_id']) && $_POST['post_status'] == "publish"  )
				{
					//var_dump($_POST);die();
					if (get_transient( 'settings_post_parent_'.$this->name))
					{
						var_dump (get_transient( 'settings_post_parent_'.$this->name));
						var_dump($location);
						//die();
						return $location."&parent_id=".$_POST['post_parent_id'];
					}
					return admin_url ('post.php?post='.$_POST['post_parent_id'].'&action=edit');
				}
				
				else
				{
					return $location;
				}
			} 
			return $location;
		}
		
		public function ajax_delete_child_post ( )
		{
			
			wp_delete_post( $_POST['post_id'], true );
			
			echo 1;
			exit;
			
		}
		
		public function initGeocodage ( )
		{
			//chargement de scripts
			wp_enqueue_script(
			'leaflet-js',
			'http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js');
			
			wp_enqueue_script(
			'admin-geoloc-js',
			plugins_url(  ) . '/custom_post_type_dynamik/scripts/admin_geoloc.js');
			
			wp_enqueue_script(
			'control-geosearch-js',
			plugins_url(  ) . '/custom_post_type_dynamik/scripts/geosearch/src/js/l.control.geosearch.js');

			wp_enqueue_script(
			'geosearch-js',
			plugins_url(  ) . '/custom_post_type_dynamik/scripts/geosearch/src/js/l.geosearch.provider.google.js');
			
			//Chargement des styles
			wp_enqueue_style(
			'leaflet-css',
			'http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css',
			array(),
			'1.0');
		}
		
		
		
		/**
		 * Suppression des données inutiles dans l'api rest
		 *
		 */
		public function removeRestData ( $rest_data )
		{
			//var_dump($rest_data->data);
			$rest_data->data['title'] = $rest_data->data['title']['rendered'];
			
			for ( $i=0; $i<count($this->arr_rest_delete['data']); $i++ )
			{
				unset ( $rest_data->data[$this->arr_rest_delete['data'][$i]] );	
			}
			
			for ( $i=0; $i<count($this->arr_rest_delete['link']); $i++ )
			{
				$rest_data->remove_link ( $this->arr_rest_delete['link'][$i] );	
			}
			
			//var_dump($rest_data);
			return $rest_data;
		}
		
		
		/**
		 * Méthode qui permet d'enregistrer le post
		 */
		public function savePost ( $post_id, $post )
		{
			
			if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || $post->post_type != $this->name ) return;
	
			// Check post type 
			if ( $post->post_type == $this->name && isset ( $_POST['post_type']) ) 
			{
				//On vérifie la saisie du titre
				$error_title = "";
				if ( $post->post_title == "" )
				{
					$champ_titre = ($this->placeholder_title=='Saisissez le titre ici')?'Titre':$this->placeholder_title;

					$error_title = "<li>Le champ '".$champ_titre."' ne peut pas être vide.</li>";
				}

				//On vérifie les champs
				$error_message = $this->saveFields ( $post_id, $error_title );
				
				
				//On lance l'action avant la sauvegarde, uniquement si aucune erreur ne survient à l'enregistrement des données
				if ( $error_message == "" )
				{					
					$error_message .= apply_filters ( 'cpt_before_save_post', $post, $error_message );
					
					/*
					if ( $this->is_child_post == true )
						add_filter( 'redirect_post_location', array($this,'redirect_post_location') );
						*/
				}
				
				
				//On affecte le titre automatique du post au besoin
				if ( $this->title_auto == true )
				{
					$post_title = "";
					
					for ( $x=0; $x<count($this->title_field); $x++ )
					{
						if ( isset ( $_POST[$this->title_field[$x]] ) )
						{
							$post_title .= $_POST[$this->title_field[$x]];
							
							if ( $x < count ( $this->title_field )-1 )
							{
								$post_title .= $this->title_separator;
							}
						}
					}
					remove_action ('save_post', array ( $this, "savePost" ) );
					wp_update_post(array('ID' => $post_id, 'post_title' => $post_title));
					add_action ('save_post', array ( $this, "savePost" ) );
				}
				
				//Ajout d'un messsage d'erreur    
				if ( $error_message != "" )
				{
					//var_dump($_POST);die();
					if ( $this->is_child_post == true )
					{
						//add_filter( 'redirect_post_location', array($this,'redirect_post_location') );
						set_transient( 'settings_post_parent_'.$this->name, $_POST['post_parent_id'], 5 );
					}
					
					
					//Création du message d'erreur formaté
					add_settings_error
					(
						$this->name,
						esc_attr( 'settings_updated' ),
						$error_message,
						"error"
					);
					
					//Sauvegarde temporaire du message d'erreur dans les options via transient
					set_transient( 'settings_errors_'.$this->name, get_settings_errors(), 5 );
					
					remove_action ('save_post', array ( $this, "savePost" ) );
		
					// update the post to change post status
					wp_update_post(array('ID' => $post_id, 'post_status' => 'draft'));
		
					// re-hook this function again
					add_action ('save_post', array ( $this, "savePost" ) );
				}
				//Pas d'erreur
				else
				{
					//On peut appeller l'action à effecture après la sauvegarde, le cas échéant
					do_action( 'cpt_after_save_post', $post );
				}

				
			}	
		}
		
		
		/**
		 * Affichage des erreurs le cas échéant.
		 */
		public function error_notice ( )
		{
			if ( ! ( $errors = get_transient( 'settings_errors_'.$this->name ) ) )
			{
				return;
			}
			
			
			
			$message = '<div id="message" class="error below-h2"><ul>';
			foreach ( $errors as $error )
			{
				$message .= '<li>' . $error['message'] . '</li>';
			}
			$message .= '</ul></div><!-- #error -->';
			
			
			
			//Dans le cas où l'on a une erreur dans le save d'un post enfant.
			if ( $this->is_child_post == true )
			{
				//$post_parent = (get_transient( 'settings_post_parent_'.$this->name));
				//$message .= "<script>jQuery(document).ready(function () {jQuery('#post_parent_id').val(".$post_parent."); });</script>";
				delete_transient('settings_post_parent_'.$this->name);
			}
			
			
			// Write them out to the screen
			echo $message;
			
			// Clear and the transient and unhook any other notices so we don't see duplicate messages
			delete_transient( 'settings_errors_'.$this->name/*'settings_errors'*/ );
			remove_action( 'admin_notices', array($this,'error_notice' ) );
		}

    };
}

	if( !function_exists( 'extraMetaBoxGeolocalisation' ) )
	{
		function extraMetaBoxGeolocalisation ( $post, $cpt ) 
		{
			//var_dump($cpt->geocodage_params);die();
			//die('ixi');
			//global $wp_evenement;
					
			$params = $cpt->geocodage_params;
					
			$lat = get_post_meta  ( $post->ID, $params['field_lat'], true );
			$lng = get_post_meta  ( $post->ID, $params['field_lng'], true );
					
			$lat = $lat==""?0:$lat;
			$lng = $lng==""?0:$lng;                             
			
			?>
			
			
			
			
		
			<script type="text/javascript">
		
				var lat = <?php echo $lat==""?0:$lat; ?>;

				var field_lat = "<?php echo $params['field_lat']; ?>";
				var field_lng = "<?php echo $params['field_lng']; ?>";
				
				var field_adresse = "<?php echo $params['field_adresse']; ?>";
				var field_cp = "<?php echo $params['field_cp']; ?>";
				var field_ville = "<?php echo $params['field_ville']; ?>";
				var field_pays = "<?php echo $params['field_pays']; ?>";
				
				jQuery(document).ready( function ()
				{      
					
					initMap ( <?php echo $lat==""?0:$lat; ?>, <?php echo $lng==""?0:$lng; ?> );
					
					 jQuery('<?php echo "#".$params['field_adresse']; ?>, <?php echo "#".$params['field_cp']; ?>, <?php echo "#".$params['field_ville']; ?>').keypress(
						 function(event){
							 if(event.keyCode == 13){
								 event.preventDefault();
							 }
						 }
					 );
					 
					 <?php
					 if ( $params['field_pays'] != '' )
					 {
						?>
						jQuery('<?php echo "#".$params['field_pays']; ?>').keypress(
						 function(event){
							 if(event.keyCode == 13){
								 event.preventDefault();
							 }
						 }
					 );
						<?php
					 }
					 ?>
					
					
				});
				
				
			</script>
			
			<div style="text-align: center;margin:10px;"><input type="button" value="Geocoder" class="button button-primary button-large" onclick="geocode_admin ()" /></div>
			<div id="map_admin" style="width:100%;height:350px;">
				
			</div>
			
			<?php
			
		}
		
		
	}
	
?>