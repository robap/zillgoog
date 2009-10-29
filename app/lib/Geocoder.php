<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Google_Geocoder
{
    private $_key;

    private $_addresses = array();

    public function __construct( $key )
    {
        $this->_key = $key;
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
        $uri  = "http://maps.google.com/maps/geo?";
        $uri .= "q=" . urlencode($address);
        $uri .= "&key=" . $this->_key;
        $uri .= "&sensor=false";
        $uri .= "&output=json";
        return $uri;
    }
}

?>
