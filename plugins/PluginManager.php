<?php

class PluginManager {
    private static $instance = null;
    private $plugins = [];
    private $hooks = [];
    private $pluginPath;
    
    private function __construct() {
        $this->pluginPath = __DIR__;
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Load all plugins from the plugins directory
     */
    public function loadPlugins() {
        $directories = glob($this->pluginPath . '/*', GLOB_ONLYDIR);
        
        foreach ($directories as $dir) {
            $pluginName = basename($dir);
            $manifestPath = $dir . '/plugin.json';
            
            if (!file_exists($manifestPath)) {
                continue;
            }
            
            // Load plugin manifest
            $manifest = json_decode(file_get_contents($manifestPath), true);
            
            // Check if plugin is enabled
            if (!isset($manifest['enabled']) || !$manifest['enabled']) {
                continue;
            }
            
            // Load plugin class
            $className = $manifest['class'] ?? $pluginName;
            $classFile = $dir . '/' . $className . '.php';
            
            if (file_exists($classFile)) {
                require_once $classFile;
                
                if (class_exists($className)) {
                    $plugin = new $className($manifest);
                    $this->plugins[$pluginName] = [
                        'instance' => $plugin,
                        'manifest' => $manifest,
                        'path' => $dir
                    ];
                    
                    // Initialize plugin
                    if (method_exists($plugin, 'init')) {
                        $plugin->init();
                    }
                }
            }
        }
    }
    
    /**
     * Register a hook
     */
    public function registerHook($hookName, $callback, $priority = 10) {
        if (!isset($this->hooks[$hookName])) {
            $this->hooks[$hookName] = [];
        }
        
        $this->hooks[$hookName][] = [
            'callback' => $callback,
            'priority' => $priority
        ];
        
        // Sort by priority
        usort($this->hooks[$hookName], function($a, $b) {
            return $a['priority'] - $b['priority'];
        });
    }
    
    /**
     * Execute a hook
     */
    public function executeHook($hookName, $data = null) {
        if (!isset($this->hooks[$hookName])) {
            return $data;
        }
        
        foreach ($this->hooks[$hookName] as $hook) {
            if (is_callable($hook['callback'])) {
                $data = call_user_func($hook['callback'], $data);
            }
        }
        
        return $data;
    }
    
    /**
     * Render hook - for HTML output
     */
    public function renderHook($hookName, $data = null) {
        if (!isset($this->hooks[$hookName])) {
            return '';
        }
        
        $output = '';
        foreach ($this->hooks[$hookName] as $hook) {
            if (is_callable($hook['callback'])) {
                $result = call_user_func($hook['callback'], $data);
                if (is_string($result)) {
                    $output .= $result;
                }
            }
        }
        
        return $output;
    }
    
    /**
     * Get all loaded plugins
     */
    public function getPlugins() {
        return $this->plugins;
    }
    
    /**
     * Get a specific plugin
     */
    public function getPlugin($name) {
        return $this->plugins[$name] ?? null;
    }
    
    /**
     * Check if plugin is loaded
     */
    public function isPluginLoaded($name) {
        return isset($this->plugins[$name]);
    }
    
    /**
     * Get plugin assets URL
     */
    public function getPluginAssetUrl($pluginName, $asset) {
        if (!isset($this->plugins[$pluginName])) {
            return '';
        }
        
        return BASE_URL . '/plugins/' . $pluginName . '/assets/' . $asset;
    }
    
    /**
     * Load plugin view
     */
    public function loadPluginView($pluginName, $viewName, $data = []) {
        if (!isset($this->plugins[$pluginName])) {
            return '';
        }
        
        $viewPath = $this->plugins[$pluginName]['path'] . '/views/' . $viewName . '.php';
        
        if (file_exists($viewPath)) {
            extract($data);
            ob_start();
            include $viewPath;
            return ob_get_clean();
        }
        
        return '';
    }
}

/**
 * Base Plugin Class
 */
abstract class BasePlugin {
    protected $manifest;
    protected $pluginManager;
    
    public function __construct($manifest) {
        $this->manifest = $manifest;
        $this->pluginManager = PluginManager::getInstance();
    }
    
    /**
     * Initialize plugin - override in child classes
     */
    abstract public function init();
    
    /**
     * Get plugin name
     */
    public function getName() {
        return $this->manifest['name'] ?? 'Unknown Plugin';
    }
    
    /**
     * Get plugin version
     */
    public function getVersion() {
        return $this->manifest['version'] ?? '1.0.0';
    }
    
    /**
     * Register a hook
     */
    protected function registerHook($hookName, $callback, $priority = 10) {
        $this->pluginManager->registerHook($hookName, $callback, $priority);
    }
    
    /**
     * Get asset URL
     */
    protected function getAssetUrl($asset) {
        $pluginName = $this->manifest['directory'] ?? basename(get_class($this));
        return $this->pluginManager->getPluginAssetUrl($pluginName, $asset);
    }
    
    /**
     * Load view
     */
    protected function loadView($viewName, $data = []) {
        $pluginName = $this->manifest['directory'] ?? basename(get_class($this));
        return $this->pluginManager->loadPluginView($pluginName, $viewName, $data);
    }
}
?>