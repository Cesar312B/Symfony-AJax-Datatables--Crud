$(document).ready( function () {

    $('#add_btn').on("click", function(e){
      e.preventDefault();
      const postData= {
        nombre:$('#add_nombre').val(),
        codigo:$('#add_codigo').val(),
        descripcion:$('#add_descripcion').val(),
        codigo_descripcion:$('#add_cdescripcion').val()
      };

      $.post('/new',postData,function(data){
         console.log(data);
         $('#add_form').trigger('reset');
         $("#exampleModal").modal('hide');
         $('#mydatatable').DataTable().ajax.reload();
      });
      
    });
 
  });