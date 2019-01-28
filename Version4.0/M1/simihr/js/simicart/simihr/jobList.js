function getSelectedJob( id) {
    var ids = window.document.getElementById('departmentjob_offer_ids');
    if(document.getElementById(id).checked == true){
        if((ids.value).indexOf(''+id+'') == -1) { // not contain id
            if(ids.value == '') {
                ids.value += id;
            }
            else {
                ids.value += ',' + id;
            }
        }
    } else {
        var text1 = ','+id;
        var text2 = id+',';
        ids.value = ids.value.replace(text1,'');
        ids.value = ids.value.replace(text2,'');
        ids.value = ids.value.replace(id,'');
    }
}