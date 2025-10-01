<?php

/**
 * Represents a directive ID in the interchange format.
 */
class HTMLPurifier_ConfigSchema_Interchange_Id
{
    
    public $namespace, $directive;
    
    public function __construct($namespace, $directive) {
        $this->namespace = $namespace;
        $this->directive = $directive;
    }
    
    /**
     * @warning This is NOT magic, to ensure that people don't abuse SPL and
     *          cause problems for PHP 5.0 support.
     */
    public function toString() {
        return $this->namespace . '.' . $this->directive;
    }
    
    /**
     * Creates a new HTMLPurifier_ConfigSchema_Interchange_Id instance from a given ID string..
     *
     * This static method takes an ID string in the format 'namespace.directive' and splits it into the namespace and directive components.
     * It then creates and returns a new HTMLPurifier_ConfigSchema_Interchange_Id object with the extracted values.
     *
     * @param string $id The ID string in the format 'namespace.directive'
     * @return HTMLPurifier_ConfigSchema_Interchange_Id A new HTMLPurifier_ConfigSchema_Interchange_Id instance with the namespace and directive extracted from the input ID string
     */
    /**
     * Creates a new HTMLPurifier_ConfigSchema_Interchange_Id instance from a given ID string..
     *
     * This static method takes an ID string in the format 'namespace.directive' and splits it into the namespace and directive components.
     * It then creates and returns a new HTMLPurifier_ConfigSchema_Interchange_Id object with the extracted values.
     *
     * @param string $id The ID string in the format 'namespace.directive'
     * @return HTMLPurifier_ConfigSchema_Interchange_Id A new HTMLPurifier_ConfigSchema_Interchange_Id instance with the namespace and directive extracted from the input ID string
     */
    /**
     * Creates a new HTMLPurifier_ConfigSchema_Interchange_Id instance from a given ID string..
     *
     * This static method takes an ID string in the format 'namespace.directive' and splits it into the namespace and directive components.
     * It then creates and returns a new HTMLPurifier_ConfigSchema_Interchange_Id object with the extracted values.
     *
     * @param string $id The ID string in the format 'namespace.directive'
     * @return HTMLPurifier_ConfigSchema_Interchange_Id A new HTMLPurifier_ConfigSchema_Interchange_Id instance with the namespace and directive extracted from the input ID string
     */
    public static function make($id) {
        list($namespace, $directive) = explode('.', $id);
        return new HTMLPurifier_ConfigSchema_Interchange_Id($namespace, $directive);
    }
    
}
