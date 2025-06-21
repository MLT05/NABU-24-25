
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
            zoom: 7 // starting zoom
        });



        const url = "../api/localizacao.php";
        fetch(url).then(function (res) {
            return res.json();
        }).then(function (data) {
            console.log("Marcadores da base de dados:", data);


            for (var i = 0; i < data.length; i++) {
                const anuncio = data[i];

                const popupHTML = `
  <div style="
    min-width: 230px;
    max-width: 250px;
    background-color: #f8f9fa;
    font-family: sans-serif;
    font-size: 14px;
    color: #333;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    position: relative;
  ">


    <!-- Imagem -->
    <div style="height: 120px; overflow: hidden;">
      <img src="../uploads/capas/${anuncio.capa}" alt="${anuncio.nome_produto}"
           style="width: 100%; height: 100%; object-fit: cover;">
    </div>

    <!-- Conteúdo -->
    <div style="padding: 10px;">
      <h3 style="margin: 0 0 4px 0; font-size: 16px; color: #14532d; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
        ${anuncio.nome_produto}
      </h3>
      <p style="margin: 0 0 6px 0; font-size: 14px;">📍 ${anuncio.localizacao}</p>
      <p style="margin: 0 0 6px 0; font-weight: bold; color: #27ae60; font-size: 15px;">
        ${anuncio.preco} € / ${anuncio.ref_medida}
      </p>
      <div style="text-align: right;">
        <a href="./produto.php?id=${anuncio.id}" target="_blank"
           style="color: #14532d; text-decoration: underline; font-weight: 500;">
          Ver mais
        </a>
      </div>
    </div>
  </div>`;






                const popup = new mapboxgl.Popup({ offset: 25 })
                    .setHTML(popupHTML);

                new mapboxgl.Marker({ color: 'green' })
                    .setLngLat([anuncio.lng, anuncio.lat])
                    .setPopup(popup)
                    .addTo(map);
            }

        }).catch(function (error) {x
            console.log(error);
        });

        console.log("Tua localização:", { lng, lat });
        const marker = new mapboxgl.Marker({ color: 'red' })
            .setLngLat([lng, lat])
            .addTo(map);

    }

    function error() {
        alert("Localização nao disponível, ligue a sua localização");
    }
    getLocation();


</script>