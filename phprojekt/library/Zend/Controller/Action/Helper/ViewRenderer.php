<?php
/**
 * Compatibility shim for Zend_Controller_Action_Helper_ViewRenderer
 */

class Zend_Controller_Action_Helper_ViewRenderer extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * View object
     * @var Zend_View
     */
    protected $_view;

    /**
     * View script path spec
     * @var string
     */
    protected $_viewScriptPathSpec = ':module/:controller/:action.:suffix';

    /**
     * View script path no controller spec
     * @var string
     */
    protected $_viewScriptPathNoControllerSpec = ':action.:suffix';

    /**
     * View base path spec
     * @var string
     */
    protected $_viewBasePathSpec = ':moduleDir/views';

    /**
     * View suffix
     * @var string
     */
    protected $_viewSuffix = 'phtml';

    /**
     * Whether to auto-render
     * @var bool
     */
    protected $_noRender = false;

    /**
     * Whether view has been rendered
     * @var bool
     */
    protected $_rendered = false;

    /**
     * Constructor
     */
    public function __construct($view = null)
    {
        if ($view === null) {
            $this->_view = new Zend_View();
        } else {
            $this->_view = $view;
        }
    }

    /**
     * Set view
     */
    public function setView(Zend_View $view)
    {
        $this->_view = $view;
        return $this;
    }

    /**
     * Get view
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * Set no render flag
     */
    public function setNoRender($flag = true)
    {
        $this->_noRender = $flag;
        return $this;
    }

    /**
     * Get no render flag
     */
    public function getNoRender()
    {
        return $this->_noRender;
    }

    /**
     * Set view suffix
     */
    public function setViewSuffix($suffix)
    {
        $this->_viewSuffix = $suffix;
        return $this;
    }

    /**
     * Get view suffix
     */
    public function getViewSuffix()
    {
        return $this->_viewSuffix;
    }

    /**
     * Set view script path spec
     */
    public function setViewScriptPathSpec($spec)
    {
        $this->_viewScriptPathSpec = $spec;
        return $this;
    }

    /**
     * Get view script path spec
     */
    public function getViewScriptPathSpec()
    {
        return $this->_viewScriptPathSpec;
    }

    /**
     * Set view base path spec
     */
    public function setViewBasePathSpec($spec)
    {
        $this->_viewBasePathSpec = $spec;
        return $this;
    }

    /**
     * Get view base path spec
     */
    public function getViewBasePathSpec()
    {
        return $this->_viewBasePathSpec;
    }

    /**
     * Add view script path
     */
    public function addScriptPath($path, $prefix = null)
    {
        $this->_view->addScriptPath($path);
        return $this;
    }

    /**
     * Set view script paths
     */
    public function setScriptPath($path)
    {
        $this->_view->setScriptPath($path);
        return $this;
    }

    /**
     * Render view script
     */
    public function render($action = null, $name = null, $noController = false)
    {
        if ($this->_noRender) {
            return;
        }

        if ($this->_rendered) {
            return;
        }

        $request = $this->getRequest();

        if ($action === null) {
            $action = $request->getActionName();
        }

        $script = $this->getViewScript($action, $noController);

        try {
            $this->_response->appendBody($this->_view->render($script));
            $this->_rendered = true;
        } catch (Exception $e) {
            // View script not found - silently fail for compatibility
        }
    }

    /**
     * Get view script name
     */
    public function getViewScript($action = null, $noController = false)
    {
        $request = $this->getRequest();

        if ($action === null) {
            $action = $request->getActionName();
        }

        $spec = $noController
            ? $this->_viewScriptPathNoControllerSpec
            : $this->_viewScriptPathSpec;

        $module = $request->getModuleName();
        $controller = $request->getControllerName();

        $script = str_replace(
            [':module', ':controller', ':action', ':suffix'],
            [$module, $controller, $action, $this->_viewSuffix],
            $spec
        );

        return $script;
    }

    /**
     * Post-dispatch - auto-render view
     */
    public function postDispatch()
    {
        $this->render();
    }

    /**
     * Direct call
     */
    public function direct($action = null, $name = null, $noController = false)
    {
        $this->render($action, $name, $noController);
    }
}
