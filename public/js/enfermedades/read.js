$(document).ready(function() {

 
    var table = $('#mydatatable').DataTable({
        language: {
            "url": "https://cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
        },


        responsive: true,
        ordering: false,
        processing: true,
        stateSave: true,
        serverSide: true, 

        lengthMenu: [
            [5, 10, 25],
            [5, 10, 25],
        ],
        ajax: "/server-processing",
        columnDefs: [
            { name: "nombre", targets: 0},
            { name: "codigo", targets: 1 },
            { name: "descripcion", targets: 2 },
            { name: "codigo_descripcion", targets: 3 },
            { name: "id", targets: 4,  render: () => {
                
                return `
                <button class="taskEditBtn btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal2"><i class="bi bi-pen"></i></button>
                <button class="task-delete btn btn-danger btn-sm"><i class="bi bi-archive"></i></button>
                `;

               
         } },  
        ],

        rowGroup: {
            startRender: null,
            startRender: function ( rows, group ) {
                return group +' ('+rows.count()+')';
            },
            dataSrc: 2
        },


  
        "initComplete": function () {
            this.api().columns().every( function () {
                
                var that = this;

                $( 'input', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                        }
                });
            })
        },


        
        

    });

    
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
           table.draw();
        });
        
      });

      var tableRow = {};
      var x = {};


    $(document).on('click', '.taskEditBtn', function () {
        tableRow = table.row($(this).parents('tr')).data();
        x=table.row($(this).parents('tr')).index();
    
        
        $('#edit_nombre').val(tableRow[0]);
        $('#edit_codigo').val(tableRow[1]);
        $('#edit_descripcion').val(tableRow[2]);
        $('#edit_cdescripcion').val(tableRow[3]);
    
    
       
            /*$.ajax({
                 url: '/edit',
                 type: 'PUT',
                 async: true,
                 cache: false,
                 data: {postData},
                 
              success: function(data,result){

                 

                    
        
                
                 } 
            
            });*/


      
      
            
        
        });   


        $('#adit_form').submit(function(e){
            e.preventDefault();

         const  postData= {
                nombre:$('#edit_nombre').val(),
                codigo:$('#edit_codigo').val(),
                descripcion:$('#edit_descripcion').val(),
                codigo_descripcion:$('#edit_cdescripcion').val(),
                id: tableRow[4]
              };


            
        $.post('/edit',{postData},function(data){
            tableRow[0]= postData.nombre;
            tableRow[1]=postData.codigo;
            tableRow[2]=postData.descripcion;
            tableRow[3]= postData.codigo_descripcion;

             $('#adit_form').trigger('reset');
             $("#exampleModal2").modal('hide');
             table.row(x).data(tableRow).draw();
             console.log(data);
          
         });
    
    });



    $(document).on('click','.task-delete', function(e){

        var tableRow = table.row($(this).parents('tr')).data();
       var x=table.row($(this).parents('tr')).index();

        if(confirm('Esta seguro de eliminar el item'+'"'+tableRow[0]+'"')){
            $.post('/delete',{id:tableRow[4]},function(data){
                console.log(data);
                table.row(x).remove().draw(false);
             
             });
        }


    });


  


 $('tfoot').each(function () {
    $(this).insertAfter($(this).siblings('thead'));
});



});