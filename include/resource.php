<?php
class Resource
{
    public $dir = ''; // directory where data lives (should be absolute pathname)
    public $id = ''; // Numeric code of resource, the same as basename of $dir.
    public $summary = '';
    public $name = '';
    public $category = '';
    public $license = '';
    public $files = array(); // each element of file-list is an array containing
                                  // the filename, then possibly an explanatory string.
    public $alternate_urls = array(); // each element of alternate_urls is an array
                                  // containing the alternate URL, then possibly an 
                                  // explanatory string.

    // Resource describes
    public function __construct($dir) {
       $this->dir = $dir;
       $this->id = basename($dir);
       if ( !(intval($this->id) > 0) ) {
         if ($this->id !== "." && $this->id !== "..") {
           error_log("Base of directory name $dir is not numeric");
         }
         $this->dir = '';
         return;
       }
       if ( !($handle = fopen($dir . "/info.txt", "r")) ) {
         error_log("No such file $dir/info.txt");
         $this->dir = '';
         return;
       }
       while (($line = fgets($handle)) !== false) {
         $this->parse_info_line($line);
       }
     }
     public function get_about_html() {
       $about_html = file_get_contents($this->dir . "/about.html");
       return $about_html; // may be false if not found; calling script will deal with it.
     } 
     public function ok () { return ($this->dir !== ''); }

     private function parse_info_line($line) {
       // This doesn't make sure that $line is suitable to be parsed in this way.
       // If it has multiple spaces between fields we'll get the wrong answer.
       // We'll write a separate script to statically check the correctness of the 
       // info.txt files 

       $strings = explode(' ', $line);

       if ($strings[0] === 'summary:') {
         $this->summary = implode(' ', array_slice($strings, 1));
       } elseif ($strings[0] === 'name:') {
         $this->name = implode(' ', array_slice($strings, 1));
       } elseif ($strings[0] === 'category:') {
         $this->category = ucfirst(implode(' ', array_slice($strings, 1)));
       } elseif ($strings[0] === 'license:') {
         $this->license = implode(' ', array_slice($strings, 1));
       } elseif ($strings[0] === 'file:') {
         if (count($strings) == 2) {  // just the filename.
           array_push($this->files, array($strings[1]));
         } elseif (count($strings) > 2) {  // filename with explanation.
           array_push($this->files, array($strings[1], implode(' ', array_slice($strings, 2))));
         }
       } elseif ($strings[0] == 'alternate_url:') {
         if (count($strings) == 2) {  // just the alternate url name.
           array_push($this->alternate_urls, array($strings[1]));
         } elseif (count($strings) > 2) {  // alternate url name with explanation.
           array_push($this->alternate_urls, array($strings[1], implode(' ', array_slice($strings, 2))));
         }
       }
     }
}

function get_resource_id_list($root) {
  // This function, given a root directory (e.g. /var/www/openslr/resources/,
  // returns a list of resource-ids, e.g. ('1', '2', '3', ... ), derived
  // from iterating over the directory, finding numeric subdirectories, 
  // and sorting them.
  $dir = new DirectoryIterator($root);
  $id_list = array();
  foreach ($dir as $fileinfo) {
    $filename = $fileinfo->getFilename();
    if ($filename === strval(intval($filename))) { // filename is an integer
      array_push($id_list, intval($filename)); // push onto array as integer.
    }
  }
  sort($id_list, SORT_NUMERIC); // sort $id_list by numeric value.
  for ($n = 0; $n < count($id_list); $n++) { // convert back into strings.
    $id_list[$n] = strval($id_list[$n]);
  }
  return $id_list;
}

?>
