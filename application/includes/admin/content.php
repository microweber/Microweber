<?

 $rand = uniqid(); ?>
<script  type="text/javascript">


 

$(document).ready(function(){
	
	 
 
$('#pages_tree_toolbar .pages_tree a[data-page-id]').live('click',function(e) { 

$p_id = $(this).parent().attr('data-page-id');
 
 
 mw_select_page_for_editing($p_id);
 return false;
 
 
 
 });
   
});



$('#holder_temp_<? print $rand  ?> .category_tree a[data-category-id]').live('click',function(e) { 

	$p_id = $(this).parent().attr('data-category-id');
 
 	mw_select_category_for_editing($p_id);
return false;
 
 
 
 
 });
 
 
 



function mw_select_page_for_editing($p_id){
	 
	 
	 
	 
	 
	//$('#edit_content_admin_<? print $rand  ?>').attr('data-page-id',$p_id);
	
	
	$('#holder_temp2_<? print $rand  ?>').attr('data-page-id',$p_id);
  	 mw.load_module('content/edit_page','#holder_temp2_<? print $rand  ?>');
	 
	 
	 
	 
 // mw.reload_module('#edit_content_admin_<? print $rand  ?>');
	
}



















function mw_set_edit_categories<? print $rand  ?>(){
	$('#holder_temp_<? print $rand  ?>').empty();
	$('#holder_temp2_<? print $rand  ?>').empty();
	 mw.load_module('categories','#holder_temp_<? print $rand  ?>');
	 
	 
	 $('#holder_temp_<? print $rand  ?> a').live('click',function() { 

	$p_id = $(this).parent().attr('data-category-id');

 	mw_select_category_for_editing($p_id);
 

 return false;});
	
}




function mw_select_category_for_editing($p_id){
	 $('#holder_temp2_<? print $rand  ?>').attr('data-category-id',$p_id);
  	 mw.load_module('categories/edit_category','#holder_temp2_<? print $rand  ?>');

	
}




function mw_set_edit_posts<? print $rand  ?>(){
	$('#holder_temp_<? print $rand  ?>').empty();
	$('#holder_temp2_<? print $rand  ?>').empty();
	 $('#holder_temp_<? print $rand  ?>').attr('data-limit','10');
	 mw.load_module('posts','#holder_temp_<? print $rand  ?>');
	 $('#holder_temp_<? print $rand  ?> .paging a').live('click',function() { 
	 
	 $p_id = $(this).attr('data-page-number');
	 $p_param = $(this).attr('data-paging-param'); 
	 $('#holder_temp_<? print $rand  ?>').attr('data-page-number',$p_id);
	 $('#holder_temp_<? print $rand  ?>').attr('data-page-param',$p_param);
	 mw.load_module('posts','#holder_temp_<? print $rand  ?>');
		 return false;
	 });
	 
	 
	 
	  $('#holder_temp_<? print $rand  ?> .content-list a').live('click',function() { 
	 $p_id = $(this).parents('.content-item:first').attr('data-content-id');
	  
	 mw_select_post_for_editing($p_id);
	 
	 
		 return false;
	 });
	 
	 
	 
	
}




function mw_select_post_for_editing($p_id){
	 $('#holder_temp2_<? print $rand  ?>').attr('data-content-id',$p_id);
  	 mw.load_module('content/edit_post','#holder_temp2_<? print $rand  ?>');

	
}
</script>
   <button onclick="mw_select_page_for_editing(0)">new page</button>
        <button onclick="mw_select_category_for_editing(0)">new category</button>
         <button onclick="mw_select_post_for_editing(0)">new post</button>
<button onclick="mw_set_edit_categories<? print $rand  ?>()">mw_set_edit_categories<? print $rand  ?></button>
<button onclick="mw_set_edit_posts<? print $rand  ?>()">mw_set_edit_posts<? print $rand  ?></button>
<table  border="1" id="pages_temp_delete_me" style="z-index:9999999999; background-color:#efecec; position:absolute;" >
  <tr>
    <td><div id="holder_temp_<? print $rand  ?>">
        <module data-type="pages_menu" include_categories="true" id="pages_tree_toolbar"  />
     
      </div></td>
    <td><div id="holder_temp2_<? print $rand  ?>"><module data-type="content/edit_page" id="edit_content_admin_<? print $rand  ?>"  /></div></td>
  </tr>
</table>
 
