function openModal(id) {
    document.getElementById(id).style.display = "block";
    document.body.style.overflow = "hidden";
}

function closeModal(id) {
    document.getElementById(id).style.display = "none";
    document.body.style.overflow = "auto";
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.style.display = "none";
        document.body.style.overflow = "auto";
    }
};

// =======================
// 👉 THÊM BÁC SĨ
// =======================
function openAddDoctor() {

    document.getElementById("modalTitle").innerText = "Đăng ký bác sĩ mới";
    document.getElementById("modalForm").action = "../../backend/admin/add_doctor.php";

    document.getElementById("modalBody").innerHTML = `
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="Email" required>
        </div>

        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="Password" required>
        </div>

        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" name="HoTen" required>
        </div>

        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" name="SoDienThoai">
        </div>

        <div class="form-group">
            <label>Chuyên khoa</label>
            <select name="MaChuyenKhoa" required>
                ${window.specialtiesOptions}
            </select>
        </div>
    `;

    openModal('mainModal');
}

// =======================
// 👉 THÊM CHUYÊN KHOA
// =======================
function openAddSpecialty() {

    document.getElementById("modalTitle").innerText = "Thêm chuyên khoa";
    document.getElementById("modalForm").action = "../../backend/admin/add_specialty.php";

    document.getElementById("modalBody").innerHTML = `
        <div class="form-group">
            <label>Tên chuyên khoa</label>
            <input type="text" name="TenChuyenKhoa" required>
        </div>

        <div class="form-group">
            <label>Mô tả</label>
            <textarea name="MoTa"></textarea>
        </div>
    `;

    openModal('mainModal');
}

// =======================
// 👉 THÊM TÀI KHOẢN
// =======================
function openAddUser() {

    document.getElementById("modalTitle").innerText = "Tạo tài khoản";
    document.getElementById("modalForm").action = "../../backend/admin/add_user.php";

    document.getElementById("modalBody").innerHTML = `
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="Email" required>
        </div>

        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="Password" required>
        </div>

        <div class="form-group">
            <label>Quyền</label>
            <select name="Role">
                <option value="admin">Admin</option>
                <option value="doctor">Doctor</option>
                <option value="user">User</option>
            </select>
        </div>
    `;

    openModal('mainModal');
}