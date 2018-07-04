<?php

  function minify_js($content) {

    include_once('JavascriptPacker.php');
    $content = new JavascriptPacker($content);
    $content = $content->pack();

    str_replace('\n', '', $content);
    str_replace('\"use strict\"', '', $content);
    str_replace('\'use strict\'', '', $content);

    return $content;
  }


  function minify_html($content) {
    $content = preg_replace(
      [
        '/ {2,}/',
        '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s',
        '/\>[^\S ]+/s',
        '/[^\S ]+\</s',
        '/(\s)+/s'
      ],
      [
        ' ',
        '',
        '>',
        '<',
        '\\1'
      ],
      $content
    );

    $point_start = mb_strpos($content, '<script>') + 8;
    $point_end = mb_strpos($content, '</script', $point_start);

    $html_before = mb_substr($content, 0, $point_start);
    $javascript = mb_substr($content, $point_start, $point_end - $point_start);
    $html_after = mb_substr($content, $point_end);

    $content = $html_before . minify_js($javascript) . $html_after;
//    $content = minify_js($javascript);

//    $content = mb_substr($content, $point_start, $point_end - $point_start);
//    //minify js
//    pointStart = html.indexOf('>', html.indexOf('<script')) + 1;
//    pointEnd = html.indexOf('</script', pointStart);
//    if (pointStart !== -1 && pointEnd !== -1) {
//      html = html.substring(0, pointStart) + core.minifyJs(html.substring(pointStart, pointEnd)) + html.substring(pointEnd);
//    }
//    //-------------
//
//    //minify css
//    pointStart = html.indexOf('>', html.indexOf('<style')) + 1;
//    pointEnd = html.indexOf('</style', pointStart);
//    if (pointStart !== -1 && pointEnd !== -1) {
//      html = html.substring(0, pointStart) + core.minifyCss(html.substring(pointStart, pointEnd)) + html.substring(pointEnd);
//    }
//    //-------------
//

    return $content;
  }

