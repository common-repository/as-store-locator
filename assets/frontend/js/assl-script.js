/*!
 * Copyright (c) 2016. Developed by Alfio Salanitri | Web: www.alfiosalanitri.it | Support: dev@alfiosalanitri.it
 */
var defaultStoreVar = document.getElementById('assl-store-default-var');
if (defaultStoreVar !== null) {
    var storeSettingsOption = jQuery(defaultStoreVar).data("storesettings");
    var mapstyle = jQuery(defaultStoreVar).data("mapstyle");
    var centerLat = storeSettingsOption.centerlat;
    var centerLng = storeSettingsOption.centerlng;
    var actualPosition = storeSettingsOption.defaultposition;
    var yourPos = storeSettingsOption.yourpos;
    var geoProbl = storeSettingsOption.geoprobl;
    var geoBrowser = storeSettingsOption.geobrowser;
    var ClusterActivation = storeSettingsOption.clusteractivation;
    var mapGlobalOptions = {
        zoom: 14,
        center: new google.maps.LatLng(centerLat, centerLng),
        disableDefaultUI: storeSettingsOption.disableui,
        mapTypeControl: storeSettingsOption.maptype,
        streetViewControl: storeSettingsOption.streetview,
        styles: mapstyle,
        scrollwheel: storeSettingsOption.scrollwheel,
        backgroundColor: 'none'
    };
}
var AsslMainFunction = {
    Inizialize: function () {
        var divID = 'assl-gmap';
        if (document.getElementById(divID) !== null) {
            this.Go(divID);
            jQuery(document).on("click", ".assl-reset-filter", function (ev) {
                ev.preventDefault();
                var asslContainer = jQuery('#' + divID).closest("#assl-store-locator-container");
                asslContainer.find('#toggleAllStore').prop('checked', true);
                asslContainer.find('.assl-cat').prop('checked', true);
                asslContainer.find('input.radius').prop('checked', false);
                jQuery("#assl-radius").data("radius", 0);
                jQuery("#assl-no-results").hide();
                AsslMainFunction.Go(divID);
            });
        }
    }, Go: function (elm) {
        jQuery.ajax({
            cache: false,
            url: assl_script_ajax.ajax_url,
            type: "POST",
            dataType: "json",
            data: ({action: 'getStores'}),
            success: function (data) {
                AsslGoogleMap.loadMap(elm, data.mappa);
                jQuery("#assl-list").empty().html("<ul>" + data.lista + "</ul>");
                if (data.lista == '') {
                    jQuery("#assl-no-results").show();
                }
                if (data.totstores === null) {
                    jQuery(".totStoreFound").empty().html("0");
                } else {
                    jQuery(".totStoreFound").empty().html(data.totstores);
                }
            },
            complete: function () {
                AsslMainFunction.AfterAjax();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('Spiacente, qualcosa è andato storto durante il caricamento.');
            }
        });
        this.ShowFilterContainer();
        this.SelectUnselectAll();
        this.TypeFilter();
        this.RadiusFilter();
        this.RemoveRadiusFilter();
        this.SearchFilter();

    }, ShowFilterContainer: function () {
        jQuery(".assl-buttons").on("click", function (e) {
            e.stopImmediatePropagation();
            var valore = jQuery(this).data("button");
            jQuery("#assl-" + valore).slideToggle();
            jQuery(".assl-filters:not(#assl-" + valore + ")").hide();
        });
    },
    SelectUnselectAll: function () {
        jQuery('#toggleAllStore').click(function (e) {
            jQuery('.assl-cat').prop('checked', this.checked);
        });
    },
    TypeFilter: function () {
        var type_button = jQuery("#getStoreByCat");
        jQuery(type_button).on('click', function (e) {
            e.preventDefault();
            jQuery(".assl-filters").fadeOut();
            var categories = AsslMainFunction.CreateCategoriesArray();
            var Distance = jQuery("#assl-radius").data("radius");
            var UserGeo = AsslMainFunction.CreateUserGeoArray(Distance);
            var AjaxData = {action: 'getStores', 'categories': categories, 'usergeo': UserGeo};
            AsslMainFunction.AjaxCall(AjaxData);
        });
    }, RadiusFilter: function () {
        jQuery("input[name='radius-value']").on('click', function () {
            jQuery(".assl-filters").fadeOut();
            var categories = AsslMainFunction.CreateCategoriesArray();
            var Distance = jQuery(this).val();
            var UserGeo = AsslMainFunction.CreateUserGeoArray(Distance);
            var AjaxData = {action: 'getStores', 'categories': categories, 'usergeo': UserGeo};
            AsslMainFunction.AjaxCall(AjaxData);
            UserGeo = [];
        });
    }, RemoveRadiusFilter: function () {
        jQuery("#assl-clear-radius").on('click', function () {
            jQuery(".assl-filters").fadeOut();
            jQuery(".radius").prop('checked', false);
            jQuery("#assl-radius").data("radius", 0);
            var UserGeo = AsslMainFunction.CreateUserGeoArray(0);
            var categories = AsslMainFunction.CreateCategoriesArray();
            var AjaxData = {action: 'getStores', 'categories': categories, 'usergeo': UserGeo};
            AsslMainFunction.AjaxCall(AjaxData);
            UserGeo = [];
        });
    }, SearchFilter: function () {
        jQuery('#assl-search-store').enterKey(function () {
            AsslMainFunction.SearchGo();
        });
        jQuery("#searchStore").on('click', function () {
            AsslMainFunction.SearchGo();
        });
    },
    SearchGo: function () {
        jQuery(".assl-filters").fadeOut();
        var categories = AsslMainFunction.CreateCategoriesArray();
        var Distance = jQuery("#assl-radius").data("radius");
        var UserGeo = AsslMainFunction.CreateUserGeoArray(Distance);
        var keyword = jQuery("#assl-search-store").val();
        var AjaxData = {
            action: 'getStores',
            'categories': categories,
            'keyword': keyword,
            'usergeo': UserGeo
        };
        AsslMainFunction.AjaxCall(AjaxData);
    },
    CreateUserGeoArray: function (distance) {
        var UserGeo = [],
            asslRadius = jQuery("#assl-radius"),
            UserLat = asslRadius.data("userlat"),
            UserLng = asslRadius.data("userlng");
        if (distance != 0) {
            UserGeo.push(distance);
            UserGeo.push(UserLat);
            UserGeo.push(UserLng);
        }
        asslRadius.data("radius", distance);
        return UserGeo;
    }, CreateCategoriesArray: function () {
        var categories = [];
        jQuery(".assl-cat").each(function () {
            if (jQuery(this).is(':checked')) {
                categories.push(jQuery(this).val());
            }
        });
        if (categories.length === 0) {
            categories.push(0);
        }
        return categories;

    }, AjaxCall: function (AjaxData) {
        jQuery.ajax({
            cache: false,
            url: assl_script_ajax.ajax_url,
            type: "POST",
            dataType: "json",
            data: (AjaxData),
            beforeSend: function () {
                jQuery("#assl-no-results").hide();
                jQuery("#assl-loading").show();
                AsslGoogleMap.circle.setMap(null);
            },
            success: function (data) {
                jQuery("#assl-loading").hide();
                AsslGoogleMap.loadPoi(data.mappa);
                jQuery("#assl-list").empty().html("<ul>" + data.lista + "</ul>");
                if (data.lista == '') {
                    jQuery("#assl-no-results").show();
                }
                if (data.totstores === null) {
                    jQuery(".totStoreFound").empty().html("0");
                } else {
                    jQuery(".totStoreFound").empty().html(data.totstores);
                }
            },
            complete: function () {
                AsslMainFunction.AfterAjax();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('Spiacente, qualcosa è andato storto durante il caricamento.');
            }
        });
    }, AfterAjax: function () {
        jQuery("#assl-search-store").val('');
    }
};
var AsslGoogleMap = {
    elementId: false,
    poi: false,
    map: false,
    circle: false,
    userlat: false,
    userlng: false,
    Usermarker: false,
    mc: false,
    directionsDisplay: new google.maps.DirectionsRenderer(),
    directionsService: new google.maps.DirectionsService(),
    geocoder: new google.maps.Geocoder,
    markers: [],
    loadMap: function (elementId, poi) {
        jQuery("#assl-loading").show();
        this.elementId = elementId;
        this.poi = poi;
        this.map = new google.maps.Map(document.getElementById(elementId), mapGlobalOptions);
        if (ClusterActivation == "si") {
            this.SetMarkerCluster(this.map);
        }
        this.Geolocation(this.map, poi);
    },
    SetMarkerCluster: function (map) {
        var clustertxtcolor = storeSettingsOption.clustertext;
        var clusterimgsmall = storeSettingsOption.clusterimgsmall;
        var clusterimgmedium = storeSettingsOption.clusterimgmedium;
        var clusterimglarge = storeSettingsOption.clusterimglarge;
        var clusterStyles = [{
            textColor: clustertxtcolor,
            url: clusterimgsmall,
            height: 30,
            width: 30
        }, {textColor: clustertxtcolor, url: clusterimgmedium, height: 40, width: 40}, {
            textColor: clustertxtcolor,
            url: clusterimglarge,
            height: 50,
            width: 50
        }];
        var mcOptions = {gridSize: 50, styles: clusterStyles, maxZoom: 15};
        this.mc = new MarkerClusterer(map, [], mcOptions);
    },
    Geolocation: function (map, poi) {
        var UserPosition = jQuery("#assl-radius");
        var UserGeolocalized = false;
        if (storeSettingsOption.ishttps) {
            if (navigator.geolocation) {

                navigator.geolocation.getCurrentPosition(function (position) {
                    var pos = {lat: position.coords.latitude, lng: position.coords.longitude};
                    if (poi.length == 0) {
                        map.setCenter(pos);
                        map.setZoom(13);
                    }
                    AsslGoogleMap.geocodeLatLng(position.coords.latitude, position.coords.longitude, map, yourPos);
                    AsslGoogleMap.userlat = position.coords.latitude;
                    AsslGoogleMap.userlng = position.coords.longitude;
                    jQuery(UserPosition).data("userlat", position.coords.latitude);
                    jQuery(UserPosition).data("userlng", position.coords.longitude);
                    UserGeolocalized = true;
                    AsslGoogleMap.loadPoi(poi);
                }, function () {
                    AsslGoogleMap.geocodeLatLng(centerLat, centerLng, map, geoProbl);
                    var pos = {lat: centerLat, lng: centerLng};
                    if (poi.length == 0) {
                        map.setCenter(pos);
                        map.setZoom(13);
                    }
                    AsslGoogleMap.userlat = centerLat;
                    AsslGoogleMap.userlng = centerLng;
                    jQuery(UserPosition).data("userlat", centerLat);
                    jQuery(UserPosition).data("userlng", centerLng);
                    UserGeolocalized = true;
                    AsslGoogleMap.loadPoi(poi);
                });
            }
            setTimeout(function () {
                if (!UserGeolocalized) {
                    AsslGoogleMap.geocodeLatLng(centerLat, centerLng, map, geoBrowser);
                    var pos = {lat: centerLat, lng: centerLng};
                    if (poi.length == 0) {
                        map.setCenter(pos);
                        map.setZoom(13);
                    }
                    AsslGoogleMap.userlat = centerLat;
                    AsslGoogleMap.userlng = centerLng;
                    jQuery(UserPosition).data("userlat", centerLat);
                    jQuery(UserPosition).data("userlng", centerLng);
                    AsslGoogleMap.loadPoi(poi);
                }
            }, 5000);
        } else {
            AsslGoogleMap.geocodeLatLng(centerLat, centerLng, map, actualPosition);
            var pos = {lat: centerLat, lng: centerLng};
            if (poi.length == 0) {
                map.setCenter(pos);
                map.setZoom(13);
            }
            AsslGoogleMap.userlat = centerLat;
            AsslGoogleMap.userlng = centerLng;
            jQuery(UserPosition).data("userlat", centerLat);
            jQuery(UserPosition).data("userlng", centerLng);
            AsslGoogleMap.loadPoi(poi);
        }

    },
    geocodeLatLng: function (lat, lng, map, message) {
        if (assl_script_ajax.main_script_enabled == 'enabled') {
            var latlng = {lat: parseFloat(lat), lng: parseFloat(lng)};
            this.geocoder.geocode({'location': latlng}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        jQuery(".assl-bottom-bar-results").empty().html(message + ": <strong>" + results[1].formatted_address + "</strong>");
                        jQuery(".assl-bottom-bar").fadeIn();
                    } else {
                        jQuery(".assl-bottom-bar-results").empty().html(message);
                        jQuery(".assl-bottom-bar").fadeIn();
                    }
                } else {
                    jQuery(".assl-bottom-bar-results").empty().html(message);
                    jQuery(".assl-bottom-bar").fadeIn();
                }
            });
        }
    },
    drawPoi: function (map, poi) {
        var bounds = new window.google.maps.LatLngBounds();
        var infowindow = new google.maps.InfoWindow({content: ''});
        for (var i = 0; i < poi.length; i++) {
            if ((poi[i].lat != 0) && (poi[i].lon != 0)) {
                this.loadMarker(map, poi[i], infowindow);
                var myLatlng = new window.google.maps.LatLng(poi[i].lat, poi[i].lng);
                bounds.extend(myLatlng);
            }
        }
        if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
            var extendPoint = new google.maps.LatLng(bounds.getNorthEast().lat() + 0.001, bounds.getNorthEast().lng() + 0.001);
            bounds.extend(extendPoint);
        }
        this.PoiLoaded(bounds);
    },
    loadMarker: function (map, poi, infowindow) {
        var myLatLng = new google.maps.LatLng(poi.lat, poi.lng);
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            bounds: true,
            title: poi.title,
            icon: new google.maps.MarkerImage(poi.icon, null, null, null, new google.maps.Size(parseInt(storeSettingsOption.pinimagewidth), parseInt(storeSettingsOption.pinimageheight)))
        });
        var image, via, city, telefono, telefono2, fax, email, website;

        if (poi.image) {
            image = '<img src="' + poi.image + '" height="75" width="75" />';
        } else {
            image = '';
        }
        if (poi.via) {
            via = poi.via + ', ' + poi.num + '<br>';
        } else {
            via = '';
        }
        if (poi.cap) {
            city = poi.cap + ' - ' + poi.citta;
        } else {
            city = poi.citta;
        }
        if (poi.telefono) {
            telefono = '<div class="markerPhone"><i class="i-phone"></i> ' + poi.telefono + '</div>';
        } else {
            telefono = '';
        }
        if (poi.telefono2) {
            telefono2 = '<div class="markerPhone"><i class="i-phone"></i> ' + poi.telefono2 + '</div>';
        } else {
            telefono2 = '';
        }
        if (poi.fax) {
            fax = '<div class="markerPhone">Fax ' + poi.fax + '</div>';
        } else {
            fax = '';
        }
        if (poi.email) {
            email = '<i class="i-mail"></i> <a href="mailto:' + poi.email + '">' + poi.email + '</a><br>';
        } else {
            email = '';
        }
        if (poi.email2) {
            email += '<i class="i-mail"></i> <a href="mailto:' + poi.email2 + '">' + poi.email2 + '</a><br>';
        } else {
            email += '';
        }
        if (poi.website) {
            website = '<i class="i-website"></i> <a href="' + poi.website + '" target="_blank">' + poi.website.replace(/(^\w+:|^)\/\//, '') + '</a><br>';
        } else {
            website = '';
        }
        var calcTitle = storeSettingsOption.calctitle;
        var contentString = '<div id="poi-' + poi.id + '" class="assl-map-box"><div class="markerImage">' + image + '</div><div class="markerTextContainer"><div class="markerTitle">' + poi.title + '</div><div class="markerAddress">' + via + city + '</div>' + telefono + telefono2 + fax + '<div class="markerMail">' + email + website + '</div></div><div class="clear"></div><hr><div class="assl-calc-directions"><h5>' + calcTitle + '</h5><a class="calc-dir" data-mode="DRIVING" data-endlat="' + poi.lat + '" data-endlng="' + poi.lng + '"><i class="i-car"></i></a><a class="calc-dir" data-mode="WALKING" data-endlat="' + poi.lat + '" data-endlng="' + poi.lng + '"><i class="i-walking"></i></a><p class="calc-results"></p></div></div>';
        google.maps.event.addListener(marker, 'click', function () {
            infowindow.close();
            infowindow.setContent(contentString);
            infowindow.open(map, marker);
        });
        this.markers.push(marker);
    },
    RefreshGeocodeAddress: function (lat, lng) {
        var latlng = {lat: parseFloat(lat), lng: parseFloat(lng)};
        this.geocoder.geocode({'location': latlng}, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                if (results[1] && assl_script_ajax.main_script_enabled == 'enabled') {
                    jQuery(".assl-bottom-bar-results").find("strong").empty().html(results[1].formatted_address);
                }
            }
        });
    },
    loadUserMarker: function (bounds, lat, lng) {
        if (this.Usermarker) {
            this.Usermarker.setMap(null);
            this.Usermarker = null;
        }
        AsslGoogleMap.userlat = lat;
        AsslGoogleMap.userlng = lng;
        var UserPosition = jQuery("#assl-radius");
        jQuery(UserPosition).data("userlat", lat);
        jQuery(UserPosition).data("userlng", lng);
        var UserPoint = new google.maps.LatLng(lat, lng);
        bounds.extend(UserPoint);
        this.map.fitBounds(bounds);
        var infowindow = new google.maps.InfoWindow({content: ''});
        var usericon = storeSettingsOption.pinimage;
        this.Usermarker = new google.maps.Marker({
            position: UserPoint,
            map: this.map,
            draggable: true,
            animation: google.maps.Animation.DROP,
            icon: new google.maps.MarkerImage(usericon, null, null, null, new google.maps.Size(parseInt(storeSettingsOption.pinimagewidth), parseInt(storeSettingsOption.pinimageheight)))
        });
        this.RefreshGeocodeAddress(lat, lng);
        var title;
        if (storeSettingsOption.ishttps) {
            title = storeSettingsOption.yourpos;
        } else {
            title = storeSettingsOption.defaultposition;
        }
        var contentString = '<div class="assl-map-box">' + '<div class="markerTextContainer"><div class="markerTitle">' + title + '</div></div></div>';
        google.maps.event.addListener(this.Usermarker, 'click', function () {
            infowindow.close();
            infowindow.setContent(contentString);
            infowindow.open(AsslGoogleMap.map, this);
        });
        google.maps.event.addListener(this.Usermarker, "dragend", function (event) {
            bounds.extend(new google.maps.LatLng(event.latLng.lat(), event.latLng.lng()));
            AsslGoogleMap.map.fitBounds(bounds);
            AsslGoogleMap.RefreshGeocodeAddress(event.latLng.lat(), event.latLng.lng());
            AsslGoogleMap.userlat = event.latLng.lat();
            AsslGoogleMap.userlng = event.latLng.lng();
            jQuery(UserPosition).data("userlat", event.latLng.lat());
            jQuery(UserPosition).data("userlng", event.latLng.lng());
            AsslGoogleMap.ResetRoute();
        });
        this.drawRadius(this.map, UserPoint);
    },
    drawRadius: function (map, UserPoint) {
        var radiusElement = document.getElementById('assl-radius');
        if (radiusElement != null) {
            if (this.circle) {
                this.circle.setMap(null);
                this.circle = null;
            }
            var Radius = jQuery(radiusElement).data("radius");
            var radiusSettings = jQuery(radiusElement).data("radiussettings");
            var sColor = radiusSettings.strokecolor;
            var sOpacity = radiusSettings.strokeopacity;
            var sWeight = radiusSettings.strokeweight;
            var fColor = radiusSettings.fillcolor;
            var fOpacity = radiusSettings.fillopacity;

            this.circle = new google.maps.Circle({
                center: UserPoint,
                map: map,
                radius: Radius * 1000,
                strokeColor: sColor,
                strokeOpacity: sOpacity,
                strokeWeight: sWeight,
                fillColor: fColor,
                fillOpacity: fOpacity
            });
        }
    },
    flushMarkers: function () {
        if (ClusterActivation == "si") {
            this.mc.clearMarkers();
        } else {
            for (i = 0; i < this.markers.length; i++) {
                this.markers[i].setMap(null);
            }
        }
    },
    loadPoi: function (poi) {
        this.poi = poi;
        this.markers = [];
        this.flushMarkers();
        this.drawPoi(this.map, poi);
        if (ClusterActivation == "si") {
            this.mc.addMarkers(this.markers);
        }
        AsslGoogleMap.ResetRoute();
    },
    PoiLoaded: function (bounds) {
        this.loadUserMarker(bounds, this.userlat, this.userlng);
        jQuery("#assl-loading").fadeOut();
        this.CalcRoute(".calc-dir");
        if (assl_script_ajax.main_script_enabled == 'enabled') {
            this.NewUserAutocomplete(bounds);
        }
        this.openMarker();
    },
    CalcRoute: function (button) {
        jQuery(document).on('click', button + ':not(.active)', function (e) {
            e.preventDefault();
            jQuery(".calc-dir").removeClass("active");
            jQuery(this).addClass('active');
            var dirStrokeWeight = storeSettingsOption.dirstrokeweight;
            var dirStrokeOpacity = storeSettingsOption.dirstrokeopacity;
            var dirStrokeColor = storeSettingsOption.dirstrokecolor;
            AsslGoogleMap.directionsDisplay.setMap(AsslGoogleMap.map);
            AsslGoogleMap.directionsDisplay.setOptions({
                suppressMarkers: true,
                polylineOptions: {
                    strokeWeight: dirStrokeWeight,
                    strokeOpacity: dirStrokeOpacity,
                    strokeColor: dirStrokeColor
                }
            });
            var endLatitude = jQuery(this).data("endlat");
            var endLongitude = jQuery(this).data("endlng");
            var mode = jQuery(this).data("mode");
            var unit = jQuery("#assl-radius").data("unit");
            var start = new google.maps.LatLng(AsslGoogleMap.userlat, AsslGoogleMap.userlng);
            var end = new google.maps.LatLng(endLatitude, endLongitude);
            var request = {
                origin: start,
                destination: end,
                travelMode: google.maps.TravelMode[mode],
                unitSystem: google.maps.UnitSystem[unit]
            };
            var durationText = '<strong>' + storeSettingsOption.duration + ': </strong>';
            var distanceText = '<strong>' + storeSettingsOption.distance + ': </strong>';
            var infoboxContainer = jQuery(this).closest(".assl-map-box").attr("id");
            AsslGoogleMap.directionsService.route(request, function (response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    AsslGoogleMap.directionsDisplay.setDirections(response);
                    var directionPanel = document.getElementById('direction-content');
                    if (directionPanel) {
                        jQuery("#assl-gmap").addClass("resized");
                        jQuery("#direction-panel").slideDown();
                        jQuery("#direction-close").css({display: 'block'});
                        AsslGoogleMap.directionsDisplay.setPanel(directionPanel);
                    }
                    var legs = response.routes[0].legs;
                    for (var i = 0; i < legs.length; ++i) {
                        var totalDistance = legs[i].distance.text;
                        var totalDuration = legs[i].duration.text;
                        jQuery("#" + infoboxContainer).find(".calc-results").empty().html(durationText + totalDuration + '<br>' + distanceText + totalDistance);
                    }
                }
            });
        });
        jQuery(document).on('click', '#direction-close', function (e) {
            e.preventDefault();
            AsslGoogleMap.ResetRoute();
            jQuery(this).hide();
        });
    },
    ResetRoute: function () {
        AsslGoogleMap.directionsDisplay.setMap(null);
        jQuery("#assl-gmap").removeClass("resized");
        jQuery("#direction-panel").slideUp();
        jQuery("#direction-content").empty();
        jQuery(".calc-results").each(function () {
            jQuery(this).empty();
        });
        jQuery(".calc-dir").each(function () {
            jQuery(this).removeClass("active");
        });
    },
    NewUserAutocomplete: function (bounds) {
        var autocomplete = new google.maps.places.Autocomplete((document.getElementById('set-new-user-marker')), {types: ['geocode']});
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            AsslGoogleMap.codeNewAddress(bounds);
        });
    },
    codeNewAddress: function (bounds) {
        var address = document.getElementById('set-new-user-marker').value;
        this.geocoder.geocode({'address': address}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                AsslGoogleMap.loadUserMarker(bounds, results[0].geometry.location.lat(), results[0].geometry.location.lng());
                AsslGoogleMap.ResetRoute();
            } else {
                jQuery(".assl-bottom-bar-results").empty().html("Geocode was not successful for the following reason: <strong>" + status + "</strong>");
            }
        });
    },
    openMarker: function () {
        jQuery(document).on("click", ".assl-list-title", function () {
            var markerID = jQuery(this).data("marker");
            jQuery("#assl-list").slideUp();
            //Pan to the position and zoom in.
            AsslGoogleMap.map.setZoom(17);
            AsslGoogleMap.map.panTo(AsslGoogleMap.markers[markerID].position);
            google.maps.event.trigger(AsslGoogleMap.markers[markerID], 'click');
        });
    }
};
jQuery(document).ready(function ($) {
    jQuery.fn.enterKey = function (fnc) {
        return this.each(function () {
            jQuery(this).keypress(function (ev) {
                var keycode = (ev.keyCode ? ev.keyCode : ev.which);
                if (keycode == '13') {
                    fnc.call(this, ev);
                }
            })
        })
    };
    AsslMainFunction.Inizialize();
});