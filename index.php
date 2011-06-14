<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
 
error_reporting(E_ALL);

include 'app/lib/functions.php';
include 'app/lib/Geocoder.php';
include 'app/lib/Placemark.php';
include 'app/config/config.php';
include $config['pillow_install'] . 'lib/pillow.php';

//Initialize page variables
$address            = '';
$ga_tracker_key     = $config['google_tracker_key'];
$chart_url          = '';
$sidebar_title      = '';
$map                = '';
$primary_placemark  = new stdClass;
$comps              = array();
$comp_placemarks    = array();
$view_files         = array(
    'begin'      => 'app/views/property_begin.php',
    'results'    => 'app/views/property_results.php',
    'no_results' => 'app/views/property_no_results.php'
);

if( strlen(trim($config['google_tracker_key'])) > 0 )
    $ga_enabled = TRUE;
else
    $ga_enabled = FALSE;

$labels = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
                   'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                   'Y', 'Z');

$label_position = 1;

//With no address request, the beginning page is assumed.
if( ! array_key_exists('address', $_GET) )
{
    echo getView($view_files['begin'], get_defined_vars());
    exit;
}


//Remember that $_GET is already urldecoded by php.
$address = htmlspecialchars( $_GET['address'] );

//Attempt to geocode the provided address. This serves two purposes:
// 1) The supplied address can be somewhat free-form - we don't need to
//    provide separated address, city, state, zip fields for the user.
//    The geocode service will give us back an address (assuming if finds
//    one) in a format that can be used to query the Zillow service.
// 2) Provides the first Placemark (along with coordinates) neeeded for our
//    map.
$gc                 = new Google_Geocoder( $google_api_key );
$primary_placemark  = $gc->geocode( $address );

//The google geocode service has either found the supplied addres or not. We
// check for a valid Placemark and act accordingly.
if( FALSE === $primary_placemark->isValid() )
{
    //If the primary placemark was not valid, show the no_results page
    // and exit so that no further code is executed
    echo getView($view_files['no_results'], get_defined_vars());
    exit;
}

//Since the geocode found a proper placemark, attempt to get a zillow property
// result. We'll start by creating a factory which can be used to create
// other objects from the Zillow service.
$pf = new Pillow_Factory( $config['zillow_api_key'] );

try {
    //$search will be an array with 1 or more Pillow_Property objects. Exact
    // matches will be found at $search[0]
    $search = $pf->findDeepProperties(
        $primary_placemark->streetNumber . ' ' . $primary_placemark->street,
        $primary_placemark->zip
    );
    //dump($search);
} catch (Exception $e) {

    echo getView($view_files['no_results'], get_defined_vars());
    exit;

}

//If the zillow service was able to find any properties which matched the
// address, we can proceed with trying to get additional information about
// the property
if( count($search) > 0 )
{
    $primary_property = $search[0];
    //dump($primary_property);

    //Let's see if there is a zestimate history chart available
    try {
        $chart = $primary_property->getChart( Pillow_Property::CHART_UNIT_DOLLAR, 275, 200 );
        $chart_url = $chart->url;
    } catch (Exception $e) {
        $chart_url = '/images/no_data.png';
    }

    //Let's see if there are any comparables available - try to get 5 of them.
    try {
        $comps = $primary_property->getDeepComps(5);
        foreach( $comps as $comp )
        {
            $comp_placemarks[] = $gc->geocode( $comp->street . ' ' . $comp->city . ' ' . $comp->state . ' ' . $comp->zipcode );
        }
        //dump($comps);
    } catch (Exception $e) {

    }
}
else
{
    echo getView($view_files['no_results'], get_defined_vars());
    exit;
}

echo getView($view_files['results'], get_defined_vars());
exit;


?>