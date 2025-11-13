<?php
/**
 * Compatibility shim for Zend_Validate
 */

use Laminas\Validator\ValidatorChain;

class Zend_Validate
{
    /**
     * Check if a value is valid
     */
    public static function is($value, $classBaseName, array $args = [])
    {
        $className = 'Zend_Validate_' . $classBaseName;

        if (!class_exists($className)) {
            // Try Laminas validator
            $laminasClass = 'Laminas\\Validator\\' . $classBaseName;
            if (class_exists($laminasClass)) {
                $validator = new $laminasClass(...$args);
                return $validator->isValid($value);
            }
            throw new Zend_Validate_Exception("Validator class '$className' not found");
        }

        $validator = new $className(...$args);
        return $validator->isValid($value);
    }
}

/**
 * Abstract validator
 */
abstract class Zend_Validate_Abstract implements Zend_Validate_Interface
{
    /**
     * @var array
     */
    protected $_messages = [];

    /**
     * @var array
     */
    protected $_errors = [];

    /**
     * @var mixed
     */
    protected $_value;

    /**
     * Check if value is valid
     */
    abstract public function isValid($value);

    /**
     * Get messages
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Get errors
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Set error message
     */
    protected function _error($messageKey, $value = null)
    {
        $this->_errors[] = $messageKey;
        $this->_messages[$messageKey] = $this->_createMessage($messageKey, $value);
    }

    /**
     * Create message
     */
    protected function _createMessage($messageKey, $value)
    {
        return "Validation failed";
    }
}

/**
 * Validator interface
 */
interface Zend_Validate_Interface
{
    public function isValid($value);
    public function getMessages();
}

/**
 * Exception class
 */
class Zend_Validate_Exception extends Exception
{
}

/**
 * Email validator
 */
class Zend_Validate_EmailAddress extends Zend_Validate_Abstract
{
    public function isValid($value)
    {
        $this->_value = $value;

        if (!is_string($value)) {
            $this->_error('emailInvalid');
            return false;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->_error('emailInvalidFormat');
            return false;
        }

        return true;
    }
}

/**
 * Not empty validator
 */
class Zend_Validate_NotEmpty extends Zend_Validate_Abstract
{
    public function isValid($value)
    {
        $this->_value = $value;

        if (empty($value) && $value !== '0' && $value !== 0) {
            $this->_error('isEmpty');
            return false;
        }

        return true;
    }
}

/**
 * String length validator
 */
class Zend_Validate_StringLength extends Zend_Validate_Abstract
{
    protected $_min;
    protected $_max;

    public function __construct($options = [])
    {
        if (is_array($options)) {
            $this->_min = $options['min'] ?? null;
            $this->_max = $options['max'] ?? null;
        }
    }

    public function isValid($value)
    {
        $this->_value = $value;
        $length = strlen($value);

        if ($this->_min !== null && $length < $this->_min) {
            $this->_error('stringLengthTooShort');
            return false;
        }

        if ($this->_max !== null && $length > $this->_max) {
            $this->_error('stringLengthTooLong');
            return false;
        }

        return true;
    }
}
