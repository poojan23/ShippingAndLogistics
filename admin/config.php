<?php
# HTTP
define('HTTP_SERVER', 'http://localhost/ShippingAndLogistics/admin/');
define('HTTP_CATALOG', 'http://localhost/ShippingAndLogistics/');

# HTTPS
define('HTTPS_SERVER', 'http://localhost/ShippingAndLogistics/admin/');
define('HTTPS_CATALOG', 'http://localhost/ShippingAndLogistics/');

# DIR
define('DIR_APPLICATION', 'C:/xampp/htdocs/ShippingAndLogistics/admin/');
define('DIR_SYSTEM', 'C:/xampp/htdocs/ShippingAndLogistics/system/');
define('DIR_IMAGE', 'C:/xampp/htdocs/ShippingAndLogistics/image/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
define('DIR_CATALOG', 'C:/xampp/htdocs/ShippingAndLogistics/template/');
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
define('DB_DATABASE', 'logistics');
define('DB_PORT', '3306');
define('DB_PREFIX', 'pt_');

# POPAYA API
define('POPAYA_SERVER', 'https://www.popaya.in/');
