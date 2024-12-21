<?php
session_start();
require_once("../mod/database.php");
require_once("../mod/hanghoaCls.php");

// Tắt báo lỗi để tránh output không mong muốn
error_reporting(0);
ini_set('display_errors', 0);

// Đảm bảo gửi header JSON cho các action không phải upload
if (!isset($_REQUEST["reqact"]) || $_REQUEST["reqact"] !== "addnew") {
    header('Content-Type: application/json; charset=utf-8');
}

function deleteImageFile($imagePath)
{
    if ($imagePath) {
        // Xây dựng đường dẫn đầy đủ đến file ảnh
        $fullPath = dirname(dirname(dirname(__FILE__))) . '/' . $imagePath;

        // Xóa file ảnh nếu tồn tại
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
    }
    return true; // Trả về true nếu không có file để xóa
}

try {
    if (isset($_REQUEST["reqact"])) {
        $requestAction = $_REQUEST["reqact"];
        $hanghoa = new hanghoa();

        switch ($requestAction) {
            case "addnew":
                if (isset($_FILES['files']) && is_array($_FILES['files']['name'])) {
                    $files = $_FILES['files'];
                    $uploadDir = '../../uploads/';

                    // Đảm bảo thư mục uploads tồn tại
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $allSuccess = true;
                    $uploadedFiles = [];

                    for ($i = 0; $i < count($files['name']); $i++) {
                        if ($files['error'][$i] === UPLOAD_ERR_OK) {
                            $fileName = $files['name'][$i];
                            $fileType = $files['type'][$i];
                            $fileSize = $files['size'][$i];
                            $fileTmp = $files['tmp_name'][$i];

                            // Tạo tên file duy nhất
                            $uniqueName = uniqid() . '_' . $fileName;
                            $uploadPath = $uploadDir . $uniqueName;

                            // Kiểm tra và tạo thư mục nếu chưa tồn tại
                            if (!file_exists(dirname($uploadPath))) {
                                mkdir(dirname($uploadPath), 0777, true);
                            }

                            if (move_uploaded_file($fileTmp, $uploadPath)) {
                                // Format kích thước file
                                $formattedSize = $fileSize < 1024 ? $fileSize . ' B' : ($fileSize < 1048576 ? round($fileSize / 1024, 2) . ' KB' :
                                    round($fileSize / 1048576, 2) . ' MB');

                                // Đường dẫn tương đối để lưu vào database
                                $relativePath = 'uploads/' . $uniqueName;

                                $result = $hanghoa->ThemHinhAnh($fileName, $fileType, $formattedSize, $relativePath);

                                if (!$result) {
                                    $allSuccess = false;
                                    // Xóa file đã upload nếu không thể lưu vào database
                                    if (file_exists($uploadPath)) {
                                        unlink($uploadPath);
                                    }
                                } else {
                                    $uploadedFiles[] = $uploadPath;
                                }
                            } else {
                                $allSuccess = false;
                            }
                        } else {
                            $allSuccess = false;
                        }
                    }

                    if ($allSuccess) {
                        header("location: ../../index.php?req=hinhanhview&result=ok");
                    } else {
                        // Nếu có lỗi, xóa tất cả các file đã upload
                        foreach ($uploadedFiles as $file) {
                            if (file_exists($file)) {
                                unlink($file);
                            }
                        }
                        header("location: ../../index.php?req=hinhanhview&result=notok");
                    }
                } else {
                    header("location: ../../index.php?req=hinhanhview&result=notok");
                }
                break;

            case "deletemultiple":
                $json = file_get_contents('php://input');
                $data = json_decode($json);

                if (!$data || !isset($data->ids) || !is_array($data->ids)) {
                    throw new Exception("Dữ liệu không hợp lệ");
                }

                $success = true;
                $failedIds = [];

                foreach ($data->ids as $id) {
                    // Kiểm tra xem hình ảnh có đang được sử dụng không
                    $products = $hanghoa->GetProductsByImageId($id);
                    if (!empty($products)) {
                        $success = false;
                        $failedIds[] = $id;
                        continue;
                    }

                    // Lấy đường dẫn ảnh trước khi xóa
                    $imagePath = $hanghoa->GetImagePath($id);

                    // Xóa file ảnh
                    if (!deleteImageFile($imagePath)) {
                        $success = false;
                        $failedIds[] = $id;
                        continue;
                    }

                    // Xóa record trong database
                    if (!$hanghoa->XoaHinhAnh($id)) {
                        $success = false;
                        $failedIds[] = $id;
                    }
                }

                if ($success) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Xóa tất cả hình ảnh thành công'
                    ]);
                } else {
                    if (count($failedIds) === count($data->ids)) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'Không thể xóa các hình ảnh đã chọn vì đang được sử dụng hoặc có lỗi xảy ra'
                        ]);
                    } else {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Một số hình ảnh không thể xóa vì đang được sử dụng hoặc có lỗi xảy ra'
                        ]);
                    }
                }
                break;

            case "deletehinhanh":
                if (!isset($_REQUEST["id"])) {
                    throw new Exception("Thiếu ID hình ảnh");
                }

                $id = intval($_REQUEST["id"]);

                // Kiểm tra xem hình ảnh có đang được sử dụng không
                $products = $hanghoa->GetProductsByImageId($id);

                if (!empty($products)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Hình ảnh này đang được sử dụng bởi một số sản phẩm. Không thể xóa.',
                        'products' => $products
                    ]);
                    exit;
                }

                // Lấy đường dẫn ảnh trước khi xóa
                $imagePath = $hanghoa->GetImagePath($id);

                // Xóa file ảnh
                if (!deleteImageFile($imagePath)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Không thể xóa file ảnh'
                    ]);
                    exit;
                }

                // Xóa record trong database
                $result = $hanghoa->XoaHinhAnh($id);

                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Xóa hình ảnh thành công'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Không thể xóa hình ảnh khỏi database'
                    ]);
                }
                break;

            default:
                throw new Exception("Hành động không hợp lệ");
        }
    } else {
        throw new Exception("Thiếu tham số hành động");
    }
} catch (Exception $e) {
    if ($requestAction === "addnew") {
        header("location: ../../index.php?req=hinhanhview&result=notok");
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Đảm bảo kết thúc thực thi sau khi gửi JSON
if ($requestAction !== "addnew") {
    exit();
}
