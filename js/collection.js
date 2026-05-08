// Xóa bỏ phần code bị lặp lại ở đầu file nếu có
// Chỉ giữ lại một khối window.addEventListener("DOMContentLoaded", ...) duy nhất

// ===== CHỜ DOM LOAD =====
window.addEventListener("DOMContentLoaded", () => {

            // ===== EFFECT: HEADER SCROLL =====
            const header = document.querySelector(".header");
            window.addEventListener("scroll", () => {
                if (window.scrollY > 50) {
                    header.classList.add("scrolled");
                } else {
                    header.classList.remove("scrolled");
                }
            });

            // ===== SELECT =====
            const productsContainer = document.querySelector(".products");

            // ===== FETCH DATA FROM DATABASE =====
            function loadProducts() {
                fetch('../manage_products.php')
                    .then(res => res.json())
                    .then(data => {
                            productsContainer.innerHTML = "";

                            // ĐỒNG BỘ: Chỉ lọc lấy các sản phẩm thuộc nhóm Fashion như trong Admin
                            const fashionCategories = ['tops', 'pants', 'dress', 'jacket', 't-shirt', 'shirt'];
                            const fashionItems = data.filter(item => fashionCategories.includes(item.category.toLowerCase()));

                            updateNotifications(data);
                            if (fashionItems.length === 0) {
                                productsContainer.innerHTML = "<p style='grid-column: 1/-1; text-align: center; color: #888;'>Chưa có sản phẩm thời trang nào được thêm.</p>";
                                return;
                            }

                            fashionItems.forEach(item => {
                                        const card = document.createElement("div");
                                        card.className = `card ${item.category} ${item.gender}`;

                                        let filename = item.image_url || '';
                                        let imgPath = filename;
                                        if (filename && !filename.startsWith('http')) {
                                            imgPath = (item.is_video == 1 ? '../video/' : '../img/') + filename;
                                        }

                                        const safeName = (item.name || 'Unnamed Product').replace(/'/g, "\\'");

                                        card.innerHTML = `
                                <div class="img-container">
                                    ${item.is_video == 1 
                                        ? `<video src="${imgPath}" autoplay muted loop playsinline></video>` 
                                        : `<img src="${imgPath}" onerror="this.src='../img/v4.jpg';">`}
                                </div>
                                <h3>${item.name || 'Fashion Item'}</h3>
                                <p>$${item.price}</p>
                                <button class="add-to-cart" onclick="addToCart('${safeName}', ${item.price}, '${imgPath}', ${item.is_video})">Add to Cart</button>
                            `;
                            productsContainer.appendChild(card);
                        });
                    })
                    .catch(err => {
                        console.error("Lỗi khi tải sản phẩm:", err);
                        productsContainer.innerHTML = "<p>Lỗi khi kết nối đến cơ sở dữ liệu.</p>";
                    });
            }

    // Gọi hàm load dữ liệu khi trang web mở ra
    loadProducts();

    const sidebar = document.querySelector(".sidebar");

    const genderCheckboxes = document.querySelectorAll(".gender");
    const categoryCheckboxes = document.querySelectorAll(".category");
    const categoryBox = document.querySelector(".category-box");
    const categoryLabels = document.querySelectorAll(".category-box label");

    // ===== HEADER MENU =====
    window.showCategory = function(e, type) {
        e.preventDefault();

        productsContainer.style.display = "none";
        sidebar.style.visibility = "hidden"; // Dùng visibility thay vì display để không nhảy layout

        if (type === "fashion") {
            productsContainer.style.display = "grid";
            sidebar.style.visibility = "visible";

            // Hiệu ứng hiện ra vài giây rồi ẩn
            sidebar.classList.add("show-temp");
            setTimeout(() => {
                sidebar.classList.remove("show-temp");
            }, 3000); // 3 giây

            // reset sidebar
            categoryBox.style.display = "none";
            genderCheckboxes.forEach(g => g.checked = false);
            categoryCheckboxes.forEach(c => c.checked = false);
            document.querySelectorAll(".card").forEach(card => card.style.display = "block");
        } else if (type === "watch") {
            // Chuyển hướng sang trang watch
            window.location.href = "watch.php";
        } else if (type === "makeup") {
            // Chuyển hướng sang trang makeup
            window.location.href = "makeup.php";
        }
    };


    // ===== GENDER =====
    genderCheckboxes.forEach(cb => {
        cb.addEventListener("change", () => {

            let selected = [];

            genderCheckboxes.forEach(g => {
                if (g.checked) selected.push(g.value);
            });

            // show/hide category
            categoryBox.style.display = selected.length ? "block" : "none";

            // filter category theo gender
            categoryLabels.forEach(label => {
                let type = label.getAttribute("data-type");

                if (selected.includes(type)) {
                    label.style.display = "block";
                } else {
                    label.style.display = "none";
                    label.querySelector("input").checked = false;
                }
            });

            updateFilter();
        });
    });


    // ===== CATEGORY =====
    categoryCheckboxes.forEach(cb => {
        cb.addEventListener("change", updateFilter);
    });


    // ===== FILTER =====
    function updateFilter() {

        let selectedCategories = [];
        let selectedGenders = [];

        categoryCheckboxes.forEach(cb => {
            if (cb.checked) selectedCategories.push(cb.value);
        });

        genderCheckboxes.forEach(cb => {
                if (cb.checked) selectedGenders.push(cb.value);
        });
        const cards = document.querySelectorAll(".card");

        cards.forEach(card => {

            let matchCategory =
                selectedCategories.length === 0 ||
                selectedCategories.some(c => card.classList.contains(c));

            let matchGender =
                selectedGenders.length === 0 ||
                selectedGenders.some(g => card.classList.contains(g));

            card.style.display = (matchCategory && matchGender) ? "block" : "none";
        });
    }

    // ===== LOGIC GIỎ HÀNG =====
    // Khởi tạo giỏ hàng từ LocalStorage (để không bị mất khi load lại trang)
    let cart = JSON.parse(localStorage.getItem("fashion_cart")) || [];
    let hideTimeout;

    // Hàm đóng/mở giỏ hàng
    window.toggleCart = function() {
        const cartSidebar = document.getElementById("cart-sidebar");
        const overlay = document.querySelector(".cart-overlay");
        if (cartSidebar) {
            cartSidebar.classList.toggle("active");
            if (overlay) overlay.classList.toggle("active");
        }
    };

    // Hàm thêm sản phẩm vào giỏ
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
            window.showToast(`${name} đã nằm trong túi đồ`);
        }

        // Tự động ẩn sau 3 giây
        clearTimeout(hideTimeout);
        hideTimeout = setTimeout(() => {
            if (cartSidebar) cartSidebar.classList.remove("active");
            if (overlay) overlay.classList.remove("active");
        }, 3000);
    };

    // Hàm xóa sản phẩm
    window.removeFromCart = function(name) {
        cart = cart.filter(item => item.name !== name);
        updateCartUI();
    };

    // Cập nhật giao diện giỏ hàng
    function updateCartUI() {
        const cartItemsContainer = document.getElementById("cart-items");
        const cartCount = document.getElementById("cart-count");
        const cartTotal = document.getElementById("cart-total");

        let total = 0;
        let count = 0;

        cart.forEach(item => {
            total += item.price * item.quantity;
            count += item.quantity;
        });

        // Luôn cập nhật Header
        if (cartCount) cartCount.innerText = count;
        localStorage.setItem("fashion_cart", JSON.stringify(cart));

        // Nếu thiếu Sidebar thì không chạy tiếp phần dưới
        if (!cartItemsContainer || !cartTotal) return;

        cartItemsContainer.innerHTML = "";
        cart.forEach(item => {

            const mediaHtml = item.isVideo ?
                `<video src="${item.img}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" autoplay muted loop></video>` :
                `<img src="${item.img}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">`;

            cartItemsContainer.innerHTML += `
                <div class="cart-item" style="display: flex; gap: 15px; margin-bottom: 20px; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                    ${mediaHtml}
                    <div style="flex: 1;">
                        <h4 style="font-size: 14px; margin: 0; color: black;">${item.name}</h4>
                        <p style="color: #c9a96e; margin: 5px 0 0 0; font-size: 13px;">$${item.price} x ${item.quantity}</p>
                    </div>
                    <button onclick="removeFromCart('${item.name}')" style="background: none; border: none; color: #ff4444; cursor: pointer; font-size: 20px;">&times;</button>
                </div>
            `;
        });

        cartTotal.innerText = total;
    }

    // ===== NOTIFICATION LOGIC =====
    window.toggleNotifications = function(e) {
        e.stopPropagation();
        document.getElementById("notification-dropdown").classList.toggle("active");
    };

    document.addEventListener("click", () => {
        const dropdown = document.getElementById("notification-dropdown");
        if(dropdown) dropdown.classList.remove("active");
    });

    function updateNotifications(allProducts) {
        const list = document.getElementById("notification-list");
        const countBadge = document.getElementById("notification-count");
        if (!list || !countBadge) return;

        // Lọc thông báo: Hết hàng và Hàng mới
        const lowStock = allProducts.filter(p => p.quantity > 0 && p.quantity < 10);
        const newItems = allProducts.slice(0, 2); // 2 sản phẩm mới nhất

        let html = "";
        lowStock.forEach(p => {
            html += `<div class="notification-item">🔥 <b>Sắp hết!</b> ${p.name} chỉ còn lại ${p.quantity} sản phẩm.</div>`;
        });
        newItems.forEach(p => {
            html += `<div class="notification-item">🆕 <b>Hàng mới:</b> ${p.name} hiện đã sẵn sàng phục vụ bạn!</div>`;
        });

        if (html === "") {
            html = `<div class="notification-item">Hôm nay không có thông báo gì mới.</div>`;
        }

        list.innerHTML = html;
        countBadge.innerText = lowStock.length + newItems.length;
    }

    // Hàm thanh toán tự động trừ kho qua Database
    window.checkout = function() {
        if (cart.length === 0) {
            alert("Giỏ hàng của bạn đang trống!");
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
                window.toggleCart();
            } else {
                alert(data.message);
                if (data.message.includes("đăng nhập")) window.location.href = "../login.php";
            }
        })
        .catch(err => console.error("Lỗi thanh toán:", err));
    };

    // Cập nhật số lượng hiển thị ngay khi trang web vừa tải xong
    updateCartUI();
});