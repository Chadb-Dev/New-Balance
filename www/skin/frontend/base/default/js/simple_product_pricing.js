/**
 * @category    Ayasoftware
 * @package     Ayasoftware_SimpleProductPricing
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */
/*
 Some of these override earlier varien/product.js methods, therefore
 varien/product.js must have been included prior to this file.
 some of these functions were initially written by Matt Dean ( http://organicinternet.co.uk/ )
 NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.
 */

var localCache = {
    /**
     * timeout for cache in millis
     * @type {number}
     */
    timeout: 30000,
    /** 
     * @type {{_: number, data: {}}}
     **/
    data: {},
    remove: function (url) {
        delete localCache.data[url];
    },
    exist: function (url) {
        return !!localCache.data[url] && ((new Date().getTime() - localCache.data[url]._) < localCache.timeout);
    },
    get: function (url) {
        return localCache.data[url].data;
    },
    set: function (url, cachedData, callback) {
        localCache.remove(url);
        localCache.data[url] = {
            _: new Date().getTime(),
            data: cachedData
        };
        if (Object.isFunction(callback)) {
            callback(cachedData);
        }
    }
};

Product.Config.prototype.getMatchingSimpleProduct = function () {
    var inScopeProductIds = this.getInScopeProductIds();
    if ((typeof inScopeProductIds != 'undefined') && (inScopeProductIds.length == 1)) {
        return inScopeProductIds[0];
    }
    return false;
};

/*
 Find products which are within consideration based on user's selection of
 config options so far
 Returns a normal array containing product ids
 allowedProducts is a normal numeric array containing product ids.
 childProducts is a hash keyed on product id
 optionalAllowedProducts lets you pass a set of products to restrict by,
 in addition to just using the ones already selected by the user
 */
Product.Config.prototype.getInScopeProductIds = function (optionalAllowedProducts) {
    if (typeof this.config.childProducts == 'undefined') {
        return;
    }
    var childProducts = this.config.childProducts;
    var allowedProducts = [];

    if ((typeof optionalAllowedProducts != 'undefined') && (optionalAllowedProducts.length > 0)) {
        allowedProducts = optionalAllowedProducts;
    }

    for (var s = 0, len = this.settings.length - 1; s <= len; s++) {
        if (this.settings[s].selectedIndex <= 0) {
            break;
        }
        var selected = this.settings[s].options[this.settings[s].selectedIndex];
        if (s == 0 && allowedProducts.length == 0) {
            allowedProducts = selected.config.allowedProducts;
        } else {
            allowedProducts = allowedProducts.intersect(selected.config.allowedProducts).uniq();
        }
    }

    //If we can't find any products (because nothing's been selected most likely)
    //then just use all product ids.
    if ((typeof allowedProducts == 'undefined') || (allowedProducts.length == 0)) {
        productIds = Object.keys(childProducts);
    } else {
        productIds = allowedProducts;
    }
    return productIds;
};


Product.Config.prototype.getProductIdOfCheapestProductInScope = function (priceType, optionalAllowedProducts) {
    if (typeof this.config.childProducts == 'undefined') {
        return;
    }
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
};


Product.Config.prototype.getProductIdOfMostExpensiveProductInScope = function (priceType, optionalAllowedProducts) {
    if (typeof this.config.childProducts == 'undefined') {
        return;
    }
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
};

//This triggers reload of price and other elements that can change
//once all options are selected
Product.Config.prototype.reloadSimplePrice = function () {
    var childProductId = this.getMatchingSimpleProduct();
    var inScopeProductIds = this.getInScopeProductIds();
    var childProducts = this.config.childProducts;
    if (this.config.configure) {
        childProductId = this.config.configure;
    }
    if (childProductId) {
        this.updateProductPrice(childProductId);
        if (this.config.updateProductSku) {
            this.updateProductSku(childProductId);
        }
        if (this.config.updateProductName) {
            this.updateProductName(childProductId);
        }
        if (this.config.updateShortDescription) {
            this.updateProductShortDescription(childProductId);
        }
        if (this.config.updateDescription) {
            this.updateProductDescription(childProductId);
        }
        if (this.config.productAttributes) {
            this.updateProductAttributes(childProductId);
        }
        if (this.config.customStockDisplay) {
            this.updateProductAvailability(childProductId);
        }
        this.showTierPricingBlock(childProductId);
        if (this.config.updateproductimage) {
            var has_image = childProducts[childProductId]["has_image"];
            if (has_image) {
                if (this.optionsUnselected() != false) {
                    this.updateProductImage(childProductId);
                }
            }
        }
        if (this.config.updateUrl) {
            history.pushState({}, '', childProducts[childProductId]["urlKey"]);
        }
    } else {
        var cheapestPid = this.getProductIdOfCheapestProductInScope("finalPrice");
        this.updateProductPrice(cheapestPid);
        this.showTierPricingBlock(false);
        if (this.optionsUnselected() == false) {
            if (this.config.updateProductName) {
                this.updateProductName(false);
            }
            if (this.config.updateProductSku) {
                this.updateProductSku(false);
            }
            if (this.config.updateShortDescription) {
                this.updateProductShortDescription(false);
            }
            if (this.config.updateDescription) {
                this.updateProductDescription(false);
            }
            if (this.config.productAttributes) {
                this.updateProductAttributes(false);
            }
            if (this.config.customStockDisplay) {
                this.updateProductAvailability(false);
            }
        }
        if (this.config.updateproductimage) {
            id = this.findFirstItemWithAvailableImages(inScopeProductIds);
            this.updateProductImage(id);
        }
        if (this.config.updateUrl) {
            history.pushState({}, '', this.config.cUrlKey);
        }
    }
};

/**
 * Find simple product in scope with available images
 * @param {type} inScopeProductIds
 * @returns int childProductId 
 */

Product.Config.prototype.findFirstItemWithAvailableImages = function (inScopeProductIds) {
    var childProducts = this.config.childProducts;
    for (var s = 0, len = inScopeProductIds.length - 1; s <= len; s++) {
        if (childProducts[inScopeProductIds[s]]["has_image"]) {
            return inScopeProductIds[s];
        }
    }
    return this.config.productId;
}
;
/*
 * Update simple product image. 
 * @param {type} productId
 * @returns {undefined}
 */


Product.Config.prototype.updateProductImage = function (productId) {
    var product_image_markup = this.config.product_image_markup;
    var zoomtype = this.config.zoomtype;
    var coUrl = this.config.ajaxBaseUrl + "image/?id=" + this.config.productId;
    if (productId) {
        coUrl = this.config.ajaxBaseUrl + "image/?id=" + productId;
    }
    if (typeof jQuery !== 'undefined') {
        jQuery(function ($) {
            function imageSwap(data) {
                $(product_image_markup).html(data.responseText);
                switch (zoomtype) {
                    case '2':
                        ProductMediaManager.init();
                        break;
                    case '4':
                        $('.cloud-zoom, .cloud-zoom-gallery').CloudZoom().live('hover', function () {
                            $('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
                        });
                        break;
                }
            }
            $.ajax({
                url: coUrl,
                cache: true,
                beforeSend: function () {
                    if (coUrl in localCache.data) {
                        imageSwap(localCache.get(coUrl));
                        return false;
                    }
                    return true;
                },
                complete: function (jqXHR, textStatus) {
                    localCache.set(coUrl, jqXHR, imageSwap);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        });
    }
};
Product.Config.prototype.updateProductName = function (productId) {
    var product_name_markup = this.config.product_name_markup;
    var productName = this.config.productName;
    if (productId && this.config.ProductNames[productId].ProductName) {
        productName = this.config.ProductNames[productId].ProductName;
    }
    $$(product_name_markup).each(function (el) {
        el.innerHTML = productName;
    });
};
Product.Config.prototype.updateProductSku = function (productId) {
    var pSku = this.config.productSku;
    var product_sku_markup = this.config.product_sku_markup;
    if (productId && this.config.ProductSkus[productId].ProductSku) {
        pSku = this.config.ProductSkus[productId].ProductSku;
    }
    $$(product_sku_markup).each(function (el) {
        el.innerHTML =  pSku;
    });
};
Product.Config.prototype.updateProductAvailability = function (productId) {
    var stockInfo = this.config.stockInfo;
    var product_customstockdisplay_markup = this.config.product_customstockdisplay_markup;
    var is_in_stock = false;
    var stockLabel = '';
    if (productId && stockInfo[productId]["stockLabel"]) {
        stockLabel = stockInfo[productId]["stockLabel"];
        stockQty = stockInfo[productId]["stockQty"];
        is_in_stock = stockInfo[productId]["is_in_stock"];
    }
    $$(product_customstockdisplay_markup + ' span').each(function (el) {
        if (is_in_stock) {
            $$(product_customstockdisplay_markup).each(function (es) {
                es.removeClassName('availability out-of-stock');
                es.addClassName('availability in-stock');
            });
            el.innerHTML = stockQty + '  ' + stockLabel;
        } else {
            $$(product_customstockdisplay_markup).each(function (ef) {
                ef.removeClassName('availability in-stock');
                ef.addClassName('availability out-of-stock');
            });
            el.innerHTML = stockLabel;
        }

    });
};
Product.Config.prototype.updateProductShortDescription = function (productId) {
    var shortDescription = this.config.shortDescription;
    var product_shortdescription_markup = this.config.product_shortdescription_markup;
    if (productId && this.config.shortDescriptions[productId].shortDescription) {
        shortDescription = this.config.shortDescriptions[productId].shortDescription;
    }
    $$(product_shortdescription_markup).each(function (el) {
        el.innerHTML = shortDescription;
    });
};
Product.Config.prototype.updateProductDescription = function (productId) {
    var description = this.config.description;
    var product_description_markup = this.config.product_description_markup;
    if (productId && this.config.Descriptions[productId].Description) {
        description = this.config.Descriptions[productId].Description;
    }
    $$(product_description_markup).each(function (el) {
        el.innerHTML = description;
    });
};
/*
 * updates product attributes 
 */
Product.Config.prototype.updateProductAttributes = function (productId) {
    var productAttributes = this.config.productAttributes;
    var product_attributes_markup = this.config.product_attributes_markup;
    var coUrl;
    if (productId) {
        coUrl = this.config.ajaxBaseUrl + "productattributes/?id=" + productId;
    } else {
        coUrl = this.config.ajaxBaseUrl + "productattributes/?id=" + this.config.productId;
    }
    new Ajax.Request(coUrl, {
        method: 'POST',
        onFailure: function (transport) {
            vJSONResp = transport.responseText;
            var JSON = eval("(" + vJSONResp + ")");
            updateStatus(JSON.code + ": " + JSON.message);
        },
        onSuccess: function (transport) {
            if (200 == transport.status) {
                productAttributes = transport.responseText;
                $$(product_attributes_markup).each(function (el) {
                    el.innerHTML = productAttributes;
                });
            }
        }
    });
};

String.prototype.replaceAll = function (search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'gm'), replacement);
};

/*
 * Update product price block 
 */
Product.Config.prototype.updateProductPrice = function (productId) {

    var product_price_markup = this.config.product_price_markup;
    var cProductId = this.config.productId;
    var priceFromLabel = '';
    var childProducts = this.config.childProducts;
    var inScopeProductIds = this.getInScopeProductIds();
    var inScopePrices = [];
    inScopeProductIds.forEach(function (id) {
        inScopePrices.push(childProducts[id]["finalPrice"]);
    });
    var inScopePricesLength = (inScopePrices.uniq()).length;
    if (typeof spConfig != "undefined" && inScopePricesLength > 1) {
        priceFromLabel = '<span class="label" id="configurable-price-from-' + cProductId + '"><span class="configurable-price-from-label">' + this.config.priceFromLabel + '</span></span>'
    }
    var coUrl = this.config.ajaxBaseUrl + "price/?id=" + productId;

    if (typeof jQuery !== 'undefined') {
        jQuery(function ($) {
            function priceUpdate(data) {
                parser = new DOMParser();
                priceDoc = parser.parseFromString(data.responseText, "text/html");
                var x = priceDoc.getElementsByClassName('price-box');
                productPrice = x[0].innerHTML;
                productPrice = productPrice.replaceAll("price-" + productId, "price-" + cProductId);
                $(product_price_markup).html(priceFromLabel + productPrice);
                var price = childProducts[productId]["price"];
                var finalPrice = childProducts[productId]["finalPrice"];
                optionsPrice.productPrice = finalPrice;
                optionsPrice.productOldPrice = price;
                optionsPrice.reload();
            }
            $.ajax({
                url: coUrl,
                cache: true,
                beforeSend: function () {
                    if (coUrl in localCache.data) {
                        priceUpdate(localCache.get(coUrl));
                        return false;
                    }
                    return true;
                },
                complete: function (jqXHR, textStatus) {
                    localCache.set(coUrl, jqXHR, priceUpdate);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                }
            });
        });

        return;
    }
};
//SPP: Forces the 'next' element to have it's optionLabels reloaded too
Product.Config.prototype.configureElement = function (element) {
    this.reloadOptionLabels(element);
    if (element.value) {
        this.state[element.config.id] = element.value;
        if (element.nextSetting) {
            element.nextSetting.disabled = false;
            this.fillSelect(element.nextSetting);
            this.reloadOptionLabels(element.nextSetting);
            this.resetChildren(element.nextSetting);
        }
    } else {
        this.resetChildren(element);
    }

    if (typeof this.config.childProducts == 'undefined') {
        this.reloadPrice();
    } else {
        this.reloadSimplePrice();
    }
};
//SPP: Changed logic to use absolute price ranges rather than price differentials
Product.Config.prototype.reloadOptionLabels = function (element) {
    if (typeof this.config.childProducts == 'undefined') {
        return Product.Config.prototype.$super = element;
    }
    var childProducts = this.config.childProducts;
    var stockInfo = this.config.stockInfo;

    //Don't update elements that have a selected option
    if (element.options[element.selectedIndex].config) {
        return;
    }
    for (var i = 0; i < element.options.length; i++) {
        var stock = '';
        if (element.options[i].config) {
            if (element.options[i].config.products) {
                var cheapestPid = this.getProductIdOfCheapestProductInScope("finalPrice", element.options[i].config.products);
                var mostExpensivePid = this.getProductIdOfMostExpensiveProductInScope("finalPrice", element.options[i].config.products);
            } else {
                var cheapestPid = this.getProductIdOfCheapestProductInScope("finalPrice", element.options[i].config.allowedProducts);
                var mostExpensivePid = this.getProductIdOfMostExpensiveProductInScope("finalPrice", element.options[i].config.allowedProducts);
            }
            var cheapestFinalPrice = childProducts[cheapestPid]["finalPrice"];
            var mostExpensiveFinalPrice = childProducts[mostExpensivePid]["finalPrice"];
            if (cheapestPid == mostExpensivePid) {
                if (!stockInfo[cheapestPid]["is_in_stock"]) {
                    stock = '( ' + stockInfo[cheapestPid]["stockLabel"] + ' )';
                } else {
                    if (this.config.instocklabel) {
                        stock = '(' + stockInfo[cheapestPid]["stockLabel"] + ' )';
                    }
                }
                if (this.config.disable_out_of_stock_option) {
                    if (!stockInfo[cheapestPid]["is_in_stock"]) {
                        element.options[i].disabled = true;
                    }
                }
            }
            var tierpricing = childProducts[mostExpensivePid]["tierpricing"];
            element.options[i].text = this.getSimpleProductPricingOptionLabel(element.options[i].config, cheapestFinalPrice, mostExpensiveFinalPrice, stock, tierpricing);
        }
    }
};
Product.Config.prototype.showTierPricingBlock = function (productId) {
    var coUrl;
    if (productId) {
        coUrl = this.config.ajaxBaseUrl + "co/?id=" + productId;
        $$('span.spp-please-wait').each(function (el) {
            el.setStyle({
                display: 'inline',
                opacity: 0.5
            });
        });

        new Ajax.Updater('sppTierPricingDiv', coUrl, {
            method: 'get',
            evalScripts: true,
            onComplete: function () {
                $$('span.spp-please-wait').each(function (el) {
                    el.hide();
                });
            }
        });
    } else {
        if ($('sppTierPricingDiv') !== undefined) {
            $('sppTierPricingDiv').innerHTML = '';
        }
    }
};
//SPP: Changed label formatting to show absolute price ranges rather than price differentials
Product.Config.prototype.getSimpleProductPricingOptionLabel = function (option, lowPrice, highPrice, stock, tierpricing) {

    var str = option.label;
    if (typeof stock == "undefined") {
        stock = '';
    }
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
};
//SPP: Refactored price calculations into separate function
Product.Config.prototype.getTaxPrices = function (price) {
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
};

/**
 * Check if at least 1 option is selected
 * @returns {Boolean}
 */
Product.Config.prototype.optionsUnselected = function () {
    var optionsSelected = false;
    $('product_addtocart_form').getElements().each(function (el) {
        if (el.type == 'select-one') {
            if (el.options[el.selectedIndex].config) {
                optionsSelected = true;
            }
        }
    });
    return optionsSelected;
};

//SPP: Forces price labels to be updated on load
//so that first select shows ranges from the start
document.observe("dom:loaded", function () {
    //Really only needs to be the first element that has configureElement set on it,
    //rather than all.
    if (typeof opConfig != "undefined") {
        opConfig.reloadPrice();
    }

    if (spConfig.config.preselect == false && spConfig.config.showfromprice ) {
        var priceFromLabel = '';
        var cProductId = spConfig.config.productId;
        var childProducts = spConfig.config.childProducts;
        var inScopeProductIds = spConfig.getInScopeProductIds();
        var inScopePrices = [];
        inScopeProductIds.forEach(function (id) {
            inScopePrices.push(childProducts[id]["finalPrice"]);
        });
        var inScopePricesLength = (inScopePrices.uniq()).length;
        if (typeof spConfig != "undefined" && inScopePricesLength > 1) {
            priceFromLabel = '<span class="label" id="configurable-price-from-' + cProductId + '"><span class="configurable-price-from-label">' + spConfig.config.priceFromLabel + '</span></span>'
        }
        if (typeof jQuery !== 'undefined') {
        jQuery(function ($) {
             $(spConfig.config.product_price_markup).prepend(priceFromLabel);
        });
    }
    
        
    }
    $('product_addtocart_form').getElements().each(function (el) {
        if (el.type == 'select-one') {
            if (el.options && (el.options.length > 1)) {
                el.options[0].selected = true;
                if (typeof spConfig != "undefined") {
                    spConfig.reloadOptionLabels(el);
                }
            }
        }
    });

});