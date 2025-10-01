<?php

/**
 * Interchange component class describing configuration directives.
 */
/**
 * Represents a configuration directive in the HTML Purifier configuration schema..
 *
 * This class encapsulates the metadata for a single configuration directive in the HTML Purifier configuration schema.
 * It stores information such as the directive's ID, type, default value, description, and other metadata like allowed values, aliases, deprecation status, and external dependencies.
 *
 * @param HTMLPurifier_ConfigSchema_Interchange_Id $id The unique identifier for this configuration directive.
 * @param string $type The data type of the configuration directive, e.g. 'integer' or 'istring'.
 * @param mixed $default The default value for this configuration directive.
 * @param string $description The HTML description of this configuration directive.
 * @param boolean $typeAllowsNull Whether null is an allowed value for this configuration directive.
 * @param array|null $allowed A lookup table of allowed scalar values for this configuration directive, or null if all values are allowed.
 * @param HTMLPurifier_ConfigSchema_Interchange_Id[] $aliases A list of alternative identifiers for this configuration directive.
 * @param array|null $valueAliases A hash of value aliases for this configuration directive, or null if value aliasing is disabled.
 * @param string|null $version The version of HTML Purifier in which this configuration directive was introduced, or null if it has always existed.
 * @param HTMLPurifier_ConfigSchema_Interchange_Id|null $deprecatedUse The ID of the configuration directive that supersedes this deprecated directive, or null if this directive is not deprecated.
 * @param string|null $deprecatedVersion The version of HTML Purifier in which this configuration directive was deprecated, or null if it is not deprecated.
 * @param string[] $external A list of external projects that this configuration directive depends on.
 * @see HTMLPurifier_ConfigSchema_Interchange_Id
 */
/**
 * Represents a configuration directive in the HTML Purifier configuration schema..
 *
 * This class encapsulates the metadata for a single configuration directive in the HTML Purifier configuration schema.
 * It stores information such as the directive's ID, type, default value, description, and other metadata like allowed values, aliases, deprecation status, and external dependencies.
 *
 * @param HTMLPurifier_ConfigSchema_Interchange_Id $id The unique identifier for this configuration directive.
 * @param string $type The data type of the configuration directive, e.g. 'integer' or 'istring'.
 * @param mixed $default The default value for this configuration directive.
 * @param string $description The HTML description of this configuration directive.
 * @param boolean $typeAllowsNull Whether null is an allowed value for this configuration directive.
 * @param array|null $allowed A lookup table of allowed scalar values for this configuration directive, or null if all values are allowed.
 * @param HTMLPurifier_ConfigSchema_Interchange_Id[] $aliases A list of alternative identifiers for this configuration directive.
 * @param array|null $valueAliases A hash of value aliases for this configuration directive, or null if value aliasing is disabled.
 * @param string|null $version The version of HTML Purifier in which this configuration directive was introduced, or null if it has always existed.
 * @param HTMLPurifier_ConfigSchema_Interchange_Id|null $deprecatedUse The ID of the configuration directive that supersedes this deprecated directive, or null if this directive is not deprecated.
 * @param string|null $deprecatedVersion The version of HTML Purifier in which this configuration directive was deprecated, or null if it is not deprecated.
 * @param string[] $external A list of external projects that this configuration directive depends on.
 * @see HTMLPurifier_ConfigSchema_Interchange_Id
 */
/**
 * Represents a configuration directive in the HTML Purifier configuration schema..
 *
 * This class encapsulates the metadata for a single configuration directive in the HTML Purifier configuration schema.
 * It stores information such as the directive's ID, type, default value, description, and other metadata like allowed values, aliases, deprecation status, and external dependencies.
 *
 * @param HTMLPurifier_ConfigSchema_Interchange_Id $id The unique identifier for this configuration directive.
 * @param string $type The data type of the configuration directive, e.g. 'integer' or 'istring'.
 * @param mixed $default The default value for this configuration directive.
 * @param string $description The HTML description of this configuration directive.
 * @param boolean $typeAllowsNull Whether null is an allowed value for this configuration directive.
 * @param array|null $allowed A lookup table of allowed scalar values for this configuration directive, or null if all values are allowed.
 * @param HTMLPurifier_ConfigSchema_Interchange_Id[] $aliases A list of alternative identifiers for this configuration directive.
 * @param array|null $valueAliases A hash of value aliases for this configuration directive, or null if value aliasing is disabled.
 * @param string|null $version The version of HTML Purifier in which this configuration directive was introduced, or null if it has always existed.
 * @param HTMLPurifier_ConfigSchema_Interchange_Id|null $deprecatedUse The ID of the configuration directive that supersedes this deprecated directive, or null if this directive is not deprecated.
 * @param string|null $deprecatedVersion The version of HTML Purifier in which this configuration directive was deprecated, or null if it is not deprecated.
 * @param string[] $external A list of external projects that this configuration directive depends on.
 * @see HTMLPurifier_ConfigSchema_Interchange_Id
 */
class HTMLPurifier_ConfigSchema_Interchange_Directive
{
    
    /**
     * ID of directive, instance of HTMLPurifier_ConfigSchema_Interchange_Id.
     */
    public $id;
    
    /**
     * String type, e.g. 'integer' or 'istring'.
     */
    public $type;
    
    /**
     * Default value, e.g. 3 or 'DefaultVal'.
     */
    public $default;
    
    /**
     * HTML description.
     */
    public $description;
    
    /**
     * Boolean whether or not null is allowed as a value.
     */
    public $typeAllowsNull = false;
    
    /**
     * Lookup table of allowed scalar values, e.g. array('allowed' => true).
     * Null if all values are allowed.
     */
    public $allowed;
    
    /**
     * List of aliases for the directive,
     * e.g. array(new HTMLPurifier_ConfigSchema_Interchange_Id('Ns', 'Dir'))).
     */
    public $aliases = array();
    
    /**
     * Hash of value aliases, e.g. array('alt' => 'real'). Null if value
     * aliasing is disabled (necessary for non-scalar types).
     */
    public $valueAliases;
    
    /**
     * Version of HTML Purifier the directive was introduced, e.g. '1.3.1'.
     * Null if the directive has always existed.
     */
    public $version;
    
    /**
     * ID of directive that supercedes this old directive, is an instance
     * of HTMLPurifier_ConfigSchema_Interchange_Id. Null if not deprecated.
     */
    public $deprecatedUse;
    
    /**
     * Version of HTML Purifier this directive was deprecated. Null if not
     * deprecated.
     */
    public $deprecatedVersion;
    
    /**
     * List of external projects this directive depends on, e.g. array('CSSTidy').
     */
    public $external = array();
    
}
