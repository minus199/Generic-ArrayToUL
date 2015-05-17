# Generic-ArrayToUL
Convert any array into an html unordered list, keeping the same structure.

This package allows you to output the list both in a form a full html page or just the ul element itself.
jQuery is used in order to add collapsable list functionality. 
There's a toggle button that is added to the main root of the list if flag supplied with true. Also, each parent element is clickable and will result with toggling the visibility of its children.

The whole point of this package is to allow converting any array to a pretty output which can also be maniuplated easliy with javascript.

<u>Tip:</u> it's fairly simple to change the <ul><li> into any other <parent><child> elements. Like <div><span>.
The functionality to choose which elements will be used is planned to added at some point.

<b>Install with composer</b>
<pre>{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/minus199/php_array_to_ul.git"
    }
  ],
  "require": {
    "minus199/php_array_to_ul": "dev-master"
  }
}
</pre>


Feel free to contact me at minus199@gmail.com  
