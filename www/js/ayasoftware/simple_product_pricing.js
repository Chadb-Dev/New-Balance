/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Varien
 * @package     js
 * @copyright   Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (typeof Product == 'undefined') {
    var Product = {};
}

/**************************** CONFIGURABLE PRODUCT **************************/
Product.Config = Class.create();
Product.Config.prototype = {
    initialize: function(config){
        this.config     = config;
        this.taxConfig  = this.config.taxConfig;
        if (config.containerId) {
            this.settings   = $$('#' + config.containerId + ' ' + '.super-attribute-select');
        } else {
            this.settings   = $$('.super-attribute-select');
        }
        this.state      = new Hash();
        this.priceTemplate = new Template(this.config.template);
        this.prices     = config.prices;

        // Set default values from config
        if (config.defaultValues) {
            this.values = config.defaultValues;
        }

        // Overwrite defaults by url
        var separatorIndex = window.location.href.indexOf('#');
        if (separatorIndex != -1) {
            var paramsStr = window.location.href.substr(separatorIndex+1);
            var urlValues = paramsStr.toQueryParams();
            if (!this.values) {
                this.values = {};
            }
            for (var i in urlValues) {
                this.values[i] = urlValues[i];
            }
        }

        // Overwrite defaults by inputs values if needed
        if (config.inputsInitialized) {
            this.values = {};
            this.settings.each(function(element) {
                if (element.value) {
                    var attributeId = element.id.replace(/[a-z]*/, '');
                    this.values[attributeId] = element.value;
                }
            }.bind(this));
        }

        // Put events to check select reloads
        this.settings.each(function(element){
            Event.observe(element, 'change', this.configure.bind(this))
        }.bind(this));

        // fill state
        this.settings.each(function(element){
            var attributeId = element.id.replace(/[a-z]*/, '');
            if(attributeId && this.config.attributes[attributeId]) {
                element.config = this.config.attributes[attributeId];
                element.attributeId = attributeId;
                this.state[attributeId] = false;
            }
        }.bind(this))

        // Init settings dropdown
        var childSettings = [];
        for(var i=this.settings.length-1;i>=0;i--){
            var prevSetting = this.settings[i-1] ? this.settings[i-1] : false;
            var nextSetting = this.settings[i+1] ? this.settings[i+1] : false;
            if (i == 0){
                this.fillSelect(this.settings[i])
            } else {
                this.settings[i].disabled = true;
            }
            $(this.settings[i]).childSettings = childSettings.clone();
            $(this.settings[i]).prevSetting   = prevSetting;
            $(this.settings[i]).nextSetting   = nextSetting;
            childSettings.push(this.settings[i]);
        }

        // Set values to inputs
        this.configureForValues();
        document.observe("dom:loaded", this.configureForValues.bind(this));
    },

    configureForValues: function () {
        if (this.values) {
            this.settings.each(function(element){
                var attributeId = element.attributeId;
                element.value = (typeof(this.values[attributeId]) == 'undefined')? '' : this.values[attributeId];
                this.configureElement(element);
            }.bind(this));
        }
    },

    configure: function(event){
        var element = Event.element(event);
        this.configureElement(element);
    },

    configureElement : function(element) {
        this.reloadOptionLabels(element);
        if(element.value){
            this.state[element.config.id] = element.value;
            if(element.nextSetting){
                element.nextSetting.disabled = false;
                this.fillSelect(element.nextSetting);
                this.resetChildren(element.nextSetting);
            }
        }
        else {
            this.resetChildren(element);
        }
        this.reloadPrice();
    },
    
    reloadOptionLabels : function (element) {
    // if childProducts is not defined, then product does not use simple
    // product pricing, use the default function. 
    if (typeof this.config.childProducts === 'undefined') {
        return Product.Config.prototype.$super = element;
    }
    var childProducts = this.config.childProducts;
    var stockInfo = this.config.stockInfo;
    var stock = '';
    //Don't update elements that have a selected option
    if (element.options[element.selectedIndex].config) {
        return;
    }
    for (var i = 0; i < element.options.length; i++) {
        if (element.options[i].config) {
            var cheapestPid = this.getProductIdOfCheapestProductInScope("finalPrice", element.options[i].config.allowedProducts);
            var mostExpensivePid = this.getProductIdOfMostExpensiveProductInScope("finalPrice", element.options[i].config.allowedProducts);
            var cheapestFinalPrice = childProducts[cheapestPid]["finalPrice"];
            var mostExpensiveFinalPrice = childProducts[mostExpensivePid]["finalPrice"];
            if (cheapestPid == mostExpensivePid) {
                if (stockInfo[cheapestPid]["stockLabel"] != '') {
                    stock = '( ' + stockInfo[cheapestPid]["stockLabel"] + ' )';
                }
            }
            if (this.config.showOutOfStock) {
                if (this.config.disable_out_of_stock_option) {
                    if (!stockInfo[cheapestPid]["is_in_stock"]) {
                        if (cheapestPid == mostExpensivePid) {
                            element.options[i].disabled = true;
                            stock = '( ' + stockInfo[cheapestPid]["stockLabel"] + ' )';
                        }
                    }
                }
            }
            var tierpricing = childProducts[mostExpensivePid]["tierpricing"];
            element.options[i].text = this.getOptionLabel(element.options[i].config, cheapestFinalPrice, mostExpensiveFinalPrice, stock, tierpricing);
        }
    }
},

    _reloadOptionLabels: function(element){
        var selectedPrice;
        if(element.options[element.selectedIndex].config && !this.config.stablePrices){
            selectedPrice = parseFloat(element.options[element.selectedIndex].config.price)
        }
        else{
            selectedPrice = 0;
        }
        for(var i=0;i<element.options.length;i++){
            if(element.options[i].config){
                element.options[i].text = this.getOptionLabel(element.options[i].config, element.options[i].config.price-selectedPrice);
            }
        }
    },
    getOptionLabel : function (option, lowPrice, highPrice, stock, tierpricing) {

            var str = option.label;
    if (tierpricing > 0 && tierpricing < lowPrice) {
        var tierpricinglowestprice = ': As low as (' + this.formatPrice(tierpricing, false) + ')';
    } else {
        var tierpricinglowestprice = '';
    }
    if (!this.config.showPriceRangesInOptions) {
        return str;
    }

    if (!this.config.showOutOfStock) {
        stock = '';
    }

    lowPrices = this.getTaxPrices(lowPrice);
    highPrices = this.getTaxPrices(highPrice);
    if (this.config.hideprices) {
        if (this.config.showOutOfStock) {
            return str + '  ' + stock + '  ';
        } else {
            return str;
        }
    }

    var to = ' ' + this.config.rangeToLabel + ' ';
    var separator = ': ( ';
    if (lowPrice && highPrice) {
        if (this.config.showfromprice) {
            this.config.priceFromLabel = this.config.priceFromLabel; //'From: ';
        }
        if (lowPrice != highPrice) {
            if (this.taxConfig.showBothPrices) {
                str += separator + this.formatPrice(lowPrices[2], false) + ' (' + this.formatPrice(lowPrices[1], false) + ' ' + this.taxConfig.inclTaxTitle + ')';
                str += to + this.formatPrice(highPrices[2], false) + ' (' + this.formatPrice(highPrices[1], false) + ' ' + this.taxConfig.inclTaxTitle + ')';
                str += " ) ";
            } else {
                str += separator + this.formatPrice(lowPrices[0], false);
                str += to + this.formatPrice(highPrices[0], false);
                str += " ) ";
            }
        } else {

            if (this.taxConfig.showBothPrices) {
                str += separator + this.formatPrice(lowPrices[2], false) + ' (' + this.formatPrice(lowPrices[1], false) + ' ' + this.taxConfig.inclTaxTitle + ')';
                str += " ) ";
                str += stock;
                str += tierpricinglowestprice;
            } else {
                if (tierpricing == 0) {
                    str += separator + this.formatPrice(lowPrices[0], false);
                    str += " ) ";
                }
                str += tierpricinglowestprice;
                str += '  ' + stock;
            }
        }
    }
    return str;
        },
        getTaxPrices : function (price) {
    var price = parseFloat(price);
    if (this.taxConfig.includeTax) {
        var tax = price / (100 + this.taxConfig.defaultTax) * this.taxConfig.defaultTax;
        var excl = price - tax;
        var incl = excl * (1 + (this.taxConfig.currentTax / 100));
    } else {
        var tax = price * (this.taxConfig.currentTax / 100);
        var excl = price;
        var incl = excl + tax;
    }
    if (this.taxConfig.showIncludeTax || this.taxConfig.showBothPrices) {
        price = incl;
    } else {
        price = excl;
    }

    return [price, incl, excl];
},
    getInScopeProductIds : function (optionalAllowedProducts) {
            var productIds;
            var childProducts = this.config.childProducts; 
            //var childProducts = this.options.spConfig.childProducts;
            var allowedProducts = [];
            if ((typeof optionalAllowedProducts != 'undefined') && (optionalAllowedProducts.length > 0)) {
                allowedProducts = optionalAllowedProducts;
            }

            if ((typeof allowedProducts == 'undefined') || (allowedProducts.length == 0)) {
                productIds = Object.keys(childProducts);
            } else {
                productIds = allowedProducts;
            }

            return productIds;
        },
        getProductIdOfCheapestProductInScope: function (priceType, optionalAllowedProducts) {
            var childProducts = this.config.childProducts;
            var productIds = this.getInScopeProductIds(optionalAllowedProducts);

            var minPrice = Infinity;
            var lowestPricedProdId = false;

            //Get lowest price from product ids.
            for (var x = 0, len = productIds.length; x < len; ++x) {
                var thisPrice = Number(childProducts[productIds[x]][priceType]);
                if (thisPrice < minPrice) {
                    minPrice = thisPrice;
                    lowestPricedProdId = productIds[x];
                }
            }

            return lowestPricedProdId;
        },
        getProductIdOfMostExpensiveProductInScope : function (priceType, optionalAllowedProducts) {

            var childProducts = this.config.childProducts;
            var productIds = this.getInScopeProductIds(optionalAllowedProducts);

            var maxPrice = 0;
            var highestPricedProdId = false;

            //Get highest price from product ids.
            for (var x = 0, len = productIds.length; x < len; ++x) {
                var thisPrice = Number(childProducts[productIds[x]][priceType]);
                if (thisPrice >= maxPrice) {
                    maxPrice = thisPrice;
                    highestPricedProdId = productIds[x];
                }
            }
            return highestPricedProdId;
        },

    resetChildren : function(element){
        if(element.childSettings) {
            for(var i=0;i<element.childSettings.length;i++){
                element.childSettings[i].selectedIndex = 0;
                element.childSettings[i].disabled = true;
                if(element.config){
                    this.state[element.config.id] = false;
                }
            }
        }
    },

    fillSelect: function(element){
        var attributeId = element.id.replace(/[a-z]*/, '');
        var options = this.getAttributeOptions(attributeId);
        this.clearSelect(element);
        element.options[0] = new Option('', '');
        element.options[0].innerHTML = this.config.chooseText;

        var prevConfig = false;
        if(element.prevSetting){
            prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
        }

        if(options) {
            var index = 1;
            for(var i=0;i<options.length;i++){
                var allowedProducts = [];
                if(prevConfig) {
                    for(var j=0;j<options[i].products.length;j++){
                        if(prevConfig.config.allowedProducts
                            && prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1){
                            allowedProducts.push(options[i].products[j]);
                        }
                    }
                } else {
                    allowedProducts = options[i].products.clone();
                }

                if(allowedProducts.size()>0){
                    options[i].allowedProducts = allowedProducts;
                     var childProducts = this.config.childProducts;
                        var stockInfo = this.config.stockInfo;
                        var cheapestPid = this.getProductIdOfCheapestProductInScope("finalPrice", allowedProducts);
                        var mostExpensivePid = this.getProductIdOfMostExpensiveProductInScope("finalPrice", allowedProducts);
                        var cheapestFinalPrice = childProducts[cheapestPid]["finalPrice"];
                        var mostExpensiveFinalPrice = childProducts[mostExpensivePid]["finalPrice"];
                    var stock = '';
                        if (cheapestPid == mostExpensivePid) {
                            if (stockInfo[cheapestPid]["stockLabel"] != '') {
                                stock = '( ' + stockInfo[cheapestPid]["stockLabel"] + ' )';
                            }
                        }

                        if (!stockInfo[cheapestPid]["is_in_stock"]) {
                            if (cheapestPid == mostExpensivePid) {
                                stock = '( ' + stockInfo[cheapestPid]["stockLabel"] + ' )';
                            }
                        }
                        var tierpricing = childProducts[mostExpensivePid]["tierpricing"];
                    var optionLabel = this.getOptionLabel(options[i], cheapestFinalPrice, mostExpensiveFinalPrice, stock, tierpricing);
                    element.options[index] = new Option(optionLabel, options[i].id);
                    if (typeof options[i].price != 'undefined') {
                        element.options[index].setAttribute('price', options[i].price);
                    }
                    element.options[index].config = options[i];
                    index++;
                }
            }
        }
    },

    _getOptionLabel: function(option, price){
        var price = parseFloat(price);
        if (this.taxConfig.includeTax) {
            var tax = price / (100 + this.taxConfig.defaultTax) * this.taxConfig.defaultTax;
            var excl = price - tax;
            var incl = excl*(1+(this.taxConfig.currentTax/100));
        } else {
            var tax = price * (this.taxConfig.currentTax / 100);
            var excl = price;
            var incl = excl + tax;
        }

        if (this.taxConfig.showIncludeTax || this.taxConfig.showBothPrices) {
            price = incl;
        } else {
            price = excl;
        }

        var str = option.label;
        if(price){
            if (this.taxConfig.showBothPrices) {
                str+= ' ' + this.formatPrice(excl, true) + ' (' + this.formatPrice(price, true) + ' ' + this.taxConfig.inclTaxTitle + ')';
            } else {
                str+= ' ' + this.formatPrice(price, true);
            }
        }
        return str;
    },

    formatPrice: function(price, showSign){
        var str = '';
        price = parseFloat(price);
        if(showSign){
            if(price<0){
                str+= '-';
                price = -price;
            }
            else{
                str+= '+';
            }
        }

        var roundedPrice = (Math.round(price*100)/100).toString();

        if (this.prices && this.prices[roundedPrice]) {
            str+= this.prices[roundedPrice];
        }
        else {
            str+= this.priceTemplate.evaluate({price:price.toFixed(2)});
        }
        return str;
    },

    clearSelect: function(element){
        for(var i=element.options.length-1;i>=0;i--){
            element.remove(i);
        }
    },

    getAttributeOptions: function(attributeId){
        if(this.config.attributes[attributeId]){
            return this.config.attributes[attributeId].options;
        }
    },

    reloadPrice: function(){
        if (this.config.disablePriceReload) {
            return;
        }
        var price    = 0;
        var oldPrice = 0;
        for(var i=this.settings.length-1;i>=0;i--){
            var selected = this.settings[i].options[this.settings[i].selectedIndex];
            if(selected.config){
                price    += parseFloat(selected.config.price);
                oldPrice += parseFloat(selected.config.oldPrice);
            }
        }

        //optionsPrice.changePrice('config', {'price': price, 'oldPrice': oldPrice});
        //optionsPrice.reload();
        return price;

        if($('product-price-'+this.config.productId)){
            $('product-price-'+this.config.productId).innerHTML = price;
        }
        this.reloadOldPrice();
    },

    reloadOldPrice: function(){
        if (this.config.disablePriceReload) {
            return;
        }
        if ($('old-price-'+this.config.productId)) {

            var price = parseFloat(this.config.oldPrice);
            for(var i=this.settings.length-1;i>=0;i--){
                var selected = this.settings[i].options[this.settings[i].selectedIndex];
                if(selected.config){
                    price+= parseFloat(selected.config.price);
                }
            }
            if (price < 0)
                price = 0;
            price = this.formatPrice(price);

            if($('old-price-'+this.config.productId)){
                $('old-price-'+this.config.productId).innerHTML = price;
            }

        }
    }
}
