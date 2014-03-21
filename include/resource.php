<?php
class Resource
{
    public $dir = ''; // directory where data lives (should be absolute pathname)
    public $id = ''; // Numeric code of resource, the same as basename of $dir.
    public $summary = '';
    public $name = '';
    public $category = '';
    public $license = '';
    public $file_list = array(); // each element of file-list is an array containing
                                  // the filename, then possibly an explanatory string.
    public $alternate_url_list = array(); // each element of alternate_url_list is an array
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
       $about_html = file_get_contents($dir . "/about.html");
       if ($about_html === false) {
         error_log("Warning: no such file $dir/about.html");
         $about_html = "No further information supplied";
       }
       return $about_html;
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
         $this->category = implode(' ', array_slice($strings, 1));
       } elseif ($strings[0] === 'license:') {
         $this->license = implode(' ', array_slice($strings, 1));
       } elseif ($strings[0] === 'file:') {
         if (count($strings) == 2) {  // just the filename.
           array_push($this->file_list, array($strings[1]));
         } elseif (count($strings) > 2) {  // filename with explanation.
           array_push($this->file_list, array($strings[1], implode(' ', array_slice($strings, 1))));
         }
       } elseif ($strings[0] == 'alternate_url:') {
         if (count($strings) == 2) {  // just the alternate url name.
           array_push($this->alternate_url_list, array($strings[1]));
         } elseif (count($strings) > 2) {  // alternate url name with explanation.
           array_push($this->alternate_url_list, array($strings[1], implode(' ', array_slice($strings, 1))));
         }
       }
     }
}
?>
