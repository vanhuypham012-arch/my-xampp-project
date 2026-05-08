window.addEventListener("DOMContentLoaded", () => {
            let cart = JSON.parse(localStorage.getItem("fashion_cart")) || [];
            let hideTimeout;
            const watchProductsContainer = document.getElementById("watch-products");

            // ===== EFFECT: HEADER SCROLL =====
            const header = document.querySelector(".header");
            window.addEventListener("scroll", () => {
                if (!header) {
                    console.warn("Không tìm thấy phần tử header cho hiệu ứng cuộn.");
                    return;
                }
                if (window.scrollY > 50) {
                    header.classList.add("scrolled");
                } else {
                    header.classList.remove("scrolled");
                }
            });

            // Tải sản phẩm động
            function loadWatchProducts() {
                // Lấy dữ liệu thật từ Database
                fetch('../manage_products.php?category=watch')
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) renderWatch(data);
                        else watchProductsContainer.innerHTML = "<p style='grid-column: 1/-1; text-align: center; color: #888;'>Chưa có sản phẩm đồng hồ nào trong hệ thống.</p>";
                    })
                    .catch(err => console.error("Lỗi:", err));
            }

            function renderWatch(items) {
                if (!watchProductsContainer) {
                    console.error("LỖI: Không tìm thấy container '#watch-products' trong HTML!");
                    return;
                }
                watchProductsContainer.innerHTML = ""; // Xóa nội dung tĩnh cũ
                items.forEach(item => {
                            const card = document.createElement("div");
                            card.className = "card";

                            let filename = item.image_url || '';
                            let imgPath = filename;
                            if (filename && !filename.startsWith('http')) {
                                imgPath = (item.is_video == 1 ? '../video/' : '../img/') + filename;
                            }

                            const safeName = item.name.replace(/'/g, "\\'");

                            card.innerHTML = `
                <div class="img-container">
                    ${item.is_video == 1 
                        ? `<video src="${imgPath}" autoplay muted loop playsinline style="width:100%; height:100%; object-fit:cover;"></video>` 
                        : `<img src="${imgPath}" onerror="this.src='../img/w1.jpg'">`}
                </div>
                <h3>${item.name}</h3>
                <p>$${item.price}</p>
                <button class="add-to-cart" onclick="event.stopPropagation(); addToCart('${safeName}', ${item.price}, '${imgPath}', ${item.is_video})">
                    Add to Bag
                </button>
            `;
            watchProductsContainer.appendChild(card);
        });
    }

    loadWatchProducts();

    // Hàm bật/tắt video khi BẤM vào hình (Đồng bộ với Makeup)
    window.toggleVideo = function(container) {
        const video = container.querySelector('.hover-video');
        const overlay = container.querySelector('.play-btn-overlay');
        const mainImg = container.querySelector('.main-img');
        
        if (!video) return;

        if (video.paused) {
            video.play();
            container.classList.add('playing');
            if (overlay) overlay.style.display = 'none';
            if (mainImg) mainImg.style.opacity = '0';
        } else {
            video.pause();
            container.classList.remove('playing');
            if (overlay) overlay.style.display = 'flex';
            if (mainImg) mainImg.style.opacity = '1';
        }
    };

    // Logic Giỏ hàng (tương tự makeup.js)
    window.toggleCart = function() {
        const sidebar = document.getElementById("cart-sidebar");
        if (sidebar) sidebar.classList.toggle("active");
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
        
        if (window.showToast) {
            window.showToast(`Đã thêm ${name} vào túi`);
        }

        const sidebar = document.getElementById("cart-sidebar");
        if (sidebar) {
            sidebar.classList.add("active");
            clearTimeout(hideTimeout);
            hideTimeout = setTimeout(() => sidebar.classList.remove("active"), 3000);
        }
    };

    window.removeFromCart = function(name) {
        cart = cart.filter(item => item.name !== name);
        updateCartUI();
    };

    function updateCartUI() {
        const container = document.getElementById("cart-items");
        const cartCount = document.getElementById("cart-count");
        const cartTotal = document.getElementById("cart-total");

        let total = 0;
        let count = 0;

        cart.forEach(item => {
            total += item.price * item.quantity;
            count += item.quantity;
            });

            // Cập nhật số lượng giỏ hàng trên Header
            if (cartCount) cartCount.innerText = count;
            localStorage.setItem("fashion_cart", JSON.stringify(cart));

            // Kiểm tra các phần tử sidebar
            if (!container || !cartTotal) return;

            container.innerHTML = "";
            cart.forEach(item => {

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

        cartTotal.innerText = total;
    }

    // Hàm thanh toán (tương tự makeup.js)
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