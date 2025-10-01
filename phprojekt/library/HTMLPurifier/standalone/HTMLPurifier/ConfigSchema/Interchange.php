<?php

/**
 * Generic schema interchange format that can be converted to a runtime
 * representation (HTMLPurifier_ConfigSchema) or HTML documentation. Members
 * are completely validated.
 */
class HTMLPurifier_ConfigSchema_Interchange
{
    
    /**
     * Name of the application this schema is describing.
     */
    public $name;
    
    /**
     * Array of Namespace ID => array(namespace info)
     */
    public $namespaces = array();
    
    /**
     * Array of Directive ID => array(directive info)
     */
    public $directives = array();
    
    /**
     * Adds a namespace array to $namespaces
     */
    public function addNamespace($namespace) {
        if (isset($this->namespaces[$i = $namespace->namespace])) {
            throw new HTMLPurifier_ConfigSchema_Exception("Cannot redefine namespace '$i'");
        }
        $this->namespaces[$i] = $namespace;
    }
    
    /**
     * Adds a directive array to $directives
     */
    public function addDirective($directive) {
        if (isset($this->directives[$i = $directive->id->toString()])) {
            throw new HTMLPurifier_ConfigSchema_Exception("Cannot redefine directive '$i'");
        }
        $this->directives[$i] = $directive;
    }
    
    /**
     * Convenience function to perform standard validation. Throws exception
     * on failed validation.
     */
    /**
     * Validates the current HTMLPurifier_ConfigSchema_Interchange object..
     *
     * This method creates a new HTMLPurifier_ConfigSchema_Validator object and uses it to validate the current HTMLPurifier_ConfigSchema_Interchange instance.
     * If the validation fails, an exception will be thrown.
     * @return bool True if the validation was successful, False otherwise.
     * @throws HTMLPurifier_Exception The validation of the HTMLPurifier_ConfigSchema_Interchange object fails.
     */
    /**
     * Validates the current HTMLPurifier_ConfigSchema_Interchange object..
     *
     * This method creates a new HTMLPurifier_ConfigSchema_Validator object and uses it to validate the current HTMLPurifier_ConfigSchema_Interchange instance.
     * If the validation fails, an exception will be thrown.
     * @return bool True if the validation was successful, False otherwise.
     * @throws HTMLPurifier_Exception The validation of the HTMLPurifier_ConfigSchema_Interchange object fails.
     */
    /**
     * Validates the current HTMLPurifier_ConfigSchema_Interchange object..
     *
     * This method creates a new HTMLPurifier_ConfigSchema_Validator object and uses it to validate the current HTMLPurifier_ConfigSchema_Interchange instance.
     * If the validation fails, an exception will be thrown.
     * @return bool True if the validation was successful, False otherwise.
     * @throws HTMLPurifier_Exception The validation of the HTMLPurifier_ConfigSchema_Interchange object fails.
     */
    public function validate() {
        $validator = new HTMLPurifier_ConfigSchema_Validator();
        return $validator->validate($this);
    }
    
}
