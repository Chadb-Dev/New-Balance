<?php

class IWD_StoreLocator_Adminhtml_Iwd_Storelocator_ListController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/system/iwdall/storelocator');
    }

    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('storelocator/list')
            ->_addBreadcrumb(
                Mage::helper('storelocator')->__('Store Locator'), Mage::helper('storelocator')->__('Store Locator')
            )
            ->_addBreadcrumb(
                Mage::helper('storelocator')->__('Manage Store'), Mage::helper('storelocator')->__('Manage Store')
            );
        return $this;
    }


    public function indexAction()
    {

        $this->_title($this->__('Store Locator'))->_title($this->__('Manage Stores'));

        $this->_getSession()->addNotice(
            $this->__('<a href="https://docs.google.com/a/interiorwebdesign.com/document/d/12F4yvI2IHA7L-WfvYcuHTTDrLewxJLQrZc7lkL-iV74/edit" target="_blank">Store Locator Documentation</a>')
        );

        $this->loadLayout();
        $this->_setActiveMenu('slocator/list');
        $this->_addBreadcrumb(
            Mage::helper('storelocator')->__('Manage Store Locations'),
            Mage::helper('storelocator')->__('Store Locator')
        );
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Store Locator'))
            ->_title($this->__('Store'))
            ->_title($this->__('Manage Store'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('store_id');
        $model = Mage::getModel('storelocator/stores');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('storelocator')->__('This store no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Store'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('storelocator_store', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('storelocator')->__('Edit Store')
                    : Mage::helper('storelocator')->__('New Store'),
                $id ? Mage::helper('storelocator')->__('Edit Store')
                    : Mage::helper('storelocator')->__('New Store'));

        $this->renderLayout();

    }

    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('custom_theme_from', 'custom_theme_to'));
        return $data;
    }


    public function saveAction()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            $data = $this->_filterPostData($data);
            //init model and set data
            $model = Mage::getModel('storelocator/stores');
            $id = $this->getRequest()->getParam('entity_id');
            if ($id) {
                $model->load($id);
            }

            //ICON
            if (isset($data['icon']['delete']) && $data['icon']['delete'] == 1) {
                unset($data['icon']['delete']);
                $data['icon'] = '';
            }

            if (isset($data['icon']['value'])) {
                $data['icon'] = $data['icon']['value'];
            }

            //IMAGE
            if (isset($data['image']['delete']) && $data['image']['delete'] == 1) {
                unset($data['image']['delete']);
                $data['image'] = '';
            }

            if (isset($data['image']['value'])) {
                $data['image'] = $data['image']['value'];
            }

            //update stores
            //icon
            if (isset($_FILES['icon']['name']) and (file_exists($_FILES['icon']['tmp_name']))) {
                try {
                    $path = Mage::getBaseDir('media') . DS . 'iwd/storelocator/';
                    $uploader = new Varien_File_Uploader('icon');

                    $uploader->setAllowedExtensions(array('jpg', 'png', 'gif', 'jpeg'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $destFile = $path . $_FILES['icon']['name'];
                    $filename = $uploader->getNewFileName($destFile);
                    $result = $uploader->save($path, $filename);
                    $data['icon'] = 'iwd/storelocator/' . $result['file'];
                } catch (Exception $e) {
                    unset($data['icon']);
                    $this->_getSession()->addError($e->getMessage());
                    $this->_getSession()->setFormData($data);
                    $this->_redirect(
                        'adminhtml/iwd_storelocator_list/edit',
                        array('page_id' => $this->getRequest()->getParam('entity_id'))
                    );
                }
            }


            //image
            if (isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name']))) {
                try {
                    $path = Mage::getBaseDir('media') . DS . 'iwd/storelocator/';
                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg', 'png', 'gif', 'jpeg'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $destFile = $path . $_FILES['image']['name'];
                    $filename = $uploader->getNewFileName($destFile);
                    $result = $uploader->save($path, $filename);
                    $data['image'] = 'iwd/storelocator/' . $result['file'];
                } catch (Exception $e) {
                    unset($data['image']);
                    $this->_getSession()->addError($e->getMessage());
                    $this->_getSession()->setFormData($data);
                    $this->_redirect(
                        'adminhtml/iwd_storelocator_list/edit',
                        array('page_id' => $this->getRequest()->getParam('entity_id'))
                    );
                }
            }

            // try to save it
            try {
                $website = $data['website'];
                if (!preg_match('/http/i', $website)) {
                    $data['website'] = isset($website) && trim($website) != '' ? 'http://' . $website : $website;
                }

                $model->setData($data);
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('storelocator')->__('The store has been saved.')
                );

                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                //update stores
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        'adminhtml/iwd_storelocator_list/edit', array('store_id' => $model->getId(), '_current' => true)
                    );
                    return;
                }

                // go to grid
                $this->_redirect('adminhtml/iwd_storelocator_list/index');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('storelocator')->__('An error occurred while saving the store information.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect(
                'adminhtml/iwd_storelocator_list/edit',
                array('page_id' => $this->getRequest()->getParam('entity_id'))
            );
            return;
        }

        $this->_redirect('*/*/');
    }


    protected function _updateStores($stores, $id)
    {
        $collection = Mage::getModel('storelocator/store')->getCollection()->addFieldToFilter('locatorstore', array('eq' => $id));
        foreach ($collection as $item) {
            try {
                $item->delete();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        foreach ($stores as $store_id) {
            $model = Mage::getModel('storelocator/store');
            $model->setData('store_id', $store_id);
            $model->setData('locatorstore', $id);
            try {
                $model->save();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('store_id');
        if ($id) {
            try {
                // init model and delete
                $model = Mage::getModel('storelocator/stores');
                $model->load($id);

                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('storelocator')->__('The store has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                // go back to edit form
                $this->_redirect('*/*/edit', array('store_id' => $id));
                return;
            }
        }

        // display error message
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('storelocator')->__('Unable to find a store to delete.')
        );
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     *  Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'customers.csv';
        $content = $this->getLayout()->createBlock('storelocator/adminhtml_list_export')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

}