<?php

/*

type: layout

name: Default

description: Default Menu skin

*/

  //$template_file = false; ?>

<script>mw.moduleCSS("<?php print $config['url_to_module']; ?>style.css", true);</script>



<div class="module-navigation module-navigation-default">
      <?php
      $mt =  menu_tree($menu_filter);
      if($mt != false){
        print ($mt);
      }
      else {
        print lnotif("There are no items in the menu <b>".$params['menu-name']. '</b>');
      }
      ?>
</div>
