window.addEventListener("DOMContentLoaded", () => {
            let cart = JSON.parse(localStorage.getItem("fashion_cart")) || [];
            const fragranceProductsContainer = document.getElementById("fragrance-products");

            // Tải sản phẩm động
            function loadFragranceProducts() {
                fetch('../manage_products.php?category=fragrance')
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) renderFragrance(data);
                        else fragranceProductsContainer.innerHTML = "<p style='grid-column: 1/-1; text-align: center; color: #888;'>Chưa có sản phẩm nước hoa nào.</p>";
                    })
                    .catch(err => console.error("Lỗi khi tải fragrance:", err));
            }

            // Gọi hàm tải sản phẩm khi DOM đã sẵn sàng
            loadFragranceProducts();

            function renderFragrance(items) {
                if (!fragranceProductsContainer) {
                    console.error("LỖI: Không tìm thấy container '#fragrance-products' trong HTML!");
                    return;
                }
                fragranceProductsContainer.innerHTML = ""; // Xóa nội dung tĩnh cũ
                items.forEach(item => {
                            const card = document.createElement("div");
                            card.className = "card";

                            let filename = item.image_url || '';
                            let imgPath = filename;
                            if (filename && !filename.startsWith('http') && !filename.startsWith('../')) {
                                // Đồng bộ đường dẫn: nếu là video thì vào thư mục video, ngược lại vào img
                                imgPath = (item.is_video == 1 ? '../video/' : '../img/') + filename;
                            }

                            card.innerHTML = `
                <div class="img-container">
                    ${item.is_video == 1 
                        ? `<video src="${imgPath}" autoplay muted loop playsinline style="width:100%; height:100%; object-fit:cover;"></video>` 
                        : `<img src="${imgPath}" onerror="this.src='../img/f1.jpg'">`}
                </div>
                <div class="card-content" style="padding: 20px;">
                    <h3>${item.name}</h3>
                    <p>$${item.price}.00</p>
                    <button class="add-to-cart" onclick="addToCart('${item.name.replace(/'/g, "\\'")}', ${item.price}, '${imgPath}', ${item.is_video})">Add to Cart</button>
                </div>
            `;
            fragranceProductsContainer.appendChild(card);
        });
    }

    // ===== CART LOGIC =====
    window.toggleCart = function() {
        const sidebar = document.getElementById("cart-sidebar");
        const overlay = document.querySelector(".cart-overlay");
        if (sidebar) sidebar.classList.toggle("active");
        if (overlay) overlay.classList.toggle("active");
    };

    window.addToCart = function(name, price, img, isVideo = false) {
        const existingItem = cart.find(item => item.name === name);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({ name, price, img, quantity: 1, isVideo });
        }
        updateCartUI();

        // Hiệu ứng rung icon giỏ hàng
        const cartIcon = document.querySelector('.cart-icon-wrapper i');
        if (cartIcon) {
            cartIcon.classList.add('shake');
            setTimeout(() => cartIcon.classList.remove('shake'), 500);
        }

        // Hiển thị thông báo thay vì mở sidebar
        if (window.showToast) {
            window.showToast(`${name} đã được thêm vào giỏ hàng`);
        }
    };

    window.removeFromCart = function(name) {
        cart = cart.filter(item => item.name !== name);
        updateCartUI();
    };

    function updateCartUI() {
        const container = document.getElementById("cart-items");
        const cartTotal = document.getElementById("cart-total");
        const cartCount = document.getElementById("cart-count");

        let total = 0;
        let count = 0;

        cart.forEach(item => {
            total += item.price * item.quantity;
            count += item.quantity;
        });

        // Luôn cập nhật số lượng trên Header và lưu vào LocalStorage
        if (cartCount) cartCount.innerText = count;
        localStorage.setItem("fashion_cart", JSON.stringify(cart));

        // Nếu không có container giỏ hàng (sidebar) thì dừng tại đây, không báo lỗi
        if (!container || !cartTotal) return;

        container.innerHTML = "";
        cart.forEach(item => {

            // Kiểm tra nếu là video thì hiện tag video, nếu là ảnh thì hiện tag img
            const mediaHtml = item.isVideo ?
                `<video src="${item.img}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" autoplay muted loop></video>` :
                `<img src="${item.img}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">`;

            container.innerHTML += `
                <div class="cart-item" style="display: flex; gap: 15px; margin-bottom: 20px; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                    ${mediaHtml}
                    <div style="flex: 1;">
                        <h4 style="font-size: 14px; margin: 0; color: white;">${item.name}</h4>
                        <p style="color: #c9a96e; margin: 5px 0 0 0; font-size: 13px;">$${item.price} x ${item.quantity}</p>
                    </div>
                    <button onclick="removeFromCart('${item.name}')" style="background: none; border: none; color: #ff4444; cursor: pointer; font-size: 20px;">&times;</button>
                </div>
            `;
        });

        cartTotal.innerText = total; // Đảm bảo tổng tiền được cập nhật
    }

    window.checkout = function() {
        if (cart.length === 0) {
            alert("Giỏ hàng trống!");
            return;
        }
        fetch('process_checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cart: cart })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    cart = [];
                    updateCartUI();
                    toggleCart();
                } else {
                    alert(data.message);
                    if (data.message.includes("đăng nhập")) window.location.href = "../login.php";
                }
            })
            .catch(err => console.error("Lỗi thanh toán:", err));
    };

    updateCartUI();
});