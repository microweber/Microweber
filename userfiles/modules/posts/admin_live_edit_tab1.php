<?php

only_admin_access();
 
$set_content_type = 'post';
if(isset($params['global']) and $params['global'] != false){
	$set_content_type =  get_option('data-content-type', $params['id']); 
}
$rand = uniqid(); ?>
<script type="text/javascript">

    function mw_reload_content_mod_window(){
		
	
	$(mwd.body).ajaxStop(function() {
		
		 setTimeout(function(){
		 
		 window.location.reload()
		 
		 },100)
		
	});
		
		
	
		 
		 
		 
		 
 	}
 

 </script>
<?php if(!isset($is_shop) or $is_shop == false): ?>
<?php $is_shop = false; $pages = get_content('content_type=page&subtype=dynamic&is_shop=n&limit=1000');   ?>
<?php else:  ?>
<?php $pages = get_content('content_type=page&is_shop=y&limit=1000');   ?>
<?php endif; ?>
<?php $posts_parent_page =  get_option('data-page-id', $params['id']); ?>
<?php if(isset($params['global']) and $params['global'] != false) :  ?>
<?php if($set_content_type =='product'):  ?>
<?php $is_shop = 1; $pages = get_content('content_type=page&is_shop=y&limit=1000');   ?>
<?php endif; ?>




<label class="mw-ui-label"><?php _e("Content type"); ?></label>
<div class="mw-ui-select" style="width: 100%;">
  <select name="data-content-type" id="the_post_data-content-type<?php print  $rand ?>"  class="mw_option_field"  onchange="mw_reload_content_mod_window()"  >
    <option  <?php if(('' == trim($set_content_type))): ?>   selected="selected"  <?php endif; ?>><?php _e("Choose content type"); ?></option>
    <option  value="page"    <?php if(('page' == trim($set_content_type))): ?>   selected="selected"  <?php endif; ?>><?php _e("Pages"); ?></option>
    <option  value="post"    <?php if(('post' == trim($set_content_type))): ?>   selected="selected"  <?php endif; ?>><?php _e("Posts"); ?></option>
    <option  value="product"    <?php if(('product' == trim($set_content_type))): ?>   selected="selected"  <?php endif; ?>><?php _e("Product"); ?></option>
    <option  value="none"   <?php if(('none' == trim($set_content_type))): ?>   selected="selected"  <?php endif; ?>><?php _e("None"); ?></option>
  </select>
</div>
<?php endif; ?>
<?php if(!isset($set_content_type) or $set_content_type != 'none') :  ?>
<div class="mw-ui-field-holder">
<label class="mw-ui-label"><?php _e("Display"); ?> <?php print ($set_content_type) ?>  <?php _e("from page"); ?></label>
<div class="mw-ui-select" style="width: 100%;">
  <select name="data-page-id" id="the_post_data-page-id<?php print  $rand ?>"  class="mw_option_field" onchange="mw_reload_content_mod_window()"   >
    <option  value="current_page"    <?php if(('current_page' == ($posts_parent_page))): ?>   selected="selected"  <?php endif; ?>>--<?php _e("Current page"); ?></option>

    <option  value="0"    <?php if($posts_parent_page != 'current_page' and (0 == intval($posts_parent_page))): ?>   selected="selected"  <?php endif; ?>><?php _e("All pages"); ?></option>
	 
    <?php
$pt_opts = array();
  $pt_opts['link'] = "{empty}{title}";
$pt_opts['list_tag'] = " ";
$pt_opts['list_item_tag'] = "option";
//$pt_opts['include_categories'] = "option";
$pt_opts['active_ids'] = $posts_parent_page;
$pt_opts['remove_ids'] = $params['id'];
$pt_opts['active_code_tag'] = '   selected="selected"  ';
 if($is_shop != false){
	 $pt_opts['is_shop'] = 'y';
 }
 if($set_content_type == 'product'){
	  $pt_opts['is_shop'] = 'y';
}


 pages_tree($pt_opts);

  ?>
  </select>
</div>
</div>

<?php if($posts_parent_page != false and intval($posts_parent_page) > 0): ?>
<?php $posts_parent_category =  get_option('data-category-id', $params['id']); ?>

<div class="mw-ui-field-holder">
<label class="mw-ui-label"><?php _e("Show only from category"); ?></label>
<div class="mw-ui-select" style="width: 100%;">
  <select name="data-category-id" id="the_post_data-page-id<?php print  $rand ?>"  class="mw_option_field"   data-also-reload="<?php print  $config['the_module'] ?>"    >

    <option  value=''  <?php if((0 == intval($posts_parent_category))): ?>   selected="selected"  <?php endif; ?>><?php _e("Select a category"); ?></option>

    <?php
        $pt_opts = array();
        $pt_opts['link'] = "{empty}{title}";
        $pt_opts['list_tag'] = " ";
        $pt_opts['list_item_tag'] = "option";
        $pt_opts['active_ids'] = $posts_parent_category;
        $pt_opts['active_code_tag'] = '   selected="selected"  ';
        $pt_opts['rel'] = 'content';
        $pt_opts['rel_id'] = $posts_parent_page;
        category_tree($pt_opts);
  ?>
  	      <option  value='0'  <?php if((0 == intval($posts_parent_category))): ?>   selected="selected"  <?php endif; ?>>--<?php _e("None"); ?></option>

  </select>
</div>
</div>
<?php endif; ?>
<?php $show_fields =  get_option('data-show', $params['id']);
if(is_string($show_fields)){
$show_fields = explode(',',$show_fields);
 $show_fields = array_trim($show_fields);
}
if($show_fields == false or !is_array($show_fields)){
$show_fields = array();	
}

 ?>
<style type="text/css">

    .mw-ui-check input + span + span, body{
      font-size: 11px;
    }

    .fields-controlls li{
      list-style: none;
      clear: both;
      min-height: 32px;
      overflow: hidden;
      padding: 6px 6px 4px;
      border-radius:2px;
    }
    .fields-controlls li:hover{background: #F8F8F8}

    .mw-ui-label-horizontal{
      display: inline-block;
      float: left;
      text-align: right;
      padding-right: 10px;
      margin-top: 5px;
    }

    .mw-ui-check input + span{
      top: 5px;
    }

    </style>
<div class="vSpace"></div>
<label class="mw-ui-label"><?php _e("Display on"); ?> <?php print ($set_content_type) ?>: </label>
<ul id="post_fields_sort_<?php print  $rand ?>" class="fields-controlls">
  <li>
    <label class="mw-ui-check left">
      <input type="checkbox" name="data-show" value="thumbnail" class="mw_option_field" <?php if(in_array('thumbnail',$show_fields)): ?>   checked="checked"  <?php endif; ?> />
      <span></span> <span><?php _e("Thumbnail"); ?></span> </label>
    <div class="right">
      <label class="mw-ui-label-horizontal"><?php _e("Size"); ?></label>
      <input name="data-thumbnail-size" class="mw-ui-field mw_option_field"   type="text" style="width:65px;" placeholder="250x200"  value="<?php print get_option('data-thumbnail-size', $params['id']) ?>" />
    </div>
  </li>
  <li>
    <label class="mw-ui-check">
      <input type="checkbox" name="data-show" value="title" class="mw_option_field" <?php if(in_array('title',$show_fields)): ?>   checked="checked"  <?php endif; ?> />
      <span></span> <span><?php _e("Title"); ?></span></label>
    <div class="right">
      <label class="mw-ui-label-horizontal"><?php _e("Length"); ?></label>
      <input name="data-title-limit" class="mw-ui-field mw_option_field"   type="text" placeholder="255" style="width:65px;"  value="<?php print get_option('data-title-limit', $params['id']) ?>" />
    </div>
  </li>
  <li>
    <label class="mw-ui-check">
      <input type="checkbox" name="data-show" value="description" class="mw_option_field" <?php if(in_array('description',$show_fields)): ?>   checked="checked"  <?php endif; ?> />
      <span></span> <span><?php _e("Description"); ?></span></label>
    <div class="right">
      <label class="mw-ui-label-horizontal"><?php _e("Length"); ?></label>
      <input name="data-character-limit" class="mw-ui-field mw_option_field"   type="text" placeholder="80" style="width:65px;"  value="<?php print get_option('data-character-limit', $params['id']) ?>" />
    </div>
  </li>
  <?php if($is_shop): ?>
  <li>
    <label class="mw-ui-check">
      <input type="checkbox" name="data-show" value="price" class="mw_option_field" <?php if(in_array('price',$show_fields)): ?>   checked="checked"  <?php endif; ?> />
      <span></span> <span><?php _e("Show price"); ?></span></label>
  </li>
  <li>  
    <label class="mw-ui-check">
      <input type="checkbox" name="data-show" value="add_to_cart" class="mw_option_field"  <?php if(in_array('add_to_cart',$show_fields)): ?>   checked="checked"  <?php endif; ?> />
      <span></span> <span><?php _e("Add to cart button"); ?></span></label>
    <div class="right">
      <label class="mw-ui-label-horizontal"><?php _e("Title"); ?></label>
      <input name="data-add-to-cart-text" class="mw-ui-field mw_option_field" style="width:65px;" placeholder="<?php _e("Add to cart"); ?>"  type="text"    value="<?php print get_option('data-add-to-cart-text', $params['id']) ?>" />
    </div>
  </li>
  <?php endif; ?>
  <li>
    <label class="mw-ui-check">
      <input type="checkbox" name="data-show" value="read_more" class="mw_option_field"  <?php if(in_array('read_more',$show_fields)): ?>   checked="checked"  <?php endif; ?> />
      <span></span> <span><?php _e("Read More Link"); ?></span></label>
    <div class="right">
      <label class="mw-ui-label-horizontal"><?php _e("Title"); ?></label>
      <input name="data-read-more-text" class="mw-ui-field mw_option_field"   type="text" placeholder="<?php _e("Read more"); ?>" style="width:65px;"   value="<?php print get_option('data-read-more-text', $params['id']) ?>" />
    </div>
  </li>
  <li>
    <label class="mw-ui-check">
      <input type="checkbox" name="data-show" value="created_on" class="mw_option_field"  <?php if(in_array('created_on',$show_fields)): ?>   checked="checked"  <?php endif; ?> />
      <span></span> <span><?php _e("Date"); ?></span></label>
  </li>
  <li>
    <label class="mw-ui-check left">
      <input type="checkbox" name="data-hide-paging" value="y" class="mw_option_field" <?php if(get_option('data-hide-paging', $params['id']) =='y'): ?>   checked="checked"  <?php endif; ?> />
      <span></span><span><?php _e("Hide paging"); ?></span></label>
    <div class="right">
      <label class="mw-ui-labe-horizontall"><?php _e("Posts per page"); ?></label>
      <input name="data-limit" class="mw-ui-field mw_option_field"   type="number"  style="width:65px;" placeholder="10"  value="<?php print get_option('data-limit', $params['id']) ?>" />
    </div>
  </li>
  <li>
    <label class="mw-ui-check left">
      <?php $ord_by = get_option('data-order-by', $params['id']); ?>
      <span></span><span><?php _e("Order by"); ?></span></label>
    <div class="right">
      <div class="mw-ui-select" >
        <select name="data-order-by"   class="mw_option_field" data-also-reload="<?php print  $config['the_module'] ?>"   >
          <option  value=""    <?php if((0 == intval($ord_by))): ?>   selected="selected"  <?php endif; ?>><?php _e("Position"); ?> (ASC)</option>
          <option  value="position asc"    <?php if(('position asc' == trim($ord_by))): ?>   selected="selected"  <?php endif; ?>><?php _e("Position"); ?> (DESC)</option>
          <option  value="created_on desc"    <?php if(('created_on desc' == trim($ord_by))): ?>   selected="selected"  <?php endif; ?>><?php _e("Date"); ?> (ASC)</option>
          <option  value="created_on asc"    <?php if(('created_on asc' == trim($ord_by))): ?>   selected="selected"  <?php endif; ?>><?php _e("Date"); ?> (DESC)</option>
          <option  value="title asc"    <?php if(('title asc' == trim($ord_by))): ?>   selected="selected"  <?php endif; ?>><?php _e("Title"); ?> (ASC)</option>
          <option  value="title desc"    <?php if(('title desc' == trim($ord_by))): ?>   selected="selected"  <?php endif; ?>><?php _e("Title"); ?> (DESC)</option>
        </select>
      </div>
    </div>
  </li>
</ul>
<?php endif; ?>
