var spConfigIndex = 0;
var preSelected = false;
Event.observe(window, "load", function () {
    preSelect();
});
function findLabel(options, id) {
    for (var s = 0, len = options.length - 1; s <= len; s++) {
        if (options[s].id == id) {
            return options[s].label;
        }
    }
}
function preSelect() {
    if (spConfigIndex >= spConfig.settings.length) {
        preSelected = true;
        return;
    }
    var spi = spConfigIndex;
    var obj = spConfig.settings[spConfigIndex];
    if (spConfig.settings[spi].config.preselect) {
        var selectThis = spConfig.settings[spi].config.preselect;
        for (var spj = 0; spj < spConfig.settings[spi].options.length; ++spj) {
            if (spConfig.settings[spi].options[spj].value == selectThis || selectThis === 'one') {
                if (selectThis === 'one') {
                    spConfig.settings[spi].selectedIndex = 1;
                }
                else {
                    spConfig.settings[spi].selectedIndex = spj;
                }
                var attr_id = spConfig.settings[spi].config.id;
                var attr_code = spConfig.config.attributes[attr_id].code;
                
                $$("li#option" + spConfig.settings[spi].options[spj].value).each(function (swatch) {
                     swatch.addClassName('selected');
                });
                $$("#select_label_" + attr_code).each(function (select_label) {
                     select_label.innerHTML = findLabel(spConfig.settings[spi].config.options, spConfig.settings[spi].options[spj].value); 
                });
                
                Event.observe(obj, "change", function () {
                });
                var isIE9Plus = Prototype.Browser.IE && parseInt(navigator.userAgent.substring(navigator.userAgent.indexOf("MSIE") + 5)) >= 9;
                if (!isIE9Plus && document.createEventObject) {
                    var evt = document.createEventObject();
                    obj.fireEvent("onchange", evt);
                } else {
                    var evt = document.createEvent("HTMLEvents");
                    evt.initEvent("change", true, true);
                    !obj.dispatchEvent(evt);
                }
                break;
            }
        }
    }
    ++spConfigIndex;
    window.setTimeout("preSelect()", 1);
}