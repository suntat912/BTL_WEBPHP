document.addEventListener('DOMContentLoaded', function() {
    // Tính năng tìm kiếm sản phẩm
    const searchForm = document.querySelector('.search form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(event) {
            const searchTerm = searchForm.querySelector('input[name="search"]').value.trim();
            if (!searchTerm) {
                alert('Vui lòng nhập từ khóa tìm kiếm.');
                event.preventDefault(); // Ngăn chặn gửi biểu mẫu khi không có từ khóa
            }
        });
    }

    // Tính năng thêm sản phẩm vào giỏ hàng
    const addToCartForms = document.querySelectorAll('.product form');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            const quantityInput = form.querySelector('input[name="quantity"]');
            const quantity = parseInt(quantityInput.value, 10);

            if (quantity < 1) {
                alert('Vui lòng nhập số lượng hợp lệ.');
                event.preventDefault(); // Ngăn chặn gửi biểu mẫu khi số lượng không hợp lệ
            } else {
                alert('Sản phẩm đã được thêm vào giỏ hàng!'); // Thông báo thành công
            }
        });
    });

    // Tính năng xác nhận đặt hàng
    const checkoutForm = document.querySelector('form[action="checkout.php"]');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(event) {
            const shippingAddressInput = checkoutForm.querySelector('textarea[name="shipping_address"]');
            if (!shippingAddressInput.value.trim()) {
                alert('Vui lòng nhập địa chỉ giao hàng.');
                event.preventDefault(); // Ngăn chặn gửi biểu mẫu khi địa chỉ không hợp lệ
            } else {
                alert('Đặt hàng thành công!'); // Thông báo thành công
            }
        });
    }

    // Tính năng xóa sản phẩm khỏi giỏ hàng
    const removeButtons = document.querySelectorAll('.remove-from-cart');
    removeButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const productName = button.closest('.product').querySelector('h3').innerText;
            if (confirm(`Bạn có chắc chắn muốn xóa ${productName} khỏi giỏ hàng?`)) {
                // Xử lý xóa sản phẩm ở đây (có thể gọi đến một API hoặc gửi yêu cầu đến server)
                alert(`${productName} đã được xóa khỏi giỏ hàng.`);
            }
        });
    });
});