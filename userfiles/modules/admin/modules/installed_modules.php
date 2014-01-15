<script  type="text/javascript">



mw.require('forms.js');


$(document).ready(function(){
   


    mw.$('#modules_categories_tree_<?php print $params['id']; ?>').prepend('<ul class="category_tree"><li><a href="#?category=0" data-category-id="0" onclick="mw.url.windowHashParam(\'category\', 0);return false;"><?php _e("All"); ?></a></li></ul>')

    mw.$('#modules_categories_tree_<?php print $params['id']; ?> li a').each(function(){
        var el = this;
        var id = el.attributes['data-category-id'].nodeValue;
        el.href = '#?category=' + id;
        el.className += ' cat-'+ id;
        el.setAttribute('onclick', 'mw.url.windowHashParam("category",' + id + ');return false;');
    });


    if(mw.url.getHashParams(window.location.hash).installed === '0' ){
       mw.$('.installed_switcher').addClass('mw-switcher-off');
       mwd.getElementById('installed_0').checked=true;
    }
    else{
       mw.$('.installed_switcher').removeClass('mw-switcher-off');
       mwd.getElementById('installed_1').checked=true;
    }

 var h = mw.hash();

if( h === '' || h=== '#' || h==='#?' ){
    _modulesSort();
}
else{
  var hash = mw.url.getHashParams(h);
  try {mwd.querySelector(".modules-index-bar input[value='"+hash.ui+"']").checked = true; } catch(e){}
}



});



</script>
<script  type="text/javascript">
function mw_reload_all_modules(){

	mw.$('#modules_admin_<?php print $params['id']; ?>').attr('reload_modules',1);
	mw.$('#modules_admin_<?php print $params['id']; ?>').attr('cleanup_db',1);

  	 mw.load_module('admin/modules/manage','#modules_admin_<?php print $params['id']; ?>');
	// mw.$('#modules_admin_<?php print $params['id']; ?>').removeAttr('cleanup_db');

}


_modulesSort = function(){

    var hash = mw.url.getHashParams(window.location.hash);

    //hash.ui === undefined ? mw.url.windowHashParam('ui', 'admin') : '' ;
   // hash.category === undefined ? mw.url.windowHashParam('category', '0') : '' ;





    var attrs  = mw.url.getHashParams(window.location.hash);
    var holder = mw.$('#modules_admin_<?php print $params['id']; ?>');

    var arr = ['data-show-ui','data-search-keyword','data-category','data-installed','install_new'], i=0, l=arr.length;

    var sync = ['ui','search','category','installed','install_new'];

    for(;i<l;i++){
      holder.removeAttr(arr[i]);
    }

	if(hash.ui === undefined){
		 holder.attr('data-show-ui', 'admin');
	}



    for (var x in attrs){
        if(x==='category' && (attrs[x]==='0' || attrs[x]===undefined)) continue;
        holder.attr(arr[sync.indexOf(x)], attrs[x]);
    }
    mw.load_module('admin/modules/manage','#modules_admin_<?php print $params['id']; ?>', function(){
      $('#module_keyword').removeClass('loading');

	  var el = $( "#modules_admin_<?php print $params['id']; ?> .mw-modules-admin" );
 // $( "#modules_admin_<?php print $params['id']; ?> .mw-modules-admin" ).sortable('destroy');
        el.sortable({
		handle: ".mw_admin_modules_sortable_handle",
		items: "li",
        axis:'y',
		update: function(){
          var serial = el.sortable('serialize');
          $.ajax({
            url: mw.settings.api_url+'module/reorder_modules',
            type:"post",
            data:serial
          });
        }
    });
    });
}


mw.on.hashParam('ui', _modulesSort);

mw.on.hashParam('search', function(){
  _modulesSort();

  var field = mwd.getElementById('module_keyword');

  if(!field.focused){
    field.value = this;
  }

});

mw.on.hashParam('install_new', function(){

  _modulesSort();

});



mw.on.hashParam('category', function(){
  _modulesSort();
  mw.$("#mw_index_modules a.active").removeClass('active');
  mw.$("#mw_index_modules .cat-"+this).addClass('active');
});
mw.on.hashParam('installed', function(){

    _modulesSort();

});



</script>

<div id="mw_index_modules">
  <div class="mw_edit_page_left mw_edit_page_default" id="mw_edit_page_left">
    <div class="mw-admin-sidebar"><h2 class="mw-side-main-title"><span class="ico imanage-module"></span><span><?php _e("Modules"); ?></span></h2></div>
    <div class="mw-admin-side-nav" id="modules_categories_tree_<?php print $params['id']; ?>" >

      <module type="categories" data-for="modules" id="modules_admin_categories_<?php print $params['id']; ?>" />
      <div style="padding-left: 46px">
        <div class="vSpace"></div>
        <label class="mw-ui-label"><?php _e("Show"); ?>: </label>
        <div onmousedown="mw.switcher._switch(this);" class="mw-switcher unselectable installed_switcher"> <span class="mw-switch-handle"></span>
          <label><?php _e("Installed"); ?>
            <input type="radio" name="installed" checked="checked" onchange="mw.url.windowHashParam('installed', 1);" id="installed_1" />
          </label>
          <label><?php _e("Uninstalled"); ?>
            <input type="radio" name="installed" onchange="mw.url.windowHashParam('installed', 0);" id="installed_0"  />
          </label>
        </div>
        <div class="vSpace">&nbsp;</div>
        <a href="javascript:mw.url.windowHashParam('install_new', 1);" class="mw-ui-btn" style="width: 147px;margin-left: -47px;"><span class="ico iplus"></span><span><?php _e("Add new modules"); ?></span></a>
   <div class="vSpace">&nbsp;</div>

<small  onclick="mw_reload_all_modules()" class="mw-ui-link-small"><?php _e("Reload modules"); ?></small>




         </div>
    </div>
  </div>
  <div class="mw_edit_page_right" style="padding: 20px;">







    <div class="modules-index-bar"> <span class="mw-ui-label-help font-11 left"><?php _e("Sort modules"); ?>:</span>
      <?php $def =  _e("Search for modules", true);  ?>
      <input
        name="module_keyword"
        id="module_keyword"
        autocomplete="off"
        class="mw-ui-searchfield right"
        type="text"
        value="<?php print $def; ?>"
        data-default='<?php print $def; ?>'
        onfocus='mw.form.dstatic(event);'
        onblur='mw.form.dstatic(event);'
        onkeyup="mw.form.dstatic(event);mw.on.stopWriting(this, function(){mw.url.windowHashParam('search', this.value)});"
          />
      <div class="mw_clear"></div>
      <ul class="mw-ui-inline-selector">
        <li>
          <label class="mw-ui-check">
            <input name="module_show"  class="mw_modules_filter_show"  type="radio" value="live_edit" onchange="mw.url.windowHashParam('ui', this.value)" />
            <span></span><span><?php _e("Live edit modules"); ?></span></label>
        </li>
        <li>
          <label class="mw-ui-check">
            <input name="module_show" class="mw_modules_filter_show" type="radio" value="admin"   checked="checked"  onchange="mw.url.windowHashParam('ui', this.value)" />
            <span></span><span><?php _e("Admin modules"); ?></span></label>
        </li>
        <li>
          <label class="mw-ui-check">
            <input name="module_show"  class="mw_modules_filter_show"  type="radio" value="advanced"  onchange="mw.url.windowHashParam('ui', this.value)" />
            <span></span><span><?php _e("Advanced"); ?></span></label>
        </li>
      </ul>
    </div>
    <div class="vSpace"></div>
    <div id="modules_admin_<?php print $params['id']; ?>" ></div>
  </div>
</div>

