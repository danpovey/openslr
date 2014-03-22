<?php 
   require_once $_SERVER['DOCUMENT_ROOT'] . '/include/resource.php';
   ?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="description" content="Open Speech and Language Resources."/>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="txt/css" href="/style.css"/> 
    <title>openslr.org</title>
    
  </head>
  <body>
    <div class="container">
      <div id="centeredContainer">
        <div class="header"><h2 id="topline">Open Speech and Language Resources</h2></div>
        <div id="top">
          <a class="topButtons" href="/index.html">Home</a>
          <a class="topButtons" href="/resources.php">Resources</a>
        </div>
        <div id="rightCol">
          <div class = "contact_info">
            <div class ="contactTitle">Contact</div>
            <a href=mailto:contact@openslr.org> contact@openslr.org </a>  <br/>
            Phone: 424 247 4129  <br/>
            (Daniel Povey) <br/>
          </div>
        </div>
        <div id="mainContent">

          <div class="container" >
            <?php 
              $id = $_GET['id'];
              $ok = true;
              if ($id === false || preg_match('/^[0-9]+$/', $id) != 1) {
                print "<h2> Badly formatted resource: $id </h2>\n";
                $ok = false;
              }
              if ($ok) {
                $resource_dir = dirname(__FILE__) . "/resources/" . $id;
                // e.g. $root = /var/www/openslr/resources/1
                $resource = new Resource($resource_dir);
                if (! $resource->ok()) { 
                  print "<h2> Resource not found: $id </h2>\n";
                  $ok = false; 
                }
              }
              if ($ok) {
                 print "<h2 class=\"resource\"> $resource->name </h2>\n";
                 print "<p class=\"resource\"> <b>Identifier:</b> SLR$resource->id </p>\n";
                 print "<p class=\"resource\"> <b>Summary:</b> $resource->summary </p>\n";
                 print "<p class=\"resource\"> <b>Category:</b> $resource->category </p>\n";
                 print "<p class=\"resource\"> <b>License:</b> $resource->license </p>\n";
                 if (count($resource->files) == 1) {
                    $f_array = $resource->files[0]; // array of size 0 or 1.
                    $file = $f_array[0];
                    $comment = (count($f_array) > 1 ? $f_array[1] : '');
                    print "<p class=\"resource\"> <b>Download:</b> <a href=\"http://www.openslr.org/resources/$id/$file\"> $file </a> &nbsp; $comment </p>\n";
                 } elseif (count($resource->files) > 1) {
                    print "<p class=\"resource\"> <b>Downloads:</b> <br>";
                    foreach ($resource->files as $f_array) {
                       $file = $f_array[0];
                       $comment = (count($f_array) > 1 ? $f_array[1] : '');
                       $file_url="http://www.openslr.org/resources/$id/$file";
                       print "<a href=\"$file_url\"> $file </a> </p> &nbsp; $comment <br> \n";
                    }
                    print '</p>';
                 }
                 print "<p class=\"resource\"><b>About this resource:</b></p>";
                 $about = $resource->get_about_html();
                 if ($about !== false) {
                    print '<div id="about">';
                    print $about; // dump out the html from $resource_dir/about.html
                    print '</div>';
                 }
                 if (count($resource->alternate_urls) == 1) {
                    $u_array = $resource->alternate_urls[0]; // array of size 0 or 1.
                    $url = $u_array[0];
                    $comment = (count($f_array) > 1 ? $u_array[1] : '');
                    print "<p class=\"resource\"> <b>External URL:</b> <a href=\"$url\"> $url </a> &nbsp; $comment </p> \n";
                 } elseif (count($resource->alternate_urls) > 1) {
                    print "<p class=\"resource\"> <b>External URLs:</b> <br>";
                    foreach ($resource->alternate_urls as $u_array) {
                      $url = $u_array[0];
                      $comment = (count($f_array) > 1 ? $u_array[1] : '');
                      print "<a href=\"$url\"> $url </a> &nbsp; $comment <br> </p> \n";
                    }
                    print "</p>";
                 }
              } ?>
          
            <div style="height:300px"> </div>

          </div>
          
        </div>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <div style="clear: both"></div>

        <div id="footer"> 
          <p>
	        <a href="http://jigsaw.w3.org/css-validator/check/referer">
	          <img style="border:0;width:88px;height:31px"
                   src="http://jigsaw.w3.org/css-validator/images/vcss-blue"
                   alt="Valid CSS!" />
	        </a>
          </p>
        </div>
      </div>
  </body>      
</html>

