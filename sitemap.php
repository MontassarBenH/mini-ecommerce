<?php
// sitemap.php - XML Sitemap Generator

require_once 'config.php';

// Get SEO plugin
$pluginManager = PluginManager::getInstance();
$seoPlugin = $pluginManager->getPlugin('SEOOptimizer');

if (!$seoPlugin) {
    http_response_code(500);
    die('SEO Optimizer plugin not loaded');
}

// Generate sitemap
$sitemap = $seoPlugin['instance']->generateSitemap();

// Set headers for XML
header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

// Output sitemap
echo $sitemap;
?>