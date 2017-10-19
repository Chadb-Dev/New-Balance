<?php

class IWD_StoreLocator_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
    const MODULE = 'IWD_StoreLocator';
    const CONTROLLER = 'index';

    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $front->addRouter('storelocator', $this);
        return $this;
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $d = explode('/', $identifier, 3);
        if (!$request->isAjax() && $identifier != Mage::helper('storelocator')->getRoute()) {
            return false;
        }

        $controller = isset($d[1]) ? $d[1] : self::CONTROLLER;
        $action = isset($d[2]) ? $d[2] : 'index';
        $request->setModuleName(self::MODULE)
            ->setControllerName($controller)
            ->setActionName($action);

        $request->setAlias(
            Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $identifier
        );

        $front = $this->getFront();
        $controllerClassName = $this->_validateControllerClassName(self::MODULE, $controller);
        $controllerInstance = new $controllerClassName($request, $front->getResponse());
        $request->setDispatched(true);
        $controllerInstance->dispatch($action);
        return true;
    }

    protected function _validateControllerClassName($realModule, $controller)
    {
        $controllerFileName = $this->getControllerFileName($realModule, $controller);
        if (!$this->validateControllerFileName($controllerFileName)) {
            return false;
        }

        $controllerClassName = $this->getControllerClassName($realModule, $controller);
        if (!$controllerClassName) {
            return false;
        }

        if (!$this->_inludeControllerClass($controllerFileName, $controllerClassName)) {
            return false;
        }

        return $controllerClassName;
    }

    protected function _inludeControllerClass($controllerFileName, $controllerClassName)
    {
        if (!class_exists($controllerClassName, false)) {
            if (!file_exists($controllerFileName)) {
                return false;
            }

            include $controllerFileName;

            if (!class_exists($controllerClassName, false)) {
                throw Mage::exception(
                    'Mage_Core', Mage::helper('core')->__('Controller file was loaded but class does not exist')
                );
            }
        }

        return true;
    }

}