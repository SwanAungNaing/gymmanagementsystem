<?php
require '../requires/common.php';
require "../requires/common_function.php";
require "../requires/db.php";

$success_msg = "";
$error_msg = "";

if (!isset($_GET['trainer_id']) || empty($_GET['trainer_id'])) {
    header("Location: trainer_list.php?error=Invalid trainer ID");
    exit;
}

$trainer_id = $mysqli->real_escape_string($_GET['trainer_id']);

$trainer_res = selectData('trainers', $mysqli, "*", "WHERE id = '$trainer_id'");
if (!$trainer_res || $trainer_res->num_rows == 0) {
    header("Location: trainer_list.php?error=Trainer not found");
    exit;
}
$trainer = $trainer_res->fetch_assoc();

if (isset($_GET['delete_cert_id'])) {
    $cert_id = $mysqli->real_escape_string($_GET['delete_cert_id']);

    $cert_res = selectData('certificates', $mysqli, "img_path", "WHERE id = '$cert_id'");
    if ($cert_res && $cert_row = $cert_res->fetch_assoc()) {
        if (!empty($cert_row['img_path']) && file_exists('../' . $cert_row['img_path'])) {
            unlink('../' . $cert_row['img_path']);
        }
    }

    $res_delete = deleteData('certificates', $mysqli, "id = '$cert_id'");
    if ($res_delete) {
        header("Location: trainer_certificates.php?trainer_id=$trainer_id&success=Certificate+Deleted+Successfully");
        exit;
    } else {
        header("Location: trainer_certificates.php?trainer_id=$trainer_id&error=Certificate+Delete+Failed");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['certificates'])) {
    $uploaded_files = [];
    $errors = [];

    $upload_dir = '../uploads/certificates/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf', 'docx', 'xls', 'xlsx'];
    $max_size = 10 * 1024 * 1024;

    foreach ($_FILES['certificates']['name'] as $key => $name) {
        if ($_FILES['certificates']['error'][$key] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['certificates']['tmp_name'][$key];
            $file_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_extensions)) {
                $errors[] = "File $name has invalid extension. Allowed: " . implode(', ', $allowed_extensions);
                continue;
            }

            if ($_FILES['certificates']['size'][$key] > $max_size) {
                $errors[] = "File $name is too large. Maximum size is 10MB.";
                continue;
            }

            $file_name = time() . '_' . uniqid() . '.' . $file_ext;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($tmp_name, $target_path)) {
                $relative_path = str_replace('../', '', $target_path);

                $insert_data = [
                    'name' => pathinfo($name, PATHINFO_FILENAME),
                    'trainer_id' => $trainer_id,
                    'img_path' => $relative_path
                ];

                $res_insert = insertData('certificates', $mysqli, $insert_data);
                if (!$res_insert) {
                    $errors[] = "Failed to save certificate $name to database.";
                    unlink($target_path);
                } else {
                    $uploaded_files[] = $name;
                }
            } else {
                $errors[] = "Failed to upload $name.";
            }
        } elseif ($_FILES['certificates']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = "Error uploading $name: " . getUploadError($_FILES['certificates']['error'][$key]);
        }
    }

    if (!empty($uploaded_files)) {
        $success_msg = "Successfully uploaded: " . implode(', ', $uploaded_files);
    }
    if (!empty($errors)) {
        $error_msg = implode('<br>', $errors);
    }
}

function getUploadError($error_code)
{
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE in form',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];
    return $errors[$error_code] ?? 'Unknown upload error';
}

if (isset($_GET['success'])) {
    $success_msg = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error_msg = $_GET['error'];
}

$certificates = selectData('certificates', $mysqli, "*", "WHERE trainer_id = '$trainer_id'", "ORDER BY created_at DESC");

require "./layouts/header.php";
?>

<div style="overflow-y: auto; height:80vh;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold py-3 mb-4">
                    <span class="text-muted fw-light">Trainer /</span>
                    <?= htmlspecialchars($trainer['name']) ?> /
                    <span class="text-muted fw-light">Certificates</span>
                </h4>
                <div>
                    <a href="<?= htmlspecialchars($admin_base_url . "trainer_list.php") ?>" class="btn btn-secondary">Back to List</a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php if ($success_msg) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <?= htmlspecialchars($success_msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                    <?php if ($error_msg) { ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <?= $error_msg ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Add New Certificates</h5>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="certificates" class="form-label">Select Certificate Files</label>
                                    <input class="form-control" type="file" name="certificates[]" id="certificates" multiple required>
                                    <div class="form-text">
                                        Allowed: JPG, JPEG, PNG, PDF, DOCX, XLS, XLSX (Max 10MB each)
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload Certificates</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Existing Certificates</h5>
                            <span class="badge bg-primary">
                                <?= ($certificates && $certificates->num_rows > 0) ? $certificates->num_rows : '0' ?> Certificates
                            </span>
                        </div>
                        <div class="card-body">
                            <?php if ($certificates && $certificates->num_rows > 0) { ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>File</th>
                                                <th>Uploaded</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($cert = $certificates->fetch_assoc()) {
                                                $ext = strtolower(pathinfo($cert['img_path'], PATHINFO_EXTENSION));
                                                $file_type = '';
                                                $btn_class = 'btn-outline-secondary';

                                                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                                                    $file_type = 'Image';
                                                    $btn_class = 'btn-outline-primary';
                                                } elseif ($ext === 'pdf') {
                                                    $file_type = 'PDF';
                                                    $btn_class = 'btn-outline-danger';
                                                } elseif ($ext === 'docx') {
                                                    $file_type = 'Word';
                                                    $btn_class = 'btn-outline-primary';
                                                } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                                    $file_type = 'Excel';
                                                    $btn_class = 'btn-outline-success';
                                                }
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($cert['name']) ?></td>
                                                    <td><?= $file_type ?></td>
                                                    <td>
                                                        <a href="../<?= htmlspecialchars($cert['img_path']) ?>"
                                                            class="btn btn-sm <?= $btn_class ?>"
                                                            <?= in_array($ext, ['jpg', 'jpeg', 'png', 'pdf']) ? 'target="_blank"' : 'download' ?>>
                                                            <?= in_array($ext, ['jpg', 'jpeg', 'png', 'pdf']) ? 'View' : 'Download' ?>
                                                        </a>
                                                    </td>
                                                    <td><?= date("M d, Y h:i A", strtotime($cert['created_at'])) ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-danger delete-cert-btn" data-id="<?= htmlspecialchars($cert['id']) ?>">
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <div class="alert alert-info">No certificates found for this trainer.</div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require "./layouts/footer.php";
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.delete-cert-btn').click(function() {
            const certId = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "This certificate will be permanently deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "trainer_certificates.php?trainer_id=<?= $trainer_id ?>&delete_cert_id=" + certId;
                }
            });
        });

        $('#certificates').on('change', function() {
            const files = $(this)[0].files;
            let fileNames = [];
            for (let i = 0; i < files.length; i++) {
                fileNames.push(files[i].name);
            }

            if (fileNames.length > 0) {
                $(this).next('.form-text').html('Selected files: ' + fileNames.join(', '));
            } else {
                $(this).next('.form-text').html('Allowed: JPG, JPEG, PNG, PDF, DOCX, XLS, XLSX (Max 10MB each)');
            }
        });
    });
</script>