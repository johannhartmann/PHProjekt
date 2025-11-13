<?php
/**
 * Compatibility shim for Zend_Filter
 */

class Zend_Filter
{
    /**
     * Filter a value
     */
    public static function filterStatic($value, $classBaseName, array $args = [])
    {
        $className = 'Zend_Filter_' . $classBaseName;

        if (!class_exists($className)) {
            // Try Laminas filter
            $laminasClass = 'Laminas\\Filter\\' . $classBaseName;
            if (class_exists($laminasClass)) {
                $filter = new $laminasClass(...$args);
                return $filter->filter($value);
            }
            throw new Zend_Filter_Exception("Filter class '$className' not found");
        }

        $filter = new $className(...$args);
        return $filter->filter($value);
    }
}

/**
 * Abstract filter
 */
abstract class Zend_Filter_Abstract implements Zend_Filter_Interface
{
    /**
     * Filter a value
     */
    abstract public function filter($value);
}

/**
 * Filter interface
 */
interface Zend_Filter_Interface
{
    public function filter($value);
}

/**
 * Exception class
 */
class Zend_Filter_Exception extends Exception
{
}

/**
 * String trim filter
 */
class Zend_Filter_StringTrim extends Zend_Filter_Abstract
{
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        return trim($value);
    }
}

/**
 * Strip tags filter
 */
class Zend_Filter_StripTags extends Zend_Filter_Abstract
{
    protected $_allowedTags = [];

    public function __construct($options = [])
    {
        if (is_array($options) && isset($options['allowTags'])) {
            $this->_allowedTags = $options['allowTags'];
        }
    }

    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        if (empty($this->_allowedTags)) {
            return strip_tags($value);
        }

        $allowed = '<' . implode('><', $this->_allowedTags) . '>';
        return strip_tags($value, $allowed);
    }
}

/**
 * HTML entities filter
 */
class Zend_Filter_HtmlEntities extends Zend_Filter_Abstract
{
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        return htmlentities($value, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * String to lower filter
 */
class Zend_Filter_StringToLower extends Zend_Filter_Abstract
{
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        return strtolower($value);
    }
}

/**
 * String to upper filter
 */
class Zend_Filter_StringToUpper extends Zend_Filter_Abstract
{
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }
        return strtoupper($value);
    }
}

/**
 * Alpha numeric filter
 */
class Zend_Filter_Alnum extends Zend_Filter_Abstract
{
    protected $_allowWhiteSpace = false;

    public function __construct($allowWhiteSpace = false)
    {
        $this->_allowWhiteSpace = $allowWhiteSpace;
    }

    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $pattern = $this->_allowWhiteSpace ? '/[^a-zA-Z0-9\s]/' : '/[^a-zA-Z0-9]/';
        return preg_replace($pattern, '', $value);
    }
}

/**
 * Digits filter
 */
class Zend_Filter_Digits extends Zend_Filter_Abstract
{
    public function filter($value)
    {
        return preg_replace('/[^0-9]/', '', (string)$value);
    }
}
