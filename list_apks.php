<?php
  date_default_timezone_set("Asia/Jerusalem");
  setlocale(LC_ALL, 'he_IL.UTF-8');
  mb_internal_encoding('UTF-8');
  header('Content-Type: text/json; charset=utf-8');

  require_once("./ApkInfo.php");

  $is_small = isset($_REQUEST['small']) && (mb_strtolower((string)$_REQUEST['small']) !== 'false'); //this way ?small === ?small=true by not ?small=false
  $is_zip = isset($_REQUEST['zip']) && (mb_strtolower((string)$_REQUEST['zip']) !== 'false'); //base64_encode(gzencode(toJSON($datas, $is_small)))],$is_small)

  function get_list_of_apk_files() {
    $files = files_in('./resources', '/.(apk|zip|tar|gzip)$/');


    $response = [
      'total'                 => count($files),
      'packages_with_json'    => [],
      'packages_without_json' => [],
      'is_all_have_json'      => true
    ];


    foreach ($files as $file) {
      $json_file = './resources/' . $file . '.json';
      $is_has_json = @file_exists($json_file);

      if ($is_has_json)
        array_push($response['packages_with_json'], $file);
      else
        array_push($response['packages_without_json'], $file);

      $response['is_all_have_json'] = $response['is_all_have_json'] && $is_has_json;
    }

    return $response;
  }

  $response = get_list_of_apk_files();
  $response = toJSON($response, $is_small);
  $response = $is_zip ? toJSON(['zip' => base64_encode(gzencode($response))], $is_small) : $response; //optionally zip output.

  echo $response;
?>
