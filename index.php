<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Kazakhstan MBTiles</title>

<link href="https://unpkg.com/maplibre-gl@5.6.2/dist/maplibre-gl.css" rel="stylesheet">
<script src="https://unpkg.com/maplibre-gl@5.6.2/dist/maplibre-gl.js"></script>

<style>
html,body,#map{
    width:100%;
    height:100%;
    margin:0;
}

.maplibregl-popup{
    max-width:500px;
}

.popup-table{
    border-collapse:collapse;
    width:100%;
    font-size:12px;
}

.popup-table td{
    border:1px solid #ccc;
    padding:4px;
}
</style>
</head>
<body>

<div id="map"></div>

<script>

const BASE_URL = window.location.origin;

const map = new maplibregl.Map({
    container:'map',

    center:[67.5,48.5],
    zoom:5,
    minZoom:0,
    maxZoom:14,

    style:{
        version:8,

        glyphs: BASE_URL + '/fonts/{fontstack}/{range}.pbf',

        sources:{
            osm:{
                type:'vector',
                tiles:[
                    BASE_URL + '/tiles.php?z={z}&x={x}&y={y}'
                ],
                minzoom:0,
                maxzoom:14
            }
        },

        layers:[

            {
                id:'background',
                type:'background',
                paint:{
                    'background-color':'#f5f5f5'
                }
            },

            {
                id:'water',
                type:'fill',
                source:'osm',
                'source-layer':'water_polygons',
                paint:{
                    'fill-color':'#9fd3ff'
                }
            },

            {
                id:'buildings',
                type:'fill',
                source:'osm',
                'source-layer':'buildings',
                paint:{
                    'fill-color':'#d9d9d9',
                    'fill-outline-color':'#999'
                }
            },

            {
                id:'roads',
                type:'line',
                source:'osm',
                'source-layer':'streets',
                paint:{
                    'line-color':'#555',
                    'line-width':[
                        'interpolate',
                        ['linear'],
                        ['zoom'],
                        5,0.5,
                        10,2,
                        14,5
                    ]
                }
            },

            {
                id:'cities',
                type:'symbol',
                source:'osm',
                'source-layer':'place_labels',
                layout:{
                    'text-field':[
                        'coalesce',
                        ['get','name:ru'],
                        ['get','name']
                    ],
                    'text-font':['Noto Sans Regular'],
                    'text-size':[
                        'interpolate',
                        ['linear'],
                        ['zoom'],
                        5,10,
                        10,14,
                        14,18
                    ]
                },
                paint:{
                    'text-color':'#111',
                    'text-halo-color':'#fff',
                    'text-halo-width':1
                }
            }

        ]
    }
});

map.addControl(new maplibregl.NavigationControl());

map.on('load', () => {

    console.log('Карта загружена');

    map.on('click', (e) => {

        const features = map.queryRenderedFeatures(e.point);

        if (!features.length) return;

        const feature = features[0];

        let html = '<table class="popup-table">';

        for(const key in feature.properties){
            html += `
            <tr>
                <td>${key}</td>
                <td>${feature.properties[key]}</td>
            </tr>`;
        }

        html += `
        <tr>
            <td>lon</td>
            <td>${e.lngLat.lng}</td>
        </tr>
        <tr>
            <td>lat</td>
            <td>${e.lngLat.lat}</td>
        </tr>`;

        html += '</table>';

        new maplibregl.Popup()
            .setLngLat(e.lngLat)
            .setHTML(html)
            .addTo(map);
    });

});

map.on('error', e => {
    console.error('MapLibre error:', e);
});

</script>

</body>
</html>