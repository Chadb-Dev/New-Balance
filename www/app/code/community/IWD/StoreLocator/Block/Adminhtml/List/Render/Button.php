<?php

class IWD_StoreLocator_Block_Adminhtml_List_Render_Button extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        //You can write html for your button here
        $html = '<button>Fill automatically</button>';

        $html = array();
        $html[] = '<tr>';
        $html[] = '<td class="label"><label for="page_latitude"> </label></td>';
        $html[] = '<td class="value"><button type="button" id="load-map-data">Fill automatically</button></td></tr>';
        return implode('', $html);
    }


}