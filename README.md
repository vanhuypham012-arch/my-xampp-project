# 🛍️ Fashion Club - Web Bán Hàng Thời Trang

Dự án ứng dụng web bán hàng thời trang được xây dựng bằng **PHP, MySQL, HTML, CSS, JavaScript**.

---

## 📋 Yêu Cầu Cài Đặt

Trước khi chạy dự án, bạn cần cài đặt:
- **XAMPP** (hoặc Apache + PHP + MySQL)
- **phpMyAdmin** (để quản lý database)
- **Git** (tùy chọn, để clone repo)

---

## 🚀 Hướng Dẫn Setup

### **Bước 1: Cài XAMPP**
1. Tải XAMPP từ: https://www.apachefriends.org/
2. Cài đặt và chọn đường dẫn: `C:\xampp\`
3. Khởi động **Apache** và **MySQL** từ **XAMPP Control Panel**

### **Bước 2: Tải Dự Án**
Bạn có thể lấy dự án bằng 2 cách:

**Cách 1: Clone từ GitHub**
```bash
cd C:\xampp\htdocs
git clone https://github.com/vanhuypham012-arch/my-xampp-project.git
cd my-xampp-project
```

**Cách 2: Tải file ZIP**
- Vào: https://github.com/vanhuypham012-arch/my-xampp-project
- Click **Code** → **Download ZIP**
- Giải nén vào `C:\xampp\htdocs\`

### **Bước 3: Tạo Database**

1. Mở **phpMyAdmin**: http://localhost/phpmyadmin
2. Vào tab **SQL** (hoặc **Import**)
3. Copy toàn bộ nội dung file `html/db.sql`
4. Paste vào phpMyAdmin và click **Execute**
5. Database `fashion_club` sẽ được tạo tự động ✅

**Hoặc bạn có thể import file trực tiếp:**
- Click tab **Import**
- Chọn file `html/db.sql`
- Click **Go**

### **Bước 4: Chạy Website**

Mở trình duyệt (Chrome, Firefox, v.v.) và truy cập:
```
http://localhost/my-xampp-project/
```

---

## 👤 Tài Khoản Test

**Admin (Quản trị viên):**
- **Username:** `admin`
- **Mật khẩu:** `123`

**Khách hàng:**
- **Username:** `khachhang1`
- **Mật khẩu:** `123`

---

## 📁 Cấu Trúc Thư Mục

```
my-xampp-project/
├── index.php              # Trang chủ
├── login.php              # Đăng nhập
├── register.php           # Đăng ký
├── config.php             # Cấu hình database
├── header.php             # Header chung
│
├── html/                  # Các trang HTML & PHP
│   ├── collection.php     # Trang sản phẩm
│   ├── makeup.php         # Mỹ phẩm
│   ├── fragrance.php      # Nước hoa
│   ├── watch.php          # Đồng hồ
│   ├── admin.php          # Quản lý sản phẩm
│   ├── db.php             # Kết nối database (html)
│   ├── login.php          # Đăng nhập (html)
│   ├── db.sql             # File SQL database
│   └── ...
│
���── css/                   # Các file CSS
│   ├── index.css
│   ├── collection.css
│   ├── makeup.css
│   ├── fragrance.css
│   ├── watch.css
│   └── common.css
│
├── js/                    # Các file JavaScript
│   ├── collection.js
│   ├── makeup.js
│   ├── fragrance.js
│   ├── watch.js
│   └── index.js
│
├── img/                   # Hình ảnh sản phẩm
├── video/                 # Video sản phẩm
└── README.md              # File hướng dẫn này
```

---

## ✨ Tính Năng Chính

✅ **Đăng nhập / Đăng ký** - Tài khoản người dùng  
✅ **Bảo mật** - Mã hóa mật khẩu với PASSWORD_BCRYPT  
✅ **Chống SQL Injection** - Dùng Prepared Statements  
✅ **Giỏ hàng** - Lưu trữ bằng localStorage  
✅ **Danh mục sản phẩm** - Makeup, Fragrance, Watch, Clothing  
✅ **Quản lý sản phẩm** - Admin có thể thêm/sửa/xóa sản phẩm  
✅ **Hỗ trợ Tiếng Việt** - Charset UTF-8  
✅ **Đa ngôn ngữ** - Hỗ trợ EN/VN  

---

## 🔧 Công Nghệ Sử Dụng

| Công Nghệ | Phiên Bản |
|-----------|---------|
| PHP | 7.x+ |
| MySQL | 5.7+ |
| HTML5 | - |
| CSS3 | - |
| JavaScript | ES6+ |
| Bootstrap | Không dùng (CSS riêng) |

---

## 🐛 Lỗi Thường Gặp & Giải Pháp

### **1. "Lỗi kết nối Database"**
**Nguyên nhân:** MySQL không chạy hoặc database chưa được tạo  
**Giải pháp:**
- Kiểm tra MySQL chạy trong XAMPP Control Panel
- Import file `html/db.sql` vào phpMyAdmin

### **2. "Trang không hiển thị, lỗi 404"**
**Nguyên nhân:** Đường dẫn sai hoặc Apache không chạy  
**Giải pháp:**
- Kiểm tra Apache chạy trong XAMPP
- Kiểm tra folder nằm trong `C:\xampp\htdocs\`
- Truy cập: `http://localhost/my-xampp-project/`

### **3. "Tệp CSS/JS không load"**
**Nguyên nhân:** Đường dẫn file sai  
**Giải pháp:**
- Kiểm tra các file .css trong thư mục `css/`
- Kiểm tra các file .js trong thư mục `js/`

### **4. "Giỏ hàng không lưu được"**
**Nguyên nhân:** JavaScript bị vô hiệu hóa hoặc localStorage bị khóa  
**Giải pháp:**
- Bật JavaScript trong trình duyệt
- Xóa cache trình duyệt

---

## 📞 Hỗ Trợ

Nếu gặp lỗi, vui lòng:
1. Kiểm tra lại các bước setup trên
2. Đảm bảo XAMPP, Apache, MySQL đang chạy
3. Kiểm tra database có tồn tại không (trong phpMyAdmin)
4. Xem console browser (F12) để xem lỗi JavaScript

---

## 📝 Ghi Chú

- **Mật khẩu database:** Để trống (mặc định XAMPP)
- **Database:** `fashion_club`
- **Tài khoản admin mặc định:** admin / 123
- **Tài khoản khách mặc định:** khachhang1 / 123
- **Ảnh sản phẩm:** Nằm trong thư mục `img/`
- **Video sản phẩm:** Nằm trong thư mục `video/`

---

## 👨‍💻 Tác Giả

- **Tên:** Phạm Văn Huy và Nguyễn Ngọc Như Nguyệt
- **GitHub:** https://github.com/vanhuypham012-arch
- **Repo:** https://github.com/vanhuypham012-arch/my-xampp-project

---

**Chúc bạn sử dụng vui vẻ! 🎉**
