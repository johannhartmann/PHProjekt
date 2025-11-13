<?php
/**
 * Compatibility shim for Zend_Controller_Action_Helper_Url
 */

class Zend_Controller_Action_Helper_Url extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Generate URL
     */
    public function url($urlOptions = [], $name = null, $reset = false, $encode = true)
    {
        return $this->simple(
            $urlOptions['action'] ?? 'index',
            $urlOptions['controller'] ?? null,
            $urlOptions['module'] ?? null,
            $urlOptions
        );
    }

    /**
     * Generate simple URL
     */
    public function simple($action, $controller = null, $module = null, array $params = [])
    {
        $url = '/';

        if ($module !== null) {
            $url .= $module . '/';
        }

        if ($controller !== null) {
            $url .= $controller . '/';
        }

        $url .= $action;

        // Remove action, controller, module from params
        unset($params['action'], $params['controller'], $params['module']);

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * Direct call
     */
    public function direct($action, $controller = null, $module = null, array $params = [])
    {
        return $this->simple($action, $controller, $module, $params);
    }
}
