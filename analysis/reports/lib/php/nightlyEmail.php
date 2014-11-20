<?php

require_once 'vendor/autoload.php';

// Configuration
$config = Spyc::YAMLLoad(realpath(dirname(__FILE__)) . '/../../../config/config.yaml');

// Default Timezone. See: http://us3.php.net/manual/en/timezones.php
$DEFAULT_TIMEZONE = $config['nightly']['timezone'];
date_default_timezone_set($DEFAULT_TIMEZONE);

// Display format for date
$DAY_DISPLAY = date($config['nightly']['displayFormat'], strtotime('yesterday'));

// Info for regular report recipients
$RECIPIENTS  = $config['nightly']['recipients'];
$GREETING    = "Below are the hourly Suma counts for " . $DAY_DISPLAY . ". " . "Please note that missing counts may have been performed but not yet uploaded to the Suma server.";
$SUBJECT     = "Student Manager Head Counts: " . $DAY_DISPLAY;

// Info for error report recipients
$ERROR_RECIPIENTS = $config['nightly']['errorRecipients'];
$ERROR_GREETING = 'The nightly Suma report encountered an error.';
$ERROR_SUBJECT = 'ERROR: Suma Nightly Report: ' . $DAY_DISPLAY;

// Run Script
//$data = `php nightly.php`; //original Suma code, no locations
$data = `php nightlyByLocation.php`; // location-based reporting as appropriate
$errorCheck = explode(" ", $data);

if ($errorCheck[0] === "Error:")
{
    $message = $ERROR_GREETING . "\n" . $data;
    mail($ERROR_RECIPIENTS, $ERROR_SUBJECT, $message);
}
else
{
  $headers = "From: Suma Server <kirwin@wittenberg.edu>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $message = $GREETING . "\n" . $data;
    mail($RECIPIENTS, $SUBJECT, $message, $headers);
}
