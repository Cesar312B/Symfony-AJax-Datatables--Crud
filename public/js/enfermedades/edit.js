$(document).ready( function () {
    

    $(document).on('click','.taskEditBtn', function(e){


        let element = $(this)[0].parentElement.parentElement;
        let info= $(element).attr('attr_info');
        array_info = info.split(',');
        $('#edit_nombre').val(array_info[4]);
        $('#edit_codigo').val(array_info[3]);
        $('#edit_descripcion').val(array_info[2]);
        $('#edit_cdescripcion').val(array_info[1]);


        $(document).on('click','#btnEditForm', function(e){
            e.preventDefault();

         const  postData= {
                nombre:$('#edit_nombre').val(),
                codigo:$('#edit_codigo').val(),
                descripcion:$('#edit_descripcion').val(),
                codigo_descripcion:$('#edit_cdescripcion').val(),
                id: array_info[0]
              };

            $.ajax({
                 url: '/edit',
                 data: {postData},
                 type: 'POST',
                 success: function(data){
                    console.log(data);
                    $('#adit_form').trigger('reset');
                    $("#exampleModal2").modal('hide');
                    $('#mydatatable').DataTable().ajax.reload();  
                 } 
            });  

        });   



    });


  
});