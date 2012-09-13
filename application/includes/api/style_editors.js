



mw.current_element_styles = {}

Registered_Sliders = ['margin', 'opacity'];


var t = mwd.body.style;


mw.CSSPrefix = t.perspective!==undefined?"": t.MozPerspective!==undefined?"-moz-": t.WebkitPerspective!==undefined?"-webkit-": t.OPerspective!==undefined?"-o-":"-ms-";




mw.css3fx = {
  perspective:function(el,a,b){
    el.style.WebkitTransform = "perspective( "+a+"px ) rotateY( "+b+"deg )";
    el.style.MozTransform = "perspective( "+a+"px ) rotateY( "+b+"deg )";
    el.style.OTransform = "perspective( "+a+"px ) rotateY( "+b+"deg )";
    el.style.transform = "perspective( "+a+"px ) rotateY( "+b+"deg )";
    $(el).addClass("mwfx");

  },

  set_obj:function(element, option, value){

    if(mw.is.defined(element.attributes["data-mwfx"])){

     var json = mw.css3fx.read(element);

     json[option] = value;

     var string = JSON.stringify(json);

     var string = string.replace(/{/g, "").replace(/}/g);
     var string = string.replace(/"/g, "XX");

     $(element).attr("data-mwfx", string);
    }
    else{
      $(element).attr("data-mwfx", "XX"+option+"XX:XX"+value+"XX");
    }
  },
  init_css:function(el){
    var el = el || ".mwfx";
    $(el).each(function(){
      var elem = this;
      var json = mw.css3fx.read(el);
      $.each(json, function(a,b){
         $(elem).css(mw.CSSPrefix+a, b);
         mw.log(mw.CSSPrefix+a + " : " + b)
      });
    });
  },
  read:function(el){
    var el = $(el);
    var attr = el.attr("data-mwfx");
    if(mw.is.defined(attr)){
      var attr = attr.replace(/XX/g, '"');
      var attr = attr.replace(/undefined/g, '');
      var json = $.parseJSON('{'+attr+'}');
      return json;
    }
    else{return false;}
  }
}

mw.config_element_styles=function(){
    var q = mw.current_element_styles;

    $.each(Registered_Sliders, function(a,b){
        var val = q[b];
        if(val == "") {var val = 0;}
        var val = parseFloat(val);


        if(b=='opacity'){
           $("#tb_design_holder ."+b+"-slider").slider("option", "value", val*100);
         }
         else{
           $("#tb_design_holder ."+b+"-slider").slider("option", "value", val);
         }
    });


   $(".square_map_item").removeClass("active");
   $(".square_map_item_default").addClass("active");

}


mw.alignem = function(align){
    var el = $(".element-current");
    switch(align){
      case "left":
        el.removeClass("right").removeClass("center").addClass("left");
        break;
      case "right":
        el.removeClass("left").removeClass("center").addClass("right");
        break;
      case "center":
        el.removeClass("right").removeClass("left").addClass("center");
        break;
      default:
        el.removeClass("right").removeClass("left").removeClass("center");
    }
}

mw.sliders_settings = {
  css:{
    slide:function(event,ui){
        var type = $(this).attr("data-type");
        var val = (ui.value);
        type=='opacity'?  val = val/100 :'';
        $(".element-current").css($(this).attr("data-type"), val);
    },
    min:0,
    max:100,
    value:0
  }
}

init_square_maps = function(){
  var items = $(".square_map .square_map_item");
  items.hover(function(){
     var val = $(this).html();
     $(this).parents(".square_map").find(".square_map_value").html(val);
  }, function(){
     var val = $(this).parents(".square_map").find(".active").html();
     $(this).parents(".square_map").find(".square_map_value").html(val);
  });
  items.mousedown(function(){
    var el = $(this);
    if(!el.hasClass("active")){
        el.parents(".square_map").find(".active").removeClass("active");
        el.addClass("active");
        el.parents(".mw_dropdown").setDropdownValue(el.attr("data-value"), true, true, el.html());
    }
  });

  $(".mw_dropdown_func_slider").change(function(){
    var val = $(this).getDropdownValue();
    var who = $(this).attr("data-for");
    $("#"+who).attr("data-type", val);
  });
}


$(document).ready(function(){
  var elements = $(".element");
  elements.mousedown(function(){
         elements.removeClass("element-current");
         $(this).addClass("element-current");
         mw.current_element_styles = window.getComputedStyle(this, null);
         $(".es_item").trigger("change");
  });


  $(".ed_slider").each(function(){
    var el = $(this);
    el.slider(mw.sliders_settings.css);
    var max = $(this).attr("data-max");
    var min = $(this).attr("data-min");

    el.slider("option", "max", max!=undefined?parseFloat(max):100);
    el.slider("option", "min", min!=undefined?parseFloat(min):0);
    console.log(el.slider("option", "min"))
  });


  init_square_maps();

  $("#fx_element").change(function(){
    var val = $(this).getDropdownValue();
    $("#element_effx .fx").hide();
    $("#fx_"+val).show();
  });

  $(".perspective-slider").slider({
    slide:function(event,ui){
      mw.css3fx.perspective($(".element-current")[0], $(".element-current").width(), ui.value);
    },
    stop:function(event,ui){
        mw.css3fx.set_obj($(".element-current")[0], 'transform', "perspective( "+$(".element-current").width()+"px ) rotateY( "+ui.value+"deg )");
    },
    min:0,
    max:180,
    value:0
  });

    mw.css3fx.init_css();

});


