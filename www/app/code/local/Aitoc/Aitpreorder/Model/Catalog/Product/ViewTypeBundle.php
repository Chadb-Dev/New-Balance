<?php

class Aitoc_Aitpreorder_Model_Catalog_Product_ViewTypeBundle extends Aitoc_Aitpreorder_Model_Abstract 
{

    protected function _toHtml($html)
    {   
        $block   = $this->getBlock();
        $product = $block->getProduct();

        if ($block->getNameInLayout() == 'product.info.availability'
            && !$product->isAvailable()
            && $product->isSalable()
        ) {
            return str_replace(
                array(Mage::helper('catalog')->__('Out of stock'), 'class="availability out-of-stock"'),
                array(Mage::helper('aitpreorder')->__('Pre-Order'), 'class="availability in-stock"'),
                $html
            );
        }

        if ($block->getNameInLayout() != 'product.info.bundle.options') {
            return $html;
        }

        $swatch = "
            Product.Bundle.prototype.changeSelection = function(selection) {
                var parts = selection.id.split('-');
                if (this.config['options'][parts[2]].isMulti) {
                    selected = new Array();
                    if (selection.tagName == 'SELECT') {
                        for (var i = 0; i < selection.options.length; i++) {
                            if (selection.options[i].selected && selection.options[i].value != '') {
                                selected.push(selection.options[i].value);
                            }
                        }
                    } else if (selection.tagName == 'INPUT') {
                        selector = parts[0]+'-'+parts[1]+'-'+parts[2];
                        selections = $$('.'+selector);
                        for (var i = 0; i < selections.length; i++) {
                            if (selections[i].checked && selections[i].value != '') {
                                selected.push(selections[i].value);
                            }
                        }
                    }
                    this.config.selected[parts[2]] = selected;
                } else {
                    if (selection.value != '') {
                        this.config.selected[parts[2]] = new Array(selection.value);
                    } else {
                        this.config.selected[parts[2]] = new Array();
                    }
                    this.populateQty(parts[2], selection.value);
                    var tierPriceElement = $('bundle-option-' + parts[2] + '-tier-prices'),
                        tierPriceHtml = '';
                    if (selection.value != '' && this.config.options[parts[2]].selections[selection.value].customQty == 1) {
                        tierPriceHtml = this.config.options[parts[2]].selections[selection.value].tierPriceHtml;
                    }
                    tierPriceElement.update(tierPriceHtml);
                }
                this.reloadPrice();

                var preorder_title = 0;
                for (var selgroup in this.config.options) {
                    var current_selection_value = 0;
                    var preorder = 0;
                    if (!this.config.options[selgroup].isMulti) {
                        var length = document.getElementsByName('bundle_option[' + selgroup + ']').length;
                        if (length > 1) {
                            for (var check = 0; check < length; check++) {
                                current_selection_value = 0;
                                    var checked = document.getElementsByName('bundle_option[' + selgroup + ']')[check].checked;
                                    if (checked) {
                                        current_selection_value = document.getElementsByName('bundle_option[' + selgroup + ']')[check].value;
                                        break;    
                                    }
                            }
                        } else {
                            current_selection_value = document.getElementsByName('bundle_option[' + selgroup + ']')[0].value;
                        }

                    } else {
                        var length = document.getElementsByName('bundle_option[' + selgroup + '][]').length;   
                        for (var check = 0; check < length; check++) {
                                current_selection_value = 0;
                                    var checked = document.getElementsByName('bundle_option[' + selgroup + '][]')[check].checked;
                                    var preorder = this.config.options[selgroup].selections[document.getElementsByName('bundle_option[' + selgroup + '][]')[check].value].ispreorder;
                                    if (checked && preorder) {
                                        current_selection_value = document.getElementsByName('bundle_option[' + selgroup + '][]')[check].value;
                                        break;    
                                    }
                            }
                    }
                    if (current_selection_value != 0) {
                        preorder = this.config.options[selgroup].selections[current_selection_value].ispreorder;
                    }   else {
                        preorder = 0;
                    }

                    if (preorder == 1) {
                        preorder_title = 1;
                        break;
                    }
                }
                var status_title = button_title = '" . $this->__('Pre-Order') . "';
                if (!preorder_title) {
                    status_title = '" . $this->__('In Stock') . "';
                    button_title = '" . $this->__('Add to Cart') . "';
                }
                    
                var status = document.getElementsByClassName('availability')[0];
                status = status.getElementsByTagName('span');
                for (var stat in status) {
                    status[stat].innerHTML = status_title;
                }
                var buttons = document.getElementsByClassName('button btn-cart');
                for (var button in buttons) {
                    document.getElementsByClassName('button btn-cart')[button].innerHTML =
                        '<span><span>' + button_title + '</span></span>';
                }
            }
        ";

        return $html . '
        <input type="hidden" value="' . Mage::helper('aitpreorder')->__('Pre-Order') . '" id="saypreorder">
        <input type="hidden" value="' . Mage::helper('aitpreorder')->__('Add to cart') . '" id="sayaddtocart">
        <script type="text/javascript">' . $swatch . '</script><div id="canBePreorder"></div>';
    }
}
