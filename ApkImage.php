<?php

  class ApkImage {
    /**
     * returns an associative-array of images, and their basic meta-data such as height, width, channel level, bits for
     * each color, and mime-type.
     *
     * @param string $apk_file_path           - an operation-system-sensitive path to the apk file
     * @param bool   $is_dump_images_to_files - saving image files
     *
     * @return array
     */
    public static
    function get_array_of_images_from_apk($apk_file_path, $is_dump_images_to_files = false) {
      $icon_paths = [
        //ordered by groups of quality, highest on top, from each per, the mipmap is better, at last, placed two old formats.
        'res/mipmap-xxxhdpi/ic_launcher.png',           // 192x192 pixels                 - used in google or new packages
        'res/mipmap-xxxhdpi/icon.png',                  // alternative name: 192x192 pixels                 - used in google or new packages
        'res/mipmap-xxxhdpi/app_icon.png',                  // alternative name: 192x192 pixels                 - used in google or new packages
        'res/drawable-xxxhdpi/ic_launcher.png',         // 192x192 pixels (not constant..)  - used in most packages
        'res/drawable-xxxhdpi/icon.png',                // 192x192 pixels (not constant..)  - used in most packages
        'res/drawable-xxxhdpi/app_icon.png',                // 192x192 pixels (not constant..)  - used in most packages

        'res/drawable-xxhdpi/ic_launcher.png',               // 144x144 pixels (not constant..)  - used in most packages
        'res/mipmap-xxhdpi/icon.png',                        // 144x144 pixels                - used in google or new packages
        'res/mipmap-xxhdpi/app_icon.png',                    // 144x144 pixels                - used in google or new packages
        'res/drawable-xxhdpi/icon.png',                      // 144x144 pixels (not constant..)  - used in most packages
        'res/drawable-xxhdpi/app_icon.png',                  // 144x144 pixels (not constant..)  - used in most packages
        'res/mipmap-xxhdpi/ic_launcher.png',                 // 144x144 pixels                - used in google or new packages
        'res/drawable-xxhdpi-v0/ic_launcher_auto_media.png', // 144x144 pixels                                   - used in packages that has version appended
        'res/drawable-xxhdpi-v1/ic_launcher_auto_media.png', // 144x144 pixels                                   - used in packages that has version appended
        'res/drawable-xxhdpi-v2/ic_launcher_auto_media.png', // 144x144 pixels                                   - used in packages that has version appended
        'res/drawable-xxhdpi-v3/ic_launcher_auto_media.png', // 144x144 pixels                                   - used in packages that has version appended
        'res/drawable-xxhdpi-v4/ic_launcher_auto_media.png', // 144x144 pixels                                   - used in packages that has version appended
        'res/drawable-xxhdpi-v5/ic_launcher_auto_media.png', // 144x144 pixels                                   - used in packages that has version appended
        'res/drawable-xxhdpi-v0/ic_launcher_auto_messaging.png',  // 144x144 pixels         - used in custom-packages that has version appended
        'res/drawable-xxhdpi-v1/ic_launcher_auto_messaging.png',  // 144x144 pixels         - used in custom-packages that has version appended
        'res/drawable-xxhdpi-v2/ic_launcher_auto_messaging.png',  // 144x144 pixels         - used in custom-packages that has version appended
        'res/drawable-xxhdpi-v3/ic_launcher_auto_messaging.png',  // 144x144 pixels         - used in custom-packages that has version appended
        'res/drawable-xxhdpi-v4/ic_launcher_auto_messaging.png',  // 144x144 pixels         - used in custom-packages that has version appended
        'res/drawable-xxhdpi-v5/ic_launcher_auto_messaging.png',  // 144x144 pixels         - used in custom-packages that has version appended

        'res/mipmap-xhdpi/ic_launcher.png',                 // 96x96 pixels                   - used in google or new packages
        'res/drawable-xhdpi/ic_launcher.png',               // 96x96 pixels (not constant..)  - used in most packages
        'res/mipmap-xhdpi/icon.png',                        // alternative name: 96x96 pixels                   - used in google or new packages
        'res/mipmap-xhdpi/app_icon.png',                    // alternative name: 96x96 pixels                   - used in google or new packages
        'res/drawable-xhdpi/icon.png',                      // alternative name: 96x96 pixels (not constant..)  - used in most packages
        'res/drawable-xhdpi/app_icon.png',                  // alternative name: 96x96 pixels (not constant..)  - used in most packages
        'res/drawable-xhdpi-v0/ic_launcher_auto_media.png', // 96x96 pixels                                     - used in packages that has version appended
        'res/drawable-xhdpi-v1/ic_launcher_auto_media.png', // 96x96 pixels                                     - used in packages that has version appended
        'res/drawable-xhdpi-v2/ic_launcher_auto_media.png', // 96x96 pixels                                     - used in packages that has version appended
        'res/drawable-xhdpi-v3/ic_launcher_auto_media.png', // 96x96 pixels                                     - used in packages that has version appended
        'res/drawable-xhdpi-v4/ic_launcher_auto_media.png', // 96x96 pixels                                     - used in packages that has version appended
        'res/drawable-xhdpi-v5/ic_launcher_auto_media.png', // 96x96 pixels                                     - used in packages that has version appended
        'res/drawable-xhdpi-v0/ic_launcher_auto_messaging.png',  // 96x96 pixels         - used in custom-packages that has version appended
        'res/drawable-xhdpi-v1/ic_launcher_auto_messaging.png',  // 96x96 pixels         - used in custom-packages that has version appended
        'res/drawable-xhdpi-v2/ic_launcher_auto_messaging.png',  // 96x96 pixels         - used in custom-packages that has version appended
        'res/drawable-xhdpi-v3/ic_launcher_auto_messaging.png',  // 96x96 pixels         - used in custom-packages that has version appended
        'res/drawable-xhdpi-v4/ic_launcher_auto_messaging.png',  // 96x96 pixels         - used in custom-packages that has version appended
        'res/drawable-xhdpi-v5/ic_launcher_auto_messaging.png',  // 96x96 pixels         - used in custom-packages that has version appended

        'res/mipmap-hdpi/ic_launcher.png',                  // 72x72 pixels                   - used in google or new packages
        'res/drawable-hdpi/ic_launcher.png',                // 72x72 pixels (not constant..)  - used in most packages
        'res/mipmap-hdpi/icon.png',                         // alternative name: 72x72 pixels                   - used in google or new packages
        'res/mipmap-hdpi/app_icon.png',                     // alternative name: 72x72 pixels                   - used in google or new packages
        'res/drawable-hdpi/icon.png',                       // alternative name: 72x72 pixels (not constant..)  - used in most packages
        'res/drawable-hdpi/app_icon.png',                   // alternative name: 72x72 pixels (not constant..)  - used in most packages
        'res/drawable-hdpi-v0/ic_launcher_auto_media.png',      // 72x72 pixels         - used in packages that has version appended
        'res/drawable-hdpi-v1/ic_launcher_auto_media.png',      // 72x72 pixels         - used in packages that has version appended
        'res/drawable-hdpi-v2/ic_launcher_auto_media.png',      // 72x72 pixels         - used in packages that has version appended
        'res/drawable-hdpi-v3/ic_launcher_auto_media.png',      // 72x72 pixels         - used in packages that has version appended
        'res/drawable-hdpi-v4/ic_launcher_auto_media.png',      // 72x72 pixels         - used in packages that has version appended
        'res/drawable-hdpi-v5/ic_launcher_auto_media.png',      // 72x72 pixels         - used in packages that has version appended
        'res/drawable-hdpi-v0/ic_launcher_auto_messaging.png',  // 72x72 pixels         - used in custom-packages that has version appended
        'res/drawable-hdpi-v1/ic_launcher_auto_messaging.png',  // 72x72 pixels         - used in custom-packages that has version appended
        'res/drawable-hdpi-v2/ic_launcher_auto_messaging.png',  // 72x72 pixels         - used in custom-packages that has version appended
        'res/drawable-hdpi-v3/ic_launcher_auto_messaging.png',  // 72x72 pixels         - used in custom-packages that has version appended
        'res/drawable-hdpi-v4/ic_launcher_auto_messaging.png',  // 72x72 pixels         - used in custom-packages that has version appended
        'res/drawable-hdpi-v5/ic_launcher_auto_messaging.png',  // 72x72 pixels         - used in custom-packages that has version appended

        'res/mipmap-mdpi/ic_launcher.png',                  // 48x48 pixels                   - used in google or new packages
        'res/drawable-mdpi/ic_launcher.png',                // 48x48 pixels (not constant..)  - used in most packages
        'res/mipmap-mdpi/icon.png',                         // alternative name: 48x48 pixels                   - used in google or new packages
        'res/drawable-mdpi/icon.png',                       // alternative name: 48x48 pixels (not constant..)  - used in most packages
        'res/drawable-mdpi/app_icon.png',                   // alternative name: 48x48 pixels (not constant..)  - used in most packages
        'res/drawable-mdpi-v0/ic_launcher_auto_media.png',  // 48x48 pixels                                     - used in packages that has version appended
        'res/drawable-mdpi-v1/ic_launcher_auto_media.png',  // 48x48 pixels                                     - used in packages that has version appended
        'res/drawable-mdpi-v2/ic_launcher_auto_media.png',  // 48x48 pixels                                     - used in packages that has version appended
        'res/drawable-mdpi-v3/ic_launcher_auto_media.png',  // 48x48 pixels                                     - used in packages that has version appended
        'res/drawable-mdpi-v4/ic_launcher_auto_media.png',  // 48x48 pixels                                     - used in packages that has version appended
        'res/drawable-mdpi-v5/ic_launcher_auto_media.png',  // 48x48 pixels                                     - used in packages that has version appended
        'res/drawable-mdpi-v0/ic_launcher_auto_messaging.png',  // 48x48 pixels         - used in custom-packages that has version appended
        'res/drawable-mdpi-v1/ic_launcher_auto_messaging.png',  // 48x48 pixels         - used in custom-packages that has version appended
        'res/drawable-mdpi-v2/ic_launcher_auto_messaging.png',  // 48x48 pixels         - used in custom-packages that has version appended
        'res/drawable-mdpi-v3/ic_launcher_auto_messaging.png',  // 48x48 pixels         - used in custom-packages that has version appended
        'res/drawable-mdpi-v4/ic_launcher_auto_messaging.png',  // 48x48 pixels         - used in custom-packages that has version appended
        'res/drawable-mdpi-v5/ic_launcher_auto_messaging.png',  // 48x48 pixels         - used in custom-packages that has version appended

        'res/drawable-ldpi/ic_launcher.png',            // 36x36 pixels (not constant..)  - used in most packages
        'res/drawable-ldpi/icon.png',                   // alternative name: 36x36 pixels (not constant..)  - used in most packages
        'res/drawable-ldpi/app_icon.png',               // alternative name: 36x36 pixels (not constant..)  - used in most packages

        'res/drawable/ic_launcher.png',                  // 72x72 pixels || any (not constant..) - used in very old structure packages
        'res/drawable/icon.png',                         // 72x72 pixels || any (not constant..) - used in very old structure packages
        'res/drawable/app_icon.png'                      // 72x72 pixels || any (not constant..) - used in very old structure packages


      ];


      $images_data = [];

      foreach ($icon_paths as $icon_path) {
        $path = 'zip://' . $apk_file_path . '#' . $icon_path;

        $img = @file_get_contents($path);
        $isValid = !!$img;

        if ($isValid) {
          $img = self::getImageData($img);
          $img['path'] = $icon_path;
          array_push($images_data, $img);
        }
      }

      if (count($images_data) === 0) {
        $img = @file_get_contents('./assets/default.png'); //use default image
        $img = self::getImageData($img);
        $img['path'] = "*external_default*";
        array_push($images_data, $img);
      }

      //---------------------------------------------------------------- dump image to file,
      foreach ($images_data as &$image_data) { //
        if (true === $is_dump_images_to_files) {
          $image_filename = $apk_file_path . '_' . $image_data['width'] . 'x' . $image_data['height'] . '.' . $image_data['mime_type_ext'];
          $image_data['path_filename'] = json_decode(json_encode(pathinfo($image_filename), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), true);
          self::base64image_to_file($image_data['image'], $image_filename, false);
        }
        else {
          $image_data['path_filename'] = "";
        }
      }
      $image_filename = $apk_file_path . '.' . $image_data['mime_type_ext']; //also dump "main"/"best" image to a generic name..
      self::base64image_to_file($images_data[0]['image'], $image_filename, false);

      //----------------------------------------------------------------


      return $images_data;
    }


    /**
     * try to fetch logos from some of the most common places in the APK package (read content w/o extracting).
     *
     * @param binary $binary_image - a binary content of an image.
     * @param bool   $with_base64  - keep text of image in the result.
     *
     * @return array
     */
    public static
    function getImageData($binary_image, $with_base64 = false) {
      $img_metadata = @getimagesizefromstring($binary_image);
      $image_data = [
        'width'               => isset($img_metadata[0]) ? $img_metadata[0] : 0,
        'height'              => isset($img_metadata[1]) ? $img_metadata[1] : 0,
        'channels'            => isset($img_metadata[2]) ? ($img_metadata[2] === 3 ? 'RGB' : 'CMYK') : 'RGB',
        'bits_for_each_color' => isset($img_metadata['bits']) ? $img_metadata['bits'] : 0,
        'mime_type'           => isset($img_metadata['mime']) ? $img_metadata['mime'] : 'image/png'
      ];

      $mime_type_parts = explode('/', $image_data['mime_type']);
      $image_data['mime_type_ext'] = isset($mime_type_parts[1]) ? $mime_type_parts[1] : 'png';

      $image_data['base64_prefix'] = "data:" . $image_data['mime_type'] . ";base64,";

      //debug:
//      if(true === $with_base64) {
      $image_data['image'] = base64_encode($binary_image);
      $image_data['base64_with_prefix'] = $image_data['base64_prefix'] . $image_data['image'];
//      }

      //unset($image_data['image']);

      return $image_data;
    }

    public static
    function base64image_to_file($base64image, $filename_path, $force_overwrite = false) {
      if (true === @file_exists($filename_path) && false === $force_overwrite) return;

      $filename = @fopen($filename_path, 'w');
      fwrite($filename, base64_decode($base64image));
      fclose($filename);

//      file_put_contents()
    }


//    function dump_images to_image_files($apk_file_path, $images_data){
//    spri
//
//    //save all images in format: [apk name].apk_96x96.png
//  foreach ($images_data as $image_data) {
//  $image_filename = $apk_file_path . '_' . $image_data['width'] . 'x' . $image_data['height'] . '.' . $image_data['mime_type_ext'];
//
//  if (!@file_exists($image_filename))
//  @file_put_contents($image_filename, base64_decode($image_data['image']));
//  }

//  //also, save the last item on the list in the format [apk name].apk.png (generic..)
//  $image_filename = $apk_file_path . '.' . $image_data['mime_type_ext'];
//  if (!@file_exists($image_filename))
//    @file_put_contents($image_filename, base64_decode($image_data['image']));
//  }


  }

?>
