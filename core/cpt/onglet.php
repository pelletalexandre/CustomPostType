<?php

if( !class_exists( 'Onglet' ) ) {

    class Onglet
    {
        public $id;
        public $libelle;
        public $callback;
        //public $emplacement='normal';
        //public $priority='high';
        public $cpt = null;
        
        
        /**
         * Construction de l'objet MetaBox
         *
         * @param   $cpt            string      Nom du cpt
         * @param   $id             string      Id de la metabox
         * @param   $libelle        string      Libellé de la box
         * @param   $callback       string      Nom de la fonction qui est appellée pour le display
         * @param   $emplacement    string      Emplacement de la metabox
         * @param   $priority       string      Priorité d'affichage de la box
         */
        public function __construct ( $arr_param/* $id, $libelle, $emplacement='normal', $priority='high', $callback=''*/ )
        {

            if ( isset ( $arr_param['id'] ) )
            {
                $this->id = $arr_param['id'];    
            }
            
            if ( isset ( $arr_param['libelle'] ) )
            {
                $this->libelle = $arr_param['libelle'];    
            }

            /*if ( isset ( $arr_param['emplacement'] ) )
            {
                $this->emplacement = $arr_param['emplacement'];    
            }
            */

            /*if ( isset ( $arr_param['priorite'] ) )
            {
                $this->priorite = $arr_param['priorite'];    
            }
            */

            if ( isset ( $arr_param['callback'] ) )
            {
                $this->callback = $arr_param['callback'];    
            }

            $this->cpt = null;
            $this->fields = null;
        }
        
        
        /**
         * Fonction qui ajoute la metabox sur la page.
         *
         * @param   $cpt    CPT         Objet custom post type associé
         */
        public function display ( $cpt )
        {
            $this->cpt = $cpt;
            $this->fields = $cpt->fields;
            
            $arr_extra = array();
            $arr_extra['cpt'] = $cpt;
            
            //Permet d'appeler une fonction complémentaire à éxecuter en fin de la routine classique, comme par exemple le géocodage
            if ( $this->callback != "" )
                $arr_extra['callback'] = $this->callback;
            
            //die("ixi");
            //echo "coucou";
            /*
            //Ajout du metabox
            add_meta_box( $cpt->name.'_'.$this->id.'_meta_box',
                        $this->libelle,
                        //$this->callback,
                        array( $this, "callbackShowFields" ),
                        $cpt->name,
                        $this->emplacement,
                        $this->priority,
                        $arr_extra
            );
            */
        }
        

        /**
         * Méthode qui affiche le contenu de la métabox
         *
         * @param   $post   WP_POST     Objet de type WP_POST sur lequel s'applique la metabox
         * @param   $extra  array       Tableau qui contient tous les arguments supplémentaire du champ afin de récupérer la callback
         *
         */
        /*
        public function callbackShowFields ( $post, $extra )
        {
            echo "<table class='form-table'>";
            
            echo $this->cpt->displayFields ( $this->id, $post->ID );
            
            echo "</table>";
            
            if ( isset ( $extra["args"]['callback'] ) )
            {
                call_user_func ( $extra['args']['callback'], $post );
            }
            
            //On insert les éventuels script inline
            echo $this->cpt->inlineScript ();
        }
        */
    };
    
}



?>