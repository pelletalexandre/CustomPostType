<?php

if( !class_exists( 'Option' ) )
{
    class Option
    {
        
        public $name;
        public $libelle;
        
        
        /**
         * Constructeur pour une page d'option
         *
         * @param   $arr_param      array   Tableau associatif contenant tout ce qui utile pour gérer les options de la page
         *
         */
        public function __construct ( $arr_param )
        {
            $this->name = $arr_param['name'];
            $this->libelle = $arr_param['libelle'];
			
			if ( isset ( $arr_param['REST-API'] ) )
			{
				$this->rest_api = $arr_param['REST-API'];	
			}
			
			
			
        }
        
        
        /**
         * Initialisation de l'option en vue de l'affichage
         *
         * @param   $arr_fields         array(array)   Tableau de tableaux associatif contenant les paramètres des champs
         */
        public function initAdmin ( $arr_fields )
		{
			$this->initFields ( $arr_fields );
			            
            //$this->enqueueScript ( );
            //$this->enqueueStyle ( );
            
            //add_action( 'admin_menu', array ( $this, 'addMenuPage' ) );
            //do_action ( 'admin_menu' );

            //$this->addMenuPage();
			//add_action( 'save_post', array ( $this, 'savePost' ), 10, 2 );
            
		}


        public function addMenuPage (  )
        {
            
            add_menu_page ( $this->libelle, $this->libelle, 'manage_options', 'menu123', array ($this, 'displayOptionPage' ) );
            
            //var_dump(current_user_can('manage_options'));die();
            //add_submenu_page('my-menu', 'Submenu Page Title', 'Whatever You Want', 'manage_options', 'my-menu' );
            //add_submenu_page( 'options-general.php', 'Informations de contact', 'Informations de contact', 'manage_options', 'fenetre-contact-option-menu', 'display_fenetre_contact_option_menu' );
        }
		
        
        public function displayOptionPage ( )
        {
            ?>
            <div class="wrap">
                <div id="icon-options-general" class="icon32"><br/></div>
                <h2><?php echo $this->libelle; ?></h2>
                
                <?php
                if ( isset ( $_REQUEST['action'] ) && $_REQUEST['action'] == "option_contact" )
            	{
		
        			$errors = $this->saveFields ( );
		
        			?>
			
        			<div class="updated">
                        <p>Enregistrement effectué.</p>
                        <p><?php echo $errors; ?></p>
                    </div>
			
        			<?php
                }              
                ?>
		
                <form method="post" enctype="multipart/form-data">
					<input type="hidden" name="action" value="option_contact" /> 
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" /> 

                    <table class="form-table">
                        <?php echo $this->displayFields (); ?>
                    </table>
                    
                    <div class="submit" style="margin-left:150px;margin-top:50px;">
						<input type="submit" class="button button-primary" name="save_option_contact" value="Enregistrer" class="button"/>
					</div>
                    
                </form>
            </div>	
            <?php
        }

        
        public function saveFields ( )
        {
            $error_message = "";
            for ( $i=0; $i<count($this->fields); $i++ )
            {
                $this->fields[$i]->saveValue ( );
                
                //Erreur de validation durant l'enregistrement
                if ( $this->fields[$i]->error == true )
                {
                    $error_message .= $this->fields[$i]->error_message;
                }
            }
            
            if ( $error_message != "" )
            {
                $error_message = "<ul>".$error_message."</ul>";
            }
            
            return $error_message;
        }
        
        
        
        /**
         * Initialisation de l'option en vue de l'utilisation dans l'api rest
         *
         * @param   $arr_fields         array(array)   Tableau de tableaux associatif contenant les paramètres des champs
         * @param   $arr_rest_delete    array           Tableau contenant les éléments à supprimer des retours de l'api rest    
         */
		public function initRest ( $arr_fields, $arr_rest_delete, $route )
		{
			$this->initFields ( $arr_fields );
			
			$this->arr_rest_delete = $arr_rest_delete;
            
            register_rest_route( 'wp/v2', '/'.$route, array (
                'methods' => 'GET',
                'callback' => array ( $this, 'getOptionRest' ),
            ));
		} 


        function getOptionRest( $data )
        {
            $arr = array ( );
            
            //echo count($this->fields);
            
            for ( $i=0; $i<count($this->fields);$i++ )
            {
                //echo $this->fields[$i]->getRestValue();
                //echo $this->fields[$i]->id;
                //die();
                $arr [ $this->fields[$i]->id ] = $this->fields[$i]->getRestValue();
            }
            return $arr;
        }

        
        
        
        /**
         * Initialisation des champs du custom post type
         *
         * @param	$arr_data	array	Tableau contenant tous les champs du custom post type
         * 
         */
        public function initFields (  $arr_data )
        {
            //$this->fields = $arr_field;
            $this->fields = array ();
            
            for ( $i=0; $i<count($arr_data); $i++ )
            {
				
                switch ( $arr_data[$i]['type'] )
                {
                    case Option_Field::TYPE_INPUT_TEXT :
                        $this->fields [] = new Option_Field_Input_Text ( $arr_data [$i], $this );
                        break;

                    case Option_Field::TYPE_VISUAL_EDITOR :
                        $this->fields [] = new Option_Field_Visual_Editor ( $arr_data [$i], $this );
                        break;
					case Option_Field::TYPE_INPUT_FLOAT :
						$this->fields [] = new Option_Field_Input_Float ( $arr_data [$i], $this );
						break;
                    
                    case Option_Field::TYPE_INPUT_URL :
                        $this->fields [] = new Option_Field_Input_Url ( $arr_data [$i], $this );
                        break;
                    
                    case Option_Field::TYPE_INPUT_EMAIL :
                        $this->fields [] = new Option_Field_Input_Email ( $arr_data [$i], $this );
                        break;

                    case Option_Field::TYPE_PAGE_LINK :
                        $this->fields [] = new Option_Field_Page_Link ( $arr_data [$i], $this );
                        break;
                    
                    /*
                    case CPT_Field::TYPE_INPUT_HIDDEN :
                        $this->fields [] = new CPT_Field_Input_Hidden ( $arr_data [$i], $this );
                        break;

                    case CPT_Field::TYPE_SELECT :
                        $this->fields [] = new CPT_Field_Select ( $arr_data [$i], $this );
                        break;

                    case CPT_Field::TYPE_RADIO :
                        $this->fields [] = new CPT_Field_Radio ( $arr_data [$i], $this );
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
					*/
                }
            }            
        }
        
        
        /**
         * Affichage des champs de la page d'option dans le formulaire
         *
         */
        public function displayFields ( )
        {
			
            $html = '';
            for ( $i=0; $i<count($this->fields); $i++ )
            {
                if ( $this->fields[$i]->bloc == $bloc )
                {
                    $html .= $this->fields[$i]->display ( );
                }
            }
            
            return $html;
        }
        
        
        
        /**
		 * Suppression des données inutiles dans l'api rest
		 *
		 * @param   $rest_data  array   Tableau associatif contenant les éléments à supprimer du retour de l'api
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
    }
}

?>