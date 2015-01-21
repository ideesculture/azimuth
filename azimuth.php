<?php

	// Source : http://cosinekitty.com/compass.html

	// à supprimer
	function $ (id)
    {
        return document.getElementById (id);
    }

	// rewritten
    function ParseAngle($angle, $limit = 360) {
        if (is_nan($angle) || ($angle < -$limit) || ($angle > $limit)) {
            return null;
        } else {
            return $angle;
        }
    }

	// rewritten
    function ParseElevation($angle)
    {
        if (is_nan($angle)) {
            return null;
        } else {
            return $angle;
        }
    }

    // rewritten
    function ParseLocation(array $prefix)
    {
        $lat = ParseAngle($prefix['_lat'], 90.0);
        $location = null;
        if ($lat != null) {
            $lon = ParseAngle ($prefix['_lon'], 180.0);
            if ($lon != null) {
                $elv = ParseElevation($prefix['_elv']);
                if ($elv != null) {
                    $location = array('lat'=>$lat, 'lon'=>$lon, 'elv'=>$elv);
                }
            }
        }
        return $location;
    }

    // rewritten
    function EarthRadiusInMeters($latitudeRadians) {
        // http://en.wikipedia.org/wiki/Earth_radius
        $a = 6378137.0;  // equatorial radius in meters
        $b = 6356752.3;  // polar radius in meters
        $cos = cos($latitudeRadians);
        $sin = sin($latitudeRadians);
        $t1 = $a * $a * $cos;
        $t2 = $b * $b * $sin;
        $t3 = $a * $cos;
        $t4 = $b * $sin;
        return sqrt(($t1*$t1 + $t2*$t2) / ($t3*$t3 + $t4*$t4));
    }

    // rewritten
    function LocationToPoint(array $c) {
        // Convert (lat, lon, elv) to (x, y, z).
        $lat = $c["lat"] * pi() / 180.0;
        $lon = $c["lon"] * pi() / 180.0;
        $radius = $c["elv"] + EarthRadiusInMeters($lat);
        $cosLon = cos($lon);
        $sinLon = sin($lon);
        $cosLat = cos($lat);
        $sinLat = sin($lat);
        $x = $cosLon * $cosLat * $radius;
        $y = $sinLon * $cosLat * $radius;
        $z = $sinLat * $radius;
        return array('x'=>$x, 'y'=>$y, 'z'=>$z, 'radius'=>$radius);
    }

		// rewritten
    function Distance (array $ap, array $bp) {
        $dx = $ap["x"] - $bp["x"];
        $dy = $ap["y"] - $bp["y"];
        $dz = $ap["z"] - $bp["z"];
        return sqrt($dx*$dx + $dy*$dy + $dz*$dz);
    }

    // rewritten
    function RotateGlobe(array $b, array $a, $bradius, $aradius) {
        // Get modified coordinates of 'b' by rotating the globe so that 'a' is at lat=0, lon=0.
        $br = array('lat'=> $b["lat"], 'lon'=> ($b["lon"] - $a["lon"]), 'elv'=>$b["elv"]);
        $brp = LocationToPoint($br);

        // scale all the coordinates based on the original, correct geoid radius...
        $brp["x"] *= ($bradius / $brp["radius"]);
        $brp["y"] *= ($bradius / $brp["radius"]);
        $brp["z"] *= ($bradius / $brp["radius"]);
        $brp["radius"] = $bradius;   // restore actual geoid-based radius calculation

		    // Rotate brp cartesian coordinates around the z-axis by a.lon degrees,
        // then around the y-axis by a.lat degrees.
        // Though we are decreasing by a.lat degrees, as seen above the y-axis,
        // this is a positive (counterclockwise) rotation (if B's longitude is east of A's).
        // However, from this point of view the x-axis is pointing left.
        // So we will look the other way making the x-axis pointing right, the z-axis
        // pointing up, and the rotation treated as negative.

        $alat = -$a["lat"] * pi() / 180.0;
        $acos = cos($alat);
        $asin = sin($alat);

        $bx = ($brp["x"] * $acos) - ($brp["z"] * $asin);
        $by = $brp["y"];
        $bz = ($brp["x"] * $asin) + ($brp["z"] * $acos);

        return array('x'=>$bx, 'y'=>$by, 'z'=>$bz);
    }

    function Calculate()
    {
        var a = ParseLocation ('a');
        if (a != null) {
            var b = ParseLocation ('b');
            if (b != null) {
                var ap = LocationToPoint (a);
                var bp = LocationToPoint (b);
                var distKm = 0.001 * Math.round(Distance (ap, bp));
                $('div_Distance').innerHTML = distKm + '&nbsp;km';

                // Let's use a trick to calculate azimuth:
                // Rotate the globe so that point A looks like latitude 0, longitude 0.
                // We keep the actual radii calculated based on the oblate geoid,
                // but use angles based on subtraction.
                // Point A will be at x=radius, y=0, z=0.
                // Vector difference B-A will have dz = N/S component, dy = E/W component.

                var br = RotateGlobe (b, a, bp.radius, ap.radius);
                var theta = Math.atan2 (br.z, br.y) * 180.0 / Math.PI;
                var azimuth = 90.0 - theta;
                if (azimuth < 0.0) {
                    azimuth += 360.0;
                }
                if (azimuth > 360.0) {
                    azimuth -= 360.0;
                }
                $('div_Azimuth').innerHTML = (Math.round(azimuth*10)/10) + '&deg;';

                // Calculate altitude, which is the angle above the horizon of B as seen from A.
                // Almost always, B will actually be below the horizon, so the altitude will be negative.
                var shadow = Math.sqrt ((br.y * br.y) + (br.z * br.z));
                var altitude = Math.atan2 (br.x - ap.radius, shadow) * 180.0 / Math.PI;
                $('div_Altitude').innerHTML = (Math.round(altitude*100)/100).toString().replace(/-/g,'&minus;') + '&deg;';
            }
        }
    }

		// Code sample :
		/*




		*/
