<?php

# Error Reporting
error_reporting(E_ALL);

# Config File
if (is_file('config.php')) {
    require_once('config.php');
}

# Install
if (!defined('DIR_APPLICATION')) {
    header('Location: ../install/index.php');
    exit;
}

# Startup
require_once(DIR_SYSTEM . 'startup.php');

start('launch');
