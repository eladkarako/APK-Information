1st Thing You Got To Know Is That This Project Is A Part Of A 1-Man 1-Day Hackaton I'm Putting MySelf Through Every Month, This Month I've Started At Nov. 14 - Nov 15. (Saturday Fun), Listening An Endless Loop Of This Track:
![Tron Track](http://icompile.eladkarako.com/wp-content/uploads/2014/11/tron_track.png "Daft Punk - Tron Legacy Reconfigured Remix")

#A PHP APK Parser: 
- Parse **AndroidManifest.xml**
  - Output Plain-Text XML.
    <small>**Easily Parse** It With Any Of **_PHP's Simple XML / DOM-Parsers_**.</small>
  - Get Specific Information From Package.
    - **Package Version** (Human Readable Format).
    - Package Version (Code Format).
    - Package Unified/Fully Qualified **Name**.
    - SDK Minimal-Support Version.
    - SDK Target Version.
    - Application's **Meta-Data**.
    - Package **Permissions**.
    - All Of The **Hardware-Features** Might Be Used Used And If They Are **Required**.
  - Documention Enabled Easily Adding More Query-Like Methods, Quite Easily.
- All **HD Images** Used For Package **Icons**.
 - **Base64** Image
 - Image **Meta-Data**
    - Width
    - Height
    - Color-Channels (RGB, CMYK)
    - Bits For Each Color
    - Mime-Type
    - Base64 ISO Mime-Type Prefix (__to make things easier__)
- APK Information
 -  **Time** Of Creation, Last Access, Last Modification.
 -  File **Size**.
 -  ZIP **Compression Rate**.

- A JSON Format Output, That Can Be Written To Prevent Future Re-Parsing and I/O.

####ApkParser.php Is Modified From [polaris1119](https://github.com/polaris1119/myutility/blob/master/php/apk_parser.php)'s


<br/>
************************************
<small>As an example for an APK,<br/>
I'll be using [Steve Pomeroy(xxv)](https://github.com/xxv)'s "24h Analog Clock", downloaded from the [F-Droid Repository](https://f-droid.org/repository/browse/?fdcategory=System&fdid=info.staticfree.android.twentyfourhour) <small>(GPLv3)</small></small>
************************************
<br/>

###Basic Usage
```php5
<?php
  date_default_timezone_set("Asia/Jerusalem");
  setlocale(LC_ALL, 'en_US.UTF-8');
  mb_internal_encoding('UTF-8');
  header('Content-Type: text/plain; charset=utf-8');

  require_once("./ApkInfo.php");

  //entire folder (but just right files):
  $files = files_in('./resources', '/.(apk|zip|tar|gzip)$/');


  //--------------------------------------------------------------------------------------------------------- single file example
//  print_r(
//    getApkFileInfo('./resources/info.staticfree.android.twentyfourhour_8.apk')
//  );


  //--------------------------------------------------------------------------------------------------------- whole folder example
//  foreach ($files as $file)
//    print_r(
//      getApkFileInfo('./resources/' . $file)
//    );


  //--------------------------------------------------------------------------------------------------------- save I/O example

  foreach ($files as $file) {
    $json_filename = './resources/' . $file . '.json';
    $data = '';

    if (@file_exists($json_filename)) {
      echo "**>  FROM FILE :)  <**\n";
      $data = @file_get_contents($json_filename);
    }
    else {
      echo "**>  LOOKALIVE :)  <**\n";
      $data = getApkFileInfo('./resources/' . $file);
      $data = toJSON($data);
      @file_put_contents($json_filename, $data); //cache.
    }

    $data = json_decode($data);

    print_r($data);
  }
```
###The Above Code Will Cache The Results As A JSON File <small>(same name with suffix of .json)</small>
```javascript
{
    "dirname": ".\/resources",
    "basename": "info.staticfree.android.twentyfourhour_8.apk",
    "extension": "apk",
    "filename": "info.staticfree.android.twentyfourhour_8",
    "basename_escape": "info.staticfree.android.twentyfourhour_8.apk",
    "size": 270282,
    "size_human_readable": "263.95kB",
    "datetime_file_access": 1416069701,
    "datetime_file_creation": 1416069701,
    "datetime_file_modification": 1416069703,
    "datetime_file_access_human_readable": "was 1 hour ago",
    "datetime_file_creation_human_readable": "was 1 hour ago",
    "datetime_file_modification_human_readable": "was 1 hour ago",
    "name": "info.staticfree.android.twentyfourhour",
    "version": "0.4.2",
    "vercode": 8,
    "sdk_minimum_version": 7,
    "sdk_target_version": 17,
    "application_meta_data": [],
    "permissions": {
        "android.permission.ACCESS_COARSE_LOCATION": "Allows an app to access approximate location derived from network location sources such as cell towers and Wi-Fi.",
        "android.permission.ACCESS_FINE_LOCATION": "Allows an app to access precise location from location sources such as GPS, cell towers, and Wi-Fi."
    },
    "hardware_features": [
        {
            "name": "android.hardware.location",
            "is_required": "false"
        },
        {
            "name": "android.hardware.location.gps",
            "is_required": "false"
        },
        {
            "name": "android.hardware.location.network",
            "is_required": "false"
        },
        {
            "name": "android.hardware.touchscreen",
            "is_required": "false"
        }
    ],
    "images": [
        {
            "width": 36,
            "height": 36,
            "channels": "RGB",
            "bits_for_each_color": 8,
            "mime_type": "image\/png",
            "base64_prefix": "data:image\/png;base64,",
            "base64_with_prefix": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAQAAABLCVATAAAEyklEQVRIx42Wa2wUVRiGn3NmZtvdBkprqxRbgQoBkbsNYJGoFaMixkQi8VJCiim\/JP5QpN7+GEExJgaSGmO8xkRRoiKXxHuNkBgBI5KgiD9ASsulSwttd9vuzjmfP3Z2drcXdCaZ5Jz5vve873fOvN8oYezrzlqZrKuZRL\/tkNP2RFv\/2LFqdKA7Gq5ZfWYFNflzkir7eWCXv6Pt3P8EWr5Sv5K8MUw3yoJ1tM7C1bx3\/vm2+H8A1U8rfz++FECna9WjepH2AAEu8ZH5xiYcrUF6Y1vatl4B6Obl6U8oB89\/XK\/UChWAZJ6CMMhz6aMaB2o+u9TYNjgq0NLmZCseZhUbHJV5masPEoBZelmfikdgwpHuu36\/MAJoyYrkbnHc9HZ3rlIBiEJCVhLeFqHRdGpU+S+JZYfTmfyghHUzL+\/wHSe9y5ur8rnoPGbZOQV86My3hq7FE98lP1Ip9ws7zpjt7vhgdchUyKCwED5zkK85MWP4u3HB2jyg+c3xmYYmNVtlAwUfUDgoXBTgAiYAyZRjr1OSNvS9XOcFQLcXX95sGOc361w1FE6BqEzFXMDmbcBa14ipSj8RAMWbTIW1LQ6hKIMUyMjyEMCS2+cHVblv6dsUALmNhgm2QUnIxwl5ZdVLCOYU7OIq15CqmLUEdH303GJLnSJc0w8PoRRAZNOHMGHEGuX4FrcJdLzBOlbWODaPjxTIMNjwDHXzIk+FIxAqMQzcDe7QHEvEzHSza\/u4qLwPQ4Uy2tmGzwaqEXx0AFWp\/6G7Ctziaf14kiugxgbHUDjENrYwBTjGG4xjI2UBD40NPpgZHARPRdziay2xcCcsGo1FoVDU8SZPc4DrqWczJXmCfVTAs1ZZYGqVm\/YsJmCgUAgWxSAJkiT4kU6KuUiS45QQJUYMDwniJFAAQ+Imz1sSQTUyTA5yGYMCDhNhHQo4zjPcRy0W0NzAxJDbGbEK6exwk+2WJAQgYCkKt\/emsOQzmM4+vuIhxmMpC6SBcFos4otx5agw6B5hXlinSjoCoXl+DdxLgo8pYR1eaCdwRISyAdDxXda3aqefeWURyikd5kAZGZYoTSyjlX1BpCB0isUcAi3Jym5hv82alkUzBwcbAltsuNlCPR9QxGMcR4C9dsATkttAQ\/9bQrv3g+Qc0GEeXl6yhAwmcR2WW2llAEF4xxdKBnr3gBJUpKRnKDY59X1EQjdSpDlGV0GVSqilIjw\/AHtsi4bo273NgWePfzW5EZ6163TuhBtcknTRA7i4lFGBCT+MTMRt6YteLNFXIYOh+Vf3nJ3gmJ16rso1oIzVagwOgsHJMzWAR1K\/RSD2Qt9LeV0kdsvQdxTF0t96Vxf0MkaMsnMt\/m4Xrjp1YWpBF0keKH5STMJrSP9qpcB9cqNchzOsT33pCpO6umYVdBGARGt0K\/6g97C87tsRUORZXafck9ofger4xYUyMGrLjjaZ1nQUSlMtzgMOw8QAXGJT6idXNFSdOjtPesf8iYgsrN57ciIKivxFdrWzQJcrByHJefnc\/9q2R0SBGox+mljL8JWG38Xrq7ryJ7y0NrmRSpX+xZSRWaMACULk\/tIT5T15Zi2IGqrpiLVGa0bPUFf69YtOltl6uq6WPnvS\/Jn+QxJjx\/4LnTax3bHVMIsAAAAASUVORK5CYII=",
            "path": "res\/drawable-ldpi\/ic_launcher.png"
        },
        {
            "width": 48,
            "height": 48,
            "channels": "RGB",
            "bits_for_each_color": 8,
            "mime_type": "image\/png",
            "base64_prefix": "data:image\/png;base64,",
            "base64_with_prefix": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAQAAAD9CzEMAAAHFUlEQVRYw6WYe4xU1R3HP+feOzM7+2D2DXZZXGHZulAhhg1WIrWUSAhFSoGtTbW2QemblNBowKSvpKbZhr4baZtSq9XamNo0BJQa2yalGkAbE6UaRYHlzT5kX\/PYmXvOr3\/cO3fundmFbZ1J5p6c8zvf7\/k9z++OEmb62VA9Ps\/6gBnT\/f8cnPEm1LUIVtU6m5o3ZpZeaaGuOCf5usHU6fcO5f\/4j3feB4FSH7uv9e5zK1R8+u31bxUOjvW9OPB\/EKxaX903sig8Y4ylbTTGUpZSpXl7vPbnzsP7M\/8Dwe03tfzi3EcCkUKnWmvdZjV45gFcjsoh\/Sp5S1meTPKS+vbffz1Dgtvvyu\/TNQBi5pv77eX+cRVFWeNTTdCnXzYS82bnPXF66yuFaxKseXhol7JApMX9bqwbBShAUIEGEtAImp36DbABWo6Obyj3R4RAqfXPnPskgHK\/ZN+lQFEydkkDCWg8knfY6WYcgPqz2dVHTkxLsPpng9sBYu5e54P+yfF1kECTqAbF72Z9xVIKGt7WPf8aL2FapeHybZe3GwyN7kGnK3RuVfEMm6wo9Sd7mWhjGO6a9Uw4xgINlq3MvEAcku5zTiIC6I09DTRWoIX4pjLBaJt52wJo\/unhHWUEyllxYqgDHPdRZ2HgWBXygUsMMFgIgosdAgfBIMA6PWEDUr3y1RcjJlrylYEOg9FftztDanvnLKBQxADlBQsKBwXk\/SOI7xXh9\/asgsGomp9EfLC0pvAtjWaRbFaExD0d4sGIiFbeigTuB0Uj6xxtNBd6lm4OEciubJOA3uWU3ClAwddBBSEa9oy3ogNpb+aLKiWCkNjjZbn38wWN5lYWhqJcgW+WEknJdASut4mmqnCvbbTmUsfiO3yCG3uGWg3o++yweUpJRSgwVQRKykbes5fZYjDU3uMTVH3OYEjJhyJibmBfInauhBR0mexc22AYWusTFNYLwoet6EZ7ytMJlVoO83xQp7yZTym0kG5eeDNYC1JDHQZj1ljlG6VsJAgmqKTeyll2sI3qMoPdSqMxGMwnwNELDJDU3VbJ4oJLbAr3Ron\/wx4y7KAnlHpFGssyQPJGcEyXAA5NkXJm+8Youve3NLApRPESP6aGB+gKIO2Iy+NKALsNLHODwVATMoGUmccgbGE\/q3kdg+YAd\/IUP+I3AXzlrk5lMOTawGGeAPWKoEIKBeIh8ygMNezjFFs5R5yN\/I5UCM6DzOOEfNXoRWIDOK5tADeSLQ4mKAmGHBnSDPIUOcAwxAnqqCZJNQlfb4iF4AXt3RYKHHdAgBHARHL1AmnSZMiggb\/yGivYjUJzgK+xhW6\/0iSpJkkbVZGYG\/PGk+CoMwZIRwqcAP2MhurqLazx6W02sJrHOMC9tGAYZwxhbtAOeDQDYhTEhsApnBQgK0M0hYLRMJuRUFlLBbYGqOHLXOBRGrmbBNBIgnwoNWFABMieBcs9rkTIxN6I3FI2rZH7KpxuXsxcx4Ms44e8gDAbg+1HnJeIE0YQMu+CNXGpaVQw6pCWSMZW0RxESRE0mtuGpTzIMH2cj4AL\/2bAFoTcfrAgd0QQjupS1AswyaLgQplKA4\/aopc9PMJnuRjoCweNtoRUOn0YLMjsE4R++3gotQwx4iyuAI1qIEAHN7GPB\/gqu8n5a0eMIKi3JAMWTBxqzQja\/lWhvKw1sTDk3Kk0uI4FgKGbp1nhrz8r\/ZYgpPcGXUX90+lecPQBuzMAVCgKxBjldSobZwEs2ukM+otSNV3n9jvQPnZqjmT9K3N0d0MGXLuvIKEG12Aj1HEL11MVuhG8lQ5WsgDBClUwgCel3wa43CfZUF9U+2TuM2CZH7DRktA5FVAgjjDOIFkcHGxitGIDeWJ+5BQ\/l9mSH45D++Dp+TIRbryabzjZXwdVhT\/HukK1v9QiutgoNDYG8ePeVJiuN388Dkj889nHI42XDF3cnihALrZ1cqLimpSgoysGohVEfdgv33CPxwGSx4rwoeY3+5g8LiJcTGyeHJnm4jSRbI+2AMI33YO2ILSfT985ZfuuqmpeySwGmJV\/ItatwtVHyp6UrWi25o\/FAdqvnPmovDZl+y659G2pkwBj8U36D2aqq5+KkwO8KWt8+LljF3rD8BWvUKo++Vx2uUc7f\/L78ZvV1U8OaR4qPG+Lt+PS2Y35o9d4R1PxxPeqt1+p8lB68vfHVlnTgZ+Sfe5flOt4k6l3R++Q0zN6jXU+PueX59uKF1zc7dFrnSVWu6pFgDyX5U35m3uY94JX9DljI3tz35HcFGAy5Ze6xM7rB8qnlU7mHbd8timTfIlOmQ5Jpv3SXPVQ25m67FVE3I6BmmdZIlcRucafIcqhq+rTVfekZo3HxxOFGAostzZfOymZkWOFR9yXZfR9\/tvii9VQTz0N8dlmxB1mlBFGZEZb\/wv+djRy8jtCngAAAABJRU5ErkJggg==",
            "path": "res\/drawable-mdpi\/ic_launcher.png"
        },
        {
            "width": 72,
            "height": 72,
            "channels": "RGB",
            "bits_for_each_color": 8,
            "mime_type": "image\/png",
            "base64_prefix": "data:image\/png;base64,",
            "base64_with_prefix": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEgAAABICAQAAAD\/5HvMAAAMcUlEQVRo3s1aaZAV1RX+bne\/ffaFmWEchmUw4oJsKqO4gAYxEkEE0WgMScoKMcaQxChaIUUSQaOioWJwqaISNUSjuCESJCKogAi4YCxR9n0YZoYZZuYt06\/7nvzo2\/1u9+s3CyFV6Vevqvv2Xb57znfPOffcZoT\/r0s71YZXjmR1WnW4f6RKqaAKVkRt7DhrTDbEjxqHac\/aT061X9ZXCU0IKJNKp9HV8aqOnHViyG9ka1pe4aveTf8PAU2IqDeWT41PaC3obYvizti6ptfMf7ybOO2ArmDa7NJ5jVVGn1WgoKqxZYHx5\/X8NAIaP7Vs4ZFh\/u+IM65CgwETnCkKmF+tql0n7l+3\/LQAunxs\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\/IfFkpmYhygW4UkkHDgEQonw+NxjUlpgB\/uajhSgIBVuswriaM7aSQEBAP1c9sdt4ijLHUfxE\/wMJ7AAK2A4cioGHFnIXq8RutjHRjqQABQkOxuEhNRjWcEDQQEhhAKPmSRfB8yd+ffHffgWtmEhdoj8fBm4WPLuHg6jTUSNqUakAIXMzm2iO3Uj2b4cLu9NqMySBwmLY5dyjwvhqMevUYvn8TiaMcBhm1dl28kULv3kNtIBBYivzhNC2240+8Q4BKBasiw9qYwcD6bhVtwDFYuwGAnHfciUaMRngkERJFaLeKjry4J2q3C3sk+aYaZpB1QMd4L43AEcdzkNq88C3InfYxcmYanUp11rB1qEMAo7Up\/bMXVDu8hkGepGnu2TOWIgFKJOmr0sB3+VZQbth4lYge\/ieUzB5x55biJd5LMTO3DEDmETHX8PioofmQ1Z3doKrEVpVnm2ytxgCBEMA4HhB3gLdZiNH0o27QDeE\/LR0L6MOp1dR+rDyrj1Yqu6xadTO3Q4FzGfPYebytzVLobR0By\/\/iheQkBS+wY0CEBVyeQH8s517\/GtggXKX\/TWrOjZ6qITQVyIahd1Mz\/uCsasNgUYgxDiUh9VWCICWqAFrxj2Cmv5HHskQBSP\/6FCtzr+d2Ad+eVSOaLgUDAMIxDohcoYajEaKjgirtqZ65\/0tbBA\/dKdD1K7a29vfBYXZ4DEntGPeRq7dxKlGIuyHlRWgrEY4gqFuUfRB7HMJDF6akf6Y0+ygY61\/a5\/woKxM7SKsjfC5CgwgSBG4FIMQ6nYw8sqC2EgxmIkIkg67dwr0LreoAOa1bIy1TqPDmflGFlpbFnqajt3vyQwLiv77HeoYKIZHeAwwRFBDHnSYZW8H\/Fe7+LedFxkTKMbOm6g4z5JTzburLd2iuPLSv2FYE2Ok4zMlUAsKw5IuCD5X3vwI\/1IUGR7O3dex9f556k\/PvBwqZjQseA9eluO\/WuGxhFnpSVEDsPKA3jXnPtqxu\/SNpxSOvQUbcmROKdk8qXENrtga3CRkew2lcAl\/oRcBiC3nSIk8Zi5xcnfd32ZWErxnJl82hWfU37Q7vgF9U+m4ZPJ6G3+Az5J0xQW89cUe4Tqo+2z6atuD1+YwiYNenafs6qvTD8SyM95LkY538C33kn8Jr3Gkc6Qtj3fp5VkdHvWQZzWH5gz0BHi2sBNXQd7zP5QN3Ay7\/Zglp6BMyC5\/5e0howeDl8ASpirjswbZO+PsTN0g76JequyXGCA9XSb\/lXQyXnoxx423qBEL08UWak2Y8BD+wod1OZkPjdQ1q3K0M2bY1hsvMlMJ9kzuOPg\/emXMranF2eurIBNGvjHfVWZknB6FputRfsMJoGnzed4UjoXG9y4fw5\/m1r7eCrNwris+Amzrl3a6xTpP1Unq0W9oK91teJVvtQ4EZQ\/U4nsbb4T6yh1Sh8SsDGR+RUT97tO\/QLGcH6tep2a3y2YVqwyV5vbFd31fcMAo+n95FxsIzrlLxtYjXp5yQNGTauH\/AqvS1+s1CgDWa1SKT5CSuM4DvD9tJ82m7sD3NOigEcOt8w31tLBHr61oB5+UDEq8GDt0RjPVYXxsF7cFdZZzhoxGtQQWIwLEKCex6Ne\/BDFuOBTtc1lhD7\/imlwS+gZjEN+7xr0+oMmlodzgmNDd5SVtxS1s960iFHZybam5F\/19fjCjgdP8ydfTEU1hmoXBG\/pV85LO7STPtGOgjwUmYGWhib9b8ZW7MVh6tNnX6zvX+kxFaWoQak6UB2lDAuUhUJaKBBIpw29K5Vupr3mh8YhHMdRNFHfP8g6FUBS4yDCiCKMEILQ0YUUEtBz25jeXP8BaGCq1w1ESsQAAAAASUVORK5CYII=",
            "path": "res\/drawable-hdpi\/ic_launcher.png"
        },
        {
            "width": 96,
            "height": 96,
            "channels": "RGB",
            "bits_for_each_color": 8,
            "mime_type": "image\/png",
            "base64_prefix": "data:image\/png;base64,",
            "base64_with_prefix": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABgCAQAAABIkb+zAAAST0lEQVR42tWceZRcVZ3HP\/fVq627Or2mk3R3VgiQQAyEBIgkMSQKZIBR0eiM4OC+MC44zigII4iicJAZPCMDqOhRQCSyyAFGUYkBgxEQISHkhGBI0ll736q6a3nv\/eaPqnp7VXdCozMvJ91d99377u97f\/vv3ldK+P996W\/GQ9ck1Ww1S81S7TTQbe2X\/bKPAxuNN2MuNVkcWDMn8v66pcnpMr0wNV9vYFEo3dGIoKNbsf5ol9GVOZh90nx44\/D\/IQBrl9atT12QWZhR2Qn01qilJp\/Y0v\/o2P0bD\/xNAazRtXc0vEc\/b6Rj5BhGa9RbddsHHxvb8OTWvwGANfHIl9qvGGgaeMMq2EhiZ9c1v33wrwhgtYp9vPmr\/e2ZSVPEehqfPfTFjc\/8VQCcc1Hrjb0Lx6r2EUskInFJqAh5shSwlNKUqjamQeofP\/xvm3a+qQDedmbbt\/tWVJJ4MdqYHZnGcWoGDaRIlmx0llHS9NPJa9Iju62M0iLhT2guqLuHvvJ015sCYGVN3V2R9\/VoIYSLbnSoVZHT1VwSeJfZ\/3Shn9f4nfWy1a0ikTCdaEt3X7\/p5kkHsHJ222NHTgkKTqJwsnZG5AxmIlSSEAn8JQgvsUm2WMOWRP39m4j8dOjDm\/OTCGDFqqYHDk8NDDXepp2nnUbUQ7wKXflyixuM0MszPGruwy9SMdqfP3jRM12TBGDNp\/Tv9MV85JiL1WXaEnARrwIrrmxS\/VAcOCM8wT1mWimfcHYc7H3XM396wwCWa\/XfzX7a5\/fNk3hf5BwP0c66V9MBCXCi+Lubh3jARPMObh0d+fgzP31DAM5qaHpo8Byv5Lca79TPZWqA\/DDiVagOiA9O8fOfeVieM0yPRrRI7qanrzpmAGfGW7f0nOYhXxZYl0WWlwhTFYhXFXVAAp\/ckIZ5gA1m3qMPzRS+8\/srjhHA+RsG1rvDM8taoT6h5vrIl1BBoqIgiU8vnM8C\/IabLDSvTcp+bMtdxwBg9VezX3MHC5q5XrtENXrIJ4QTlbXAbUSDYMr\/X+Rb0ituEK259NpnnzlKAMvfHXlg0PWQ2sJl0YtJlMhzr7ubE17iq4tR8bfl0w1BeI3bZZtpuVKttq6Bpc8fOAoAS09J\/aG7ziWJxmX63xMLSHoYJ9xwvMT7eRFGfPGvPfyAZw3DBWHe1iNn\/SkbHpYHrtObpz3qJj9qXGqTL56VdqtruUWFBhBi38N+ThgHi9dcPsRJegkhAJ2Lm++plFf4riV6w8P75ljY\/6z3l8gXW3CUjwzlaSl+Lv5L27AcgGHEO8tSbDueT1KrLClTkWf4PUuvnRAA9bn0ykLJKgiW9Q71XuIuUr3SrDyiI8Cg57PbrFuMVCBehWjPKVyp0jYdQj\/11yxZMC6A01LNVw24hi3jH1RLRbOoAp8VdR6zmrB5IShqfZojvhDQ6ytWcJXSTYeW\/XrTzeMCSF2zv8UZMstYry1wrXMYs\/GtabSCDig0NJe4yQTU\/mIuiCQKDj1Df3fqyqoAFk83P+eIT23hvfqKEFUMrnxvoM3RAjw64qh4n2shxBcAOhy8iClRscoUDaipt1QF0HhjV9KWflboZ5XWTAIRpVdwWnz2SIWYTr99avHFqio0ej2eD1HrEumuZae9pyKAt5w0eqnTtdZYrWYG1l253L6\/JcwzOHwQj+Z4R6lQb1FsPZeVGrYmpGm4aZFWAUDDLT0Re\/1lXWRpyLqHT6lCPYOXVypE7ZXvyX5LJ4DOeYxozsL2HMfloQBOOTu3zunWaK5QqSq+FEbJBkj12ni3DlRyWUVtsKrGUKfzMaVcPJj21ZMTIQASl\/cqG4B1ob6kolwWrySJEJHwEisBPx1UV6EZNU7ScjaGiwe9U9U7AwAWqprzHfszx1pmk+eXdxWaJIYLkpcL1dW1cgAoLGa9ShYcl1b7gQAAa3Vfk41RVurzK07gJlWF9gnPzoLyHbYIwdi1OOp0YrrDA7V6QcQHoPbSIft2jXkiLRWIr6zSKtRwhiWblZ+sKrq1JcxTluMPphjrfAD0cx1889TsKsSPYFZlf9COTJT4UfIV+9SynIQ4itz4Ac8OzbwlyQ5n0JLInCpymcIat\/rgDjGkCvHFPlJa8Rh6lTziZPoiNQ5313g4EL900F7\/qHGCR4GDcqmNK7sSWsYK9vHmylpVoT2OdYjNg5Fp897qAtC0zrQfOVd1jMPs8VvEl6xLlVEC5PgBb+exqsuS4kQStjcYIX6JLUJzpk070em8SJs+IRERXzwqLoEILyaGQ87wXR6hn9bSnppUXJbZpLTRUkueurU2AHPRiJ0ZJwpzok0TsM1lW6Fckq6qVqVxZ4ilq59b+CUZZvNZ3osWqFh4R7UzpDnP1aeriJigg5wwZg+r9fjF4OppWKH5k58X\/jqRBFKW\/dzMkxRYyKdZ6yl1qZBCDAjTWMpmkdLt0SmJBvpAh9hsp\/4TVw0VVlmwGKaxguF0W5Mw0r0\/t3MLW4AlfJFTfYI1WJpDQjaiZqGZZqlWMapSx9EHGsRnOCqcIgVYoYqn0VBFXUGw+AlDnrviIq\/49ybWczHPsopfcg+nIiWzbOckVexaC5pNaR5tPoCmtNh0x4klVe2EbI0VKBhawEFu4K18HaPUIh6ZtniQdXyMXVzIU9zB7BB7ZFW1WVOI2AHnGPqsohmNRl1Ze6MWr1qOkhKp3h7lElU7\/00DP+JsfuUjLcv3Wc2X6eUSNnNLKVSRir4ifK4UTuXXIN5WtEJRGt2JXiKQMHrlO6iuyuV1z2E1N3Mfl3MSd9BWqg3dxoP0M43P8KlS0i8BDlbmuyOqcXRXJTHSqpSIjm41OY0NKuKLBIMlWr+6ii8j\/hKf4Z\/ZwloEizgRRpnD53k\/Wkneq+8YSAUzLCQ8pVC9BYXokaSVsieXpIqGbNcVpXOIRk8BXULqQsW\/a\/gR+1hHAcgxg5s4D7BCqqGEGIIh6kNr2YFotwmdvG5GNbsibxGpEJKBot4VQlciXpEjQ5pt\/BDDjl+72EENtdQQD4Td4qrcFZ8wxdfi7pP17NOhQCdiZqS+TMWgFFQiQHww43XrgMUYGTJkSJOhwE5+SScRFrCfLEmy3EAHH6UeQSNJDUlqSVJr7yp7V1lVSaVyrnZzkAjoWIzYABjAhFBv6\/2UJ81oiewxWwuEF3iSLuIs4d3UldZulB+wl69zCpegky4VfMulxxqSJVA1vjxCAoKUJSZ5m5BCPwI6Ocu1CTkoY6reR7jlUeaXSZMpJTV+Jf8dj1HDct5J3GUCk3yWA9zNVnZxLqvc251kyLjGT2dRBfskJQHSxQGf68UEnUKuxxmQkVyIrXGkXjHCsK8q6nBnOQXWoodYkXau5Pf8mkfYzD\/R4QvtxM67pGr4l8MSsTfDsz1YoJHP9jieOGNlQ7E7UtgY4nzKEpvkvJBDeOW7K7mWUxniVv7LJUZu49kQsg3onmuQvJ3SRDG6MUEjP9at2wCGJR0aLFgIBQbBFWqF1RDEl8J4lVHng1xDB\/v4Bhtso+pkxfWlQrF\/9nKfYXJ2fShO4ZAIaJjGESfXHGEg4NLLzkenHmgqBVRWaG41XqppUcvn+CS1PM\/1vOAJ5OrRgCbf\/E6wN8QAhm3nayR7GEATwzgQdyqP2mBVvyjoTA\/Z4a0GBVdoV85vr+YdWPyMb9Nr92jzrLyzdE4JcrNrtvgYo6WcOPtaytaWgv6y1Vch5iwPnRlokdAQ2inUWyG8eDtXcyJ93ML3yGKRYEaI0Ljn72LYpdXDXcXIXQOjN7vPmW6r1VchMiy3TXH5Sr8dsXzcqOSQBIjyEb5MK7v5Bo8zK0RovX7gEBnL0YDBpxguVyUG+jY7QfRf5EiAeMu3HidOoNYgVYl35P7zfJAEm\/kET1ctyaTZKVndGZfZWDRlGjCYeaLeNk9j0e2S9SUq7lVOk6eBNp\/QyLhlAMuXrDhgFnIV7yLDv\/CPHPKsvHvEfp63XInXSG63FEoAJJffk3B54xeMgwFL7Dwwhg6cSNTmjldqJ6oDbp51cDWPs4zXuZgrSvsO\/mTmdbpcDxncRo+7Nto98JKyJ9wmB0PY7xjT4s\/FLj8tPh2YSOnLaa\/hBIQUt3EvM\/kDF\/CjwLhBdjBs7x81MfQQR9wAukbub7Y7D8Rek8FQ4t3q2sApVZJPqVKg8vbUWFwiQpjL\/VxNlDt5N3s8o3bxtCskmzI2trXszDUAGclur8840z5U2BcQDIeAcmIyneMrFm2p6AnE8+QYp1PjMRcX8igXkint8Zdnf5UDtgYoBnfRVQ6KypXariMvOv54Z3SzpCvYFctl7Oay0BXR+x2bWwesUMFKsJQpWD5wGl\/hf2h3zb+T35p52wJNJb2BTv8e2YH091tMJ1D\/WeEvoSbQEaIMOYQ2TiupMyXeyASTdSHFMmqAwZBUU\/OM2MqL7t254cwmGfYBkNH8juFdTlB3KLbRSleoEBRZnSwdAWniTKaGuK\/qOqAxnzOIY2HR4No\/Cxu1nV8YhkuBD\/yYw2H7xHv6b2wtODfuM3ZVqOxbPh+Z4FROI3kUOjCV5cxxHXkSz5O9V5Y\/ynYXQ+p6xx5xBMj1Do30qVeHX9FPLafi\/bHHzdZIe6iDksCGdjPL6WY\/Q+Mc+YvQSjv1vvNahNaui9dLPGiKI\/+y\/x46xQw\/K7Fr8JvtYw77N5jbMcYJFcobn6AxnaWcyWwaXA91RkVpYgGrWFiqbvR6Vr4S+Z08JZ0RZ6bYkdxjLguL5y0mGVCvdz1ft6p8vH40dlN+emwxVD3o4RRkFZBifulo0ygGFiYmcWqpJe4rYDVPwARn+T33mQ6NM8zDd7BbPG9DeQ79qVrWnPTT11xnDM4sfDM6a5wND+U7glBbAbKJQczjHse7NnF1fsAeEqdmd++H2SxS8biNZPhL5\/1tLuH4Y\/SHZt+4lWpvq2Wrq5QOmRVblCtusiZA\/jZuzffHHFpm5weu4xWRqie22DX648xu15FF7lWPSHoCe+tlQr3WKOo5LxEPDe0ILQPs5k7jVRfD2mXvBnOb9Fc98ARisrP\/X5tciy7azebTFCa8Y+n1wNGAX2YCxEMn98pGl37WMbJj7C5C3rAJnFqUHnbsu3auq+5R0K\/Lb6poj469hYoFmEP8Qn4mbkuTOjR4JS9LfgIAQHZZf9x\/X4frsF1f7Arjfsn+VYiHTu4w78ByHbCZM3DoSl6RvrBxWujTtubv7t483TVrVv+a3GYOTHiP\/liJh1e5tvDziLhKsceNvf5deVH2hI+tcPhb1bA89R+1b+n2tK4rfDnaXnXveLzzE+P1+QPX5fd7XneZYxy6O3cnz4kcFQBQjZzVeHtsthfCyfnrY4veJOKH+I3caGQ873DMsY78auwGnpOKr\/JWeQFCtbCs7j9TJxzxbNg05T+vn6s1TzLx8Cr3mT\/H8uywzMsffCx7K8+VKs5HCwBUC0uS1zedftBXsT0h\/8XoKqVPGvG9PGTdZQ753iibn957b+En\/FmqvuU7zltMqp6lscvbz99T4x+2vPCF2FsqHkuYKOkwyJPW7cYB32teCdq7995mPsELMs574OO+R6YSLIm8a+5HDzf5317VrAuNj8TmkThG4oU+\/iTfK+yI+e+0wJ7ef2drMHA4BgCgNBapRc3fjM48HLgXNRZb50TOj7RXPToZnGOMnTxhPG3uiUlg4Fyr66XRa9kueyewLhN9l1LN4uSaK2edsTcRJpCaNb+wNnKuPpPUOMQL\/WyXXxubra542Dwt1A8e+HnubrbJEEwiAFBTWKCWNHyhbm5npW9CkLb8Ym2qmqFmqTY1Q0WJAAYwwmE5JIfksHVAXlTpaPjwBB2ZgzvGbmAPr7hzrkkCAKBmc3zkvJb3aTMPa+P3jhkpKyEj2qhmRsbrG6U9N\/D68K2ynVfDQ4ZJAQAqxjyOj66f9nZmHFIWk3HVMsPo2zv0Y2sLu2T\/0Y4+hnfqVYLjmRlf37gu1dKtv7EviGihOXv4yOidxvO8zj45hhU5xm81UElm0aHmJz7U3KG39MePFoZGE\/XpoZ6Rl3P3cIRODkxc6icFAIAq1kimqhNiFzUc19CaqRkmPU6qmCBFrcSHenrSWwsP00U3nRO1N5MOoPQAnSbaaKY+dra+LDojGa+JEzPjRtzSoHiOJGqonJ63culcNmfuyT9l7KCfHo4wJG\/4i0Um7btVVIoGmkiRJEmSelWjNUVaVcLKmN0yJL1kGWOMMdL0MVAtPPsbAfDwJEoMnRgxNEzyFMhRoCDWpE\/G\/wKMd6SHc20n0QAAAABJRU5ErkJggg==",
            "path": "res\/drawable-xhdpi\/ic_launcher.png"
        }
    ]
}
```

## Logic Steps (_What Is Next?_)
- Making a lovely d0wn1oad-webs!te... \*hhha \*cough\* \*.
- The Data Can Be Processed From PHP (`json_decode`) and static-dashboard creation.
- The Data Is Available For All JavaScript Playing, All You Have To Do Is Dump (Using PHP) A List Of JSON Files. 
- *Enjoy*


<br/>
#Enjoy! 😉<sub>[☕](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=7994YX29444PA&lc=US&item_name=Elad%20Karako&item_number=stackoverflow%2dcoffee%2dicon&amount=0%2e50&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted)</sub> &nbsp;
