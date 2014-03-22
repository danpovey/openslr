<?php 
   require_once $_SERVER['DOCUMENT_ROOT'] . '/include/resource.php';
   ?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="description" content="Open Speech and Language Resources."/>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="txt/css" href="style.css"/> 
    <title>openslr.org</title>
    
  </head>
  <body>
    <div class="container">
      <div id="centeredContainer">
        <div class="header"><h2 id="topline">Open Speech and Language Resources</h2></div>
        <div id="top">
          <a class="topButtons" href="index.html">Home</a>
          <a class="myTopButton" href="resources.php">Resources</a>
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
	        <p><b> Resources </b><br/>

              <table id="resourceTable">
                <tr> <th> Resource </th> <th> Name </th> <th> Category </th>  <th> Summary </th> </tr>

                <?php
                   $root = dirname(__FILE__) . "/resources/";
                   $dir = new DirectoryIterator($root);
                   foreach ($dir as $fileinfo) {
                   $resource_dir = $root . $fileinfo;
                   $resource = new Resource($resource_dir);
                   if ($resource->ok()) {
                   print "<tr>";
                  print "<td> <a href=\"http://www.openslr.org/$resource->id/\"> SLR$resource->id </a> </td> "; 
                  print "<td> $resource->name </td> <td> $resource->category </td> <td> $resource->summary </td>\n";
                  print "</tr>";
                }
                }
                ?>
              </table>


              <div style="height:300px"></div>

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

