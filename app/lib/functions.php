<?php

function getView( $file, $vars )
{
    ob_start();

    extract($vars);
    
    include $file;

    $o = ob_get_contents();

    ob_end_clean();

    return $o;
}

function dump( $var )
{
    echo "<xmp>";
    print_r($var);
    echo "</xmp>";
}
?>
