<!DOCTYPE HTML>
<html>
<head>

{head}

<link type="text/css" rel="stylesheet" media="all" href="<?php print MW_INCLUDES_URL; ?>css/mw_framework.css"/>
<link type="text/css" rel="stylesheet" media="all" href="<?php print MW_INCLUDES_URL; ?>css/liveadmin.css"/>
<link type="text/css" rel="stylesheet" media="all" href="<?php print MW_INCLUDES_URL; ?>css/wysiwyg.css"/>
<script>
    mwAdmin = true;
</script>
<script>mw.require("jquery-ui.js");</script>
<script>mw.require("tools.js");</script>
<script>mw.require("url.js");</script>
<script>mw.require("events.js");</script>
<script>mw.require("wysiwyg.js");</script>
<script>

scaleHeight = function(){
  var pt = parseFloat(mw.$("#mw-iframe-editor-area").css("paddingTop"));
  var pb = parseFloat(mw.$("#mw-iframe-editor-area").css("paddingBottom"));
  var h = $(window).height() - mw.$("#mw-admin-text-editor").outerHeight() - pt - pb - 4;
  mw.$("#mw-iframe-editor-area").height(h)
}

PrepareEditor = function(){
  if(window.name.contains("mweditor")){
     HOLD = false;
     mw.on.DOMChange(mwd.getElementById('mw-iframe-editor-area'), function(){
          el = $(this);
          typeof HOLD === 'number' ? clearTimeout(HOLD) : '';
               HOLD = setTimeout(function(){
                 if(el[0].querySelector(".edit")===null){
                    parent.mw.$("iframe#"+window.name).trigger("change", el.html());
                 }
                 else{
                   parent.mw.$("iframe#"+window.name).trigger("change", $(el[0].querySelector(".edit")).html());
                 }
          }, 600);
     });
  }
}

$(window).load(function(){


scaleHeight();
    __area = mwd.getElementById('mw-iframe-editor-area');
   $(window).resize(function(){
   scaleHeight()
});

  mw.$("#mw-iframe-editor-area").bind("mouseup", function(e){
    if(!e.target.isContentEditable){
        var el = this.querySelector('.edit');
        if(el !== null){
            // Put Cursor at the end
            mw.wysiwyg.select_all(el);
            var range = window.getSelection().getRangeAt(0);
            range.collapse(false);
            el.focus();
            window.getSelection().collapseToEnd(); // Chrome
        }
    }
  });

});


$(document).ready(function(){


$(".module").attr("contentEditable", false);

$(".edit").attr("contentEditable", true);

$(mwd.body).bind('keydown keyup keypress mouseup mousedown click paste selectstart', function(e){
  var el= $(e.target);
  if(mw.tools.hasClass(e.target.className, 'module') || mw.tools.hasParentsWithClass(e.target, 'module')){
    e.preventDefault();
    //var curr =  mw.tools.hasClass(e.target.className, 'module') ? e.target : mw.tools.firstParentWithClass(e.target, 'module');
  }
  if(el.hasClass('edit')){
    el.addClass('changed');
  }
  else{
     $(mw.tools.firstParentWithClass(e.target, 'edit')).addClass('changed');
  }
});

 mw.$(".module").each(function(){
    var curr = this;
    if($(curr).next().length == 0){
      _next = mwd.createElement('div');
      _next.className = 'mw-wysiwyg-module-helper';
      _next.innerHTML = '&nbsp;';
    }
    if($(curr).prev().length == 0){
      _prev = mwd.createElement('div');
      _prev.className = 'mw-wysiwyg-module-helper';
      _prev.innerHTML = '&nbsp;';
    }
    if(mw.tools.hasParentsWithClass(curr,'edit')){
        $(curr).append("<span class='mw-close' onclick='delete_module(this);'></span>");
    }
    else{
      //  $(curr).addClass('disabled');
    }
 });






});


$(window).load(function(){
  var master = mwd.getElementById('the_admin_editor');
  master.addEventListener("DOMAttrModified", function(e){
      var attr = e.attrName;
      if(attr == 'style'){
        parent.mw.$("#" + window.name).css({
          height:$(master).height() + 4
        });
      }
  }, false);




      $(mwd.body).bind('mousedown', function(e){
        parent.mw.$(".mw-ui-category-selector").hide();
      });




})


delete_module = function(inner_node){
    mw.tools.confirm(mw.msg.del, function(){
      $(mw.tools.firstParentWithClass(inner_node, 'module')).remove();
    });
}
</script>



<style type="text/css">

*{
  margin: 0;
  padding: 0;
}

#mw-iframe-editor-area{
  line-height: 1.85;
}



.module {
	display: block;
	padding: 10px;
	border: 1px solid #ccc;
	background: #efecec;
	text-align: center;
	margin: 10px 5px;
	font-size: 11px;
    width: auto !important;
    height: auto !important;
    position: relative;
}

.module .mw-close{
  position: absolute;
  top: 5px;
  right: 5px;
  visibility: hidden;
}

.module:hover .mw-close{
  visibility: visible;
}

.mw-plain-module-name {
	display: block;
	padding-top: 5px;
}

.mw-admin-editor{
    background: none;
}

.mw-wysiwyg-module-helper{
  min-height: 23px;
}


::-webkit-scrollbar {
    width: 10px;
    background:#E9E6E6;
}

::-webkit-scrollbar-thumb {
    background: #787878;
}



</style>
</head>
<body style="padding: 0;margin: 0;">
<?php mw_var('plain_modules', true);
  if(is_admin() == false){
    //exit('Must be admin');
  }
 ?>


<div class="mw-admin-editor" id="the_admin_editor">
 <?php include MW_INCLUDES_DIR . DS . 'toolbar' . DS ."wysiwyg_admin.php"; ?>
  <div class="mw-admin-editor-area" id="mw-iframe-editor-area" tabindex="0" autofocus="autofocus">{content}</div>
</div>

<?php mw_var('plain_modules', false); ?>
</body>
</html>