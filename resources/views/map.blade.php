<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
</head>

<body style="margin:0">
    <div id="map" style="height: 100vh; width: 100vw"></div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        const hubs = {
            type: 'FeatureCollection',
            features: @json(\App\Models\Hub::all()->map->toFeature()),
        };

        const map = L.map('map', {
            center: [0, 0],
            zoom: 3,
            layers: [
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    subdomains: 'abcd'
                }),
                L.geoJson(hubs, {
                    onEachFeature(feature, layer) {
                        const content = Object.keys(feature.properties)
                            .map((prop) => `${prop}: ${feature.properties[prop]}`)
                            .join('\n');

                        layer.bindPopup(`<pre>${content}</pre>`);
                    },
                })
            ],
        });
    </script>
</body>

</html>
