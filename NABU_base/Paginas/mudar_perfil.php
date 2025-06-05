<?php include_once "../Componentes/cp_head.php" ?>

<?php include_once "../Componentes/cp_header.php" ?>


<form action="upload.php" method="post" enctype="multipart/form-data">
  Select image to upload:
  <input type="file" name="fileToUpload" id="fileToUpload">
  <input type="submit" value="Upload Image" name="submit">
</form>

<?php include_once "../Componentes/cp_footer.php" ?>