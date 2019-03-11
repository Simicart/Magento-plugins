match: function () {
    if(this.toggleElements.length > 0){
        this.toggleElements.toggleSingle();
    }
},
unmatch: function () {
    if(this.toggleElements.length > 0){
        this.toggleElements.toggleSingle({destruct: true});
    }
}