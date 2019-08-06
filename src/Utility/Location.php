<?php

namespace App\Utility;

class Location
{

    public $latitude;
    public $longitude;

    public static function getLocation($location)
    {
        if (!is_dir(CACHE . 'location')) {
            mkdir(CACHE . 'location');
            chmod(777, CACHE . 'location');
        }
        
       
        $location = explode(',', $location);
        foreach ($location as &$loc){
            $loc = urlencode($loc);
        }

        $location = implode(',', $location);
        
        $response = '';
        $json = null;
        $result = null;

        // 1. Check if data already exist
        if (is_file(CACHE . 'location/' . 'location-' . $location)) {
            $response = file_get_contents(CACHE . 'location/' . 'location-' . $location);
            $json = json_decode($response);
        }
        // 2. if not exist in file, get from api
        if (!isset($json->status)) {
            $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $location . "&sensor=true&key=AIzaSyCimfWUVW5af1XGqYGLbtmgtzusGQ8ynW4";
            $response = file_get_contents($request_url);
            $json = json_decode($response);

            // 3. of result correct, store this result
            if (isset($json->status) AND $json->status == 'OK') {
                file_put_contents(CACHE . 'location/' . 'location-' . $location, $response);
            }
        }

        // 4. Data is ready, generate result
        if (isset($json->status) AND $json->status == 'OK') {
            $result = new Location;
            $result->latitude = $json->results[0]->geometry->location->lat;
            $result->longitude = $json->results[0]->geometry->location->lng;
        }

        return $result;
    }
}
