$(document).ready(function () {
  $(".view-btn").click(function () {
    const order_number = $(this).data("order-number");
    $("#orderModal").removeClass("hidden");

    $.ajax({
      url: "get_order_details.php",
      type: "GET",
      data: {
        order_id: order_number,
      },
      success: function (response) {
        $("#orderDetailsContent").html(response);
      },
      error: function () {
        $("#orderDetailsContent").html(
          '<p class="text-red-500">Error loading order details.</p>'
        );
      },
    });
  });

  $("#closeModal").click(function () {
    $("#orderModal").addClass("hidden");
  });

  $(".process-btn").click(function () {
    const order_number = $(this).data("order-nuber");
    updateOrderStatus(order_number, "Processing");
  });

  $(".ship-btn").click(function () {
    const orderId = $(this).data("order-number");
    updateOrderStatus(order_number, "Shipped");
  });

  $("#saveStatusBtn").click(function () {
    const order_number = $("#orderIdInModal").val();
    const newStatus = $("#statusSelect").val();
    updateOrderStatus(order_number, newStatus);
  });

  function updateOrderStatus(orderId, newStatus) {
    $.ajax({
      url: "update_order_status.php",
      type: "POST",
      data: {
        order_number: orderId,
        new_status: newStatus,
      },
      success: function (response) {
        if (response.success) {
          alert("Order status updated successfully!");
          location.reload();
        } else {
          alert("Error updating order status: " + response.message);
        }
      },
      error: function () {
        alert("Error updating order status. Please try again.");
      },
    });
  }
});
