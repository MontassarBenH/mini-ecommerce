<?php

$projectRoot = __DIR__ . '/..';

if (file_exists($projectRoot . '/vendor/autoload.php')) {
    require_once $projectRoot . '/vendor/autoload.php';
}

require_once $projectRoot . '/config.php';

require_once $projectRoot . '/controllers/ProductController.php';
require_once $projectRoot . '/controllers/CategoryController.php';

// Plugins laden
$pluginManager = PluginManager::getInstance();
$pluginManager->loadPlugins();

/**
 * Kleine Helper-Funktion für DB-Zugriff in Tests
 *
 * @return PDO
 */
function db(): PDO
{
    return Database::getInstance()->getConnection();
}
