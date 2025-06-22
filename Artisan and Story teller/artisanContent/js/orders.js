 $(document).ready(function() {
    
    $('.view-btn').click(function() {
      const orderId = $(this).data('order-id');
      $('#orderModal').removeClass('hidden');

     
      $.ajax({
        url: 'get_order_details.php',
        type: 'GET',
        data: {
          order_id: orderId
        },
        success: function(response) {
          $('#orderDetailsContent').html(response);
        },
        error: function() {
          $('#orderDetailsContent').html('<p class="text-red-500">Error loading order details.</p>');
        }
      });
    });

    $('#closeModal').click(function() {
      $('#orderModal').addClass('hidden');
    });

    $('.process-btn').click(function() {
      const orderId = $(this).data('order-id');
      updateOrderStatus(orderId, 'Processing');
    });

    $('.ship-btn').click(function() {
      const orderId = $(this).data('order-id');
      updateOrderStatus(orderId, 'Shipped');
    });


    $('#saveStatusBtn').click(function() {
      const orderId = $('#orderIdInModal').val();
      const newStatus = $('#statusSelect').val();
      updateOrderStatus(orderId, newStatus);
    });

   
    function updateOrderStatus(orderId, newStatus) {
      $.ajax({
        url: 'update_order_status.php',
        type: 'POST',
        data: {
          order_id: orderId,
          new_status: newStatus
        },
        success: function(response) {
          if (response.success) {
            alert('Order status updated successfully!');
            location.reload(); 
          } else {
            alert('Error updating order status: ' + response.message);
          }
        },
        error: function() {
          alert('Error updating order status. Please try again.');
        }
      });
    }
  });