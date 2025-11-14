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
     * @return ViewModel
     */
    public function indexAction()
    {
        // Check authentication
        $this->checkAuthentication();

        $language = \Phprojekt_Auth::getRealUser()->getSetting(
            "language",
            \Phprojekt::getInstance()->getConfig()->language
        );

        $viewData = [
            'language' => $language,
            'compressedDojo' => (bool) \Phprojekt::getInstance()->getConfig()->compressedDojo,
            'frontendMsg' => (bool) \Phprojekt::getInstance()->getConfig()->frontendMessages,
            'pollingLoop' => \Phprojekt::getInstance()->getConfig()->pollingLoop * 1000,
        ];

        // Determine which template to render based on login status and page type
        if (\Phprojekt_Auth::isLoggedIn()) {
            $pageNamespace = new \Laminas\Session\Container('page');
            if (isset($pageNamespace->type) && $pageNamespace->type == "legacy") {
                $viewData['template'] = 'index';
            } else {
                $viewData['template'] = 'timecard';
            }
        } else {
            $viewData['template'] = 'login';
        }

        // Add login-specific data
        $viewData['isLoggedIn'] = \Phprojekt_Auth::isLoggedIn();

        // Handle domain selection for LDAP
        $authMode = \Phprojekt_Auth::getLoginMode();
        if ($authMode == 'ldap') {
            $conf = \Phprojekt::getInstance()->getConfig();
            $ldapOptions = isset($conf->authentication->ldap) ? $conf->authentication->ldap->toArray() : array();
            $domains = array();
            foreach ($ldapOptions as $server => $opts) {
                $serverName = isset($opts['accountDomainNameShort']) ? trim($opts['accountDomainNameShort']) :
                    (isset($opts['accountDomainName']) ? trim($opts['accountDomainName']) : $server);
                $domains[$server] = $serverName;
            }
            if (sizeof($domains) > 0) {
                $viewData['domains'] = $domains;
            }
        }

        return new ViewModel($viewData);
    }

    /**
     * Returns the project tree
     *
     * @return JsonModel
     */
    public function jsonTreeAction()
    {
        $model = new \Project_Models_Project();
        $tree = new \Phprojekt_Tree_Node_Database($model, 1);

        \Phprojekt_Converter_Json::echoConvert($tree->setup());

        return new JsonModel([]);
    }

    /**
     * Returns the list of items for one model
     *
     * @return JsonModel
     */
    public function jsonListAction()
    {
        $itemId = (int) $this->params()->fromQuery('id', null);
        $projectId = (int) $this->params()->fromQuery('nodeId', null);
        $count = (int) $this->params()->fromQuery('count', null);
        $offset = (int) $this->params()->fromQuery('start', null);
        $recursive = $this->params()->fromQuery('recursive', 'false');
        $this->setCurrentProjectId();

        if (!empty($itemId)) {
            $where = sprintf('id = %d', (int) $itemId);
        } else if (!empty($projectId) && $this->getModelObject()->hasField('projectId')) {
            $where = sprintf('project_id = %d', (int) $projectId);
        } else {
            $where = null;
        }

        // Handle recursive listing
        if (!empty($projectId) && $this->getModelObject()->hasField('projectId')
            && 'true' === $recursive) {
            $tree = new \Phprojekt_Tree_Node_Database(
                new \Project_Models_Project(),
                $projectId);
            $tree->setup();
            \Phprojekt_Converter_Json::echoConvert(
                $tree->getRecordsFor($this->getModelObject(), null, null, $this->getFilterWhere()),
                \Phprojekt_ModelInformation_Default::ORDERING_LIST);
        } else {
            $where = $this->getFilterWhere($where);
            $records = $this->getModelObject()->fetchAll($where, null, $count, $offset);

            \Phprojekt_Converter_Json::echoConvert($records, \Phprojekt_ModelInformation_Default::ORDERING_LIST);
        }

        return new JsonModel([]);
    }

    /**
     * Returns the metadata for this module's default module
     *
     * @return \Laminas\Http\Response
     */
    public function metadataAction()
    {
        $projectId = $this->params()->fromQuery('projectId', null);
        $this->_storeCurrentProjectId($projectId);

        $fieldDefinition = $this->getModelObject()->getInformation()->getFieldDefinition();
        \Phprojekt_CompressedSender::send(
            \Zend_Json_Encoder::encode($fieldDefinition)
        );

        return $this->getResponse();
    }

    /**
     * Returns the detail (fields and data) of one item
     *
     * @return JsonModel
     */
    public function jsonDetailAction()
    {
        $id = (int) $this->params()->fromQuery('id');
        $this->setCurrentProjectId();

        if (empty($id)) {
            $record = $this->getModelObject();
        } else {
            $record = $this->getModelObject()->find($id);
        }

        \Phprojekt_Converter_Json::echoConvert($record, \Phprojekt_ModelInformation_Default::ORDERING_FORM);

        return new JsonModel([]);
    }

    /**
     * Saves the current item
     *
     * @return JsonModel
     */
    public function jsonSaveAction()
    {
        $id = (int) $this->params()->fromPost('id', $this->params()->fromQuery('id', 0));
        $this->setCurrentProjectId();

        if (empty($id)) {
            $model = $this->getModelObject();
            $message = \Phprojekt::getInstance()->translate(self::ADD_TRUE_TEXT);
            $newItem = true;
        } else {
            $model = $this->getModelObject()->find($id);
            $message = \Phprojekt::getInstance()->translate(self::EDIT_TRUE_TEXT);
            $newItem = false;
        }

        if ($model instanceof \Phprojekt_Model_Interface) {
            $params = $this->setParams(array_merge($this->params()->fromQuery(), $this->params()->fromPost()), $model, $newItem);
            \Default_Helpers_Save::save($model, $params);

            $return = array(
                'type' => 'success',
                'message' => $message,
                'id' => $model->id
            );

            \Phprojekt_Converter_Json::echoConvert($return);
            return new JsonModel([]);
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return new JsonModel(['error' => self::NOT_FOUND]);
        }
    }

    /**
     * Save some fields for many items
     *
     * @return JsonModel
     */
    public function jsonSaveMultipleAction()
    {
        $data = (array) $this->params()->fromPost('data', $this->params()->fromQuery('data', []));
        $showId = array();
        $model = $this->getModelObject();
        $success = true;
        $this->setCurrentProjectId();

        foreach ($data as $id => $fields) {
            $model->find((int) $id);
            $params = $this->setParams($fields, $model);
            try {
                \Default_Helpers_Save::save($model, $params);
                $showId[] = $id;
            } catch (\Exception $error) {
                $message = sprintf("ID %d. %s", $id, $error->getMessage());
                $success = false;
                break;
            }
        }

        if ($success) {
            $message = \Phprojekt::getInstance()->translate(self::EDIT_MULTIPLE_TRUE_TEXT);
            $type = 'success';
        } else {
            $type = 'error';
        }

        $return = array(
            'type' => $type,
            'message' => $message,
            'id' => implode(',', $showId)
        );

        \Phprojekt_Converter_Json::echoConvert($return);
        return new JsonModel([]);
    }

    /**
     * Deletes one item
     *
     * @return JsonModel
     */
    public function jsonDeleteAction()
    {
        $id = (int) $this->params()->fromPost('id', $this->params()->fromQuery('id', 0));

        if (empty($id)) {
            $response = $this->getResponse();
            $response->setStatusCode(400);
            return new JsonModel(['error' => self::ID_REQUIRED_TEXT]);
        }

        $model = $this->getModelObject()->find($id);
        if (empty($model)) {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return new JsonModel(['error' => self::NOT_FOUND]);
        }

        if ($model->hasField('projectId')) {
            \Phprojekt::setCurrentProjectId($model->projectId);
        }

        if ($model instanceof \Phprojekt_ActiveRecord_Abstract) {
            $tmp = \Default_Helpers_Delete::delete($model);
            if ($tmp === false) {
                $message = \Phprojekt::getInstance()->translate(self::DELETE_FALSE_TEXT);
                $resultType = 'error';
            } else {
                $message = \Phprojekt::getInstance()->translate(self::DELETE_TRUE_TEXT);
                $resultType = 'success';
            }

            $return = array(
                'type' => $resultType,
                'message' => $message,
                'id' => $id
            );

            \Phprojekt_Converter_Json::echoConvert($return);
            return new JsonModel([]);
        } else {
            $response = $this->getResponse();
            $response->setStatusCode(404);
            return new JsonModel(['error' => self::NOT_FOUND]);
        }
    }

    /**
     * Deletes many items together
     *
     * @return JsonModel
     */
    public function jsonDeleteMultipleAction()
    {
        $ids = $this->params()->fromPost('ids', $this->params()->fromQuery('ids', null));
        $this->setCurrentProjectId();

        if (!empty($ids)) {
            $message = \Phprojekt::getInstance()->translate(self::DELETE_MULTIPLE_TRUE_TEXT);
            $showId = array();
            $model = $this->getModelObject();
            $idsArray = explode(",", $ids);

            if ($model instanceof \Phprojekt_ActiveRecord_Abstract) {
                foreach ($idsArray as $id) {
                    $model->find((int) $id);
                    \Default_Helpers_Delete::delete($model);
                    $showId[] = $id;
                }
            }

            $return = array(
                'type' => 'success',
                'message' => $message,
                'id' => implode(',', $showId)
            );

            \Phprojekt_Converter_Json::echoConvert($return);
        }

        return new JsonModel([]);
    }

    /**
     * Returns project-module && user-role-project permissions
     *
     * @return JsonModel
     */
    public function jsonGetModulesPermissionAction()
    {
        $projectId = (int) $this->params()->fromQuery('nodeId');
        $relation = new \Project_Models_ProjectModulePermissions();
        $modules = $relation->getProjectModulePermissionsById($projectId);

        if ($projectId == 0) {
            $data = array();
        } else {
            $allowedModules = array();
            $rights = new \Phprojekt_RoleRights($projectId);
            foreach ($modules['data'] as $module) {
                if ($module['inProject']) {
                    $tmpPermission = \Phprojekt_Acl::NONE;
                    if ($rights->hasRight('admin', $module['id'])) {
                        $tmpPermission = $tmpPermission | \Phprojekt_Acl::ADMIN;
                    }
                    if ($rights->hasRight('create', $module['id'])) {
                        $tmpPermission = $tmpPermission | \Phprojekt_Acl::CREATE;
                    }
                    if ($rights->hasRight('write', $module['id'])) {
                        $tmpPermission = $tmpPermission | \Phprojekt_Acl::WRITE;
                    }
                    if ($rights->hasRight('read', $module['id'])) {
                        $tmpPermission = $tmpPermission | \Phprojekt_Acl::READ;
                    }

                    if ($tmpPermission != \Phprojekt_Acl::NONE || \Phprojekt_Auth::isAdminUser()) {
                        $module['rights'] = \Phprojekt_Acl::convertBitmaskToArray($tmpPermission);
                        $allowedModules[] = $module;
                    }
                }
            }
            $data = $allowedModules;
        }

        \Phprojekt_Converter_Json::echoConvert($data);
        return new JsonModel([]);
    }

    /**
     * Returns all translated strings for the request language
     *
     * @return JsonModel
     */
    public function jsonGetTranslatedStringsAction()
    {
        $language = \Cleaner::sanitize('alpha', $this->params()->fromQuery('language', 'en'));
        $translate = \Phprojekt::getInstance()->getTranslate();

        \Phprojekt_Converter_Json::echoConvert($translate->getTranslatedStrings($language));
        return new JsonModel([]);
    }

    /**
     * Returns front configurations from configuration.php
     *
     * @return JsonModel
     */
    public function jsonGetConfigurationsAction()
    {
        $fronVars = \Phprojekt::getInstance()->getConfig()->front;
        $data = array();

        if (null !== $fronVars) {
            foreach ($fronVars as $key => $value) {
                $data[] = array(
                    'name' => $key,
                    'value' => $value
                );
            }
        }

        $user = \Phprojekt_Auth_Proxy::getEffectiveUser();
        $settings = $user->settings->fetchAll();

        $tutorialDisplayed = "false";
        foreach ($settings as $setting) {
            if ($setting->keyValue == "tutorialDisplayed") {
                $tutorialDisplayed = $setting->value;
                break;
            }
        }

        // Add CSRF token
        $sessionName = 'Phprojekt_CsrfToken';
        $csrfNamespace = new \Laminas\Session\Container($sessionName);
        $data[] = array(
            'name' => 'csrfToken',
            'value' => $csrfNamespace->token
        );

        $data[] = array(
            'name' => 'tutorialDisplayed',
            'value' => $tutorialDisplayed
        );

        \Phprojekt_Converter_Json::echoConvert($data);
        return new JsonModel([]);
    }

    /**
     * Returns the default extra actions
     *
     * @return JsonModel
     */
    public function jsonGetExtraActionsAction()
    {
        $actions = $this->getDefaultExtraActions();
        \Phprojekt_Converter_Json::echoConvert($actions);
        return new JsonModel([]);
    }

    /**
     * Returns frontend notification messages
     *
     * @return JsonModel
     */
    public function jsonGetFrontendMessageAction()
    {
        try {
            \Laminas\Session\SessionManager::writeClose(false);
        } catch (\Exception $error) {
            \Phprojekt::getInstance()->getLog()->debug('Error: ' . $error->getMessage());
        }

        $notification = new \Phprojekt_Notification_FrontendMessage();
        $userId = (int) \Phprojekt_Auth::getUserId();
        $data = $notification->getFrontendMessage($userId);

        $return = array("data" => $data);

        \Phprojekt_Converter_Json::echoConvert($return);
        return new JsonModel([]);
    }

    /**
     * Disables all frontend messages
     *
     * @return JsonModel
     */
    public function jsonDisableFrontendMessagesAction()
    {
        $notification = new \Phprojekt_Notification();

        try {
            $notification->disableFrontendMessages();
            $message = \Phprojekt::getInstance()->translate(self::DISABLE_FRONTEND_MESSAGES_TRUE_TEXT);
            $resultType = 'success';
        } catch (\Exception $error) {
            \Phprojekt::getInstance()->getLog()->debug('Error: ' . $error->getMessage());
            $message = \Phprojekt::getInstance()->translate(self::DISABLE_FRONTEND_MESSAGES_FALSE_TEXT);
            $resultType = 'error';
        }

        $return = array(
            'type' => $resultType,
            'message' => $message,
            'id' => 0
        );

        \Phprojekt_Converter_Json::echoConvert($return);
        return new JsonModel([]);
    }

    /**
     * Returns the ACL rights for all users of one item
     *
     * @return JsonModel
     */
    public function jsonGetUsersRightsAction()
    {
        $id = (int) $this->params()->fromQuery('id');
        $projectId = (int) $this->params()->fromQuery('nodeId');

        if (empty($id)) {
            if (empty($projectId)) {
                $record = $this->getModelObject();
            } else {
                $model = new \Project_Models_Project();
                $record = $model->find($projectId);
            }
        } else {
            $record = $this->getModelObject()->find($id);
        }

        if ($record instanceof \Phprojekt_Model_Interface) {
            \Phprojekt_Converter_Json::echoConvert($record->getUsersRights());
        } else {
            \Phprojekt_Converter_Json::echoConvert(array());
        }

        return new JsonModel([]);
    }

    /**
     * Sets the tutorialDisplayed setting
     *
     * @return JsonModel
     */
    public function jsonSetTutorialDisplayedAction()
    {
        $displayed = $this->params()->fromPost('displayed', $this->params()->fromQuery('displayed', ""));
        if ($displayed == "true") {
            $displayed = "true";
        } else {
            $displayed = "false";
        }

        $user = \Phprojekt_Auth_Proxy::getEffectiveUser();
        $settings = $user->settings->fetchAll();

        $found = false;
        foreach ($settings as $setting) {
            if ($setting->keyValue == "tutorialDisplayed") {
                $setting->value = $displayed;
                $setting->save();
                $found = true;
                break;
            }
        }

        if (!$found) {
            $record = $user->settings->create();
            $record->moduleId = 0;
            $record->keyValue = "tutorialDisplayed";
            $record->value = $displayed;
            $record->identifier = 'Core';
            $record->save();
        }

        \Phprojekt_Converter_Json::echoConvert(array());
        return new JsonModel([]);
    }

    /**
     * Export list to CSV
     *
     * @return \Laminas\Http\Response
     */
    public function csvListAction()
    {
        $projectId = (int) $this->params()->fromQuery('nodeId', null);
        $itemId = (int) $this->params()->fromQuery('id', null);
        $this->setCurrentProjectId();

        if (!empty($itemId)) {
            $where = sprintf('id = %d', (int) $itemId);
        } else if (!empty($projectId)) {
            $where = sprintf('project_id = %d', (int) $projectId);
        } else {
            $where = null;
        }

        $records = $this->getModelObject()->fetchAll($where);

        \Phprojekt_Converter_Csv::echoConvert($records, \Phprojekt_ModelInformation_Default::ORDERING_LIST);

        return $this->getResponse();
    }

    /**
     * Export multiple items to CSV
     *
     * @return \Laminas\Http\Response
     */
    public function csvExportMultipleAction()
    {
        $ids = $this->params()->fromQuery('ids', null);
        $this->setCurrentProjectId();

        if (!empty($ids)) {
            $idsArray = explode(",", $ids);
            $where = "id IN (";
            $i = 0;
            foreach ($idsArray as $id) {
                $i++;
                $where .= (int) $id;
                if ($i < count($idsArray)) {
                    $where .= ", ";
                }
            }
            $where .= ")";

            $records = $this->getModelObject()->fetchAll($where, null, 0, 0);
            \Phprojekt_Converter_Csv::echoConvert($records, \Phprojekt_ModelInformation_Default::ORDERING_LIST);
        }

        return $this->getResponse();
    }

    /**
     * Display file upload form
     *
     * @return ViewModel
     */
    public function fileFormAction()
    {
        list($model, $field, $itemId) = $this->_getFileParameters();

        $value = \Default_Helpers_Upload::initValue($model, $field, $itemId);

        return $this->_fileRenderView($itemId, $field, $value);
    }

    /**
     * Handle file upload
     *
     * @return ViewModel
     */
    public function fileUploadAction()
    {
        list($model, $field, $itemId) = $this->_getFileParameters();

        $errorMessage = null;
        try {
            $value = \Default_Helpers_Upload::uploadFile($model, $field, $itemId);
        } catch (\Exception $error) {
            $errorMessage = $error->getMessage();
            $value = \Default_Helpers_Upload::getFiles($model, $field);
        }

        return $this->_fileRenderView($itemId, $field, $value, $errorMessage);
    }

    /**
     * Download uploaded file
     *
     * @return \Laminas\Http\Response
     */
    public function fileDownloadAction()
    {
        $hash = $this->params()->fromQuery('hash', null);

        list($model, $field, $itemId) = $this->_getFileParameters();

        \Default_Helpers_Upload::downloadFile($model, $field, $itemId, $hash);

        return $this->getResponse();
    }

    /**
     * Check if user is authenticated
     *
     * @return void
     */
    public function checkAuthentication()
    {
        $isLoggedIn = \Phprojekt_Auth::isLoggedIn();

        if ($isLoggedIn) {
            $this->checkCsrfToken();
        } else {
            if ($this->params('action') != 'index') {
                $response = $this->getResponse();
                $response->setStatusCode(401);
                $response->getHeaders()->addHeaderLine('WWW-Authenticate', 'FormBased');
                return $response;
            }
        }
    }

    /**
     * Check CSRF token validity
     *
     * @return bool
     */
    public function checkCsrfToken()
    {
        $error = false;
        $controller = $this->params('controller');
        $action = $this->params('action');

        // Skip initial request and jsonGetConfigurations
        if ($controller == 'index' || $controller == 'Upgrade') {
            if ($action == 'index' || $action == 'jsonGetConfigurations') {
                return true;
            }
        }

        $sessionName = 'Phprojekt_CsrfToken';
        $csrfNamespace = new \Laminas\Session\Container($sessionName);

        if (!isset($csrfNamespace->token)) {
            $error = true;
        }

        $token = (string) $this->params()->fromPost('csrfToken', $this->params()->fromQuery('csrfToken', null));
        $headerToken = (string) $this->getRequest()->getHeader('X-CSRFToken', null);

        if (null === $token && null === $headerToken) {
            $error = true;
        }

        if (isset($csrfNamespace->token) && $csrfNamespace->token != $token && $csrfNamespace->token != $headerToken) {
            $error = true;
        }

        if ($error) {
            $response = $this->getResponse();
            $response->setStatusCode(403);
            $response->sendHeaders();
            exit;
        }

        return true;
    }

    /**
     * Get model name for this controller
     *
     * @return string
     */
    public function getModelName()
    {
        return $this->params('module', 'Default');
    }

    /**
     * Get module name for this controller
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->params('module', 'Default');
    }

    /**
     * Get model object instance
     *
     * @return \Phprojekt_Model_Interface
     */
    public function getModelObject()
    {
        $modelName = $this->getModelName();
        $moduleName = $this->getModuleName();
        $object = \Phprojekt_Loader::getModel($modelName, $moduleName);

        if (null === $object) {
            throw new \Exception('No Model object could be found');
        }

        return $object;
    }

    /**
     * Set parameters for model
     *
     * @return array
     */
    public function setParams()
    {
        $args = func_get_args();

        if (1 > count($args)) {
            throw new \InvalidArgumentException('Missing arguments in setParams function');
        }

        return $args[0];
    }

    /**
     * Keep current project ID in registry
     *
     * @return void
     */
    public function setCurrentProjectId()
    {
        $projectId = (int) $this->params()->fromQuery("nodeId", $this->params()->fromPost("nodeId", 0));

        if (empty($projectId)) {
            throw new \Exception(self::NODEID_REQUIRED_TEXT);
        } else {
            \Phprojekt::setCurrentProjectId($projectId);
        }
    }

    /**
     * Store current project ID
     *
     * @param int $projectId
     * @return void
     */
    protected function _storeCurrentProjectId($projectId)
    {
        if (empty($projectId)) {
            throw new \Exception(self::PROJECTID_REQUIRED_TEXT);
        } else {
            \Phprojekt::setCurrentProjectId($projectId);
        }
    }

    /**
     * Get filter where clause
     *
     * @param string $where
     * @return string
     */
    public function getFilterWhere($where = null)
    {
        $filters = $this->params()->fromQuery('filters', $this->params()->fromPost('filters', "[]"));

        $filters = json_decode($filters);

        if (!empty($filters)) {
            $filterClass = new \Phprojekt_Filter($this->getModelObject(), $where);
            foreach ($filters as $filter) {
                list($filterOperator, $filterField, $filterRule, $filterValue) = $filter;
                $filterOperator = \Cleaner::sanitize('alpha', $filterOperator, null);
                $filterField = \Cleaner::sanitize('alpha', $filterField, null);
                $filterRule = \Cleaner::sanitize('alpha', $filterRule, null);
                if (isset($filterOperator) && isset($filterField) && isset($filterRule) && isset($filterValue)) {
                    $filterClass->addFilter($filterField, $filterRule, $filterValue, $filterOperator);
                }
            }
            $where = $filterClass->getWhere();
        }

        return $where;
    }

    /**
     * Get default extra actions
     *
     * @return array
     */
    public function getDefaultExtraActions()
    {
        $delete = array(
            'target' => self::TARGET_ACTION_MULTIPLE,
            'action' => 'jsonDeleteMultiple',
            'label' => \Phprojekt::getInstance()->translate('Delete'),
            'mode' => self::MODE_ACTION_XHR,
            'class' => 'deleteOption'
        );

        $export = array(
            'target' => self::TARGET_ACTION_MULTIPLE,
            'action' => 'csvExportMultiple',
            'label' => \Phprojekt::getInstance()->translate('Export'),
            'mode' => self::MODE_ACTION_WINDOW,
            'class' => 'exportOption'
        );

        return array($delete, $export);
    }

    /**
     * Get file upload parameters
     *
     * @return array
     */
    private function _getFileParameters()
    {
        $model = $this->getModelObject();
        $field = \Cleaner::sanitize('alnum', $this->params()->fromQuery('field', $this->params()->fromPost('field', null)));
        $itemId = (int) $this->params()->fromQuery('id', $this->params()->fromPost('id', null));
        $this->setCurrentProjectId();

        return array($model, $field, $itemId);
    }

    /**
     * Render file upload view
     *
     * @param int $itemId
     * @param string $field
     * @param array $files
     * @param string $errorMessage
     * @return ViewModel
     */
    private function _fileRenderView($itemId, $field, $files, $errorMessage = null)
    {
        $response = $this->getResponse();
        $response->getHeaders()->clearHeaders();

        $sessionName = 'Phprojekt_CsrfToken';
        $csrfNamespace = new \Laminas\Session\Container($sessionName);
        $config = \Phprojekt::getInstance()->getConfig();
        $linkBegin = 'index.php/' . $this->getModuleName() . '/index/';
        $fieldId = $this->params()->fromQuery('fieldId', $this->params()->fromPost('fieldId', ''));

        // Build link data
        $linkData = '';
        $removeParams = array('module', 'controller', 'field', 'id', 'csrfToken', 'action', 'MAX_FILE_SIZE', 'order');
        $allParams = array_merge($this->params()->fromQuery(), $this->params()->fromPost());

        foreach ($allParams as $paramName => $paramValue) {
            if (!in_array($paramName, $removeParams)) {
                $linkData .= $paramName . '/' . $paramValue . '/';
            }
        }

        $model = $this->getModelObject();
        $model->find($itemId);

        $filesForView = array();
        $hasDownloadRight = $model->hasRight(\Phprojekt_Auth_Proxy::getEffectiveUserId(), \Phprojekt_Acl::DOWNLOAD);
        $hasWriteRight = $model->hasRight(\Phprojekt_Auth_Proxy::getEffectiveUserId(), \Phprojekt_Acl::WRITE);

        if (!empty($files)) {
            $i = 0;
            foreach ($files as $file) {
                $fileName = $file['name'];
                $fileHash = $file['md5'];
                $fileData = 'id/' . $itemId . '/field/' . $field . '/hash/' . $fileHash . '/csrfToken/' . $csrfNamespace->token;

                $filesForView[$i] = array(
                    'fileName' => $fileName,
                    'hash' => $fileHash
                );

                if ($hasDownloadRight) {
                    $filesForView[$i]['downloadLink'] = $linkBegin . 'fileDownload/' . $linkData . $fileData;
                }

                $fileinfo = \Default_Helpers_Upload::getInfosFromFile($file);
                $filesForView[$i]['size'] = $fileinfo['size'];
                $filesForView[$i]['ctime'] = $fileinfo['ctime'];

                $i++;
            }
        }

        if ($errorMessage !== null) {
            $filesForView[] = array();
        }

        return new ViewModel([
            'compressedDojo' => (bool) $config->compressedDojo,
            'formPath' => $linkBegin . 'fileUpload/' . $linkData,
            'downloadLink' => '',
            'fileName' => null,
            'itemId' => $itemId,
            'field' => $field,
            'fieldId' => $fieldId,
            'csrfToken' => $csrfNamespace->token,
            'maxUploadSize' => (isset($config->maxUploadSize)) ? (int) $config->maxUploadSize : \Phprojekt::DEFAULT_MAX_UPLOAD_SIZE,
            'disabled' => !$hasWriteRight,
            'files' => $filesForView,
            'errorMessage' => $errorMessage
        ]);
    }

    /**
     * Get validated date string parameter
     *
     * @param string $name
     * @param mixed $default
     * @return string
     */
    protected function _getDateStringParam($name, $default = null)
    {
        $value = $this->params()->fromQuery($name, $this->params()->fromPost($name, $default));

        if (!\Cleaner::validate('isoDate', $value)) {
            throw new \Phprojekt_Exception_InvalidParameter($name, $value, 'yyyy-mm-dd');
        }

        return $value;
    }
}
