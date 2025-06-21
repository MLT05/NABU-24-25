
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



        const url = "../api/localizacao.php";
        fetch(url).then(function (res) {
            return res.json();
        }).then(function (data) {
            console.log("Marcadores da base de dados:", data);


            for (var i = 0; i < data.length; i++) {
                const anuncio = data[i];

                const popupHTML =
                    ` <div style="min-width: 220px;">
            <h3 style="margin: 0 0 5px 0;">${anuncio.nome_produto}</h3>
            <p style="margin: 0;"> ${anuncio.localizacao}</p>
            <p style="margin: 0;"> <strong>${anuncio.preco} € / ${anuncio.ref_medida}</strong></p>
            <a href="./produto.php?id=${anuncio.id}" target="_blank"> Ver mais</a>
        </div>`;

                const popup = new mapboxgl.Popup({ offset: 25 })
                    .setHTML(popupHTML);

                new mapboxgl.Marker()
                    .setLngLat([anuncio.lng, anuncio.lat])
                    .setPopup(popup)
                    .addTo(map);
            }

        }).catch(function (error) {x
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