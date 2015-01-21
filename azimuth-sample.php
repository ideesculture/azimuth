<?php
# --------------------------------------------------------------------------------------------
#                  __                     /\ \__/\ \
#    __     ____  /\_\    ___ ___   __  __\ \ ,_\ \ \___
#  /'__`\  /\_ ,`\\/\ \ /' __` __`\/\ \/\ \\ \ \/\ \  _ `\
# /\ \L\.\_\/_/  /_\ \ \/\ \/\ \/\ \ \ \_\ \\ \ \_\ \ \ \ \
# \ \__/.\_\ /\____\\ \_\ \_\ \_\ \_\ \____/ \ \__\\ \_\ \_\
#  \/__/\/_/ \/____/ \/_/\/_/\/_/\/_/\/___/   \/__/ \/_/\/_/
#
#               Azimuth : Simple PHP library to compute azimuth (°), distance (km) & sight altitude (°)
#               GNU GPL v3
#               Gautier Michelin, 2015
#               based on Don Cross work, http://cosinekitty.com/compass.html
#
#               Usage example
#
# -------------------------------------------------------------------------------------------

    require_once('lib/azimuth.php');

    $tour_eiffel = array("lat"=> 48.85825, "lon"=>2.2945, "elv"=>357.5);
    $le_mans = array("lat"=> 48.006110000000010000, "lon"=>0.199556000000029600, "elv"=>134);

    // Le Mans is 181.149 km from Paris, Paris is at NW from Le Mans (58°), altitude is -1 so you should look
    // just under the horizon from the Tour Eiffel to have a look at Le Mans, but guess what, it's a bit far...

    $result = Calculate($le_mans, $tour_eiffel);
		print_r($result);
