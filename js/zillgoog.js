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
 */
zillgoog.map = function( primary_property, options ) {
    this.primaryProperty = primary_property;

    if( !options ) options = {};
    
    var map_id = options.canvas || 'map_canvas';
    var initial_zoom_level = options.zoomLevel || 8;
    var lat_lon = new google.maps.LatLng(this.primaryProperty.x, this.primaryProperty.y);
    
    this.pano_ = null;
    this.panoService_ = new google.maps.StreetViewService();
    this.map_ = new google.maps.Map(document.getElementById(map_id), {
      zoom: initial_zoom_level,
      center: lat_lon,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    this.letterPosition = 0;
    this.lastClickedStreetViewProperty = null;
    
    this.bounds_ = new google.maps.LatLngBounds();
    this.createMarker( this.primaryProperty );
    
    this.infoWindow = new google.maps.InfoWindow({
      maxWidth: 350,
      content: '<div id="infoWindowContent" style="width:350px;height:200px;"></div>'
    });
    
    this.pin_ = new google.maps.MVCObject();
    
    var zillGoogMap = this; 
    google.maps.event.addListener(this.infoWindow, 'domready', function() {
      if (zillGoogMap.pano_ != null) {
        zillGoogMap.pano_.unbind("position");
        zillGoogMap.pano_.setVisible(false);
        
      }
      
      zillGoogMap.pano_ = new google.maps.StreetViewPanorama(document.getElementById("infoWindowContent"), {
        navigationControl: false,
        enableCloseButton: false,
        addressControl: false,
        linksControl: false,
        visible: true
      });
      zillGoogMap.pano_.bindTo("position", zillGoogMap.pin_);
      zillGoogMap.pano_.setVisible(true);
      
      var loc = new google.maps.LatLng(zillGoogMap.pin_.position.lat(), zillGoogMap.pin_.position.lng());
      zillGoogMap.panoService_.getPanoramaByLocation(loc, 50, function(result, status) {
        var camera = result.location.latLng;
        // We have to know which direction to point the camera, so we calculate the
        // bearing between the address position and the camera position.
        var bearing = LatLon.bearing( camera.lat(), camera.lng(), loc.lat(), loc.lng() );
        var pov = {heading: bearing, pitch:0, zoom:1};
        zillGoogMap.pano_.setPov(pov)
      });
    });
    
    google.maps.event.addListener(this.infoWindow, 'closeclick', function() {
      zillGoogMap.pano_.unbind("position");
      zillGoogMap.pano_.setVisible(false);
      zillGoogMap.pano_ = null;
    });
};

/**
 * Creates a marker and set's it letter to the next in the list
 * @param property - a property object
 * @return {google.maps.Marker}
 */
zillgoog.map.prototype.createMarker = function( property )
{
    var zillGoogMap = this;

    var letters = zillgoog.map.letters;

    var latlong = new google.maps.LatLng(property.x, property.y);

    var icon_url = "http://www.google.com/mapfiles/marker" + letters[this.letterPosition] + ".png";
    var marker = new google.maps.Marker({
      position: latlong,
      map: this.map_,
      icon: new google.maps.MarkerImage(icon_url)
    });
    
    google.maps.event.addListener(marker, 'click', function() {
        zillGoogMap.openInfoWindow(marker, zillGoogMap);
    });
    
    this.bounds_.extend(latlong);
    this.map_.fitBounds(this.bounds_);

    this.letterPosition = this.letterPosition + 1;

    return marker;
};

zillgoog.map.prototype.openInfoWindow = function( marker, context ) {
  context.pin_.set("position", marker.getPosition());
  context.infoWindow.open(context.map_, marker);
};


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
};

zillgoog.map.letters = [
  'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
  'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
  'Y', 'Z'
];