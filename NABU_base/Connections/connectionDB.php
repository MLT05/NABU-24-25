/*Ligação à base de dados*/
<?php
function new_db_connections(){
    $env = "localhost";
    // Variables for the database connection
    if ($env == "localhost") {
        $hostname = 'localhost';
        $username = "root";
        $password = "";
        $dbname = "nabu_base";
    } else {
        $hostname = 'labmm.clients.ua.pt';
        $username = "deca_25_18";
        $password = "sUpdS875";
        $dbname = "nabu_base";
    }
#top_indian_movies_
    // Makes the connection
    $local_link = mysqli_connect($hostname, $username, $password, $dbname);

    // If it fails to connect then die and show errors
    if (!$local_link) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Define charset to avoid special chars errors
    mysqli_set_charset($local_link, "utf8");

    // Return the link
    return $local_link;
}



