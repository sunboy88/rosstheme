var DealTimeCounter = Class.create();
DealTimeCounter.prototype = {
  initialize: function(now_time,end_time,deal_id){
    this.now_time = parseInt(now_time) * 1000;
    this.end_time = parseInt(end_time) * 1000;
    this.deal_id = deal_id;
    this.end = new Date(this.end_time);
    var endDate = this.end;
    this.second = endDate.getSeconds();
    this.minute = endDate.getMinutes();
    this.hour = endDate.getHours();
    this.day = endDate.getDate();
    this.month = endDate.getMonth();
    var yr;
    if(endDate.getYear() < 1900)
      yr = endDate.getYear() + 1900;
    else
      yr = endDate.getYear();
    this.year = yr;
  },
  
  setTimeleft : function(timeleft_id)
  {
    var now = new Date(this.now_time);
    var yr;
    
    if(now.getYear() < 1900)
      yr = now.getYear() + 1900;
    else
      yr = now.getYear();
      
    var endtext = '0';
    var timerID;
    
    var sec = this.second - now.getSeconds();
    
    var min = this.minute - now.getMinutes();
    var hr = this.hour - now.getHours();
    var dy = this.day - now.getDate();
    var mnth = this.month - now.getMonth();
    yr = this.year - yr;
    
    var daysinmnth = 32 - new Date(now.getYear(),now.getMonth(), 32).getDate();
    if(sec < 0){
      sec = (sec+60)%60;
      min--;
    }
    if(min < 0){
      min = (min+60)%60;
      hr--; 
    }
    if(hr < 0){
      hr = (hr+24)%24;
      dy--; 
    }
    if(dy < 0){
      dy = (dy+daysinmnth)%daysinmnth;
      mnth--; 
    }
    if(mnth < 0){
      mnth = (mnth+12)%12;
      yr--;
    } 
    var sectext = "sec";
    var mintext = "min";
    var hrtext = "hour";
    var dytext = " days ";
    var mnthtext = " months ";
    var yrtext = " years ";
    if (yr == 1)
      yrtext = " year ";
    if (mnth == 1)
      mnthtext = " month ";
    if (dy == 1)
      dytext = " day ";
    if (hr == 1)
      hrtext = "hour";
    if (min == 1)
      mintext = "min";
    if (sec == 1)
      sectext = "sec";
  
    if (dy <10)
      dy = '0' + dy;    
    if (hr <10)
      hr = '0' + hr;
    if (min < 10)
      min = '0' + min;
    if (sec < 10)
      sec = '0' + sec;  
  
    if(yr <=0)
      yrtext =''
    else
      yrtext = '<li><span class="timeleft-value">' + yr +'</span><span class="timeleft-label">'+ yrtext+'</span></li>'
      
    if( (mnth <=0))
      mnthtext =''
    else
      mnthtext = '<li><span class="timeleft-value">'+ mnth +'</span><span class="timeleft-label">'+ mnthtext +'</span></li>';
      
    if(dy <=0 && mnth>0)
      dytext =''
    else
      dytext = '<li><span class="timeleft-value">'+ dy +'</span><span class="timeleft-label">'+ dytext +'</span></li>';
      
    if(hr <=0 && dy>0)
      hrtext =''
    else
      hrtext = '<li><span class="timeleft-value">'+ hr + '</span><span class="timeleft-label">'+ hrtext +'</span></li>';
      
    if(min < 0)
      mintext =''
    else
      mintext = '<li><span class="timeleft-value">'+ min +'</span><span class="timeleft-label">'+ mintext +'</span></li>';
      
    if(sec < 0)
      sectext =''
    else
      sectext = '<li><span class="timeleft-value">'+ sec +'</span><span class="timeleft-label">'+ sectext +'</span></li>';    
      
    if(now >= this.end){
      document.getElementById(timeleft_id).innerHTML = endtext;
      clearTimeout(timerID);
    }
    else{
      document.getElementById(timeleft_id).innerHTML = '<ul id="countdown">' + yrtext + mnthtext + dytext + hrtext +  mintext + sectext +'</ul>';
    }
    
    if(this.now_time == this.end_time){
      location.reload(true);
      return;
    }   
    
    this.now_time = this.now_time + 1000; //incres 1000 miliseconds

    timerID = setTimeout("setDealTimeleft("+ (this.now_time / 1000) +"," + (this.end_time /1000) +",'"+ timeleft_id +"','"+ this.deal_id +"');", 1000);   
  }
  
}

function setDealTimeleft(now_time,end_time,timeleft_id,deal_id)
{
  var counter = new DealTimeCounter(now_time,end_time,deal_id);
  counter.setTimeleft(timeleft_id);
}

function myPopupRelocate(element_id) {
  var scrolledX, scrolledY;
  if( self.pageYOffset ) {
    scrolledX = self.pageXOffset;
    scrolledY = self.pageYOffset;
  } else if( document.documentElement && document.documentElement.scrollTop ) {
    scrolledX = document.documentElement.scrollLeft;
    scrolledY = document.documentElement.scrollTop;
  } else if( document.body ) {
    scrolledX = document.body.scrollLeft;
    scrolledY = document.body.scrollTop;
  }

  var centerX, centerY;
  if( self.innerHeight ) {
    centerX = self.innerWidth;
    centerY = self.innerHeight;
  } else if( document.documentElement && document.documentElement.clientHeight ) {
    centerX = document.documentElement.clientWidth;
    centerY = document.documentElement.clientHeight;
  } else if( document.body ) {
    centerX = document.body.clientWidth;
    centerY = document.body.clientHeight;
  }

  var leftOffset = scrolledX + (centerX - 250) / 2;
  var topOffset = scrolledY + (centerY - 200) / 2;

  document.getElementById(element_id).style.top = topOffset + "px";
  document.getElementById(element_id).style.left = leftOffset + "px";
}

function fireMyPopup(element_id) {  
  myPopupRelocate(element_id);
  document.getElementById(element_id).style.display = "block";
  document.body.onscroll = myPopupRelocate(element_id);
  window.onscroll = myPopupRelocate(element_id);
}

function close_popup(element) {
  $('subscribe-result-message').update('');
  $('dailydeal-subscription-form').show();
  $('dailydeal_email').value = '';
  $(element).hide();
}

function close_popup_message(element){
  $(element).hide();
}

function submit_dailydeal_newsletter(newsletter_url) {
  var parameters = {
    email_address: $('dailydeal_email').value,
    customer_name: $('dailydeal_customer_name').value
  };
  show_loading(true);
  var request = new Ajax.Request(
    newsletter_url,
    {
      method: 'post',
      onSuccess: function(transport) {
        var data = transport.responseText.evalJSON(); 
        if (data.error) {
          show_loading(false);
          $('subscribe-result-message').update(data.message);
        }
        else {
          show_loading(false);
          $('subscribe-result-message').update(data.message);
        }
      },
      parameters: parameters
    }
  );
}

function show_loading(is_show) {
  if (is_show) {
    $('dailydeal-subscription-form').hide();
    $('subscribe-form-ajax').show();
  }
  else {
    //$('dailydeal-subscription-form').show();
    $('subscribe-form-ajax').hide();        
  }
}

var Deal = Class.create();
Deal.prototype = {
  initialize: function(changeProductUrl){
    
    this.changeProductUrl = changeProductUrl;
     
  },
  
  changeProduct : function(product_id)
  { 
    var url = this.changeProductUrl;
    
    url += 'product_id/' + product_id;
    new Ajax.Updater(
        'product_name_contain',
        url,
        {
          method: 'get', 
          onComplete: function(){
            $('product_name').value = $('newproduct_name').value;
            $('product_price').value = $('newproduct_price').value;
            $('product_quantity').value = $('newproduct_quantity').value;
          } ,
          onFailure: ''
        }
    );  
    
  }
}

function updateProductName()
{
  alert('hehe');
  $('product_name').value = $('newproduct_name').value;
}