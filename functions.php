<?php
include_once('config.php');

define('FOURSQUARE_VENUE_BASE_URL', 'http://foursquare.com/venue/');

function load_buildings($file) {
  $buildings = array();

  if (($fh = @fopen(BUILDING_CSV_FILE, 'r')) !== false) {
    $row = 0;
    while (($data = fgetcsv($fh)) !== false) {
      $name = str_replace('\\', '', $data[0]);

      $buildings[] = array(
        'name' => $name,
	'lat'  => $data[1],
	'lng'  => $data[2],
      );
    }
  }
  else {
    throw new Exception('Error opening building CSV file (' . $file . '); does it exist?');
  }

  return $buildings;
}

function get_url($url) {
  $ch = curl_init();

  curl_setopt_array($ch, array(
    CURLOPT_URL => $url,
    CURLOPT_HEADER => false,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 4,
  ));

  if (! $result = curl_exec($ch)) {
    trigger_error(curl_error($ch));
  }

  curl_close($ch);

  return $result;
}

function foursquare_search_venues($name, $lat, $lng) {
  $query = http_build_query(array(
    'query'         => $name,
    'll'            => "$lat,$lng",
    'client_id'     => FOURSQUARE_CLIENT_ID,
    'client_secret' => FOURSQUARE_CLIENT_SECRET,
    'v'             => '20110727',
  ));

  $url = FOURSQUARE_ENDPOINT . '?' . $query;

  $json = get_url($url);
  $data = json_decode($json);

  return $data->response->venues;
}

function foursquare_venue_url($venue) {
  return FOURSQUARE_VENUE_BASE_URL . $venue->id;
}
?>