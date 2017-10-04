

var map_admin = null;
var marker = null;

function initMap ( lat, lng )
{
    var b_marqueur = 1;
    var center = [lat, lng];
    if ( lat == 0 )
    {
        b_marqueur = 0;
        center = [46, 1.2];
    }
    
    map_admin = L.map('map_admin',{
                        zoomControl:false,
                        minZoom:3,
                        maxBounds:L.latLngBounds ([-56.46249048388979, -209.53125],[69.47296854140573, 126.21093749999999])
                        }).setView([lat, lng], 12);
	                                     
	//On ajoute OpenStreetMap
	// add an OpenStreetMap tile layer
	L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
	    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(map_admin);
	
	L.control.scale({imperial:false, position:"bottomright"}).addTo(map_admin);
	L.control.zoom({position:'topright' }).addTo ( map_admin );         
                  
    googleGeocodeProvider = new L.GeoSearch.Provider.Google();
    
    if ( b_marqueur == 1)
    {
        marker = L.marker([lat, lng]).addTo(map_admin);
    }
}


var googleGeocodeProvider;
function geocode_admin ()
{
    
    var addressText = jQuery("#"+field_adresse).val() + " " + jQuery("#"+field_cp).val() + " " + jQuery("#"+field_ville).val();
    
    if ( field_pays != "" )
    {
        addressText += jQuery("#"+field_pays).val();
    }

    googleGeocodeProvider.GetLocations( addressText, function ( data )
    {
        //console.log ( data[0].X + " " + data[0].Y);
        
        if ( marker != null )
        {
            map_admin.removeLayer ( marker );
        }
            marker = L.marker ( [data[0].Y, data[0].X]).addTo(map_admin);
            map_admin.setView ( [data[0].Y, data[0].X], 12 );
        
            jQuery ("#"+field_lat).val ( data[0].Y );
            jQuery ("#"+field_lng).val ( data[0].X );
        
    });
}