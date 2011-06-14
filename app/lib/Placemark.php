<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Google_Placemark
{
    public $fullAddress;

    public $accuracy;

    public $state;

    public $city;

    public $county;

    public $zip;
    
    public $streetNumber;

    public $street;

    public $x;

    public $y;

    private $_isValid = FALSE;

    private $_rawJson;

    public function __construct( $raw_json )
    {
        $this->_rawJson = $raw_json;

        $pm = json_decode( $raw_json );

        //The returned string may not be decode-able
        if( NULL === $pm ) return;

        if( $pm->status != 'OK') return;
        
        $results = $pm->results;
        if(count($results) < 1) return;
        
        $result = $results[0];
        
        if(isset($result->partial_match)) {
          return;
        }

        $this->fullAddress = $result->formatted_address;
        
        foreach($result->address_components as $address_component)
        {
          if(in_array('street_number', $address_component->types)) {
            $this->streetNumber = $address_component->long_name;
          }
          
          if(in_array('route', $address_component->types)) {
            $this->street = $address_component->long_name;
          }
          
          if(in_array('administrative_area_level_3', $address_component->types)) {
            $this->city = $address_component->long_name;
          }
          
          if(in_array('administrative_area_level_2', $address_component->types)) {
            $this->county = $address_component->long_name;
          }
          
          if(in_array('administrative_area_level_1', $address_component->types)) {
            $this->state = $address_component->long_name;
          }
          
          if(in_array('postal_code', $address_component->types)) {
            $this->zip = $address_component->long_name;
          }
        }
        
        $this->x = $result->geometry->location->lat;
        $this->y = $result->geometry->location->lng;
        
        $this->_isValid = TRUE;
    }

    /**
     * Returns the raw json string that was used to construct this object
     * @return string
     */
    public function getRawJson()
    {
        return $this->_rawJson;
    }

    /**
     * Checks if this object is a valid Google Geocode Placemark
     * @return boolean
     */
    public function isValid()
    {
        return $this->_isValid;
    }
}