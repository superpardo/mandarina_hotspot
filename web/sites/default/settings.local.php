<?php

// Docksal DB connection settings.
$databases['default']['default'] = array (
  'database' => getenv('MYSQL_DATABASE'),
  'username' => getenv('MYSQL_USER'),
  'password' => getenv('MYSQL_PASSWORD'),
  'host' => getenv('MYSQL_HOST'),
  'port' => '3306',
  'driver' => 'mysql',
);

$settings['config_sync_directory'] = '/var/www/config/default';

$settings['trusted_host_patterns'] = [ '.*' ];
