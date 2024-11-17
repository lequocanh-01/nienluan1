<div>Quản lý thuộc tính hàng hóa</div>
<hr>
<div>Thêm thuộc tính hàng hóa</div>
<?php
require_once './elements_LQA/mod/hanghoaCls.php';
require_once './elements_LQA/mod/thuoctinhCls.php';
require_once './elements_LQA/mod/thuoctinhhhCls.php';

// Lấy danh sách hàng hóa
$hangHoaObj = new HangHoa();
$list_hh = $hangHoaObj->HanghoaGetAll();

// Lấy danh sách thuộc tính
$thuocTinhObj = new ThuocTinh();
$list_lh_thuoctinh = $thuocTinhObj->thuoctinhGetAll();

// Lấy danh sách thuộc tính hàng hóa
$thuocTinhHHObj = new ThuocTinhHH();
$list_lh_thuoctinhhh = $thuocTinhHHObj->thuoctinhhhGetAll();
?>

<div>
    <form name="newthuoctinhhh" id="formaddthuoctinhhh" method="post" action='./elements_LQA/mthuoctinhhh/thuoctinhhhAct.php?reqact=addnew'>
        <table>
            <tr>
                <td>Chọn hàng hóa:</td>
                <td>
                    <select name="idhanghoa" id="hanghoaSelect" required>
                        <option value="">-- Chọn hàng hóa --</option>
                        <?php if (!empty($list_hh)) {
                            foreach ($list_hh as $h) { ?>
                                <option value="<?php echo htmlspecialchars($h->idhanghoa); ?>"><?php echo htmlspecialchars($h->tenhanghoa); ?></option>
                            <?php }
                        } else { ?>
                            <option value="">Không có hàng hóa nào</option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Chọn thuộc tính:</td>
                <td>
                    <select name="idThuocTinh" id="idThuocTinh" required>
                        <?php if (!empty($list_lh_thuoctinh)) {
                            foreach ($list_lh_thuoctinh as $l) { ?>
                                <option value="<?php echo htmlspecialchars($l->idThuocTinh); ?>"><?php echo htmlspecialchars($l->tenThuocTinh); ?></option>
                            <?php }
                        } else { ?>
                            <option value="">Không có thuộc tính nào</option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tên Thuộc Tính HH</td>
                <td><input type="text" name="tenThuocTinhHH" required /></td>
            </tr>
            <tr>
                <td>Ghi Chú</td>
                <td><input type="text" name="ghiChu" /></td>
            </tr>
            <tr>
                <td><input type="submit" value="Tạo mới" /></td>
            </tr>
        </table>
    </form>
</div>

<hr />
<div>Danh sách thuộc tính hàng hóa</div>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>ID Hàng Hóa</th>
            <th>ID Thuộc Tính</th>
            <th>Tên Thuộc Tính HH</th>
            <th>Ghi Chú</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list_lh_thuoctinhhh)) {
            foreach ($list_lh_thuoctinhhh as $u) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($u->idThuocTinhHH); ?></td>
                    <td><?php echo htmlspecialchars($u->idhanghoa); ?></td>
                    <td><?php echo htmlspecialchars($u->idThuocTinh); ?></td>
                    <td class="tenthuoctinhhh"><?php echo htmlspecialchars($u->tenThuocTinhHH); ?></td>
                    <td><?php echo htmlspecialchars($u->ghiChu); ?></td>
                    <td>
                        <a href="./elements_LQA/mthuoctinhhh/thuoctinhhhAct.php?reqact=deletethuoctinhhh&idThuocTinhHH=<?php echo htmlspecialchars($u->idThuocTinhHH); ?>" onclick="return confirm('Bạn có chắc muốn xóa không?');">
                            <img src="./img_LQA/delete.png" class="iconimg">
                        </a>
                        <img src="./img_LQA/Update.png" class="w_update_btn_open_tthh" data-id="<?php echo htmlspecialchars($u->idThuocTinhHH); ?>">
                    </td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="7">Không có thuộc tính hàng hóa nào.</td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<div id="w_update_tthh" style="display:none;">
    <div id="w_update_form_tthh"></div>
    <button class="close-btn" id="w_close_btn_tthh">Đóng</button>
</div>

<script>
$(document).ready(function() {
    $(".w_update_btn_open_tthh").click(function(e) {
        e.preventDefault();
        const idThuocTinhHH = $(this).data("id");
        $("#w_update_form_tthh").load("./elements_LQA/mthuoctinhhh/thuoctinhhhUpdate.php", { idThuocTinhHH: idThuocTinhHH });
        $("#w_update_tthh").show();
    });

    $("#w_close_btn_tthh").click(function() {
        $("#w_update_tthh").hide();
    });
});
</script>