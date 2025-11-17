<?php
require_once 'config.php';

echo "<h1>Plugin Manager Test</h1>";

$pm = PluginManager::getInstance();
$plugins = $pm->getPlugins();

echo "<h2>Loaded Plugins:</h2>";
if (empty($plugins)) {
    echo "<p>❌ No plugins loaded</p>";
} else {
    echo "<ul>";
    foreach ($plugins as $name => $plugin) {
        echo "<li>✅ " . $name . " - " . $plugin['manifest']['name'] . " v" . $plugin['manifest']['version'] . "</li>";
    }
    echo "</ul>";
}
?>