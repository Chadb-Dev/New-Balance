<?php

class Aitoc_Aitpreorder_Model_Catalog_Product_ViewTypeConfigurable extends Aitoc_Aitpreorder_Model_Abstract {

    protected function _toHtml($html)
    {
        if ($this->getBlock()->getNameInLayout() == 'product.info.options.configurable') {
            $swatch = '';
            if(version_compare(Mage::getVersion(), '1.9.1.0', '>=')
                && Mage::helper('configurableswatches')->isEnabled())
            {
                $swatch = '
                    Product.ConfigurableSwatches.prototype.checkStockStatus = function() {
                    var inStock = true;
                    var isPreorder = true;
                    var checkOptions = arguments.length ? arguments[0] : this._E.activeConfigurableOptions;
                    // Set out of stock if any selected item is not enabled
                    checkOptions.each( function(selectedOpt) {
                        if (!selectedOpt._f.enabled) {
                            inStock = false;
                            throw $break;
                        }
                    });

                    var first=1;
                    var resultProducts=[];
                    var i=0;
                    for(i=0;i<=checkOptions.length-1;i++)
                    {
                        if(first==1)
                        {
                            var resultProducts=checkOptions[i][\'products\'];
                        }
                        else
                        {
                            var j=0;
                            var resultProductsTemp=[];
                            for(j=0;j<=checkOptions[i][\'products\'].length-1;j++)
                            {
                                if(resultProducts.indexOf(checkOptions[i][\'products\'][j]) != -1)
                                {
                                    resultProductsTemp.push(checkOptions[i][\'products\'][j]);
                                }
                            }
                            resultProducts = resultProductsTemp;
                        }
                        first=0;
                    }

                    if(resultProducts.length==1)
                    {
                        var descr = spConfig.preorders[\'descript\'][resultProducts[0]];
                        $(\'canBePreorder\').update(\'<p class="required">*\'+descr+\'</p>\');
                        if (spConfig.preorders[resultProducts[0]] == 1)
                        {
                            var elvalue=$(\'saypreorder\').value;

                            this._E.availability.each(function(el) {
                                var el = $(el);
                                el.addClassName(\'in-stock\').removeClassName(\'out-of-stock\');
                                el.select(\'span\').invoke(\'update\', Translator.translate(descr));
                            });

                            this._E.cartBtn.btn.each(function(el, index) {
                                var el = $(el);
                                el.disabled = false;
                                el.removeClassName(\'out-of-stock\');
                                el.writeAttribute(\'onclick\', this._E.cartBtn.onclick);
                                el.title = elvalue;
                                el.select(\'span span\').invoke(\'update\', elvalue);
                            }.bind(this));
                            return true;
                        }
                    }
                    this.setStockStatus( inStock );
                }';
            }
            $preorderString = '<input type="hidden" value="' . __('Pre-Order') . '" id="saypreorder"><input type="hidden" value="' . __('Add to Cart') . '" id="sayaddtocart"><script type="text/javascript">
            '.$swatch.'
            var spConfig = new Product.ConfigPreorder(' . $this->getBlock()->getJsonConfig() . ',{"preorder":' . $this->getJsonConfigWithPreorder() . '});
            </script><div id="canBePreorder"></div>';
            if(version_compare(Mage::getVersion(), '1.9.1.0', '>=')
                && Mage::helper('configurableswatches')->isEnabled())
            {
                return preg_replace('/<script type="text\/javascript">(.|\n)*var spConfig = new Product.Config\((.)*(\n)(.)*<\/script>(\n)?/', $preorderString, $html, 1);
            }

            return $html . $preorderString;
        }

        if ($this->getBlock()->getNameInLayout() == 'product.info.availability') {
            $product = $this->getBlock()->getProduct();
            if(!$product->isAvailable() && $product->isSalable())
            {
                return str_replace(Mage::helper('catalog')->__('Out of stock'), Mage::helper('aitpreorder')->__('Pre-Order'), $html);
            }
        }

        return $html;
    }

    public function getJsonConfigWithPreorder()
    {
        foreach ($this->getBlock()->getAllowProducts() as $product) {
            if ($product->getPreorder() == '1') {
                $options[$product->getId()] = $product->getPreorder();
                if ($product->getData('is_in_stock')) {
                    $options['descript'][$product->getId()] = $product->getPreorderdescript();
                    if(version_compare(Mage::getVersion(), '1.9.1.0', '>='))
                    {
                        $options['descript'][$product->getId()] = Mage::getResourceSingleton('catalog/product')
                            ->getAttributeRawValue($product->getId(), 'preorderdescript', Mage::app()->getStore());
                    }
                    if ($options['descript'][$product->getId()] == '') {
                        $options['descript'][$product->getId()] = __('Pre-Order');
                    }
                } else {
                    $options['descript'][$product->getId()] = __('not Available');
                }
            } else {
                $options[$product->getId()] = 0;
                if ($product->getData('is_in_stock')) {
                    // $options['descript'][$product->getId()]=$product->getPreorderdescript();
                    // if($options['descript'][$product->getId()]=='')
                    {
                        $options['descript'][$product->getId()] = __('In stock');
                    }
                } else {
                    $options['descript'][$product->getId()] = __('Out stock');
                }
            }
        }
        return Mage::helper('core')->jsonEncode($options);
    }

}