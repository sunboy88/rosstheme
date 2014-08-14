var AwcpCollpur = Class.create({	
    out: new String(),
    initialize: function(data,container) {
        if(data.isJSON()) {
            jsonObj = data.evalJSON();
            this.dateNow = jsonObj.dateNow;
            this.dateTo = jsonObj.dateTo;
            this.container = container;
            this.prepareTranslation(jsonObj);
            this.getCounter();
        }
    },
    prepareTranslation: function(obj) {
        this.day = obj.translation.day;
        this.days = obj.translation.days;
        this.hours = obj.translation.hours;
        this.hour = obj.translation.hour;
        this.minute = obj.translation.minute;
        this.minutes = obj.translation.minutes;
        this.second = obj.translation.second;
        this.seconds = obj.translation.seconds;
    },
    getCounter: function() {
        this.dateNow += 1;
        out=this.out;
        amount=this.dateTo-this.dateNow;
        if(amount < 0) return false;
        days=Math.floor(amount/86400);
        amount=amount%86400;
        hours=Math.floor(amount/3600);
        amount=amount%3600;
        mins=Math.floor(amount/60);
        amount=amount%60;
        secs=Math.floor(amount);
        out += days +" "+((days==1)?this.day:this.days)+" ";
        out += hours +":";
        out += (mins<=9?'0':'')+mins;
        /*out += (secs<=9?'0':'')+secs;*/
        $(this.container).update(out);
        setTimeout(function(){
            this.getCounter();
        }.bind(this), 1000);
    }
});
 