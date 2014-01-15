<?php $pages = get_content('content_type=page&subtype=dynamic&limit=1000');   ?>
<?php $posts_parent_page =  get_option('data-content-id', $params['id']); ?>
<script type="text/javascript">
 
 
 
  mw.live_edit_load_cats_list = function(){
	  mw.simpletab.set(mwd.getElementById('mw-live-edit-cats-tab'));
	   
	   mw.load_module('categories/manage', '#mw_add_cat_live_edit', function(){
	   })
  }
mw.load_quick_cat_edit = function($id){
       mw.simpletab.set(mwd.getElementById('mw-live-edit-cats-tab'));
  if($id == undefined){
    mw.$("#mw_select_cat_to_edit_dd").val();
  }
  mw.$("#mw_quick_edit_category").attr("data-category-id",$id);
  mw.load_module('categories/edit_category', '#mw_quick_edit_category', function(){
      $(mwd.body).removeClass("loading");
  });
}
 </script>

 
<div class="mw_simple_tabs mw_tabs_layout_simple">




 
	<ul style="margin: 0;" class="mw_simple_tabs_nav">
		<li><a class="active" href="javascript:;">
			<?php _e("Options"); ?>
			</a></li>
		<li><a href="javascript:;">
			<?php _e("Skin/Template"); ?>
			</a></li>
		<li id="mw-live-edit-cats-tab"><a href="javascript:;" onclick="mw.live_edit_load_cats_list()">
			<?php _e("Edit categories"); ?>
			</a></li>
	</ul>
	<a href="javascript:mw.load_quick_cat_edit(0);" class="mw-ui-btn" style="height: 15px;position: absolute;right: 13px;top: 12px;z-index: 1"><span>
	<?php _e("Add new category"); ?>
	</span></a>
	<div class="tab">
		<label class="mw-ui-label">
			<?php _e("Show Categories From"); ?>
		</label>
		<div class="mw-ui-select" style="width: 100%">
			<select name="data-content-id" class="mw_option_field">
				<option value="0"   <?php if((0 == intval($posts_parent_page))): ?>   selected="selected"  <?php endif; ?> title="<?php _e("None"); ?>">
				<?php _e("None"); ?>
				</option>
				<?php
$pt_opts = array();
          $pt_opts['link'] = "{empty}{title}";
          $pt_opts['list_tag'] = " ";
          $pt_opts['list_item_tag'] = "option";

      $pt_opts['active_ids'] =$posts_parent_page;


$pt_opts['include_categories'] =true;
          $pt_opts['active_code_tag'] = '   selected="selected"  ';



          pages_tree($pt_opts);


          ?>
			</select>
		</div>
	</div>
	<div class="tab semi_hidden">
		<module type="admin/modules/templates"  />
	</div>
	<div class="tab semi_hidden">
		<div id="mw_add_cat_live_edit"></div>
		<div id="mw_quick_edit_category"></div>
	</div>
</div>