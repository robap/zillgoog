<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Google_Placemark
{
    public $id;

    public $fullAddress;

    public $accuracy;

    public $state;

    public $city;

    public $county;

    public $zip;

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

        if( $pm->Status->code != '200') return;

        if( FALSE === array_key_exists(0,$pm->Placemark) ) return;

        if( $pm->Placemark[0]->AddressDetails->Accuracy < 8 ) return;

        if( TRUE === array_key_exists(0, $pm->Placemark) )
        {
            $this->id = $pm->Placemark[0]->id;
            $this->fullAddress = $pm->Placemark[0]->address;
            $this->accuracy = $pm->Placemark[0]->AddressDetails->Accuracy;
            $this->state = $pm->Placemark[0]->AddressDetails->Country->AdministrativeArea->AdministrativeAreaName;
            $this->city = $pm->Placemark[0]->AddressDetails->Country->AdministrativeArea->SubAdministrativeArea->Locality->LocalityName;
            $this->county = $pm->Placemark[0]->AddressDetails->Country->AdministrativeArea->SubAdministrativeArea->SubAdministrativeAreaName;
            $this->zip = $pm->Placemark[0]->AddressDetails->Country->AdministrativeArea->SubAdministrativeArea->Locality->PostalCode->PostalCodeNumber;
            $this->street = $pm->Placemark[0]->AddressDetails->Country->AdministrativeArea->SubAdministrativeArea->Locality->Thoroughfare->ThoroughfareName;
            $this->x = $pm->Placemark[0]->Point->coordinates[0];
            $this->y = $pm->Placemark[0]->Point->coordinates[1];

            $this->_isValid = TRUE;
        }
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

?>
