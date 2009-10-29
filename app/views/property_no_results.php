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
        <script type="text/javascript">
            $(document).ready(function(){
                $('#address').watermark('Enter Address and Press Enter');
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

            <div id="map">
                <div>
                   No results could be found for address <?php echo $address ?>
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
    </body>
</html>
