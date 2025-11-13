<?php
/**
 * Compatibility shim for Zend_Controller_Action_Helper_Json
 */

class Zend_Controller_Action_Helper_Json extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Encode data to JSON and send response
     */
    public function sendJson($data, $sendNow = true, $keepLayouts = false)
    {
        $response = $this->getResponse();

        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent(json_encode($data));

        if ($sendNow) {
            $response->send();
            exit;
        }

        return $response;
    }

    /**
     * Encode JSON
     */
    public function encodeJson($data, $keepLayouts = false, $encodeData = true)
    {
        if ($encodeData) {
            $data = json_encode($data);
        }

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $response->setContent($data);

        return $data;
    }

    /**
     * Direct call
     */
    public function direct($data, $sendNow = true, $keepLayouts = false)
    {
        return $this->sendJson($data, $sendNow, $keepLayouts);
    }
}
