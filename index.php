<?php

# Version
define('VERSION', '2.0.1.0');

# Config File
if (is_file('config.php')) {
    require_once('config.php');
}

# Install
if (!defined('DIR_APPLICATION')) {
    header('Location: launch/index.php');
    exit;
}

# Startup
require_once(DIR_SYSTEM . 'startup.php');

start('template');
