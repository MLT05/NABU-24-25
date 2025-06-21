
<div id="map"></div>
<a href="javascript:history.back()" class="btn text-decoration-none position-absolute start-0 mt-4 ms-3 d-flex align-items-center" style="top: 8vh;">
    <span class="material-icons me-2 " style="font-size: 2.5rem; color: white">arrow_back</span>

</a>
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



        const url = "../api/localizacao.php";
        fetch(url).then(function (res) {
            return res.json();
        }).then(function (data) {
            console.log("Marcadores da base de dados:", data);
            for (var i = 0; i < data.length; i++) {
                const popup = new mapboxgl.Popup({ offset: 25 })
                    .setHTML(`<strong>${data[i]["morada"]}</strong>`);

                const marker = new mapboxgl.Marker()
                    .setLngLat([data[i]["lng"], data[i]["lat"]])
                    .setPopup(popup) // associa o popup ao marcador
                    .addTo(map);
            }
        }).catch(function (error) {
            console.log(error);
        });

        console.log("Tua localização:", { lng, lat });
        const marker = new mapboxgl.Marker({})
            .setLngLat([lng, lat])
            .addTo(map);

    }

    function error() {
        alert("Localização nao disponível, ligue a sua localização");
    }
    getLocation();


</script>