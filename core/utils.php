<?php


if( !function_exists( 'in_array_object' ) )
{
    /**
     * Renvoie TRUE si pour l'un des objets de $arr la valeur de son champs $field_arr est égale au champs $field_search de l'objet $search
     */	
    function in_array_object ( $arr, $search, $field_arr, $field_search )
    {
        for ( $i=0; $i<count($arr); $i++ )
        {
            
            if ( $arr[$i]->{$field_arr} == $search->{$field_search} )
            {
                return TRUE;
            }
        }
        return FALSE;
    }
}



/**
 * Retourne la liste des posts pour affichage dans la liste d'autocompletion
 *
 * @param   $post_type      string      le type de post à récupérer
 * @param   $arr_label      array       tableau des labels passé par référence pour stocker les valeurs de retour 
 * @param   $arr_all        array       tableau des objets passé par référence pour stocker les valeurs de retour 
 */
if( !function_exists( 'load_liste_post' ) )
{
function load_liste_post ( $post_type, &$arr_label, &$arr_all )
{
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
			$o->value = $query->post->ID;
			$o->ID =  $query->post->ID;
			$o->label = $query->post->post_title;   

			$arr_all [$o->label] = $o;
			$arr_label [] = $query->post->post_title;    
		}
	}
}
}

if( !function_exists( 'JourJulien' ) )
{
function JourJulien ( $s_date )
		{                                       
			if ( $s_date == "" )
				return "";
				
			//mois et jour commence à 1
			$arr_date = explode ('/', $s_date );
			
			$jour = (int)removeFirstZero ( $arr_date[0]);
			$mois = (int)removeFirstZero ( $arr_date[1]);
			$annee = (int)$arr_date[2];
			
			$jul = 0;
		
			$igreg = 15+31*(10+12*1582);
			$ja;
			$jy = $annee;
			$jm;
		
			if ($jy == 0)
				return -1;
		
			if ($jy < 0)
				++$jy;
			if ($mois > 2)
			{
				$jm = $mois+1;
			}
			else
			{
				--$jy;
				$jm = $mois+13;
			}
			$jul = (int) (floor ( 365.25 * $jy )+floor ( 30.6001 * $jm )+$jour+1720995);
		
			if ($jour+31*($mois+12*$annee) >= $igreg)
			{
				$ja = (int) (0.01*$jy);
				$jul += 2-$ja+(int) (0.25*$ja);
			}
		
			return $jul;
		}
		
		function removeFirstZero ( $s )
		{         
			$val = $s;
			if ( strlen ( $s ) == 2 )
			{
				if ( substr ( $s, 0, 1 ) == "0" )
				{
					$val = substr ( $s, 1, 1 );
				}   
				else
				{
					$val = $s;
				}
			}
			return $val;
		}
}
?>