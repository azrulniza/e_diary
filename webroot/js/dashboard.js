$(function () {

    var vectorSource = new ol.source.Vector({
        //create empty vector
    });
    
    var app = getAppVars();

    var iconStyle = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 10,
            stroke: new ol.style.Stroke({
                color: '#fff'
            }),
            fill: new ol.style.Fill({
                color: '#3399CC'
            })
        }),
        text: new ol.style.Text({
            text: "",
            fill: new ol.style.Fill({
                color: '#fff'
            })
        })
    });

    var vectorLayer = new ol.layer.Vector({
        source: vectorSource,
        style: iconStyle
    });

    var map = new ol.Map({
        layers: [new ol.layer.Tile({source: new ol.source.OSM()}), vectorLayer],
        target: document.getElementById('map-1'),
        view: new ol.View({
            center: ol.proj.fromLonLat([468.247, 2.956]),
            zoom: 5,
            // projection: 'EPSG:3857'
        })
    });

    var zoom_level;
    var locations;
    map.on('moveend', (function () {
        
        zoom_level = map.getView().getZoom();
        if (zoom_level != null) {
            
            console.log('zoom='+zoom_level);

            if (zoom_level <= 5 && typeof app.device_map.country !== 'undefined') {
                console.log("current "+zoom_level);
                locations = app.device_map.country;
                markercount(locations);
            } else if (zoom_level <= 7 && typeof app.device_map.state !== 'undefined') {
                console.log("current_state "+zoom_level);
                locations = app.device_map.state;
                markercount(locations);
            } else if (zoom_level <= 9 && typeof app.device_map.city !== 'undefined') {
                console.log("current_city "+zoom_level);
                locations = app.device_map.city;
                markercount(locations);
            } else if (zoom_level <= 11 && typeof app.device_map.district !== 'undefined') {
                console.log("current_district "+zoom_level);
                locations = app.device_map.district;
                markercount(locations);
            } else if (zoom_level <= 13 && typeof app.device_map.road !== 'undefined') {
                console.log("current_road "+zoom_level);
                locations = app.device_map.road;
                markercount(locations);
            } else if (zoom_level <= 14 && typeof app.device_map.building !== 'undefined') {
                console.log("current_building "+zoom_level);
                locations = app.device_map.building;
                markercount(locations);
            }
            else if (zoom_level > 14) {
                //console.log("current_building "+zoom_level);
                console.log("individual poi "+zoom_level);
                locations = app.device_map.building;
                markercount(locations);
                //console.log(map.getSize())
                
                //var extent = map.getView().calculateExtent(map.getSize());
                
                //console.log(extent)
                
                //extent = ol.proj.transformExtent(extent, 'EPSG:3857', 'EPSG:4326');
                
                //console.log(extent)
                
                //locations = app.device_map.building;
                //markercount();
            }

        }
    }));

    function markercount(locations) {
        vectorSource.clear()
        if (locations.length > 0) {
            // make icon features
            for (var i = 0; i < locations.length; i++) {
                var location = locations[i];
                //create the style
                var iconStyle = new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 10,
                        stroke: new ol.style.Stroke({
                            color: '#fff'
                        }),
                        fill: new ol.style.Fill({
                            color: '#3399CC'
                        })
                    }),
                    text: new ol.style.Text({
                        text: location.total + "",
                        fill: new ol.style.Fill({
                            color: '#fff'
                        })
                    })
                });

                //create a bunch of icons and add to source vector
                var iconFeature = new ol.Feature({
                    geometry: new
                            ol.geom.Point(ol.proj.transform([location.lon, location.lat], 'EPSG:4326', 'EPSG:3857')),
                            //ol.geom.Point([location.lon, location.lat]),
                    name: 'Null Island ',
                    population: 4000,
                    rainfall: 500
                });
                
                //var trns = ol.proj.transform([location.lon, location.lat], 'EPSG:4326', 'EPSG:3857');
                //var trnx = ol.proj.transform(trns, 'EPSG:3857', 'EPSG:4326');
                //console.log('tr', trns, trnx, [location.lon, location.lat]);

                iconFeature.setStyle(iconStyle);
                vectorSource.addFeature(iconFeature);
            }
        }
    }



    map.on('click', function (evt) {
        var coordinate = evt.coordinate;
        console.log(coordinate);
        var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
        console.log(lonlat);
    });

});