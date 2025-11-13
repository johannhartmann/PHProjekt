<?php
/**
 * Compatibility shim for Zend_Translate
 */

use Laminas\I18n\Translator\Translator;

class Zend_Translate
{
    /**
     * Translator instance
     * @var Translator
     */
    protected $_translator;

    /**
     * Current locale
     * @var string
     */
    protected $_locale;

    /**
     * Translation adapter
     * @var string
     */
    protected $_adapter;

    /**
     * Messages
     * @var array
     */
    protected $_messages = [];

    /**
     * Constructor
     */
    public function __construct($options = [])
    {
        $this->_translator = new Translator();

        if (is_array($options)) {
            if (isset($options['adapter'])) {
                $this->_adapter = $options['adapter'];
            }
            if (isset($options['content'])) {
                $this->addTranslation($options['content'], $options['locale'] ?? 'en');
            }
            if (isset($options['locale'])) {
                $this->setLocale($options['locale']);
            }
        }
    }

    /**
     * Add translation
     */
    public function addTranslation($content, $locale = null, $options = [])
    {
        if ($locale === null) {
            $locale = $this->_locale ?? 'en';
        }

        if (is_string($locale) && $locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        // Load translations based on adapter type
        if (is_array($content)) {
            $this->_messages[$locale] = array_merge(
                $this->_messages[$locale] ?? [],
                $content
            );
        } elseif (is_string($content) && file_exists($content)) {
            // Load from file
            $this->_loadFromFile($content, $locale);
        }

        return $this;
    }

    /**
     * Load translations from file
     */
    protected function _loadFromFile($file, $locale)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        switch (strtolower($ext)) {
            case 'php':
                $messages = include $file;
                if (is_array($messages)) {
                    $this->_messages[$locale] = array_merge(
                        $this->_messages[$locale] ?? [],
                        $messages
                    );
                }
                break;

            case 'ini':
                $messages = parse_ini_file($file, true);
                if (is_array($messages)) {
                    $this->_messages[$locale] = array_merge(
                        $this->_messages[$locale] ?? [],
                        $messages
                    );
                }
                break;

            case 'csv':
                // Simple CSV parsing
                $handle = fopen($file, 'r');
                while (($data = fgetcsv($handle)) !== false) {
                    if (count($data) >= 2) {
                        $this->_messages[$locale][$data[0]] = $data[1];
                    }
                }
                fclose($handle);
                break;
        }
    }

    /**
     * Translate a message
     */
    public function translate($messageId, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_locale ?? 'en';
        }

        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        if (isset($this->_messages[$locale][$messageId])) {
            return $this->_messages[$locale][$messageId];
        }

        // Fallback to message ID
        return $messageId;
    }

    /**
     * Magic call for translate
     */
    public function _($messageId)
    {
        return $this->translate($messageId);
    }

    /**
     * Set locale
     */
    public function setLocale($locale)
    {
        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        $this->_locale = $locale;
        return $this;
    }

    /**
     * Get locale
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Get available locales
     */
    public function getList()
    {
        return array_keys($this->_messages);
    }

    /**
     * Check if translation exists
     */
    public function isTranslated($messageId, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_locale ?? 'en';
        }

        if ($locale instanceof Zend_Locale) {
            $locale = $locale->toString();
        }

        return isset($this->_messages[$locale][$messageId]);
    }

    /**
     * Get adapter
     */
    public function getAdapter()
    {
        return $this;
    }
}

/**
 * Translate adapter interface
 */
interface Zend_Translate_Adapter
{
    public function translate($messageId);
    public function setLocale($locale);
    public function getLocale();
}
