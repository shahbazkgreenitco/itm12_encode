{{--
/**
* ------------------------------------------------------------
* File: consumable-history-modal.blade.php
* Module: Consumables
* CON/26/06
* ------------------------------------------------------------
* Version: 1.0.0
* Author: Prithvi Pillai
* Page ID: #CON-08
* Created On: 2026-01-06
* Reviewed By: -
* ------------------------------------------------------------
* Change Log:
* [1.0.0] - Initial version
* ------------------------------------------------------------
*/
--}}

<div id="consumable-history-modal" class="modal fade user-mdl-box">
        <div class="modal-dialog">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title" id="consNameHist">---</h3>
                  <div class="box-tools">
                    <div class="input-group input-group-sm" style="width: 150px;">
                    </div>
                  </div>
                </div>
                
                
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="" id="consuDtl" style=""></div>
                    <div classs="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Date</th>
                                <th>Admin</th>
                            </tr>
                        </thead>
                        <tbody id="hislist">
                        
                        </tbody>
                    </table>
                    </div>
                </div>
                <!-- /.box-body -->
              </div>
        </div>
    </div>
    @push('lib')
    <script>

    $( document ).ready(function() {

        $( "#consumable-history-modal" ).on('show.bs.modal', function(e){
            var entityId = $(e.relatedTarget).attr('data-id');

            $('#consNameHist').html($(e.relatedTarget).attr('data-name'))
            $('#consuDtl').html('');
            $('#hislist').html('');
            //populate values
            $.ajax({
                url: config.url.history + '/' + entityId,
                data: {},
                async: false,
                dataType: "json",
                success: function(data) {
                    //console.log(data);
                    if(typeof data.status != "undefined"){
                        e.preventDefault();
                        if (data.status == 'error')
                            vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Error!!</h3><p>' + data.msg + '</p></div>' });
                        return false;
                    }

                   //populate history
                   // if(data.purchase_date)
                    //    $('#consuDtl').append('<div class="col-md-12" style="padding-bottom: 5px;"><strong>Purchase Date: </strong>'+data.purchase_date+'</div>');
                   // if(data.purchase_cost)
                   //     $('#consuDtl').append('<div class="col-md-12" style="padding-bottom: 5px;"><strong>Purchase Cost: </strong>'+data.purchase_cost+'</div>');
                   // if(data.order_number)
                    //    $('#consuDtl').append('<div class="col-md-12" style="padding-bottom: 5px;"><strong>Order Number: </strong>'+data.order_number+'</div>');

                    $.each(data.assetlog, function( index, value ) {
                        console.log(value);
                        $('#hislist').append('<tr><td>'+value.consumed_by+'</td><td>'+value.created_at+'</td><td>'+value.allocated_by+'</td><td>'
                            //+ '<button onclick="revoke($(this))" data-id="" >Revoke</button>'+'</td></tr>'
                            )
                    });
                }

            })
        });

        $('.datepicker').datepicker({            autoclose: true        })
        $('.select2').select2()
   })

   function revoke(referance){
       var tr = $(referance).closest('tr');


   }

    </script>
    @endpush