<?php

/*if (Mage::getConfig()->getModuleConfig('IWD_MasterTheme')->is('active', 'true')) {

    class IWD_StoreLocator_Block_Page_Html_Head_Conflicts extends IWD_MasterTheme_Block_Rewrite_Page_Html_Head
    {
    }

} else {

    class IWD_StoreLocator_Block_Page_Html_Head_Conflicts extends Mage_Page_Block_Html_Head
    {
    }
}*/

class IWD_StoreLocator_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{

    public function addJs($name, $params = "", $position = null)
    {
        $this->addItem('js', $name, $params, null, null, $position);

        return $this;
    }

    public function addItem($type, $name, $params = null, $if = null, $cond = null, $position = null)
    {
        if ($type === 'skin_css' && empty($params)) {
            $params = 'media="all"';
        }

        $this->_data['items'][$type . '/' . $name] = array(
            'type' => $type,
            'name' => $name,
            'params' => $params,
            'if' => $if,
            'cond' => $cond,
        );

        if ($position >= 0 && $position != null) {
            $this->sort($position, $type . '/' . $name);
        }

        return $this;
    }


    public function sort($position = null, $nameSort)
    {
        $list = $this->_data['items'];
        $itemSort = $list[$nameSort];
        $this->_data['items'] = array();
        $list2 = array();
        $i = 0;
        foreach ($list as $indexName => $item) {
            if ($i != $position) {
                $list2[$indexName] = $item;
            } else {
                $list2[$nameSort] = $itemSort;
                $list2[$indexName] = $item;
            }

            $i++;
        }

        $this->_data['items'] = $list2;

        return $this;
    }
}