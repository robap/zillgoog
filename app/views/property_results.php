<?php
/**
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Zillow/Google Masher</title>
        <link href="css/custom-theme/jquery-ui-1.7.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <!--[if lte IE 7]>
        <link rel="stylesheet" type="text/css" href="css/style_ie.css" />
        <![endif]-->

        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/jquery.watermark.min.js"></script>
        <script type="text/javascript" src="js/latnlng.js"></script>
        <script type="text/javascript" src="js/zillgoog.js"></script>
        <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=<?php echo $google_api_key ?>" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $('#address').watermark('Enter Address and Press Enter');

                var pm    = <?php echo json_encode($primary_placemark) ?>;
                var comps = <?php echo json_encode($comp_placemarks) ?>;

                //map_initialize( pm );
                zg = new zillgoog.map(pm);
                zg.addComparables( comps );
                zg.enableStreetView( 'dia', 'street_view' );

                //Apply a row click effect
                $('#comps table tbody tr').each(function(){
                    var $anchor = $('a', this);
                    var anchor_text = $anchor.text();
                    var href    = $anchor.attr('href');

                    $anchor.after(anchor_text);
                    $anchor.remove();
                    
                    $(this).click(function(){
                        window.location = href;
                        return false;
                    });
                });
                
            })

            $(document).unload(function(){
                GUnload();
            });
            
        </script>
    </head>
    <body>
        <div id="main">

            <div id="header">
                <div id="logo">
                    <a href="<?php echo dirname($_SERVER['REQUEST_URI']) ?>"><span>ZILLOW / GOOGLE</span> MASHER</a>
                </div>
            </div>

            <div id="search_form_container">
                <div>
                    <a href="http://zillow.com">
                        <img class="zillow" src="http://www.zillow.com/widgets/GetVersionedResource.htm?path=/static/logos/Zillowlogo_150x40.gif" width="150" height="40" alt="Zillow Real Estate Search" />
                    </a>
                    <form action="index.php" method="get">
                        <input type="text" id="address" name="address" value="<?php echo $address ?>" />
                        <button>Search</button>
                    </form>
                </div>
            </div>
            
            <div id="inner">

                <div id="map">
                    <div id="map_and_comps">
                        <div id="map_canvas">

                        </div>
                        <div>
                            <fieldset id="comps">
                                <legend>Comparables</legend>
                                <?php if( count($comps) > 0 ) : ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Place Mark</th>
                                            <th>Street</th>
                                            <th>City</th>
                                            <th>Bed</th>
                                            <th>Bath</th>
                                            <th>Lot Size</th>
                                            <th>House Size</th>
                                            <th>Last Sold</th>
                                            <th>Last Price</th>
                                            <th>Zestimate<sup>&reg;</sup></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach( $comps as $comp ): ?>

                                        <tr title="Click to view this propertie&rsquo;s details">
                                            <td>
                                                <?php echo $labels[$label_position]; $label_position++; ?>
                                            </td>
                                            <td>
                                                <a href="?address=<?php echo urlencode($comp->street . ' ' . $comp->zipcode) ?>">
                                                <?php echo $comp->street ?>
                                                </a>
                                            </td>
                                            <td><?php echo $comp->city ?></td>
                                            <td><?php echo $comp->bedrooms ?></td>
                                            <td><?php echo $comp->bathrooms ?></td>
                                            <td><?php echo $comp->lotSizeSqFt ?></td>
                                            <td><?php echo $comp->finishedSqFt ?></td>
                                            <td><?php echo $comp->lastSoldDate ?></td>
                                            <td><?php echo number_format($comp->lastSoldPrice) ?></td>
                                            <td><?php echo number_format($comp->zestimate->amount) ?></td>
                                        </tr>

                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <div class="zillow_link">
                                    See more comparables on
                                    <a href="<?php echo $primary_property->links->comparables ?>">Zillow.com</a>
                                </div>
                                
                                <?php else: ?>
                                <div>
                                    <img src="images/no_data.png" alt="No data" />
                                </div>
                                <?php endif; ?>
                            </fieldset>
                        </div>
                    </div>
                    <div id="details">
                        <fieldset id="details_description">
                            <legend>Property Details</legend>
                            <table id="property_details">
                                <tbody>
                                    <tr>
                                        <th>Zip: </th>
                                        <td><?php echo $primary_property->zipcode ?></td>

                                        <th>State: </th>
                                        <td><?php echo $primary_property->state ?></td>
                                    </tr>
                                    <tr>
                                        <th>Use: </th>
                                        <td><?php echo $primary_property->useCode ?></td>

                                        <th>Built: </th>
                                        <td><?php echo $primary_property->yearBuilt ?></td>
                                    </tr>
                                    <tr>
                                        <th>Bed: </th>
                                        <td><?php echo $primary_property->bedrooms ?></td>

                                        <th>Bath: </th>
                                        <td><?php echo $primary_property->bathrooms ?></td>
                                    </tr>
                                    <tr>
                                        <th>Sq Ft: </th>
                                        <td><?php echo $primary_property->finishedSqFt ?></td>

                                        <th>Lot Size: </th>
                                        <td><?php echo $primary_property->lotSizeSqFt ?></td>
                                    </tr>
                                    <tr>
                                        <th>Last Sold Date: </th>
                                        <td><?php echo $primary_property->lastSoldDate ?></td>

                                        <th>Last Sold Price: </th>
                                        <td><?php echo $primary_property->lastSoldPrice ?></td>
                                    </tr>
                                    <tr class="zestimate_text">
                                        <th><span class="zillow zestimate">Zestimate<sup>&reg;</sup>:</span></th>
                                        <td colspan="3">
                                            <?php echo number_format($primary_property->zestimate->amount); ?>&nbsp;
                                            as of <?php echo $primary_property->zestimate->lastUpdated ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="zillow_link">
                                See more home details on <a href="<?php echo $primary_property->links->homedetails ?>">Zillow.com</a>
                            </div>
                        </fieldset>
                        <fieldset id="details_chart">
                            <legend>1 Year Value Change</legend>
                            <img id="zestimate" src="<?php echo $chart_url ?>" alt="no historical zestimate available" />
                            <div class="zillow_link">
                                See more graphs and data on <a href="<?php echo $primary_property->links->graphsanddata ?>">Zillow.com</a>
                            </div>
                        </fieldset>
                        
                    </div>
                </div>

            </div>
            <div id="footer">
                <p>
                   &copy; Zillow, Inc., 2008. Use is subject to <a href="/corp/Terms.htm">Terms of Use</a><br /> <a href="/wikipages/What-is-a-Zestimate/">What's a Zestimate?</a>
                </p>
                <p>
                    Search icon provided by <a href="http://chrfb.deviantart.com/">Christian F. Burprich</a>
                </p>
                <p>
                    Warning icon provided by <a href="http://www.webappers.com/">Ray Cheung</a>
                </p>
                <p>
                    &copy; Rob Apodaca, 2009
                </p>
            </div>
        </div>
<?php // dump($primary_property) ?>
        <div id="dia"><div id="street_view"></div></div>
    </body>
</html>
