<script  type="text/javascript">
$(document).ready(function(){



Rotator = mwd.getElementById('orders-rotator');

mw.tools.simpleRotator(Rotator);

/*


 var source = new EventSource('<? print site_url('api/event_stream')?>');
source.onmessage = function (event) {

  mw.$('#mw-admin-manage-orders').html(event.data);
};


*/

    mw.on.hashParam("vieworder", function(){
        if(this!=false){
          mw_select_order_for_editing(this);
        }
        else{
          mw_select_order_for_editing(0);
        }
    });
 
 


 function mw_select_order_for_editing($p_id){
	 if(parseInt($p_id) == 0){
         Rotator.go(0)
	 }
     else {
        mw.$('#mw-admin-edit-order').attr('data-order-id',$p_id);
        mw.load_module('shop/orders/edit_order','#mw-admin-edit-order', function(){
            Rotator.go(1);
        });
	 }
}





});
</script>

<div class="mw-simple-rotator" style="width: 960px;padding: 20px;">
    <div class="mw-simple-rotator-container" id="orders-rotator">

      <module type="shop/orders/manage"  id="mw-admin-manage-orders"  />
      <div id="mw-admin-edit-order"></div>

    </div>
</div>

