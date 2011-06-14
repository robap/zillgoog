<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Google_Geocoder
{
    private $_addresses = array();

    public function __construct()
    {
      
    }

    /**
     * Geocodes (or attempts to) the supplied address. This method can be safely
     * called repeatedly without hitting the google geocode service. Each address
     * result is stored in this Google_Geocoder object and used whenever same
     * address is supplied to this method.
     * @param string $address
     * @access public
     * @return Google_Placemark
     */
    public function geocode( $address )
    {
        if( FALSE === array_key_exists($address, $this->_addresses) )
        {
            $result = file_get_contents( $this->_getUri($address) );
            $this->_addresses[$address] = $result;
        }

        return new Google_Placemark( $this->_addresses[$address] );
    }

    /**
     * Fetches the address from Google's geocode service
     * @param string $address
     * @access private
     * @return string
     */
    private function _getUri( $address )
    {
        $uri  = "http://maps.googleapis.com/maps/api/geocode/json?";
        $uri .= "address=" . urlencode($address);
        $uri .= "&sensor=false";
        return $uri;
    }
}