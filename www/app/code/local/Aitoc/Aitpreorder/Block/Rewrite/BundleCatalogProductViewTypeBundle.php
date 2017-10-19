<?php
/**
* @copyright  Copyright (c) 2009 AITOC, Inc. 
*/
/* AITOC static rewrite inserts start */
/* $meta=%default,Aitoc_Aitdownloadablefiles% */
if(Mage::helper('core')->isModuleEnabled('Aitoc_Aitdownloadablefiles')){
    class Aitoc_Aitpreorder_Block_Rewrite_BundleCatalogProductViewTypeBundle_Aittmp extends Aitoc_Aitdownloadablefiles_Block_Rewrite_FrontBundleCatalogProductViewTypeBundle {} 
 }else{
    /* default extends start */
    class Aitoc_Aitpreorder_Block_Rewrite_BundleCatalogProductViewTypeBundle_Aittmp extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle {}
    /* default extends end */
}

/* AITOC static rewrite inserts end */
class Aitoc_Aitpreorder_Block_Rewrite_BundleCatalogProductViewTypeBundle extends Aitoc_Aitpreorder_Block_Rewrite_BundleCatalogProductViewTypeBundle_Aittmp                                          
{
    public function getJsonConfig()
    {
      
        $ins="\n
        bundle.reloadPrice = function() {
        var calculatedPrice = 0;
        var dispositionPrice = 0;
        var haveselectedpreorder=0;var descript='';
        for (var option in this.config.selected) {
            if (this.config.options[option]) {
                for (var i=0; i < this.config.selected[option].length; i++) {
                    var prices = this.selectionPrice(option, this.config.selected[option][i]);
                    calculatedPrice += Number(prices[0]);
                    
                    var selId=this.config.selected[option][i];
                    
                    if(this.config.options[option].selections[selId]['ispreorder']==1)
                    {
                        haveselectedpreorder=1;
                        descript=this.config.options[option].selections[selId]['preorderdescript'];
                    }                    
                    dispositionPrice += Number(prices[1]);
                }
            }
        }

      
        var masAvail=$$('.availability');
        var elmas=masAvail[0];
        var childEl = elmas.childElements();
        var spanEl = childEl[0]; 
        if(haveselectedpreorder==1)
        {
       
            if(spanEl.innerHTML!=descript)
            {
               
                bundle.st=spanEl.innerHTML;
            }
            spanEl.update(descript);
        }
        else
        {
            if(bundle.st)
            {
                spanEl.update(bundle.st);
            }            
        }
        
        if (this.config.specialPrice) {
            var newPrice = (calculatedPrice*this.config.specialPrice)/100;
            calculatedPrice = Math.min(newPrice, calculatedPrice);
        }

        optionsPrice.changePrice('bundle', calculatedPrice);
        optionsPrice.changePrice('nontaxable', dispositionPrice);
        optionsPrice.reload();
        return calculatedPrice;
    }";
    
    
    
    
      
      
        return parent::getJsonConfig().');'.$ins.'//';
        
    }
  
}            