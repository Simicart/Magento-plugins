var Simidailydeal = Class.create();
Simidailydeal.prototype = {
    initialize: function(changeProductUrl){
        this.changeProductUrl = changeProductUrl;
    },
	changeProduct : function(product_id){
		var url = this.changeProductUrl;
		if (url.indexOf('?') == -1)
			url += '?product_id=' + product_id;
		else
			url += '&product_id=' + product_id;
		new Ajax.Updater('product_name_contain',url,{method: 'get', onComplete: function(){updateProductName();} ,onFailure: ""});
	},
	checkBidderName : function(){
		var bidder_name = $('bidder_name').value;
		$('bidder_name_form_button').hide();
		
		var url = this.changeProductUrl;
		if (url.indexOf('?') == -1)		
			url += '?bidder_name='+ bidder_name;
		else
			url += '&bidder_name='+ bidder_name;					
		
		new Ajax.Updater('bidder-notice',url,{method: 'get', onComplete: function(){validBidderName();} ,onFailure: ""});
				
		$('biddername-please-wait').style.display = "block";
		$('bidder-notice').style.display = "none";
		$('bidder_name').removeClassName('validation-passed');
	}
}

var SimidailydealTimeCounter = Class.create();
SimidailydealTimeCounter.prototype = {
	//params now_time, end_time : seconds
	initialize: function(now_time,end_time,simidailydeal_id){
		this.now_time = parseInt(now_time) * 1000;
		this.end_time = parseInt(end_time) * 1000;
		this.simidailydeal_id = simidailydeal_id;
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
			yrtext = '<li class="time-year"><span class="timeleft-text">' + yr +'</span><span class="text-time">'+ yrtext+'</span></li>';
			
		if( (mnth <=0))
			mnthtext =''
		else
			mnthtext = '<li class="time-month"><span class="timeleft-text">'+ mnth +'</span><span class="text-time">'+ mnthtext +'</span></li>';
			
		if(dy <=0 && mnth>0)
			dytext =''
		else
			dytext = '<li class="time-day"><span class="timeleft-text">'+ dy +'</span><span class="text-time">'+ dytext +'</span></li>';
			
		if(hr <=0 && dy>0)
			hrtext =''
		else
			hrtext = '<li class="time-hr"><span class="timeleft-text">'+ hr + '</span><span class="text-time">'+ hrtext +'</span></li>';
			
		if(min < 0)
			mintext =''
		else
			mintext = '<li class="time-min"><span class="timeleft-text">'+ min +'</span><span class="text-time">'+ mintext +'</span></li>';
			
		if(sec < 0)
			sectext =''
		else
			sectext = '<li class="time-sec"><span class="timeleft-text">'+ sec +'</span><span class="text-time">'+ sectext +'</span></li>';			
			
		if(now >= this.end){
			document.getElementById(timeleft_id).innerHTML = endtext;
			clearTimeout(timerID);
		}
		else{
			
			document.getElementById(timeleft_id).innerHTML = yrtext + mnthtext + dytext + hrtext +  mintext + sectext;
		}
		
		if(this.now_time == this.end_time){
			location.reload(true);
			return;
		}		
		
		this.now_time = this.now_time + 1000; //incres 1000 miliseconds

		timerID = setTimeout("setSimidailydealTimeleft("+ (this.now_time / 1000) +"," + (this.end_time /1000) +",'"+ timeleft_id +"','"+ this.simidailydeal_id +"');", 1000); 	
	}
}

var timerCounters = {};
function setSimidailydealTimeleft(now_time,end_time,timeleft_id,simidailydeal_id){
	/*if($('auction_end_time_'+simidailydeal_id) != null)
		end_time = parseInt($('auction_end_time_'+simidailydeal_id).value);
	if($('auction_now_time_'+simidailydeal_id) != null){
		if($('auction_now_time_'+simidailydeal_id).value != ''){
			now_time = parseInt($('auction_now_time_'+simidailydeal_id).value);
			$('auction_now_time_'+simidailydeal_id).value = '';
		}
	}*/
	if (timerCounters[simidailydeal_id] == undefined) {
		timerCounters[simidailydeal_id] = new SimidailydealTimeCounter(now_time,end_time,simidailydeal_id);
	} else {
		timerCounters[simidailydeal_id].initialize(now_time,end_time,simidailydeal_id);
	}
	timerCounters[simidailydeal_id].setTimeleft(timeleft_id);
}

function updateProductName(){
	$('product_name').value = $('newproduct_name').value;
	$('simidailydeal_tabs_form_section').addClassName('active');
	$('simidailydeal_tabs_form_listproduct').removeClassName('active');
	$('simidailydeal_tabs_form_section_content').style.display="";
	$('simidailydeal_tabs_form_listproduct_content').style.display="none";
}
