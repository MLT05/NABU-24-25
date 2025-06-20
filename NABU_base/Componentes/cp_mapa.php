
<div id="map"></div>
<script>
    let lng = -8.25;
    let lat = 40.11;

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(success, error);
        } else {
            console.log("Geolocation is not supported by this browser.");
        }
    }

    function success(position) {
        lng = position.coords.longitude;
        lat = position.coords.latitude;

        mapboxgl.accessToken = 'pk.eyJ1IjoibWFyaWFsZW9ub3JmcmlhcyIsImEiOiJjbWM1MnJ3eTgwN3U3Mm1zOXdweDJzenY4In0.no4s5Sct2PZJSJBqtKnpnA';
        const map = new mapboxgl.Map({
            container: 'map', // container ID
            center: [lng, lat], // starting position [lng, lat]. Note that lat must be set between -90 and 90
            zoom: 10 // starting zoom
        });

        // Marcador do utilizador
        new mapboxgl.Marker({ color: 'blue' })
            .setLngLat([lng, lat])
            .setPopup(new mapboxgl.Popup().setText("Estás aqui"))
            .addTo(map);

        const url = "../api/localizacao.php";
        fetch(url).then(function (res) {
            return res.json();
        }).then(function (data) {
            console.log(data);
            for (var i = 0; i < data.length; i++) {
                const marker = new mapboxgl.Marker({})
                    .setLngLat([data[i]["lng"], data[i]["lat"]])
                    .addTo(map);
            }
            // add comments
        }).catch(function (error) {
            console.log(error);
        });
        console.log(lng);
        const marker = new mapboxgl.Marker({})
            .setLngLat([lng, lat])
            .addTo(map);

    }

    function error() {
        alert("Localização nao disponível, ligue a sua localização");
    }
    getLocation();


</script>