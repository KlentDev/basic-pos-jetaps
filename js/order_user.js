$(document).ready(function(){
                      
                      $('.btnadd').click(function(){
        
                     //     alert("You clicked the element with and ID of 'test-element'");
        var html='';
        html+='<tr>';
        html+='<td><input type="hidden" class="form-control pname" name="productname[]" ></td>';
                          html+='<td><select class="form-control productid" name="productid[]" style="width: 250px"; ><option value="">Select Option</option> <?php echo fill_product($pdo);  ?> </select></td>';
                          html+='<td><input type="text" class="form-control stock" name="stock[]" readonly></td>';
                          html+='<td><input type="text" class="form-control price" name="price[]" readonly></td>';
                          html+='<td><input type="number" min="1" class="form-control qty" name="qty[]" required></td>';
                          html+='<td><input type="text" class="form-control total" name="total[]" readonly></td>';
                          
                          html+='<td><button type="button" name="add" class= "btn btn-danger btn-sm btntbldlt" ><span class="fas fa-trash"   ></span></button></td>';
                          
            $('#producttable').append(html);
                          
                          
                        
                          
            $('.productid').select2()
                          
                $('.productid').on('change' , function(e){
                    
                    
                    var productid = this.value;
                     var tr=$(this).parent().parent();
              //var id = productid;
                    $.ajax({
                        
                        url:'getproduct.php',
                        method:'get',
                        data:{myyid: productid},
                     
       success:function(data){
                          
    //   alert(id);
                        //  console.log(data);
           tr.find(".pname").val(data["pname"]);
                         tr.find(".stock").val(data["pstock"]);
           tr.find(".price").val(data["saleprice"]);
           tr.find(".qty").val(1);
           tr.find(".total").val(tr.find(".qty").val() * tr.find(".price").val());
        
           calculate(0,0);
                        }
                
                    });
                    
                    
                });
                          
                         
        
        
    });
                      
        

        $("#producttable").delegate(".qty","keyup change" , function(){
            
            var quantity = $(this);
            var tr=$(this).parent().parent();
            if( (quantity.val()-0)> (tr.find(".stock").val()-0)){
               
               swal.fire("warning!", "Sorry Quantity not available");
                
                quantity.val(1);
                tr.find(".total").val(quantity.val() * tr.find(".price").val());
                                
                calculate(0,0);
               
               }else{
                   
                   tr.find(".total").val(quantity.val() * tr.find(".price").val());
                   calculate(0,0);
                   
               }
            
            
            
            
            
        });
        

                      function calculate(dis,paid){
                          
            var subtotal=0;
                          var tax=0;
                          var discount= dis;
                          var nrt_total=0;
                          var paid_amt= paid;
                          var due=0;
                          $(".total").each(function(){
                              
                              subtotal = subtotal+($(this).val()*1);
                              
                                        
                              
                          })
                          tax=0.00*subtotal;
                          net_total=tax+subtotal;
                          net_total=net_total-discount;
                          due=paid_amt-net_total;
                          $("#txtsubtotal").val(subtotal.toFixed(2));
                          $("#txttax").val(tax.toFixed(2));
                         $("#txttotal").val(net_total.toFixed(2));
                          $("#txtdiscount").val(discount);
                          $("#txtdue").val(due.toFixed(2));
                          
                          
                          
                          
                      } //function calculate end here
        
        $("#txtdiscount").keyup(function(){
            
            var discount = $(this).val();
            calculate(discount,0);
            
            
        });
        $("#txtpaid").keyup(function(){
            
            var paid =$(this).val();
            var discount =$("#txtdiscount").val();
            calculate(discount,paid);
            
                 
            
        });
        
        
            
                      
                      });
    
    

                
    
    
