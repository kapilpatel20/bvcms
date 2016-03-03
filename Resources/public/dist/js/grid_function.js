function deleterecord(id,headTitle,msg) {
    $.confirm({
        title: headTitle,
        content: msg,
        icon: '',
        confirmButton: 'Okay',
        cancelButton: 'Cancel',
        columnClass: 'col-md-5 col-md-offset-4',
        confirmButtonClass: 'btn-success',
        cancelButtonClass: 'btn-danger',
        theme: 'white',
        animation: 'scale',
        animationSpeed: 400,
        animationBounce: 1.5,
        keyboardEnabled: false,
        container: 'body',
        confirm: function() {
            $.ajax({
            type: "POST",
            url: deleteAjaxSource,
            data: "id=" + id,
            success: function(data) {
                dTable.fnStandingRedraw();
                //dTable.fnDraw(true);
                deleteMessage(data);
                $('#checkall').prop('checked', false);

            }
        });
            
        },
        cancel: function() {
        },
        contentLoaded: function() {
        },
        backgroundDismiss: false,
        autoClose: false,
        closeIcon: true,
    });


//    if (confirm("Are you sure you want to delete?")) {
//
//        $.ajax({
//            type: "POST",
//            url: deleteAjaxSource,
//            data: "id=" + id,
//            success: function(data) {
//
//                dTable.fnDraw(true);
//                deleteMessage(data);
//                $('#checkall').prop('checked', false);
//
//            }
//        });
//    }
}


function activeInactiverecord(id,val,ajaxActionUrl,headTitle,msg) {
	
	$.confirm({
        title: headTitle,
        content: msg,
        icon: '',
        confirmButton: 'Okay',
        cancelButton: 'Cancel',
        columnClass: 'col-md-5 col-md-offset-4',
        confirmButtonClass: 'btn-success',
        cancelButtonClass: 'btn-danger',
        theme: 'white',
        animation: 'scale',
        animationSpeed: 400,
        animationBounce: 1.5,
        keyboardEnabled: false,
        container: 'body',
        confirm: function() {
            $.ajax({
            type: "POST",
            url: ajaxActionUrl,
            data: "id=" + id+"&status="+val,
            success: function(data) {
                dTable.fnStandingRedraw();
                //dTable.fnDraw(true);
                deleteMessage(data);
                $('#checkall').prop('checked', false);

            }
        });
            
        },
        cancel: function() {
        },
        contentLoaded: function() {
        },
        backgroundDismiss: false,
        autoClose: false,
        closeIcon: true,
    });
}

function deleteMessage(data) {

    var delmsg = '<div class="alert alert-' + data.type + '">';
    delmsg += '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
    delmsg += data.message;
    delmsg += '</div>';
    $("div.alert-" + data.type).remove();
    $("div#flashMsg, div.flashMsg").prepend(delmsg);
}

$.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
        //redraw to account for filtering and sorting
        // concept here is that (for client side) there is a row got inserted at the end (for an add)
        // or when a record was modified it could be in the middle of the table
        // that is probably not supposed to be there - due to filtering / sorting
        // so we need to re process filtering and sorting
        // BUT - if it is server side - then this should be handled by the server - so skip this step
        if(oSettings.oFeatures.bServerSide === false){
            var before = oSettings._iDisplayStart;
            oSettings.oApi._fnReDraw(oSettings);
            //iDisplayStart has been reset to zero - so lets change it back
            oSettings._iDisplayStart = before;
            oSettings.oApi._fnCalculateEnd(oSettings);
        }
          
        //draw the 'current' page
        oSettings.oApi._fnDraw(oSettings);
};