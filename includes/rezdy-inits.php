<?php
add_action('init', 'rezdy_response_data');
add_action('init', 'editDriverOrManagerName');

function rezdy_response_data(){
  if(isset($_GET['rezdy_get_data'])){
    $apiKey = get_option('rezdy_apiKey');

    $body = ['orderStatus=CONFIRMED'];
    $headers = ["apiKey: $apiKey"];  
    
    echo getRequestData("https://api.rezdy.com/v1/bookings", $headers, $body);
    exit();
  }
}

function getRequestData($url, $headers, $body)
{
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url . "?" . join('&', $body));
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $out = curl_exec($curl);
  curl_close($curl);

  return $out;
}

function editDriverOrManagerName(){
  if(!isset($_POST['rezdy_id']) || !isset($_POST['driver_name']) || !isset($_POST['manager_name'])){
    return;
  }

  $id = $_POST['rezdy_id'];
  $driver = $_POST['driver_name'];
  $manager = $_POST['manager_name'];
  $savedList = get_option('rezdy_names');

  $savedList[$id] = [
    'driver' => $driver,
    'manager' => $manager
  ];

  update_option('rezdy_names', $savedList);
}