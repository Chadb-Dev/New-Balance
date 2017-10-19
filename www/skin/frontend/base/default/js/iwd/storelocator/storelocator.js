if (typeof(jQueryIWD) == "undefined") {
    jQueryIWD = jQuery.noConflict();
    $ji = jQuery.noConflict();
}

var options = {timeout: 5000, maximumAge: Infinity};
var IWD = IWD || {};

IWD.StoreLocator = {
    config: null,
    current: false,
    currentIndex: null,
    latitude: null,
    longitude: null,
    height: 230,
    page: 1,
    heightWindow: null,
    stopCheck: false,
    originalpath: null,
    prevScroll: null,
    stopLoad: false,

    showPosisiton: function (position) {
        IWD.StoreLocator.latitude = position.coords.latitude
        IWD.StoreLocator.longitude = position.coords.longitude
    },

    /** INIT EVENT TO LOAD PAGE BY SCROLL**/
    loadPage: function () {

        $ji(window).scroll(function () {
            IWD.StoreLocator.heightWindow = $ji(window).height();
            if (IWD.StoreLocator.stopLoad == true) {
                return;
            }

            if (IWD.StoreLocator.stopCheck == true) {
                return;
            }
            if ($ji('.back-to-top').length) {
                var position = $ji('.back-to-top').offset().top - IWD.StoreLocator.heightWindow;
                var scroll = $ji(window).scrollTop();
                if (scroll <= IWD.StoreLocator.prevScroll) {
                    return;
                }
                IWD.StoreLocator.prevScroll = scroll;

                var currentPosition = position - scroll;
                if (currentPosition <= 300) {
                    IWD.StoreLocator.stopCheck = true;
                    $ji('.item-pagination .pagination-loader .loader').show();
                    $ji('#storelocator-search').submit();
                }

            }

        });
    },

    showError: function (error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                console.log("User denied the request for Geolocation.");
                break;
            case error.POSITION_UNAVAILABLE:
                console.log("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                console.log("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                console.log("An unknown error occurred.");
                break;
        }
    },
    _alternativeGeoLocation: function () {
        jQuery.ajax({
            url: '//freegeoip.net/json/',
            type: 'POST',
            dataType: 'jsonp',
            success: function (location) {
                IWD.StoreLocator.latitude = location.latitude;
                IWD.StoreLocator.longitude = location.longitude;
                if (IWD.StoreLocator.config.firstload == true) {
                    $ji('#storelocator-search').submit();
                }
            }
        });
    },
    decorate: function () {

        setTimeout(function () {
            $ji('.search-result .item').removeAttr('style');
            $ji('.search-result .item').each(function () {
                var height = $ji(this).outerHeight(true);

                if (IWD.StoreLocator.height < height) {
                    IWD.StoreLocator.height = height;
                }
            });

            $ji('.search-result .item').each(function () {
                $ji(this).css('height', IWD.StoreLocator.height + 30);
            });
        }, 500)
    },


    reset: function () {
        var mapOptions = {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoom: 1,
            scrollwheel: IWD.StoreLocator.config.scrollWheel,
            scaleControl: IWD.StoreLocator.config.scaleControl,
            mapTypeControl: IWD.StoreLocator.config.mapTypeControl
        };


        $ji('#map-canvas').addClass('map-container');
        map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
        bounds = new google.maps.LatLngBounds();

        IWD.StoreLocator.stopCheck = false;
        IWD.StoreLocator.originalpath = null;
        IWD.StoreLocator.prevScroll = null;
        IWD.StoreLocator.stopLoad = false;
    },

    makeRaduisAround: function (lat, lng, radius) {
        var marker = new google.maps.Marker({
            map: map,
            position: new google.maps.LatLng(lat, lng),
            title: 'Current Location',
            icon: IWD.StoreLocator.config.searchMarker,
        });

        var circle = new google.maps.Circle({
            map: map,
            radius: radius * 1000,    // 10 miles in metres
            fillColor: IWD.StoreLocator.config.fillColor,
            fillOpacity: IWD.StoreLocator.config.fillOpacity,
            strokeColor: IWD.StoreLocator.config.strokeColor,
            strokeOpacity: IWD.StoreLocator.config.strokeOpacity,
            strokeWeight: IWD.StoreLocator.config.strokeWeight
        });

        circle.bindTo('center', marker, 'position');
    }
}


$ji(document).ready(function () {

    if (typeof(IWDStoreLocatorConfig) != "undefined") {
        IWD.StoreLocator.config = $ji.parseJSON(IWDStoreLocatorConfig);
    } else {
        console.log('Settings for store locator extensions does not exist.');
        return;
    }


    IWD.StoreLocator.loadPage();
    $ji(".storelocator select").chosen({disable_search_threshold: 10});

    if (location.protocol === 'https:') {
        if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(IWD.StoreLocator.showPosisiton, IWD.StoreLocator.showError, options);
        }
    if ("geolocation" in navigator) {
    } else {
        $ji('.btn-current-location').remove();
    }
    }
    else {
        IWD.StoreLocator._alternativeGeoLocation();
        if (IWD.StoreLocator.longitude == null || IWD.StoreLocator.latitude == "undefined") {
            $ji('.btn-current-location').remove();
        }
    }

    $ji('.btn-current-location').click(function (e) {
        e.preventDefault();
        // navigator.geolocation.getCurrentPosition(function (position) {
        //     IWD.StoreLocator.latitude = position.coords.latitude;
        //     IWD.StoreLocator.longitude = position.coords.longitude;
        // });
        IWD.StoreLocator.current = true;
        $ji('#storelocator-search').submit();
    });
    $ji('#storelocator-search button[type="submit"].button').click(function (e) {
        e.preventDefault();
        IWD.StoreLocator.current = false;
        $ji('#storelocator-search').submit();
    });
    $ji('#storelocator-search').submit(function (e) {
        if (IWD.StoreLocator.current == true) {
            IWD.StoreLocator._alternativeGeoLocation();
        }
        if (!map)
            var test = map ? map.mapTypeId : google.maps.MapTypeId.ROADMAP;
        //var test = map.mapTypeId;
        e.preventDefault();
        $ji('.loader-ajax').removeClass('hidden');
        var path = $ji(this).serialize();

        if (IWD.StoreLocator.current == true) {
            path += '&latitude=' + IWD.StoreLocator.latitude + '&longitude=' + IWD.StoreLocator.longitude + '&current=true';
        } else {
            path += '&latitude=' + IWD.StoreLocator.latitude + '&longitude=' + IWD.StoreLocator.longitude;
        }
        if (IWD.StoreLocator.stopCheck == true) {
            path = IWD.StoreLocator.originalpath + '&page=' + IWD.StoreLocator.page;
        } else {
            IWD.StoreLocator.reset();
            IWD.StoreLocator.originalpath = path;
        }

        if (test == 'hybrid') {
            test = google.maps.MapTypeId.HYBRID;
        } else {
            test = google.maps.MapTypeId.ROADMAP;
            //test = map.getMapTypeId();
        }


        $ji.post(IWD.StoreLocator.config.urlSearch, path, function (response) {
            IWD.StoreLocator.stopCheck = false;
            $ji('.item-pagination .pagination-loader .loader').hide();
            $ji('.loader-ajax').addClass('hidden');
            if (response.error == false) {

                if (response.action == "viewresult") {
                    IWD.StoreLocator.stopLoad = response.stopLoad;
                    if (response.pagination == true) {
                        $ji('.item-pagination').replaceWith(response.result)

                    } else {
                        $ji('#search-result').html(response.result);
                    }
                    if (response.maps.totalRecords == 0) {
                        var mapOptions = {
                            center: new google.maps.LatLng(0, 0),
                            zoom: 1,
                            mapTypeId: test,
                            scrollwheel: IWD.StoreLocator.config.scrollWheel,
                            scaleControl: IWD.StoreLocator.config.scaleControl,
                            mapTypeControl: IWD.StoreLocator.config.mapTypeControl
                        };
                        map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
                    } else {

                        var mapOptions = {
                            center: new google.maps.LatLng(0, 0),
                            zoom: 1,
                            mapTypeId: test,
                            scrollwheel: IWD.StoreLocator.config.scrollWheel,
                            scaleControl: IWD.StoreLocator.config.scaleControl,
                            mapTypeControl: IWD.StoreLocator.config.mapTypeControl
                        };
                        map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
                        initGMap(response.maps, response);
                    }
                }

            }
            ;
        }, 'json');

    });

    if (IWD.StoreLocator.config.firstload == true) {
        $ji('#map-canvas').addClass('map-container');
        var mapOptions = {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoom: 1,
            scrollwheel: IWD.StoreLocator.config.scrollWheel,
            scaleControl: IWD.StoreLocator.config.scaleControl,
            mapTypeControl: IWD.StoreLocator.config.mapTypeControl
        };

        map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
        bounds = new google.maps.LatLngBounds();
    }

    if (IWD.StoreLocator.config.firstload == true) {
        $ji('#storelocator-search').submit();
    }
});


var markers = [];
infoWindow = new Array();
var map = null;
var bounds = null;
function initGMap(mapsJson, response) {
    markers = [];

    if (mapsJson.totalRecords == 0) {
        return;
    }

    if (mapsJson.totalRecords == 1) {

        var myLatlng = new google.maps.LatLng(mapsJson.items[0].latitude, mapsJson.items[0].longitude);
        bounds.extend(myLatlng);

        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title: mapsJson.items[0].title,
            icon: IWD.StoreLocator.config.image,

        });

        infoWindow[0] = new InfoBox({
            content: mapsJson.items[0].content,
            disableAutoPan: false,
            maxWidth: 279,
            pixelOffset: new google.maps.Size(-139, -286),
            zIndex: null,
            boxStyle: {
                background: "none",
                opacity: 1,
                width: "279px",
                top: "-10px"
            },
            closeBoxMargin: "0 0 0 0",
            closeBoxURL: IWD.StoreLocator.config.closeButton,
            infoBoxClearance: new google.maps.Size(1, 1)
        });

        google.maps.event.addListener(marker, 'click', function () {
            infoWindow[0].open(map, this);
        });

        map.fitBounds(bounds);
        map.panToBounds(bounds);
        if (IWD.StoreLocator.config.highlight == 1) {
            var center = bounds.getCenter();
            IWD.StoreLocator.makeRaduisAround(center.lat(), center.lng(), $ji('#storelocator-search #radius').val());
        }
        IWD.StoreLocator.config.firstload = false;

        return;
    } else {

        var zoomChangeBoundsListener =
            google.maps.event.addListener(map, 'bounds_changed', function (event) {
                google.maps.event.removeListener(zoomChangeBoundsListener);
                map.setZoom(Math.min(IWD.StoreLocator.config.zoomData, map.getZoom()));
            });

        $ji.each(mapsJson.items, function (index) {
            if (this.latitude != '0' && this.longitude != '0') {

                var myLatlng = new google.maps.LatLng(this.latitude, this.longitude);
                bounds.extend(myLatlng);

                if (response.enable_group_store == 0) {
                    var marker = new google.maps.Marker({
                        position: myLatlng,
                        map: map,
                        title: this.title,
                        icon: IWD.StoreLocator.config.image
                    });
                } else {
                    var marker = new google.maps.Marker({
                        position: myLatlng,
                        title: this.title,
                        icon: IWD.StoreLocator.config.image
                    });
                    markers.push(marker);
                }

                infoWindow[index] = new InfoBox({
                    content: this.content,
                    disableAutoPan: false,
                    maxWidth: 279,
                    pixelOffset: new google.maps.Size(-139, -286),
                    zIndex: null,
                    boxStyle: {
                        background: "none",
                        opacity: 1,
                        width: "279px",
                        top: "-10px"
                    },
                    closeBoxMargin: "0 0 0 0",
                    closeBoxURL: IWD.StoreLocator.config.closeButton,
                    infoBoxClearance: new google.maps.Size(1, 1)
                });

                google.maps.event.addListener(marker, 'click', function () {
                    if (IWD.StoreLocator.currentIndex != null) {
                        infoWindow[IWD.StoreLocator.currentIndex].close();
                    }
                    infoWindow[index].open(map, this);
                    IWD.StoreLocator.currentIndex = index;
                });

                map.fitBounds(bounds);
                map.panToBounds(bounds);
            }
        });

        if (response.enable_group_store == 1) {
            var markerCluster = new MarkerClusterer(map, markers);
        }
        //console.log(response.enable_group_store);
        if (IWD.StoreLocator.config.firstload == false) {
            var center = bounds.getCenter();
            if (IWD.StoreLocator.config.highlight == 1) {
                if (response.lat == null || response.lng == null) {
                    //IWD.StoreLocator.makeRaduisAround(center.lat(), center.lng(), $ji('#storelocator-search #radius').val());
                } else {
                    IWD.StoreLocator.makeRaduisAround(response.lat, response.lng, $ji('#storelocator-search #radius').val());

                }
            }
        }
        IWD.StoreLocator.config.firstload = false;
    }

}