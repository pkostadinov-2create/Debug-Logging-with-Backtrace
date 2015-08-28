<?php
// Ignore all errors in plugins directory.
// Typically bad plugins generate many error messages that shouldn't be addressed during
// theme development. 
set_error_handler('crb_ignore_plugins_errors');
function crb_ignore_plugins_errors($errno='', $errstr='', $errfile='', $errline='', $errcontext='') {
	// Don't care about STRICT errors -- many of them are being generated because 
	// of the PHP 4 compliance (e.g. using var instead of of public / protected / private)
	if (defined('E_STRICT') && $errno === E_STRICT) {
		return;
	}

	$debug = debug_backtrace();
	$backtrace_result = array();
	foreach ($debug as $index => $entry) {
		$backtrace_result[$index]['file'] = $entry['file'];
		$backtrace_result[$index]['line'] = $entry['line'];
		$backtrace_result[$index]['function'] = $entry['function'];
	}

	// Normalize paths
	$error_file = str_replace('\\', '/', $errfile);
	$plugins_dir = str_replace('\\', '/', WP_CONTENT_DIR . '/plugins');

	if (strpos($error_file, $plugins_dir) !== false) {
		// Do nothing for errors in wp-content/plugins directory
		return true;
	}

	$errfile = str_replace(dirname(__FILE__), '', $errfile);
	$errfile = str_replace('\\', '/', $errfile);

	$error_numbers_names = array(
		'1' => 'Error',
		'2' => 'Warning',
		'4' => 'Parse',
		'8' => 'Notice',
		'16' => 'Core Error',
		'32' => 'Core Warning',
		'64' => 'Compile Error',
		'128' => 'Compile Warning',
		'256' => 'User Error',
		'512' => 'User Warning',
		'1024' => 'User Notice',
		'2048' => 'Strict',
		'4096' => 'Recoverable Error',
		'8192' => 'Deprecated',
		'16384' => 'User Deprecated',
		'30719' => 'All',
	);
	$error_type = isset($error_numbers_names[$errno]) ? $error_numbers_names[$errno] : "Unknown Error";
	
	// echo( "$error_type: $errstr in $errfile on line $errline" );
	array_unshift($backtrace_result, "$error_type: $errstr in $errfile on line $errline");
	Crb_EM_Exception::write_to_log(/*serialize*/($backtrace_result));
}

class Crb_EM_Exception {

	public static function fire( $xception_message ) {
		if ( empty($xception_message) ) {
			return;
		}

		self::write_to_log($xception_message);

		throw new Exception($xception_message);
	}

	public static function write_to_log($xception_message) {
		$message_type = 3;
		$destination = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'crb_error_logs/';

		$file_name = 'log-' . date('Y-m-d') . '.log';

		// write to log
		error_log('----------------------------------------------------------------' . "\n", $message_type, $destination . $file_name);
		error_log(' # Date ::: ' . date('Y-m-d H:i:s') . "\n", $message_type, $destination . $file_name);
		error_log('----------------------------------------------------------------' . "\n", $message_type, $destination . $file_name);
		error_log(print_r($xception_message, true) . "\n\n", $message_type, $destination . $file_name);
	}
}