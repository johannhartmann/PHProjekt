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
            $reader = new IniReader();
            $config = $reader->fromFile($filename);

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
