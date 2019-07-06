<?php
 // Site
$_['site_base']         = HTTP_SERVER;
$_['site_ssl']          = HTTP_SERVER;

// Database
$_['db_autostart']       = true;
$_['db_engine']          = DB_DRIVER; // mpdo, mssql, mysql, mysqli or postgre
$_['db_hostname']        = DB_HOSTNAME;
$_['db_username']        = DB_USERNAME;
$_['db_password']        = DB_PASSWORD;
$_['db_database']        = DB_DATABASE;
$_['db_port']            = DB_PORT;

// Language
$_['language_default']  = 'en-gb';
$_['language_autoload'] = array('en-gb');

// Session
$_['session_engine']    = 'file';
$_['session_autostart'] = true;
$_['session_name']      = 'PTSESSID';

// Template
$_['template_engine']   = 'html';
//$_['template_cache']    = true;

// Error
$_['error_display']     = true;

// Actions
$_['action_default']    = 'launch/step_1';
$_['action_router']     = 'startup/router';
$_['action_error']      = 'error/not_found';
$_['action_pre_action'] = array(
	'startup/language'
);

// Action Events
$_['action_event'] = array(
	'view/*/before' => array(
		'event/theme'
	)
);
