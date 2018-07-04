<?php
  date_default_timezone_set("Asia/Jerusalem");
  setlocale(LC_ALL, 'he_IL.UTF-8');
  mb_internal_encoding('UTF-8');
  header('Content-Type: text/json; charset=utf-8');

  $is_small = isset($_REQUEST['small']) && (mb_strtolower((string)$_REQUEST['small']) !== 'false'); //this way ?small === ?small=true by not ?small=false
  $is_zip = isset($_REQUEST['zip']) && (mb_strtolower((string)$_REQUEST['zip']) !== 'false'); //base64_encode(gzencode(toJSON($datas, $is_small)))],$is_small)

  require_once("./ApkInfo.php");
  include_once('./assets/misc.php');

  $response = @file_get_contents('index.mustache.html');

  $response = $is_small ? minify_html($response) : $response; //optionally minify content HTML+JS
  $response = ['html' => $response];

  $response = toJSON($response, $is_small);
  $response = $is_zip ? toJSON(['zip' => base64_encode(gzencode($response))], $is_small) : $response; //optionally zip output.

  echo $response;
