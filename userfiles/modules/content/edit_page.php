<?
if(!isset($edit_post_mode)){
	$edit_post_mode = false;
}

 

if(!isset($params["data-page-id"])){
	$params["data-page-id"] = PAGE_ID;
}
 
if(isset($params["data-content-id"])){
	$params["data-page-id"] = $params["data-content-id"];
}

if(isset($params["data-content"])){
	$params["data-page-id"] = $params["data-content"];
}
 //d($params);

$data = get_content_by_id($params["data-page-id"]); 
 
if($data == false or empty($data )){
include('_empty_content_data.php');	
}


if(isset($params["data-is-shop"])){
	$data["is_shop"] = $params["data-is-shop"];
}




$form_rand_id = $rand = uniqid();
?>



<br /><br /><br /><br />

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<input class="mw-ui" type="radio" id="tchk22" name="yo" />
<label for="tchk22"></label>
<br /><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input class="mw-ui"  checked="checked" name="yo" type="radio" id="tchk2211" />
<label for="tchk2211"></label>

<br /><br /><br /><br />

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

<input class="mw-ui" type="checkbox" id="tchk221" name="yo1" /><label for="tchk221"></label>
<br /><br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;






<input class="mw-ui"  name="yo1" type="checkbox" id="tchk22115" />



<label for="tchk22115"></label>






<script  type="text/javascript">

mw.require('forms.js');
 

$(document).ready(function(){
	
	 
	 
	 mw.$('#admin_edit_page_form_<? print $form_rand_id ?>').submit(function() { 

 mw_before_content_save<? print $rand ?>()
 mw.form.post(mw.$('#admin_edit_page_form_<? print $form_rand_id ?>') , '<? print site_url('api/save_content') ?>', function(){
	 
	 mw_after_content_save<? print $rand ?>();
	 
	 });
 
  
//  var $pmod = $(this).parent('[data-type="<? print $config['the_module'] ?>"]');
 	 
		  // mw.reload_module($pmod);

 return false;
 
 
 });
   
   
    mw.$('#go_live_edit_<? print $rand ?>').click(function() { 
	

mw_before_content_save<? print $rand ?>()
 
 	<? if(intval($data['id']) == 0): ?>
 mw.form.post(mw.$('#admin_edit_page_form_<? print $form_rand_id ?>') , '<? print site_url('api/save_content') ?>', function(){
  mw_after_content_save<? print $rand ?>(this);
});

<? else: ?>
  mw_after_content_save<? print $rand ?>('<? print $data['id'] ?>');

 <? endif; ?>

	 

 return false;
 
 
 });
 
 
 function mw_before_content_save<? print $rand ?>(){
	mw.$('#admin_edit_page_form_<? print $form_rand_id ?> .module[data-type="custom_fields"]').empty();
 }
 
 function mw_after_content_save<? print $rand ?>($id){
	
	mw.reload_module('[data-type="pages_menu"]');
	  <? if($edit_post_mode != false): ?>
mw.reload_module('[data-type="posts"]');
	<? endif; ?>
	
	
	
	mw.reload_module('#admin_edit_page_form_<? print $form_rand_id ?> .module[data-type="custom_fields"]');
	if($id != undefined){
				$id = $id.replace(/"/gi, "");
				$.get('<? print site_url('api_html/content_link/') ?>'+$id, function(data) {
					//console.log(data);
			   window.location.href = data+'/editmode:y';
			  
			}); 
	
	}
	
	 
 }
   
   
 


 
   
});
</script>

<form  id="admin_edit_page_form_<? print $form_rand_id ?>" class="mw_admin_edit_content_form mw-ui-form">
  <input name="id"  type="hidden" value="<? print ($data['id'])?>" />
  Page name
  <input name="title"  type="text" value="<? print ($data['title'])?>" />
  <br />
  url
  <input name="url"  type="text" value="<? print ($data['url'])?>" />
  <?  if(!isset($data["thumbnail"])){
	   $data['thumbnail'] = '';
	  
  }?>
 
   thumbnail
  <input name="thumbnail"  type="text" value="<? print ($data['thumbnail'])?>" />
  <? if($edit_post_mode == false): ?>
  <module data-type="content/layout_selector" data-page-id="<? print ($data['id'])?>"  />
  <? endif; ?>
  parent
  <? if($edit_post_mode != false): ?>
  <?
  
  if(!isset($params["subtype"])){
	  if(intval($data['id']) != 0){
		  if(isset($data["subtype"]) and trim($data["subtype"]) != ''){
			  $params['subtype'] = $data["subtype"];
		  } else {
			  $params['subtype'] = 'post';
		  }
	  } else {
		  $params['subtype'] = 'post';
	  }
	
}

 ?>
  <? if(isset($params['subtype']) and $params['subtype'] == 'product'): ?>
  <? $pages = get_content('content_type=page&subtype=dynamic&is_shop=y&limit=1000');   ?>
  <? else: ?>
  <? $pages = get_content('content_type=page&subtype=dynamic&is_shop=n&limit=1000');   ?>
  <? endif; ?>
  <? if(!isset($params['subtype'])): ?>
  <?   $params['subtype'] = 'post'; ?>
  <? endif; ?>
  <input name="subtype"  type="hidden"  value="<? print $params['subtype'] ?>" >
  <? if(!empty($pages)): ?>
  <div class="mw-ui-select">
    <select name="parent" id="the_post_parent_page<? print $rand ?>">
      <option value="0"   <? if((0 == intval($data['parent']))): ?>   selected="selected"  <? endif; ?>>None</option>
      <? if((0 != intval($data['parent']))): ?>
      <option value="<? print $data['parent'] ?>"     selected="selected"  ><? print $data['parent'] ?></option>
      <? endif; ?>
      <?
  	$include_categories_in_cat_selector = array();
  	 foreach($pages as $item):

  	$include_categories_in_cat_selector[] = $item['subtype_value'];
  	 ?>
      <option value="<? print $item['id'] ?>"   <? if(($item['id'] == $data['parent']) and $item['id'] != $data['id']): ?>   selected="selected"  <? endif; ?>  <? if($item['id'] == $data['id']): ?>    disabled="disabled"  <? endif; ?>  >
      <? print $item['title'] ?>
      </option>
      <? endforeach; ?>
    </select>
  </div>
  <? endif; ?>
  <? else: ?>
  <? $pages = get_content('content_type=page&limit=1000');   ?>
  <? if(!empty($pages)): ?>
  <div class="mw-ui-select">
    <select name="parent">
      <option value="0"   <? if((0 == intval($data['parent']))): ?>   selected="selected"  <? endif; ?>>None</option>
      <? if((0 != intval($data['parent']))): ?>
      <option value="<? print $data['parent'] ?>"     selected="selected"  ><? print $data['parent'] ?></option>
      <? endif; ?>
      <? foreach($pages as $item): ?>
      <option value="<? print $item['id'] ?>"   <? if(($item['id'] == $data['parent']) and $item['id'] != $data['id']): ?>   selected="selected"  <? endif; ?>  <? if($item['id'] == $data['id']): ?>    disabled="disabled"  <? endif; ?>  >
      <? print $item['title'] ?>
      </option>
      <? endforeach; ?>
    </select>
  </div>
  <? endif; ?>
  <? endif; ?>
  <? if($edit_post_mode != false): ?>
  <? $data['content_type'] = 'post'; ?>
  <? endif; ?>
  <input name="content_type"  type="hidden"  value="<? print $data['content_type'] ?>" >
  <? if($edit_post_mode != false): ?>
  <script  type="text/javascript">

 
 

$(document).ready(function(){
	
	 mw_load_post_cutom_fields_from_categories<? print $rand ?>()
	mw.$('#categorories_selector_for_post_<? print $rand ?> *[name="categories"]').bind('change', function(e){
   mw_load_post_cutom_fields_from_categories<? print $rand ?>()

});
   
 


 
   
});

function mw_load_post_cutom_fields_from_categories<? print $rand ?>(){
var a =	mw.$('#categorories_selector_for_post_<? print $rand ?> *[name="categories"]').val();
var holder1 = mw.$('#custom_fields_from_categorories_selector_for_post_<? print $rand ?>')
if(a == undefined || a == '' || a == '__EMPTY_CATEGORIES__'){
	holder1.empty();
	
} else {
	var cf_cats = a.split(',');
	holder1.empty();
	var i = 1;
	$.each(cf_cats, function(index, value) { 
	
	$new_div_id = 'cf_post_cat_hold_<? print $rand  ?>_'+i+mw.random();
	$new_div = '<div id="'+$new_div_id+'"></div>'
	$new_use_btn = '<button type="button" class="use_'+$new_div_id+'">use</button>'
  holder1.append($new_div);
		 mw.$('#'+$new_div_id).attr('for','categories');
		 mw.$('#'+$new_div_id).attr('to_table_id',value);
		 
  	     mw.load_module('custom_fields/index','#'+$new_div_id, function(){
			// mw.log(this);
			//	$(this).find('*').addClass('red');
		 	$(this).find('input').attr('disabled','disabled');
			$(this).find('.control-group').append($new_use_btn);
			mw.$('.use_'+$new_div_id).unbind('click');
					mw.$('.use_'+$new_div_id).bind('click', function(e){
						//   mw_load_post_cutom_fields_from_categories<? print $rand ?>()
						$closest =$(this).parent('.control-group').find('*[data-custom-field-id]:first');
						$closest= $closest.attr('data-custom-field-id');
						mw.$('#fields_for_post_<? print $rand  ?>').attr('copy_from',$closest);
						mw.reload_module('#fields_for_post_<? print $rand  ?>');
					 	mw.log($closest );
						 
						return false;
						});
			
			
			
			
			 });
  // mw.$('#'+$new_div_id).find('input').attr('disabled','disabled');
  i++;
  
});
	//holder1.html(a);
	//holder1.children().attr('disabled','disabled');
	
	
}
	
}
</script>
  <?
 $strz = '';
  if(isset($include_categories_in_cat_selector)): ?>
  <? 
 $x = implode(',',$include_categories_in_cat_selector);
 $strz = ' add_ids="'.$x.'" ';   ?>
  <? endif; ?>
  <div class="mw-ui mw-ui-category-selector">
    <? if(intval($data['id']) > 0): ?>
      <microweber module="categories/selector" for="content" id="categorories_selector_for_post_<? print $rand ?>" to_table_id="<? print $data['id'] ?>" <? print $strz ?>>
    <? else: ?>
      <microweber module="categories/selector"  id="categorories_selector_for_post_<? print $rand ?>" for="content" <? print $strz ?>>
    <? endif; ?>
  </div>


  Custom fields for post
  <div id="custom_fields_for_post_<? print $rand ?>" >
    <microweber module="custom_fields" view="admin" for="content" to_table_id="<? print $data['id'] ?>" id="fields_for_post_<? print $rand ?>" />
  </div>
  <br />
  Available custom fields
  <div id="custom_fields_from_categorories_selector_for_post_<? print $rand ?>" ></div>
  <? endif; ?>
  <h2>Advanced settings</h2>
  description
  <textarea name="description"><? print ($data['description'])?></textarea>
   
  <? if($edit_post_mode == false): ?>
  <br />
  <br />
  is_home
  <input name="is_home" type="radio"  value="n" <? if( '' == trim($data['is_home']) or 'n' == trim($data['is_home'])): ?>   checked="checked"  <? endif; ?> />
  No
  <input name="is_home" type="radio"  value="y" <? if( 'y' == trim($data['is_home'])): ?>   checked="checked"  <? endif; ?> />
  Yes <br />
  <br />
  is_shop
  <input name="is_shop" type="radio"  value="n" <? if( '' == trim($data['is_shop']) or 'n' == trim($data['is_shop'])): ?>   checked="checked"  <? endif; ?> />
  No
  <input name="is_shop" type="radio"  value="y" <? if( 'y' == trim($data['is_shop'])): ?>   checked="checked"  <? endif; ?> />
  Yes <br />
  <br />
  <br />
  subtype
  <div class="mw-ui-select">
    <select name="subtype">
      <option value="static"   <? if( '' == trim($data['subtype']) or 'static' == trim($data['subtype'])): ?>   selected="selected"  <? endif; ?>>static</option>
      <option value="dynamic"   <? if( 'dynamic' == trim($data['subtype'])  ): ?>   selected="selected"  <? endif; ?>>dynamic</option>
    </select>
  </div>
  <br />
  subtype_value
  <input name="subtype_value"  type="text" value="<? print ($data['subtype_value'])?>" />
  <br />
  <? endif; ?>
  <br />
  is_active
  <input name="is_active" type="radio"  value="n" <? if( '' == trim($data['is_active']) or 'n' == trim($data['is_active'])): ?>   checked="checked"  <? endif; ?> />
  No
  <input name="is_active" type="radio"  value="y" <? if( 'y' == trim($data['is_active'])): ?>   checked="checked"  <? endif; ?> />
  Yes <br />
  <br />
  <br />
  <input type="submit" name="save"    value="save" />
  <input type="button" onclick="return false;" id="go_live_edit_<? print $rand ?>" value="go live edit" />
  <? if($edit_post_mode == false): ?>
  <? endif; ?>




  <button type="submit">Save be</button>


</form>
