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
                $('#address').watermark('Enter a search address');
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
                        <input type="text" id="address" name="address" />
                        <button>Search</button>
                    </form>
                </div>
            </div>

            <div id="inner">

                <div id="map">
                    <div id="zillgoog_description">
                        <h3>Introduction</h3>
                        <p>
                            Zillgoog demonstrates an example of what can be achieved
                            using Zillow&rsquo;s real estate content service and
                            Google&rsquo;s maps/street view service. The back end is
                            written in php. JQuery is used for the street view dialog
                            box.
                        </p>
                        <p>
                            To access the Zillow web service, most of the api requires
                            a street address and either city/state or zip. We will
                            leverage Google's geocoder web service to take in a
                            single string for the address and find an exact address
                            match which can then be supplied to the Zillow web service.
                        </p>
                        <p>
                            This code does not utilize a framework. It is meant
                            to be as simple as possible so that you can see some
                            of the concepts used to mash Zillow and Google maps
                            together. You are strongly encouraged to take these
                            concepts and put them in your own framework.
                        </p>
                        <h3>Dependencies</h3>
                        <ul>
                            <li>>= php 5.2.x</li>
                            <li>php curl extension</li>
                            <li><a href="http://github.com/robap/pillow">Pillow PHP Zillow client</a></li>
                        </ul>
                        <h3>Install this demo</h3>
                        <ul>
                            <li>Download this demo&rsquo;s source code <a href="http://github.com/robap/zillgoog">here</a>.</li>
                            <li class="no_style">
                                <pre>
$ wget http://github.com/robap/zillgoog/tarball/master
                                </pre>
                            </li>
                            <li>Download the
                                <a href="http://github.com/robap/pillow">Pillow PHP Zillow client</a>
                            </li>
                            <li>Extract Pillow somewhere</li>
                            <li class="no_style">
                                <pre>
$ wget http://github.com/robap/pillow/tarball/master
$ tar -zxvf robap-pillow-3cc404a.tar.gz
$ mv robap-pillow-3cc404a pillow
                                </pre>
                            </li>
                        </ul>
                        <h3>Configure</h3>
                        <ul>
                            <li>
                                Get your zillow web services id
                                <a href="http://www.zillow.com/webservice/Registration.htm">
                                    here
                                </a>
                            </li>
                            <li>
                                Get your Google maps api key
                                <a href="http://code.google.com/apis/maps/signup.html">
                                    here
                                </a>
                            </li>
                            <li>
                                In the extracted source, copy the file:
                                app/config/config.template.php
                                to app/config/config.php
                            </li>
                            <li class="no_style">
                                <pre>
$ cp app/config/config.template.php app/config/config.php
                                </pre>
                            </li>
                            <li>
                                Add your Zillow and Google keys to app/config/config.php and
                                add the path to the pillow library.
                            </li>
                            <li class="no_style">
                                <pre>
$config = array(
    'google_api_key'        => 'place your google api key here',
    'zillow_api_key'        => 'place your zillow api key here',
    'pillow_intall'         => '../../pillow/'
);
                                </pre>
                            </li>
                        </ul>
                        <h3>Browsers Tested</h3>
                        <ul>
                            <li>Firefox</li>
                            <li>Google Chrome</li>
                            <li>Safari</li>
                        </ul>
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
    </body>
</html>
