<div>Quản lý loại hàng</div>
<hr>
<div>Thêm loại hàng</div>
<div>
    <form name="newloaihang" id="formaddloaihang" method="post" action='./elements_LQA/mloaihang/loaihangAct.php?reqact=addnew' enctype="multipart/form-data">
        <table>
            <tr>
                <td>Tên loại hàng</td>
                <td><input type="text" name="tenloaihang" required></td>
            </tr>
            <tr>
                <td>Mô tả</td>
                <td><input type="text" name="mota" required></td>
            </tr>
            <tr>
                <td>Hình ảnh</td>
                <td><input type="file" name="fileimage" required></td>
            </tr>
            <tr>
                <td><input type="submit" id="btnsubmit" value="Tạo mới"></td>
                <td><input type="reset" value="Làm lại"><b id="noteForm"></b></td>
            </tr>
        </table>
    </form>
    <hr />
    <?php
    require './elements_LQA/mod/loaihangCls.php';
    $lhObj = new loaihang();
    $list_lh = $lhObj->LoaihangGetAll();
    $l = count($list_lh);
    ?>
    <div class="title_loaihang">Danh sách loại hàng</div>
    <div class="content_loaihang">
        Trong bảng có: <b><?php echo $l; ?></b>

        <table border="solid">
            <thead>
                <th>ID</th>
                <th>Tên loại hàng</th>
                <th>Mô tả</th>
                <th>Hình ảnh</th>
                <th>Chức năng</th>
            </thead>
            <?php
            if ($l > 0) {
                foreach ($list_lh as $u) {
            ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u->idloaihang); ?></td>
                        <td><?php echo htmlspecialchars($u->tenloaihang); ?></td>
                        <td><?php echo htmlspecialchars($u->mota); ?></td>
                        <td align="center">
                            <img class="iconbutton" src="data:image/png;base64,<?php echo htmlspecialchars($u->hinhanh); ?>">
                        </td>
                        <td align="center">
                            <?php if (isset($_SESSION['ADMIN'])): ?>
                                <a href="./elements_LQA/mloaihang/loaihangAct.php?reqact=deleteloaihang&idloaihang=<?php echo htmlspecialchars($u->idloaihang); ?>">
                                    <img src="./img_LQA/Delete.png" class="iconimg">
                                </a>
                                <img src="./img_LQA/Update2.png" class="w_update_btn_open" value="<?php echo htmlspecialchars($u->idloaihang); ?>">
                            <?php else: ?>
                                <img src="./img_LQA/Delete.png" class="iconimg">
                            <?php endif; ?>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
        </table>
    </div>

    <div id="w_update">
        <div id="w_update_form"></div>
        <input type="button" value="close" id="w_close_btn">
    </div>
</div>