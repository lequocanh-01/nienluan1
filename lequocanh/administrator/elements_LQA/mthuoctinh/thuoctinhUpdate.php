<div align="center">Cập nhật thuộc tính</div>
<hr>
<?php
require '../../elements_LQA/mod/thuoctinhCls.php';

$idThuocTinh = isset($_POST['id']) ? $_POST['id'] : null;

if (!$idThuocTinh) {
    echo "ID thuộc tính không hợp lệ.";
    exit;
}

$lhobj = new ThuocTinh();
$getLhUpdate = $lhobj->thuoctinhGetById($idThuocTinh);

if (!$getLhUpdate) {
    echo "Không tìm thấy thuộc tính với ID này.";
    exit;
}
?>

<div>
    <form name="updatethuoctinh" id="formupdatelh" method="post" action='./elements_LQA/mthuoctinh/thuoctinhAct.php?reqact=updatethuoctinh' enctype="multipart/form-data">
        <input type="hidden" name="idThuocTinh" value="<?php echo $getLhUpdate->idThuocTinh; ?>" />
        
        <table>
            <tr>
                <td>Tên thuộc tính</td>
                <td><input type="text" name="tenThuocTinh" value="<?php echo htmlspecialchars($getLhUpdate->tenThuocTinh); ?>" /></td>
            </tr>
            <tr>
                <td>Giá trị</td>
                <td><input type="text" name="giaTri" value="<?php echo htmlspecialchars($getLhUpdate->giaTri); ?>" /></td>
            </tr>
            <tr>
                <td>Ghi Chú</td>
                <td><input type="text" name="ghiChu" value="<?php echo htmlspecialchars($getLhUpdate->ghiChu); ?>" /></td>
            </tr>
            <tr>
                <td><input type="submit" id="btnsubmit" value="Cập nhật" /></td>
                <td><b id="noteForm"></b></td>
            </tr>
        </table>
    </form>
</div>
