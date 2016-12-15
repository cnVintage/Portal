<?php
  function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }
  header('Content-Type: application/json');
  header("Access-Control-Allow-Origin: *");

  // Load flarum config.
  $config = include('config.php');
  // Fetch remote IP address
  $remoteIP = $_SERVER['REMOTE_ADDR'];
  // unused
  $operation = isset($_GET['op']) ? $_GET['op'] : 'request';
  // Response object.
  $res = (object)[]; 
  // 
  $access_token = null;

  // Connect to MySQL Server with mysqli
  $mysqli = new mysqli($config['database']['host'], 
                       $config['database']['username'], 
                       $config['database']['password'],
                       $config['database']['database']);
  // Check connection
  if ($mysqli->connect_errno) {
    $res->status = 'error';
    $res->message = 'Failed to connect to MySQL: ' . $mysqli->connect_error;
    die(json_encode($res));
  }

  // Fetch the cookie.
  if (isset($_COOKIE['flarum_remember'])) {
    $access_token = $_COOKIE['flarum_remember'];
  }
  else {
    $res->status = 'error';
    $res->message = 'Unauthorized access.';
    die(json_encode($res));
  }

  // Get user informathion form cookie.
  $access_token = $mysqli->real_escape_string($access_token);
  $result = $mysqli->query("SELECT user_id FROM fl_access_tokens WHERE fl_access_tokens.id = '$access_token'");
  $row = $result->fetch_assoc();
  $user_id = $row['user_id'];

  // Remove all old tokens
  $mysqli->query("DELETE FROM fl_telnet_access_tokens WHERE user_id = $user_id");

  // Insert new tokens
  $new_token = generateRandomString(5);
  $mysqli->query("INSERT INTO fl_telnet_access_tokens(remote_addr, user_id, access_token, flarum_token) VALUES('$remoteIP', $user_id, '$new_token', '$access_token')");
  
  // Create a new token and insert into database
  $res->status = 'success';
  $res->token = $new_token;
  echo(json_encode($res));
  