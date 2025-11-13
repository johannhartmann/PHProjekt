<?php
/**
 * Compatibility shim for Zend_Locale
 */

use Laminas\I18n\Translator\Translator;

class Zend_Locale
{
    /**
     * Locale string
     * @var string
     */
    protected $_locale;

    /**
     * Cache instance
     * @var mixed
     */
    protected static $_cache;

    /**
     * Constructor
     */
    public function __construct($locale = null)
    {
        if ($locale === null) {
            $locale = $this->_getDefaultLocale();
        }

        if ($locale instanceof self) {
            $locale = $locale->toString();
        }

        $this->_locale = $this->_parseLocale($locale);
    }

    /**
     * Get default locale
     */
    protected function _getDefaultLocale()
    {
        // Try to detect from environment
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            if (!empty($languages[0])) {
                return substr($languages[0], 0, 2);
            }
        }

        return 'en';
    }

    /**
     * Parse locale string
     */
    protected function _parseLocale($locale)
    {
        // Simple parsing - handle formats like en_US, en-US, en
        $locale = str_replace('-', '_', $locale);
        $parts = explode('_', $locale);

        if (count($parts) > 1) {
            return strtolower($parts[0]) . '_' . strtoupper($parts[1]);
        }

        return strtolower($parts[0]);
    }

    /**
     * Convert to string
     */
    public function toString()
    {
        return $this->_locale;
    }

    /**
     * Magic toString
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Get language
     */
    public function getLanguage()
    {
        $parts = explode('_', $this->_locale);
        return $parts[0];
    }

    /**
     * Get region
     */
    public function getRegion()
    {
        $parts = explode('_', $this->_locale);
        return isset($parts[1]) ? $parts[1] : null;
    }

    /**
     * Check if locale is supported
     */
    public static function isLocale($locale)
    {
        return !empty($locale) && (is_string($locale) || $locale instanceof self);
    }

    /**
     * Get browser locale
     */
    public static function getBrowser()
    {
        return new self();
    }

    /**
     * Set cache
     */
    public static function setCache($cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Get cache
     */
    public static function getCache()
    {
        return self::$_cache;
    }

    /**
     * Remove cache
     */
    public static function removeCache()
    {
        self::$_cache = null;
    }

    /**
     * Has cache
     */
    public static function hasCache()
    {
        return self::$_cache !== null;
    }
}
