 <?
 
$module_id = $params['id'];
$rand = rand();
?>
<button onclick="mw_make_new_field('text')" value="mw_make_new_field('text')">mw_make_new_field('text')</button>
<button onclick="mw_make_new_field('checkbox')" value="mw_make_new_field('checkbox')">mw_make_new_field('checkbox')</button>

<div  class="custom-fields-form-wrap custom-fields-form-wrap-<? print $rand ?>" id="custom-fields-form-wrap-<? print $rand ?>"></div>
<script type="text/javascript">
    function mw_make_new_field($type){
        mw.$('#custom-fields-form-wrap-<? print $rand ?>').load('<? print site_url('api_html/make_custom_field/settings:y/for_module_id:') ?><? print $params['id']; ?>/custom_field_type:'+$type);


    }

    $(document).ready(function(){


        //make_new_field()

    });
</script>
<module type="custom_fields" view="list" for_module_id="<? print $module_id ?>" id="mw_custom_fields_list_<? print $params['id']; ?>" />
