<?php

  require_once("./ApkParser.php");
  require_once("./ApkImage.php");

  function toJSON($str, $isSmall = false) {
    return !$isSmall ?
      json_encode($str, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
      : json_encode($str);
  }


  /**
   * @param string $fileFullPath              - full path to the .apk file.
   * @param bool   $is_dump_images_to_files   - saving image files in the same folder.
   * @param bool   $is_dump_json_data_to_json - saving the information in a json, in the same folder.
   * @param bool   $is_force_overwrite_json   - try to overwrite the old file, even if it already exist.
   * @param bool   $is_keep_base64_images     - keep the large base64-images in the json (takes a lot of space).
   *
   * @return mixed
   */
  function getApkFileInfo($fileFullPath,
                          $is_dump_images_to_files = false,
                          $is_dump_json_data_to_json = false,
                          $is_force_overwrite_json = false,
                          $is_keep_base64_images = false) {

    //generic pathinfo with more data
    $info = pathinfo_extended($fileFullPath);


    //APK-Package specific data, and images.
    $f = $info["dirname"] . '/' . $info["basename"];

    try {
      $parser = new ApkParser();
      $parser->open($f);
      $info["name"] = $parser->getPackage();
      $info["version"] = $parser->getVersionName();
      $info["vercode"] = $parser->getVersionCode();
      $info["sdk_minimum_version"] = $parser->getUsesSDKMin();
      $info["sdk_target_version"] = $parser->getUsesSDKTarget();
      $info["application_meta_data"] = $parser->getApplicationMetaData();
      $info["permissions"] = $parser->getUsesPermissions();
      //$info["permissions"] = $parser->getUsesPermissionsDictionary();
      $info["hardware_features"] = $parser->getUsesFeature();
    } catch (Exception $ex) {
    }

    $info["images"] = ApkImage::get_array_of_images_from_apk($f, $is_dump_images_to_files); //also write images.
    $info["image_main"] = json_decode(json_encode(pathinfo($f . '.png'), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), true);
    $info["image_main"]["basename_escape"] = rawurlencode($info["image_main"]["basename"]);

    //make the JSON text smaller by removing the base64 and base64-with-prefix image representations (after they have already used..)
    if (!$is_keep_base64_images) {
      foreach ($info['images'] as &$image) {
        unset($image['image']);
        unset($image['base64_with_prefix']);
      }
    }

    if ($is_dump_json_data_to_json === true) {
      $json_filename = $f . '.json';

      json_object_to_file($info, $json_filename, $is_force_overwrite_json);
    }


    return $info;
  }


  function pathinfo_extended($fileFullPath = '') {

    if (empty($fileFullPath) || false === @file_exists($fileFullPath)) return [];


    //APK external (the APK file) information
    $info = pathinfo($fileFullPath); //[dirname] => ./resources | [basename] => com.google.android.youtube-5.17.6-51706300-minAPI15.apk | [extension] => apk | [filename] => com.google.android.youtube-5.17.6-51706300-minAPI15

    $info["basename_escape"] = rawurlencode($info["basename"]);

    $f = $info["dirname"] . '/' . $info["basename"];

    $info["size"] = filesize($f);
    $info["size_human_readable"] = human_readable_bytes_size($info["size"]);

    $time = time();


    //unix time of file's last-access, file creation time, file's last-modification time.
    $info['datetime_file_access'] = date("F d Y H:i:s.", fileatime($f) || $time);
    $info['datetime_file_creation'] = date("F d Y H:i:s.", filectime($f) || $time);
    $info['datetime_file_modification'] = date("F d Y H:i:s.", filemtime($f) || $time);

    //human readable format of the same from above ("3 hours ago", or if time signature was fiddle with it may be "will be in 3 hours")
    $info['datetime_file_access_human_readable'] = human_time_diff(@fileatime($f), $time);
    $info['datetime_file_creation_human_readable'] = human_time_diff(@filectime($f), $time);
    $info['datetime_file_modification_human_readable'] = human_time_diff(@filemtime($f), $time);


    return $info;
  }

  /**
   * write a json-like object to file
   *
   * @param      $data
   * @param      $filename_path
   * @param bool $force_overwrite (default=false) if true will always write new content
   */
  function json_object_to_file($data, $filename_path, $force_overwrite = false) {
    $is_should_write = (!@file_exists($filename_path)) || (!$force_overwrite);

    if ($is_should_write) {
      @file_put_contents($filename_path, toJSON($data)); //write data to json file
    }
  }


  /**
   * human_readable_bytes_size
   *
   * @param int $bytes
   * @param int $decimals
   *
   * @return string
   */
  function human_readable_bytes_size($bytes, $decimals = 2) {
    $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $factor = (int)(floor((strlen($bytes) - 1) / 3));

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[ $factor ];
  }


  /**
   * Constants for expressing human-readable intervals,
   * in their respective number of seconds.
   */
  define('MINUTE_IN_SECONDS', 60);
  define('HOUR_IN_SECONDS', 60 * MINUTE_IN_SECONDS);
  define('DAY_IN_SECONDS', 24 * HOUR_IN_SECONDS);
  define('WEEK_IN_SECONDS', 7 * DAY_IN_SECONDS);
  define('YEAR_IN_SECONDS', 365 * DAY_IN_SECONDS);


  /**
   * Retrieve the plural or single form based on the supplied amount.
   *
   * @param string $single The text that will be used if $number is 1.
   * @param string $plural The text that will be used if $number is not 1.
   * @param int    $number The number to compare against to use either $single or $plural.
   *
   * @return string Either $single or $plural translated text.
   */
  function _n($single, $plural, $number) {
    return 1 === $number ? $single : $plural;
  }


  /**
   * Determines the difference between two timestamps.
   *
   * The difference is returned in a human readable format such as "1 hour",
   * "5 mins", "2 days".
   *
   * @param int|string $from Unix timestamp from which the difference begins.
   * @param int|string $to   Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
   *
   * @return string Human readable time difference.
   */
  function human_time_diff($from, $to = '') {
    if (empty($to)) {
      $to = time();
    }


    $diff = (int)abs($to - $from);
    $since = '';

    if ($diff < HOUR_IN_SECONDS) {
      $mins = round($diff / MINUTE_IN_SECONDS);
      if ($mins <= 1)
        $mins = 1;
      /* translators: min=minute */
      $since = sprintf(_n('%s min', '%s mins', $mins), $mins);
    }
    elseif ($diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS) {
      $hours = round($diff / HOUR_IN_SECONDS);
      if ($hours <= 1)
        $hours = 1;
      $since = sprintf(_n('%s hour', '%s hours', $hours), $hours);
    }
    elseif ($diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS) {
      $days = round($diff / DAY_IN_SECONDS);
      if ($days <= 1)
        $days = 1;
      $since = sprintf(_n('%s day', '%s days', $days), $days);
    }
    elseif ($diff < 30 * DAY_IN_SECONDS && $diff >= WEEK_IN_SECONDS) {
      $weeks = round($diff / WEEK_IN_SECONDS);
      if ($weeks <= 1)
        $weeks = 1;
      $since = sprintf(_n('%s week', '%s weeks', $weeks), $weeks);
    }
    elseif ($diff < YEAR_IN_SECONDS && $diff >= 30 * DAY_IN_SECONDS) {
      $months = round($diff / (30 * DAY_IN_SECONDS));
      if ($months <= 1)
        $months = 1;
      $since = sprintf(_n('%s month', '%s months', $months), $months);
    }
    elseif ($diff >= YEAR_IN_SECONDS) {
      $years = round($diff / YEAR_IN_SECONDS);
      if ($years <= 1)
        $years = 1;
      $since = sprintf(_n('%s year', '%s years', $years), $years);
    }

    if ($to - $from > 0) {
      $since = 'was ' . $since . ' ago';
    }
    elseif ($to - $from < 0) {
      $since = 'will be in ' . $since;
    }

    return $since;
  }

  function files_in($base_folder_path, $included_ext) {
    $files = [];

    //cheap directory listing (no iterator needed)
    $directory = @opendir($base_folder_path);

    while ($file = readdir($directory)) {
      if ($file === "." || $file === "..") continue;
      array_push($files, $file);
    }

    @closedir($directory);

    uksort($files, "strnatcasecmp");

    $matched = [];

    foreach ($files as $file) {
      if (!preg_match($included_ext, $file)) continue;
      array_push($matched, $file);
    }

    return $matched;
  }
