<?php
/**
 * Compatibility shim for Zend_Controller_Action_Helper_Redirector
 */

class Zend_Controller_Action_Helper_Redirector extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * HTTP status code for redirect
     * @var int
     */
    protected $_code = 302;

    /**
     * Whether to exit after redirect
     * @var bool
     */
    protected $_exit = false;

    /**
     * Redirect URL
     * @var string
     */
    protected $_redirectUrl;

    /**
     * Set redirect code
     */
    public function setCode($code)
    {
        $this->_code = (int)$code;
        return $this;
    }

    /**
     * Get redirect code
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Set exit flag
     */
    public function setExit($flag)
    {
        $this->_exit = (bool)$flag;
        return $this;
    }

    /**
     * Get exit flag
     */
    public function getExit()
    {
        return $this->_exit;
    }

    /**
     * Redirect to URL
     */
    public function gotoUrl($url, array $options = [])
    {
        $this->_redirectUrl = $url;

        if (isset($options['code'])) {
            $this->setCode($options['code']);
        }

        if (isset($options['exit'])) {
            $this->setExit($options['exit']);
        }

        $this->_redirect();
    }

    /**
     * Redirect to route
     */
    public function gotoRoute(array $urlOptions = [], $name = null, $reset = false)
    {
        // Simple implementation - just redirect to constructed URL
        $url = $this->_assembleUrl($urlOptions);
        $this->gotoUrl($url);
    }

    /**
     * Redirect to action
     */
    public function gotoSimple($action, $controller = null, $module = null, array $params = [])
    {
        $url = '/' . ($module ? $module . '/' : '') .
               ($controller ? $controller . '/' : '') .
               $action;

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $this->gotoUrl($url);
    }

    /**
     * Direct call - redirect to URL
     */
    public function direct($url, array $options = [])
    {
        $this->gotoUrl($url, $options);
    }

    /**
     * Perform redirect
     */
    protected function _redirect()
    {
        if ($this->_redirectUrl === null) {
            throw new Zend_Controller_Action_Exception('No redirect URL set');
        }

        $response = $this->getResponse();
        $response->setStatusCode($this->_code);
        $response->getHeaders()->addHeaderLine('Location', $this->_redirectUrl);

        if ($this->_exit) {
            $response->send();
            exit;
        }
    }

    /**
     * Assemble URL from options
     */
    protected function _assembleUrl(array $urlOptions)
    {
        $url = '/';

        if (isset($urlOptions['module'])) {
            $url .= $urlOptions['module'] . '/';
        }

        if (isset($urlOptions['controller'])) {
            $url .= $urlOptions['controller'] . '/';
        }

        if (isset($urlOptions['action'])) {
            $url .= $urlOptions['action'];
        }

        return $url;
    }
}
