<?php

if( !class_exists( 'Option_Field' ) )
{

    class Option_Field 
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
		const TYPE_INPUT_FLOAT = 11;
		const TYPE_VISUAL_EDITOR = 12;
        const TYPE_PAGE_LINK = 13;
        
        //option associée
        public $option = null;
        
        //Libellé du champ
        public $libelle = 'le libelle du champ';
        
        //Type du champ
        public $type = CPT_Field::TYPE_INPUT_TEXT;
                
        //L'identifiant dans le formulaire
        public $id = "";
        
        //Le name dans le formulaire
        public $name = "";
        
        //La valeur du champ dans le formulaire
        public $value = "";
        
        //Le placeholder pour un champ texte
        public $placeholder = "";
                
        //Est ce que la variable peut-être vide?
        public $can_be_empty = false;
        
        //Gestion des erreurs.
        public $error_message = "";
        public $error = false;
        
        //Condition de validation du champ
        public $validation = array ();
        
        //Doit-on exposer cette variable à l'api REST
        public $rest_api = false;
        
		//Javascript à charger
		public $arr_script = array (  );
		
		//Css à charger
		public $arr_style = array (  );
        
        //Titre d'un bloc de données
        public $titre_bloc = '';

        //La liste des pages WP disponibles
		public $data_page = null;
        
        /**
         * Constructeur
         *
         * @param   $arr_data   array   Tableau qui contient les données de paramètrage de l'objet.
         * @param   $cpt        CPT     L'objet CPT associé au champ - non utilisé pour l'instant
         *
         */
        public function __construct ( $arr_data, $option )
        {

            $this->option = $option;
            $this->id = $arr_data['id'];
            $this->name = $arr_data['id'];
            $this->libelle = $arr_data['label'];
            $this->value = "";
            $this->type = $arr_data['type'];
            
            if ( isset ( $arr_data['extra'] ) )
            {
                if ( isset ( $arr_data['extra']['placeholder'] ) )
                {
                    $this->placeholder = $arr_data['extra']['placeholder'];
                }
                if ( isset ( $arr_data['extra']['can_be_empty'] ) )
                {
                    $this->can_be_empty = $arr_data['extra']['can_be_empty'];
                }
                if ( isset ( $arr_data['extra']['validation'] ) )
                {
                    $this->validation = $arr_data['extra']['validation'];
                }
                if ( isset ( $arr_data['extra']['rest-api'] ) )
                {
                    $this->rest_api = $arr_data['extra']['rest-api'];
                }
				if ( isset ( $arr_data['extra']['javascript'] ) )
                {
                    $this->arr_script = $arr_data['extra']['javascript'];
                }
				if ( isset ( $arr_data['extra']['css'] ) )
                {
                    $this->arr_style = $arr_data['extra']['css'];
                }
                //Titre avant un bloc
                if ( isset ( $arr_data['extra']['titre_bloc'] ) )
                {
                    $this->titre_bloc = $arr_data['extra']['titre_bloc'];
                }
            }
            
            
            if ( $this->option->rest_api == true && $this->rest_api == true )
            {
                $this->initRestApi ( );
            }
        }
        
        
        /**
         * Création des tags de démarrage nécessaires pour l'affichage dans un tableau
         */
        protected function createStartTag ( )
        {
            $html = '';

            if ( $this->titre_bloc != '' )
            {
                $html .= '<tr class="form-field"><th colspan="2" valign="top" scope="row">'.$this->titre_bloc.'</th></tr>';
            }

            $html .= '<tr class="form-field form-required">
			<th valign="top" scope="row">'.$this->libelle.(($this->can_be_empty == false)?' (*)':'').'</th>
			<td>';

            return $html;
        }
        
        /**
         * Création des tags de fin nécessaires pour l'affichage dans un tableau
         */
        protected function createEndTag ( )
        {
            return '</td></tr>';
        }
        
        
        /**
         * Chargement de la valeur du champ en base de données
         *
         */
        public function loadValue (  )
        {
            $this->value =  get_option ( $this->id, "" );
        }

        /**
		 * Chargement des pages wp disponibles
		 */
		public function loadDataPage ( $post_type='page' )
		{
			$this->data_page = array ();

			//Réalisation de la requète
			$args = array(
				'post_type' => $post_type,
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
         * Enregistrement de la valeur du champ en base de données
         *
         */
        public function saveValue ( )
        {
            if ( $this->validate (  ) && $this->special_validation () )
            {
                update_option ( $this->id, $this->value );
            }
            else
            {
                delete_option ( $this->id );
            }
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
            $this->value = $_POST [ $this->id ];
            
            //Le champ ne doit pas être vide
            if ( $this->can_be_empty == false )
            {
                
                if ( $this->value == "" )
                {
                    $this->error = true;
                    $this->error_message .= "<li>Le champ '".$this->libelle."(".$this->id.")' ne peut pas être vide</li>";
                    
                    $b_ret = false;
                }
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
            $this->value = $_POST [ $this->id ];
            
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
        
        
        public function initRestApi ( )
        {
            
            register_rest_field ( $this->cpt->name,
                $this->post_meta,
                array(
                    'get_callback'    => array ( $this,'getRestValue' ),
                    'update_callback' => null,
                    'schema'          => null,
                )
            );
        }
        
       
        public function getRestValue (  )
        {
            $this->loadValue ( );
            return $this->value ;
        }
        
        
    };

}




?>