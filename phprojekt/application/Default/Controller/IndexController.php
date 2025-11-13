<?php
/**
 * Laminas MVC Index Controller
 */
namespace Application\Default\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;

/**
 * Default Index Controller.
 *
 * The controller gets an action and runs the necessary stuff for it.
 * The actions whose name starts with "json", returns the data in JSON format.
 * The actions whose name starts with csv, returns the data in CSV format.
 * The controller calls the class model of the module, for process all the data.
 */
class IndexController extends AbstractActionController
{
    /**
     * String to use on success in the action save for new items.
     */
    const ADD_TRUE_TEXT = "The Item was added correctly";

    /**
     * String to use on success in the action save for existing items.
     */
    const EDIT_TRUE_TEXT = "The Item was edited correctly";

    /**
     * String to use on success in the action save for many items together.
     */
    const EDIT_MULTIPLE_TRUE_TEXT = "The Items were edited correctly";

    /**
     * String to use on success in the action delete.
     */
    const DELETE_TRUE_TEXT = "The Item was deleted correctly";

    /**
     * String to use on error in the action delete.
     */
    const DELETE_FALSE_TEXT = "The Item can't be deleted";

    /**
     * String to use on success in the action delete for many items together.
     */
    const DELETE_MULTIPLE_TRUE_TEXT = "The Items were deleted correctly";

    /**
     * String for use if the item don't exists.
     */
    const NOT_FOUND = "The Item was not found";

    /**
     * String for use if the id is not in the request parameters.
     */
    const ID_REQUIRED_TEXT = "ID parameter required";

    /**
     * String for use if the nodeId is not in the request parameters.
     */
    const NODEID_REQUIRED_TEXT = "Node Id parameter required";

    /**
     * String for use if the nodeId is not in the request parameters.
     */
    const PROJECTID_REQUIRED_TEXT = "projectId parameter required";

    /**
     * Internal number for the root project.
     */
    const INVISIBLE_ROOT = 1;

    /**
     * The link will be executed using a normal POST and ajax.
     */
    const MODE_ACTION_XHR = 0;

    /**
     * The link will be executed in a new windows as a normal GET.
     */
    const MODE_ACTION_WINDOW = 1;

    /**
     * The action will be executed in the client.
     */
    const MODE_ACTION_CLIENT = 2;

    /**
     * The action is for one id,
     * used in the grid for one row.
     */
    const TARGET_ACTION_SINGLE = 0;

    /**
     * The action is for multiple ids,
     * used in the selectbox of the grid for all the checked rows.
     */
    const TARGET_ACTION_MULTIPLE = 1;

    /**
     * String to use on success in the action disableFrontendMessages.
     */
    const DISABLE_FRONTEND_MESSAGES_TRUE_TEXT = "All settings were disabled successfully!";

    /**
     * String to use on error in the action disableFrontendMessages.
     */
    const DISABLE_FRONTEND_MESSAGES_FALSE_TEXT = "No settings were disabled!";

    /**
     * Index action - main entry point
     *
     * @return ViewModel|JsonModel
     */
    public function indexAction()
    {
        // Check for AJAX request
        if ($this->getRequest()->isXmlHttpRequest()) {
            // Return JSON response
            return new JsonModel([
                'message' => 'PHProjekt Index',
                'success' => true
            ]);
        }

        // Return HTML view
        return new ViewModel();
    }
}
