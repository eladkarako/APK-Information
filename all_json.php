<?php
  /*
   * you can use http://localhost/APK-Information/all_json.php?prefix=[a-n]
   * to get a json of just the files that matches the following regex:
   * /^[a-n].*\.json$/i
   *
   * using no '?prefix=__' will match the following regex
   * /^.*\.json$/i
   * which is essentially correct as well.. (all json files).
   */

  date_default_timezone_set("Asia/Jerusalem");
  setlocale(LC_ALL, 'he_IL.UTF-8');
  mb_internal_encoding('UTF-8');
  header('Content-Type: text/json; charset=utf-8');

  //request arguments.
  //-------------------------------------------------------------------------------------------------------------------
  $is_small = isset($_REQUEST['small']) && (mb_strtolower((string)$_REQUEST['small']) !== 'false'); //this way ?small === ?small=true by not ?small=false
  $is_zip = isset($_REQUEST['zip']) && (mb_strtolower((string)$_REQUEST['zip']) !== 'false');
  $prefix = isset($_REQUEST['prefix']) ? $_REQUEST['prefix'] : '';
  //-------------------------------------------------------------------------------------------------------------------


  require_once("./ApkInfo.php");

  $file_ext = 'json';
  $files = files_in('./resources', '/^' . $prefix . '.*' . '\.' . $file_ext . '$/i');
  $files_length = count($files);

  //------------------- skip I/O if no change.
  $pre_counter = './resources/_' . $prefix . '_count.txt';
  $pre_prefix = './resources/_' . $prefix . '.json';
  $is_unmodified = file_exists($pre_counter) && (@file_get_contents($pre_counter) === (string)$files_length) && file_exists($pre_prefix);
  //------------------------------------------

  $response = '';

  if (true === $is_unmodified) {
    //reading from cache
    $response = @file_get_contents($pre_prefix);
    $response = json_decode($response, true);
    header('X-Used-Cache: true');
  }
  else {
    foreach ($files as $index => $file)
      $response .= @file_get_contents('./resources/' . $file) . ',';

    $response = (',' === mb_substr($response, -1)) ? mb_substr($response, 0, -1) : $response; //fix last char is ',' (invalid JSON text..)

    //parse to data, since we *may* want to modify the output now..
    $response = json_decode('[' . $response . ']', true);

    //writing to cache
    @file_put_contents($pre_counter, $files_length);
    @file_put_contents($pre_prefix, toJSON($response, true));
    header('X-Used-Cache: false');
  }


  $response = toJSON($response, $is_small);
  $response = $is_zip ? toJSON(['zip' => base64_encode(gzencode($response))], $is_small) : $response; //optionally zip output.

  echo $response;

