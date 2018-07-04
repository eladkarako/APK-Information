<?php
  date_default_timezone_set("Asia/Jerusalem");
  setlocale(LC_ALL, 'he_IL.UTF-8');
  mb_internal_encoding('UTF-8');
  header('Content-Type: text/json; charset=utf-8');

  require_once("./ApkInfo.php");

  //---------------------------------------------------------------------------------------------------- user arguments.
  $file = isset($_REQUEST['file']) ? './resources/' . $_REQUEST['file'] : ''; //                        mandatory.
  $is_small = isset($_REQUEST['small']) && (mb_strtolower((string)$_REQUEST['small']) !== 'false'); //this way ?small === ?small=true by not ?small=false
  $is_zip = isset($_REQUEST['zip']) && (mb_strtolower((string)$_REQUEST['zip']) !== 'false');
  $is_no_data = isset($_REQUEST['nodata']) && (mb_strtolower((string)$_REQUEST['nodata']) !== 'false');
  $is_force_overwrite = isset($_REQUEST['force']) && (mb_strtolower((string)$_REQUEST['force']) !== 'false');
  //-------------------------------------------------------------------------------------------------------------------

  $file_exist = (mb_strlen($file) >= 3) && (false !== strpos($file, 'apk')) && file_exists($file);

  $file_json = $file . '.json';
  $file_json_exist = (mb_strlen($file) >= 3) && file_exists($file_json);

  if (!$file_exist) {
    http_response_code(501);

    $response = [
      'is_success'         => false,
      'file'               => $file,
      'is_exist'           => @file_exists($file),
      'is_force_overwrite' => $is_force_overwrite,
      'verbose'            => 'use anlz_apk.php?file=... with an existing file name, you may use on of those: ' . implode(', ', files_in('./resources', '/.(apk|zip|tar|gzip)$/'))
    ];

    $response = toJSON($response, $is_small);
    $response = $is_zip ? toJSON(['zip' => base64_encode(gzencode($response))], $is_small) : $response; //optionally zip output.
    die($response);
  }


  if (!$file_json_exist || $is_force_overwrite) {
    if (is_readable(realpath($file_json))) {
      unlink(realpath($file_json)); //make sure the JSON file is rewritten by deleting old copy (WIN7 bug: won't overwrite otherwise).
    }

    $response = @getApkFileInfo( //fresh analyze of APK package (possibly ignoring existing JSON, and overwriting it).
      $file,
      true,  //save all images to files in the operation system.
      true, //save the data as a JSON file.
      $is_force_overwrite   //force overwrite json file.
    );
  }
  else {
    $response = $file_json_exist ? json_decode(@file_get_contents($file_json), true) : '{"error...":"O-o"}';
  }

  //  if (!@file_exists($file_json)) { //(paranoid) ___.apk.json write-success check:
  //    http_response_code(501) &&
  //    $response = ['is_success' => false,
  //                 'verbose'    => $file_json . ' was not written successfully . '];
  //
  //    $response = toJSON($response, $is_small);
  //    $response = $is_zip ? toJSON(['zip' => base64_encode(gzencode($response))], $is_small) : $response; //optionally zip output.
  //    die($response);
  //  }


  //optionally overwrite the response-text (the JSON file and images were already written, this is just text-response..)
  $response = (true === $is_no_data) ?
    $response = [
      'is_success'           => true,
      'is_force_overwritten' => $is_force_overwrite,
      'verbose'              => pathinfo_extended($file_json)
    ] : $response;


  //-------------


  $response = toJSON($response, $is_small);
  $response = $is_zip ? toJSON(['zip' => base64_encode(gzencode($response))], $is_small) : $response; //optionally zip output.

  echo $response;

  /*
   * http://localhost/apk-information/anlz_apk.php?file=./resources/info.staticfree.android.twentyfourhour_8.apk
   * http://localhost/apk-information/anlz_apk.php?file=./resources/info.staticfree.android.twentyfourhour_8.apk&small
   * http://localhost/apk-information/anlz_apk.php?file=./resources/info.staticfree.android.twentyfourhour_8.apk&small&zip
   */
?>
