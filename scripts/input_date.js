jQuery(function($){
			$.datepicker.regional['fr'] = {clearText: 'Effacer', clearStatus: '',
				closeText: 'Fermer', closeStatus: 'Fermer sans modifier',
				prevText: 'Préc', prevStatus: 'Voir le mois précédent',
				nextText: 'Suiv', nextStatus: 'Voir le mois suivant',
				currentText: 'Courant', currentStatus: 'Voir le mois courant',
				monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
				'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
				monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
				'Jul','Aoû','Sep','Oct','Nov','Déc'],
				monthStatus: 'Voir un autre mois', yearStatus: 'Voir un autre année',
				weekHeader: 'Sm', weekStatus: '',
				dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
				dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
				dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
				dayStatus: 'Utiliser DD comme premier jour de la semaine', dateStatus: 'Choisir le DD, MM d',
				dateFormat: 'dd/mm/yy',
                firstDay: 1,
                showOtherMonths: true,
                selectOtherMonths: true,
				initStatus: 'Choisir la date', isRTL: false};
			$.datepicker.setDefaults($.datepicker.regional['fr']);
		});


function initDatePicker ( id, dateValue )
{
	//alert('ixi')
    jQuery ( document ).ready ( function()
    {
        jQuery ( '#' + id ).datepicker ( );
        jQuery ( '#' + id ).datepicker ( "option", "dateFormat", "dd/mm/yy" );
        jQuery ( '#' + id ).datepicker ( "option", "dayNames", [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ] );

		//if ( !isNaN(dateValue) )
		{
			jQuery ( '#' + id ).datepicker("setDate", dateValue);
		}
	});
		

}
    
    /*
    jQuery('#filtre_date_du').datepicker ( "option", "dayNamesShort", [ "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam" ] );
    jQuery('#filtre_date_du').datepicker ( 'setDate', new Date ( ) );

    
    jQuery('#filtre_date_au').datepicker ( );
    jQuery('#filtre_date_au').datepicker ( "option", "dateFormat", "dd/mm/yy" );
    jQuery('#filtre_date_au').datepicker ( 'setDate', "+3m" );
    
    */