<? $c = url_param('categories');  ?>
<?   // p( $c ); ?>

<div class="categories">
<? 
  
  $i = 1;
  foreach($categories as $category): 
  if ($i % 3 == 0) {
		$cl = "cat_but2";
		} else {
		$cl = "cat_but";
	} 
 
  ?>
<? 
	
	$category_image_data = get_picture($category['id'], $for = 'category'); 
  	$thumb = get_media_thumbnail($category_image_data['id'], 263);
	?>
<a href="<? print category_url($category['id']); ?>"  class="<? print $cl ?>">
<div class="cat_lable"><? print $category['taxonomy_value']; ?></div>
<div class="cat_img"><img src="<? print $thumb; ?>" alt="<? print addslashes($category['taxonomy_value']); ?>" /></div>
<div class="cat_text"><? print $category['taxonomy_description']; ?></div>
<div class="browse_cat_sm_but">
  <div class="browse_cats">Browse Category</div>
  <div class="sm_logo"> <img src="<? print TEMPLATE_URL ?>images/sm_logo.jpg" alt=" logo" border="0" /> </div>
</div>
</a>
<? $i++; endforeach; ?>
</div>