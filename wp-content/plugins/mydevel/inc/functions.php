<?php
/**
 * Print message
 * @param  [type] $arg [description]
 * @return [type]      [description]
 */
function dpm($arg){
  // print "<pre>";
  // print_r($arg);
  // print "<pre>";

  ob_start();
  //WP_MyDevel::addMessage($arg);
  //krumo($arg);
  //d($arg);
  Kint::dump($arg);
  $message = ob_get_clean();
  //return @Kint::dump($arg);
  //$ff = @Kint::dump($arg);

  MDCommon::addMessage($message);
}

/**
 * Print message and exit
 * @param  [type] $arg [description]
 * @return [type]      [description]
 */
function dpe($arg){
  dpm($arg);
  exit();
}

/**
 * Printing object at the javascript console
 * @param  [type] $object [description]
 * @param  string $label  [description]
 */
function devel_print_log( $object = null, $label = 'Debug' ){
  $message = json_encode($object, JSON_PRETTY_PRINT);
  print '<script>console.log("'.$label.'", '.$message.');</script>';
}
function dpl( $object = null, $label = 'Debug' ){
  devel_print_log( $object , $label );
}

// function dd($data, $label = NULL) {
//   $out = ($label ? $label . ': ' : '') . print_r($data, TRUE) . "\n\n";
//   // The temp directory does vary across multiple simpletest instances.
//   // $file = '/tmp/dd.txt';
//   $uploads = wp_upload_dir();
//   $file = $uploads['basedir'] . '/dd.txt';
//   if (file_put_contents($file, $out, FILE_APPEND) === FALSE) {
//     return FALSE;
//   }
// }