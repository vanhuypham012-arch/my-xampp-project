window.addEventListener("DOMContentLoaded", () => {
            let cart = JSON.parse(localStorage.getItem("fashion_cart")) || [];
            let hideTimeout;

            // ===== EFFECT: HEADER SCROLL =====
            const header = document.querySelector(".header");
            window.addEventListener("scroll", () => {
                if (header) {
                    header.classList.toggle("scrolled", window.scrollY > 50);
                }
            });

            const productContainer = document.getElementById("makeup-products");

            function loadMakeupProducts() {
                // Lấy dữ liệu thật từ Database
                fetch('../manage_products.php?category=makeup')
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) renderMakeup(data);
                        else productContainer.innerHTML = "<p>Chưa có sản phẩm makeup nào.</p>";
                    })
                    .catch(err => console.error("Lỗi:", err));
            }

            function renderMakeup(items) {
                const productContainer = document.getElementById("makeup-products") || document.querySelector(".products");
                if (!productContainer) {
                    console.error("LỖI: Không tìm thấy container '#makeup-products' trong HTML!");
                    return;
                }
                console.log("Bắt đầu vẽ " + items.length + " sản phẩm lên màn hình.");
                productContainer.innerHTML = "";
                items.forEach(item => {
                            const card = document.createElement("div");
                            card.className = "card";

                            // Tối ưu đường dẫn ảnh: Đảm bảo luôn bắt đầu bằng ../img/ nếu chạy từ thư mục html/
                            let imgPath = item.image_url || '';
                            if (imgPath && !imgPath.startsWith('http') && !imgPath.startsWith('../')) {
                                if (imgPath.startsWith('img/') || imgPath.startsWith('video/')) {
                                    imgPath = '../' + imgPath;
                                } else {
                                    // Tự động chọn thư mục dựa trên loại tệp
                                    imgPath = (item.is_video == 1 ? '../video/' : '../img/') + imgPath;
                                }
                            }

                            card.innerHTML = `
                <div class="img-container">
                    ${item.is_video == 1 
                        ? `<video src="${imgPath}" autoplay muted loop playsinline style="width:100%; height:100%; object-fit:cover;"></video>` 
                        : `<img src="${imgPath}" onerror="this.src='../img/v4.jpg'">`}
                </div>
                <h3>${item.name}</h3>
                <p>$${item.price}</p>
                <button class="add-to-cart" onclick="event.stopPropagation(); addToCart('${item.name.replace(/'/g, "\\'")}', ${item.price}, '${imgPath}', ${item.is_video})">
                    Add to Bag
                </button>
            `;
            productContainer.appendChild(card);
        });
    }

    loadMakeupProducts();

    // Hàm bật/tắt video khi BẤM vào hình
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

    // Logic Giỏ hàng
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

        if (!container || !cartCount || !cartTotal) return;

        container.innerHTML = "";
        let total = 0;
        let count = 0;

        cart.forEach(item => {
            total += item.price * item.quantity;
            count += item.quantity;

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

        cartCount.innerText = count;
        cartTotal.innerText = total;
        localStorage.setItem("fashion_cart", JSON.stringify(cart));
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