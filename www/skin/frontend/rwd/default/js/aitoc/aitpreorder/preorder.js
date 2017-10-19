/**************************** CONFIGURABLE PRODUCT **************************/
if(typeof Product != 'undefined'){
    Product.ConfigPreorder = Class.create(Product.Config);
    //Product.ConfigPreorder.prototype = {
    Product.ConfigPreorder.prototype.initialize = function(config,preorders){
            this.config     = config;
            this.taxConfig  = this.config.taxConfig;
            this.settings   = $$('.super-attribute-select');
            this.state      = new Hash();
            this.priceTemplate = new Template(this.config.template);
            this.prices     = config.prices;
            this.configpreorders=preorders;
            this.preorders=this.configpreorders.preorder;
            this.bufstock="";

            this.settings.each(function(element){
                Event.observe(element, 'change', this.configure.bind(this))
            }.bind(this));
            if(typeof Product.ConfigurableSwatches != 'undefined')
            {
                this.configureObservers = [];
                this.loadOptions();
            }
        };

    Product.ConfigPreorder.prototype.configure = function(event){
            var element = Event.element(event);
            this.configureElement(element);
        };

    Product.ConfigPreorder.prototype.configureElement = function(element) {
            if(this.settings.length<=0)
            {
                return;
            }

            var first=1;
            var resultProducts={};
            var i=0;

            for(i=0;i<=this.settings.length-1;i++)
            {
                var attributeId = this.settings[i].id.replace(/[a-z]*/, '');
                if((first==1)&&(this.settings[i].selectedIndex>0))
                {
                    var resultProducts=this.config['attributes'][attributeId]['options'][this.settings[i].selectedIndex-1]['products'];
                }
                else
                {
                    if(this.settings[i].selectedIndex>0)
                    {
                        resultProducts = this.getCorrectValue(resultProducts,i);
                    }
                }    
                first=0;
            }

            var el=$('saypreorder');
            var el2=$('sayaddtocart');
            var masAvail=$$('.availability');
            var elmas=masAvail[0];
            var spanEl = elmas.down('.value');
            if(typeof spanEl == 'undefined')
            {
                var childEl = elmas.childElements();
                var spanEl = childEl[0];
            }

            if(resultProducts.length==1)
            {
                var descr=this.preorders['descript'][resultProducts[0]];
                $('canBePreorder').update('<p class="required">*'+descr+'</p>');
                spanEl.update(descr);
                if (this.preorders[resultProducts[0]] == 1)
                    $$('.product-view .btn-cart > span > span').first().innerHTML = el.value;
                else
                    $$('.product-view .btn-cart > span > span').first().innerHTML = el2.value;
            }
            else 
            {
                $('canBePreorder').update('');  
                $$('.product-view .btn-cart > span > span').first().innerHTML = el2.value;
            }                
        };

    Product.ConfigPreorder.prototype.getCorrectValue = function(resultProducts, i) {
            var j=0;
            var attributeId = this.settings[i].id.replace(/[a-z]*/, '');

            for(j=0;j<=this.config['attributes'][attributeId]['options'].length-1;j++)
            {
                if(this.settings[i].value == this.config['attributes'][attributeId]['options'][j].id)
                {
                    return resultProducts.intersect(this.config['attributes'][attributeId]['options'][j]['products']);
                }
            }

            return {};
        };
}