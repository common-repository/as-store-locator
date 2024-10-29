var AsslMetaboxMainScript =
{
    isEmpty: function (el) {
        return !jQuery.trim(el.html());
    },
    checkRequiredFields: function () {

        if (jQuery(".post-new-php form#post").length || jQuery(".post-php form#post").length) {
            jQuery("form#post").submit(function () {
                var message = false,
                    inputLat = jQuery("#assl_metabox_geo_lat"),
                    inputLng = jQuery("#assl_metabox_geo_lng"),
                    inputTag = jQuery("#new-tag-store_categories"),
                    TagStore = jQuery("#store_categories").find(".tagchecklist");
                //Ripulisco i campi
                jQuery(inputLat).removeClass("assl_tag_error");
                jQuery(inputLng).removeClass("assl_tag_error");
                jQuery(inputTag).removeClass("assl_tag_error");
                jQuery(".required-category").remove();

                if(AsslMetaboxMainScript.isEmpty(TagStore) && inputLat.val() == '' && inputLng.val() == '') {
                    message = assl_metabox_script.tag_lat_lng_required;
                    jQuery(inputLat).addClass("assl_tag_error");
                    jQuery(inputLng).addClass("assl_tag_error");
                    jQuery(inputTag).addClass("assl_tag_error");

                } else if(AsslMetaboxMainScript.isEmpty(TagStore) && inputLat.val() == '') {
                    message = assl_metabox_script.tag_lat_required;
                    jQuery(inputLat).addClass("assl_tag_error");
                    jQuery(inputTag).addClass("assl_tag_error");

                } else if(AsslMetaboxMainScript.isEmpty(TagStore) && inputLng.val() == '') {
                    message = assl_metabox_script.tag_lng_required;
                    jQuery(inputLng).addClass("assl_tag_error");
                    jQuery(inputTag).addClass("assl_tag_error");

                } else if(inputLat.val() == '' && inputLng.val() == '') {
                    message = assl_metabox_script.lat_lng_required;
                    jQuery(inputLat).addClass("assl_tag_error");
                    jQuery(inputLng).addClass("assl_tag_error");

                } else if(AsslMetaboxMainScript.isEmpty(TagStore)) {
                    message = assl_metabox_script.tag_required;
                    jQuery(inputTag).addClass("assl_tag_error");

                } else if(inputLat.val() == '') {
                    message = assl_metabox_script.lat_required;
                    jQuery(inputLat).addClass("assl_tag_error");

                } else if(inputLng.val() == '') {
                    message = assl_metabox_script.lng_required;
                    jQuery(inputLng).addClass("assl_tag_error");

                }
                if(message) {
                    jQuery('body').append('<div class="required-category" style="position:fixed; top: 30px; background-color:#ffe4e3; right: 20px; padding: 10px; color: red; border: 1px solid red; margin-top: 10px; font-weight:bold; text-transform: uppercase;">'+message+'</div>');
                    setTimeout(function () {
                        jQuery(".required-category").remove();
                    }, 2000);
                    return false;
                }
            });
        }
    }
};

var AsslMetaboxMapScript =
{
    place: null,
    address: null,
    addressType: null,
    geocoder: null,
    latlng: null,
    currentLat: null,
    currentLng: null,
    cc: null,
    mapOptions: {},
    map: null,
    markerGeo: null,
    autocomplete: null,
    componentForm: {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_2: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
    },
    fillInAddress: function () {
        this.place = this.autocomplete.getPlace();
        for (var i = 0; i < this.place.address_components.length; i++) {
            this.addressType = this.place.address_components[i].types[0];
            if (this.componentForm[this.addressType]) {
                var val = this.place.address_components[i][this.componentForm[this.addressType]];
                document.getElementById(this.addressType).value = val;
            }
        }
    },
    codeAddress: function () {
        this.address = document.getElementById('assl_metabox_cerca_indirizzo').value;
        this.geocoder.geocode({'address': this.address}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                AsslMetaboxMapScript.map.setCenter(results[0].geometry.location);
                document.getElementById("assl_metabox_geo_lat").value = results[0].geometry.location.lat();
                document.getElementById("assl_metabox_geo_lng").value = results[0].geometry.location.lng();
                AsslMetaboxMapScript.insertMarkerGeo(results[0].geometry.location);
            } else {
                alert('Geocode was not successful for the following reason: ' + status);
            }
        });
    },
    isFloat: function (n) {
        return n === Number(n) && n % 1 !== 0;
    },
    insertMarkerGeo: function (position) {
        if (this.markerGeo) {
            this.markerGeo.setMap(null);
            this.markerGeo = null;
        }
        this.markerGeo = new google.maps.Marker({
            map: this.map,
            position: position,
            draggable: true,
            animation: google.maps.Animation.DROP,
        });
        google.maps.event.addListener(this.markerGeo, "dragend", function (event) {
            document.getElementById("assl_metabox_geo_lat").value = event.latLng.lat();
            document.getElementById("assl_metabox_geo_lng").value = event.latLng.lng();
        });
    },
    Initialize: function () {
        this.geocoder = new google.maps.Geocoder();
        this.latlng = new google.maps.LatLng(37.474667, 15.06608289999997);
        this. mapOptions = {
            zoom: 10, 
            center: this.latlng, 
            scrollwheel: false
        };

        this.map = new google.maps.Map(document.getElementById('map-canvas'), this.mapOptions);
        this.autocomplete = new google.maps.places.Autocomplete((document.getElementById('assl_metabox_cerca_indirizzo')), {types: ['geocode']});
        google.maps.event.addListener(this.autocomplete, 'place_changed', function () {
            AsslMetaboxMapScript.fillInAddress();
            AsslMetaboxMapScript.codeAddress();
        });

        this.currentLat = parseFloat(document.getElementById("assl_metabox_geo_lat").value);
        this.currentLng = parseFloat(document.getElementById("assl_metabox_geo_lng").value);
        if (AsslMetaboxMapScript.isFloat(this.currentLat) && AsslMetaboxMapScript.isFloat(this.currentLng)) {
            this.cc = new google.maps.LatLng(this.currentLat, this.currentLng);
            AsslMetaboxMapScript.insertMarkerGeo(this.cc, this.map);
        }
    }


};

jQuery(document).ready(function () {
    AsslMetaboxMainScript.checkRequiredFields();
    if(assl_metabox_script.main_script_enabled == 'enabled') {
        AsslMetaboxMapScript.Initialize();
    }
});