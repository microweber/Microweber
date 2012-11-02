<?php $rand = uniqid(); ?>
  <?php $my_tree_id = crc32(url_string()); ?>
 
<script  type="text/javascript">






$(document).ready(function(){
    mw.onLive(function(){
      set_pagetab_size();
    });

    mw_append_pages_tree_controlls();

    mw.on.hashParam("page-posts", function(){
        mw_set_edit_posts(this);
    });

    mw.on.moduleReload("pages_tree_toolbar", function(){
        mw_append_pages_tree_controlls();
    });

});







function mw_delete_content($p_id){
	 mw.$('#pages_edit_container').attr('data-content-id',$p_id);
  	 mw.load_module('content/edit_post','#pages_edit_container');
}


mw_edit_btns = function(type, id){
  if(type==='page'){

    return "\
    <span class='mw_del_tree_content' onclick='event.stopPropagation();mw.tools.tree.del("+id+");' title='<?php _e("Delete"); ?>'>\
          <?php _e("Delete"); ?>\
      </span>\
    <span class='mw_ed_tree_content' onclick='event.stopPropagation();mw.url.windowHashParam(\"action\", \"editpage:"+id+"\");return false;' title='<?php _e("Edit"); ?>'>\
          <?php _e("Edit"); ?>\
      </span>\
      ";

  }
  else if(type==='category'){
      return "\
        <span class='mw_del_tree_content' onclick='event.stopPropagation();mw.tools.tree.del("+id+");' title='<?php _e("Delete"); ?>'>\
              <?php _e("Delete"); ?>\
          </span>\
        <span class='mw_ed_tree_content' onclick='event.stopPropagation();mw.url.windowHashParam(\"action\", \"editcategory:"+id+"\");return false;' title='<?php _e("Edit"); ?>'>\
              <?php _e("Edit"); ?>\
          </span>\
      ";
  }
}


function mw_append_pages_tree_controlls(holder){

    var holder = holder || "#pages_tree_container_<?php print $my_tree_id; ?>";

    mw.$(holder+' a').each(function(){
        var el = this;
        var href = el.href;
        el.href = 'javascript:void(0);';
        var html = el.innerHTML;
        var toggle = "";
        var show_posts = "";
        var attr = el.attributes;


        var toggle ='';

        // type: page or category
        if(attr['data-page-id']!==undefined){
            var pageid = attr['data-page-id'].nodeValue;
            if($(el.parentNode).children('ul').length>0){
                var toggle = '<span onclick="mw.tools.tree.toggleit(this.parentNode,event,'+pageid+')" class="mw_toggle_tree"></span>';
            }
            var show_posts = "<span class='mw_ed_tree_show_posts' title='<?php _e("Go Live edit"); ?>' onclick='event.stopPropagation();window.location.href=\""+href+"/editmode:y\"'></span>";
            el.innerHTML = '<span class="pages_tree_link_text">'+html+'</span>' + mw_edit_btns('page', pageid) + toggle + show_posts;
            el.setAttribute("onclick", "mw.tools.tree.openit(this,event,"+pageid+")");
        }
        else if(attr['data-category-id']!==undefined){
            var pageid = attr['data-category-id'].nodeValue;
            if($(el.parentNode).children('ul').length>0){
                var toggle = '<span onclick="mw.tools.tree.toggleit(this.parentNode,event,'+pageid+')" class="mw_toggle_tree"></span>';
            }
            var show_posts = "<span class='mw_ed_tree_show_posts' title='<?php _e("Go Live edit"); ?>' onclick='event.stopPropagation();window.location.href=\""+href+"/editmode:y\"'></span>";
            el.innerHTML = '<span class="pages_tree_link_text">'+html+'</span>' + mw_edit_btns('category', pageid) + toggle + show_posts;
            el.setAttribute("onclick", "mw.tools.tree.openit(this,event,"+pageid+");");
        }

    });


    mw.tools.tree.recall(mwd.querySelector(holder));

    mw.log(mwd.querySelector(holder).id)
    mw.log(mw.cookie.ui("tree_"+mwd.querySelector(holder).id))

}


function mw_select_page_for_editing($p_id){
    mw.$('#pages_edit_container').attr('data-page-id',$p_id);
    mw.$('#pages_edit_container').attr('data-type','content/edit_page');
    mw.$('#pages_edit_container').removeAttr('data-subtype');
    mw.$('#pages_edit_container').removeAttr('data-content-id');
    mw.load_module('content/edit_page','#pages_edit_container');
}

mw.on.hashParam("action", function(){
  var arr = this.split(":");

  if(arr[0]==='new'){
      if(arr[1]==='page'){
        mw_select_page_for_editing(0);
      }
      else if(arr[1]==='post'){
        mw_select_post_for_editing(0);
      }
      else if(arr[1]==='category'){
        mw_select_category_for_editing(0);
      }
      else if(arr[1]==='product'){
        mw_add_product(0);
      }

     mw.$(".mw_action_nav").addClass("not-active");
     mw.$(".mw_action_"+arr[1]).removeClass("not-active");
  }
  else{
      mw.$(".active-bg").removeClass('active-bg');
      mw.$(".mw_action_nav").removeClass("not-active");
      var active_item = mw.$(".item_"+arr[1]);
      active_item.addClass('active-bg');
      active_item.parents("li").addClass('active');
      if(arr[0]==='editpage'){
        mw_select_page_for_editing(arr[1])
      }
      else if(arr[0]==='showposts'){
        mw_set_edit_posts(arr[1])
      }
      else if(arr[0]==='showpostscat'){
        mw_set_edit_posts(arr[1], true)
      }
      else if(arr[0]==='editcategory'){
        mw_select_category_for_editing(arr[1])
      }
      else if(arr[0]==='editpost'){
          mw_select_post_for_editing(arr[1]);
      }
  }



 mw.on.hashParam("search", function(){

 mw.$('#pages_edit_container').attr("data-type",'content/manage');

   var dis = this.trim();
   if(dis!==''){
     mw.$('#pages_edit_container').attr("data-keyword", dis);
   }
   else{
      mw.$('#pages_edit_container').removeAttr("data-keyword");
      mw.url.windowDeleteHashParam('search')
   }
   mw.reload_module('#pages_edit_container');
 });







});
























function mw_select_category_for_editing($p_id){
	 mw.$('#pages_edit_container').attr('data-category-id',$p_id);
  	 mw.load_module('categories/edit_category','#pages_edit_container');
}




function mw_set_edit_posts($in_page, $is_cat){
       mw.$('#pages_edit_container').removeAttr('data-content-id');
	 mw.$('#pages_edit_container').removeAttr('data-page-id');
      mw.$('#pages_edit_container').removeAttr('data-category-id');

if($in_page != undefined && $is_cat == undefined){
 mw.$('#pages_edit_container').attr('data-page-id',$in_page);
}

if($in_page != undefined && $is_cat != undefined){
 mw.$('#pages_edit_container').attr('data-category-id',$in_page);
}







//	mw.$('#pages_tree_container_').empty();
//	mw.$('#pages_edit_container').empty();
//	 mw.$('#pages_tree_container_').attr('data-limit','10');






	 mw.load_module('content/manage','#pages_edit_container', function(){




	 });






	 mw.$('#pages_edit_container .paging a').live('click',function() {

	 $p_id = $(this).attr('data-page-number');
	 $p_param = $(this).attr('data-paging-param');
	 mw.$('#pages_edit_container').attr('data-page-number',$p_id);
	 mw.$('#pages_edit_container').attr('data-page-param',$p_param);




	 mw.load_module('posts','#pages_edit_container', function(){



	 });




		 return false;
	 });






}




function mw_select_post_for_editing($p_id){
	 mw.$('#pages_edit_container').attr('data-content-id',$p_id);
	 	 	 mw.$('#pages_edit_container').removeAttr('data-subtype', 'post');

  	 mw.load_module('content/edit_post','#pages_edit_container');
}

function mw_add_product(){
	 mw.$('#pages_edit_container').attr('data-content-id',0);
	 mw.$('#pages_edit_container').attr('data-subtype','product');

  	 mw.load_module('content/edit_post','#pages_edit_container');
}






</script>

<div id="mw_edit_pages">
  <div id="mw_edit_pages_content">
    <div class="mw_edit_page_left" id="mw_edit_page_left">
      <div class="mw_edit_pages_nav">
        <h2 class="mw_tree_title"><?php _e("Website  Navigation"); ?></h2>
        <span class="mw_action_nav mw_action_page" onclick="mw.url.windowHashParam('action','new:page');">
        <label>Page</label>
        <button></button>
        </span> <span class="mw_action_nav mw_action_post" onclick="mw.url.windowHashParam('action','new:post');">
        <label>Post</label>
        <button>&nbsp;</button>
        </span> <span class="mw_action_nav mw_action_category" onclick="mw.url.windowHashParam('action','new:category');">
        <label>Category</label>
        <button>&nbsp;</button>
        </span> <span class="mw_action_nav mw_action_product" onclick="mw.url.windowHashParam('action','new:product');">
        <label>Product</label>
        <button>&nbsp;</button>
        </span>
        <?php /*  <button onclick="mw_set_edit_categories()">mw_set_edit_categories</button>
        <button onclick="mw_set_edit_posts()">mw_set_edit_posts</button>
 */ ?>
      </div>

      <div class="mw_pages_posts_tree mw-tree"  id="pages_tree_container_<?php print $my_tree_id; ?>">
        <?
	  $is_shop_str = '';
	   if(isset($is_shop)){
		 $is_shop_str = " is_shop='{$is_shop}' "   ;
	   }
	   ?>
        <module data-type="pages_menu" include_categories="true" id="pages_tree_toolbar" <? print $is_shop_str ?>    />

        <div class="mw-clear"></div>





      </div>
      <div class="tree-show-hide-nav">
            <a href="javascript:;" class="mw-ui-btn" onclick="mw.tools.tree.openAll(mwd.getElementById('pages_tree_container_<?php print $my_tree_id; ?>'));">Open All</a>
            <a class="mw-ui-btn" href="javascript:;" onclick="mw.tools.tree.closeAll(mwd.getElementById('pages_tree_container_<?php print $my_tree_id; ?>'));">Close All</a>
        </div>
    </div>
    <div class="mw_edit_page_right" style="width: 70%">


    <input type="text" id="mw-search-field"  />

    <script>

    $(document).ready(function(){

        mw.$("#mw-search-field").bind("keyup", function(){
          mw.on.stopWriting(this, function(){
              mw.url.windowHashParam('search',this.value);
          });
        });


    });


    </script>



      <div class="mw_edit_pages_nav"> </div>
      <div id="pages_edit_container">
        <module data-type="content/edit_page" id="edit_content_admin_"  />
      </div>
    </div>
  </div>
</div>
