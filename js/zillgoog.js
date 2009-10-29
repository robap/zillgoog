/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * Namespace for the zillgoog app
 */
var zillgoog = {};

/**
 * Constructor for zillgoog.map
 * @constructor
 * @param primary_property - property object
 * @param options - options bag
 * @return zillgoog.map
 */
zillgoog.map = function( primary_property, options )
{
    this.primaryProperty = primary_property;

    if( !options ) options = {};

    var map_id = options.canvas || 'map_canvas';
    var initial_zoom_level = options.zoomLevel || 0;

    this.canvas = new GMap2(document.getElementById(map_id));

    var lat_lon = new GLatLng(this.primaryProperty.y, this.primaryProperty.x);

    this.canvas.setCenter(lat_lon, initial_zoom_level); 
    this.canvas.setUIToDefault();

    //The base icon from which all markers are derived
    this.baseIcon = new GIcon(G_DEFAULT_ICON);
    this.baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
    this.baseIcon.iconSize = new GSize(20, 34);
    this.baseIcon.shadowSize = new GSize(37, 34);
    this.baseIcon.iconAnchor = new GPoint(9, 34);
    this.baseIcon.infoWindowAnchor = new GPoint(9, 2);

    //Flag to determine if street view is enabled
    this.streetViewEnabled = false;
    this.streetViewDialog  = null;
    this.streetViewPan     = null;
    this.$streetView       = null;

    this.letterPosition = 0;
    this.lastClickedStreetViewProperty = null;

    //The bounds object will be used later to determine zoom level
    this.bounds = new GLatLngBounds();

    this.createMarker( this.primaryProperty );

    return this;
};

/**
 * Creates a marker and set's it letter to the next in the list
 * @param property - a property object
 * @return GMarker
 */
zillgoog.map.prototype.createMarker = function( property )
{
    var zillGoogMap = this;

    var letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
                   'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                   'Y', 'Z'];

    var latlong = new GLatLng(property.y, property.x);

    var letteredIcon = new GIcon(this.baseIcon);
    
    letteredIcon.image = "http://www.google.com/mapfiles/marker" + letters[this.letterPosition] + ".png";

    var marker = new GMarker(latlong, {icon: letteredIcon});

    GEvent.addListener(marker, "click", function() {
       //options to pass to openInfoWindowHtml
       var opts = {};

       var message = '<div class="map_address">';
          message += '<div class="title">Property Address</div>';
          message += '<div class="street">' + property.street + '</div>';
          message += '<div class="city_st_zip">' + property.city + ', ' + property.state + ' ' + property.zip + '</div>';
          message += '<div class="options">';

          if( zillGoogMap.streetViewEnabled === true )
          {
            //Add a link to click to view the street view dialog. Use the onOpenFn
            // here to bind the click event after the info window has opened.
            message += '<span id="street_view_click_' + letters[zillGoogMap.letterPosition] + '">Street View</span>';
            opts.onOpenFn = function(){
                 $('#street_view_click_' + letters[zillGoogMap.letterPosition]).click(function(){
                      zillGoogMap.streetViewDialog.dialog('open');
                      zillGoogMap.streetViewDialog.dialog('option', 'title', property.fullAddress);
                  });
              }

              zillGoogMap.lastClickedStreetViewProperty = property;
          }
        
          message += '</div>';
          message += '</div>';

        zillGoogMap.canvas.openInfoWindowHtml(marker.getPoint(), message, opts);
    });

    this.canvas.addOverlay( marker );
    this.bounds.extend( marker.getPoint() );

    this.letterPosition = this.letterPosition + 1;

    return marker;
};

zillgoog.map.prototype.enableStreetView = function( dialog_id, street_view_id )
{
    var zillGoogMap = this;

    this.$street_view = $( '#' + street_view_id );
    //this.streetViewPan = new GStreetviewPanorama(this.$street_view[0]);

    this.streetViewDialog = $( '#' + dialog_id ).dialog({
        autoOpen    : false,
        width       : 600,
        height      : 365,
        resizable   : false,
        open        : function(){
            zillGoogMap.setStreetView();
        }
    });


    this.streetViewEnabled = true;
};

zillgoog.map.prototype.setStreetView = function()
{
    var zillGoogMap = this;

    var property = this.lastClickedStreetViewProperty;
    
    var lat_lon = new GLatLng(property.y, property.x);

    var client = new GStreetviewClient();
    client.getNearestPanoramaLatLng(lat_lon, function(camera){
        if ( camera != null ) {
                // We have to know which direction to point the camera, so we calculate the
                // bearing between the address position and the camera position.
                var bearing = LatLon.bearing( camera.lat(), camera.lng(), lat_lon.lat(), lat_lon.lng() );

                // To point the camera in that direct, we have to pass a "Point of View" object.
                var pov = { yaw: bearing };

                zillGoogMap.$street_view.html(''); //fill with whirly gig later

                zillGoogMap.streetViewPan = new GStreetviewPanorama(zillGoogMap.$street_view[0]);
                // Now we put the street view on the page.
                zillGoogMap.streetViewPan.setLocationAndPOV(camera, pov);
        }

    });
}

/**
 * Adds comparable addresses to the map. When all of them have been set,
 * the map zoom level is adjusted to encompass all of the addresses (including
 * the primary property)
 * @param [properties] - array of properties to add
 * @return void
 */
zillgoog.map.prototype.addComparables = function( properties )
{
    for( var i = 0; i < properties.length; i++ )
    {
        this.createMarker( properties[i] );
    }

    this.canvas.setZoom( this.canvas.getBoundsZoomLevel( this.bounds ) );
    this.canvas.setCenter( this.bounds.getCenter() );
};