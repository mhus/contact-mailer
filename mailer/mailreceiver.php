<?php

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET,POST');

// ----
// Check Request

// Allow GET Requests for testing purposes
//if (!isset($_REQUEST['page']) ||$_SERVER['REQUEST_METHOD'] != "POST") {
if (!isset($_REQUEST['page'])) {
    exit('{"msg":"Access denied (1)"}');
} else {
  // Load the configuration file
  $page = $_REQUEST['page'];
  if ( preg_match('=^[^/?*;:{}\\\\]+\.[^/?*;:{}\\\\]+$=', $page) )
    exit('{"msg":"Access denied (2)"}');
  $file = "page_" . $page . ".php";
  if (!file_exists($file))
    exit('{"msg":"Access denied (3)"}');
  
  $config = array();
  include( $file );

  // check defined fields and content, send error message to the customer if not valid
  foreach ($config['fields'] as $f) {
    if (isset($config[$f . '_required'])) {
    	if (!isset($_REQUEST[$f]) || $_REQUEST[$f] == "" || isset($config[$f . '_regex']) && !preg_match($config[$f . '_regex'], $_REQUEST[$f]) ) {
    		exit('{"msg":"ok","text":"' . $config[$f . '_required'] . '"}');
    	}
    }
  }
	
  // ----
  // Send mail notification to the owner of the webpage

  $replyer = null;
  if (isset($config['replyToField']) && isset($_REQUEST[$config['replyToField']])) 
    $replyer = htmlspecialchars( $_REQUEST[$config['replyToField']] );

  $header  = "From: " . $config['from'] . "\n";
  if (isset($replyer))
    $header .= "Reply-To: " . $replyer . "\n";
  $header .= 'Content-type: text/html; charset=UTF-8' . "\n";

  $subject = $config['subject'];
  $mailto = $config['to'];
  
  $msg = "<body><h1>Nachricht von " . $page . "</h1><ul>";
  foreach ($config['fields'] as $f) {
    if (isset($_REQUEST[$f])) {
      $val = $_REQUEST[$f];
      $val = htmlspecialchars( $val );
      $msg .= "<li>" . $f . ": " . $val . "</li>\n";
    }
  }
  $msg .= "</ul></body>";
  
  $subject = preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)]/im", "", $subject );

  mail ($mailto,$subject,$msg,$header);

  // ----
  // Send Confirmation Mail to the user
  
  $sendConfirm = false;
  if (isset($config['confirm']) && isset($_REQUEST['email']) && filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) !== false ) {
  
    $header  = "From: " . $config['from'] . "\n";
    $header .= 'Content-type: text/html; charset=UTF-8' . "\n";
  
    $subject = $config['confirm_subject'];
    $mailto = $_REQUEST['email'];
    
    $mailto = preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)]/im", "", $mailto );
    $subject = preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)]/im", "", $subject );

    $msg = "<body>" . $config['confirm'] . "</body>";
    foreach ($config['fields'] as $f) {
      $val = "";
      if (isset($_REQUEST[$f])) $val = $_REQUEST[$f];
      $val = htmlspecialchars( $val );
      $msg = str_replace( "%" . $f . "%", $val, $msg);
    }
    
    mail ($mailto,$subject,$msg,$header);
    $sendConfirm = true;
  }

  // -----	
  // Send back the return message to the ajax call
  
    exit('{"msg":"ok", "confirm":' . ($sendConfirm ? "true" : "false") 
    	. ( isset($config['success_href']) ? ',"href": "' . $config['success_href'] . '"' : '' ) . "}"
    );

}

?>