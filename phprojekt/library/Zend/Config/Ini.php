<?php
/**
 * Compatibility shim for Zend_Config_Ini
 */

use Laminas\Config\Reader\Ini as IniReader;

/**
 * INI config class
 */
class Zend_Config_Ini extends Zend_Config
{
    /**
     * Current section
     * @var string
     */
    protected $_section;

    /**
     * Constructor
     *
     * @param string $filename INI file to load
     * @param string $section Section to load
     * @param bool $allowModifications Allow modifications
     */
    public function __construct($filename, $section = null, $allowModifications = false)
    {
        $this->_section = $section;
        $this->_allowModifications = $allowModifications;

        if (!file_exists($filename)) {
            throw new Zend_Config_Exception("File '$filename' doesn't exist");
        }

        try {
            // Parse INI file with section inheritance support
            $config = $this->_parseIniFileWithInheritance($filename);

            if ($section !== null) {
                if (!isset($config[$section])) {
                    throw new Zend_Config_Exception("Section '$section' not found in INI file");
                }
                $this->_data = $config[$section];
            } else {
                $this->_data = $config;
            }

            // Convert nested arrays to Zend_Config objects
            $this->_data = $this->_convertToConfig($this->_data);
        } catch (\Exception $e) {
            if ($e instanceof Zend_Config_Exception) {
                throw $e;
            }
            throw new Zend_Config_Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Parse INI file with ZF1-style section inheritance
     * Handles syntax like [child : parent]
     *
     * @param string $filename
     * @return array
     */
    protected function _parseIniFileWithInheritance($filename)
    {
        // Read the raw INI file content
        $content = file_get_contents($filename);

        // Remove PHP die() line if present
        $content = preg_replace('/^;\s*<\?php.*?\?>\s*$/m', '', $content);

        // Parse sections with inheritance information
        $sections = [];
        $currentSection = null;
        $sectionInheritance = [];

        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if (empty($line) || $line[0] === ';') {
                continue;
            }

            // Check for section header with inheritance: [child : parent]
            if (preg_match('/^\[([^\]:]+)(?:\s*:\s*([^\]]+))?\]$/', $line, $matches)) {
                $currentSection = trim($matches[1]);
                $parentSection = isset($matches[2]) ? trim($matches[2]) : null;

                if (!isset($sections[$currentSection])) {
                    $sections[$currentSection] = [];
                }

                if ($parentSection) {
                    $sectionInheritance[$currentSection] = $parentSection;
                }
                continue;
            }

            // Parse key = value pairs
            if ($currentSection !== null && strpos($line, '=') !== false) {
                list($key, $value) = array_map('trim', explode('=', $line, 2));

                // Remove quotes and trailing semicolons
                $value = trim($value, '"; ');

                // Handle nested keys (dot notation)
                $this->_setNestedValue($sections[$currentSection], $key, $value);
            }
        }

        // Now process inheritance - merge parent data into children
        foreach ($sectionInheritance as $child => $parent) {
            if (isset($sections[$parent])) {
                $sections[$child] = array_replace_recursive($sections[$parent], $sections[$child]);
            }
        }

        return $sections;
    }

    /**
     * Set nested array value using dot notation
     *
     * @param array &$array
     * @param string $key
     * @param mixed $value
     */
    protected function _setNestedValue(&$array, $key, $value)
    {
        if (strpos($key, '.') === false) {
            $array[$key] = $value;
            return;
        }

        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) {
                $current[$k] = $value;
            } else {
                if (!isset($current[$k]) || !is_array($current[$k])) {
                    $current[$k] = [];
                }
                $current = &$current[$k];
            }
        }
    }

    /**
     * Convert array to nested Zend_Config objects
     */
    protected function _convertToConfig($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                // Create a new config instance without going through constructor
                $config = new Zend_Config($value, $this->_allowModifications);
                $config->_data = $this->_convertToConfig($value);
                $data[$key] = $config;
            }
        }

        return $data;
    }
}
