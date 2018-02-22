/**
 * @category    Ayasoftware
 * @package     Ayasoftware_PreSelector
 * @copyright   2017 Ayasoftware (http://www.ayasoftware.com)
 * @author      EL HASSAN MATAR <support@ayasoftware.com>
 */

/**
 * De-Select radio-button on click event. 
 * @param {type} i
 * @returns {undefined}
 */
function removeAttr(i) {
    document.addEventListener('click', function(e){
   if (e.ctrlKey == true && 
       e.target.tagName == 'INPUT' && 
       e.target.type == "radio" && 
       e.target.checked == true) {
       e.target.checked = false;
   }
});
}