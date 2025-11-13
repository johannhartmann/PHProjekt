<?php
/**
 * Compatibility shim for Zend_Controller_Response_Http
 */

use Laminas\Http\PhpEnvironment\Response as LaminasResponse;

class Zend_Controller_Response_Http extends LaminasResponse
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set body
     */
    public function setBody($content, $name = null)
    {
        $this->setContent($content);
        return $this;
    }

    /**
     * Append to body
     */
    public function appendBody($content, $name = null)
    {
        $existing = $this->getContent();
        $this->setContent($existing . $content);
        return $this;
    }

    /**
     * Get body
     */
    public function getBody($spec = false)
    {
        return $this->getContent();
    }

    /**
     * Clear body
     */
    public function clearBody($name = null)
    {
        $this->setContent('');
        return $this;
    }

    /**
     * Send response
     */
    public function sendResponse()
    {
        $this->send();
    }
}
