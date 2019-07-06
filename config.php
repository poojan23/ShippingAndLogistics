<?php
# HTTP
define('HTTP_SERVER', 'http://localhost/jolly/');

# HTTPS
define('HTTPS_SERVER', 'http://localhost/jolly/');

# DIR
define('DIR_APPLICATION', 'C:/ATDI/LMS/htdocs/jolly/template/');
define('DIR_SYSTEM', 'C:/ATDI/LMS/htdocs/jolly/system/');
define('DIR_IMAGE', 'C:/ATDI/LMS/htdocs/jolly/image/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/html/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

# DB
define('DB_DRIVER', 'pdo');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'root');
define('DB_DATABASE', 'jolly');
define('DB_PORT', '3306');
define('DB_PREFIX', 'pt_');