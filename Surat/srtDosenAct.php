<?php
// Include database connection
include 'koneksi.php';

// Assume $id_srt is obtained from a GET or POST request
$id_srt = $_GET['id_srt'] ?? ''; 

// Fetch data from tb_srt_dosen
$query = "SELECT * FROM tb_srt_dosen WHERE id_srt = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$id_srt]);


if (!$row) {
    echo "Data not found.";
    exit;
}
?>

<div class="txt-disposisi">
    <h3>Aksi</h3>
</div>

<div class="btn-kirim">
    <button class="memo-button" data-id="<?php echo $row['id_srt']; ?>">
        <i class="fas fa-sticky-note" style="color:#ffde21; background-color: none;"></i> Tambah Memo
    </button>

    <?php if ($row['verifikasi'] == 1): ?>
        <button style="cursor: not-allowed;" class="verify-button" data-id="<?php echo $row['id_srt']; ?>" disabled>
            <i class="fa-solid fa-check"></i> Verifikasi
        </button>
    <?php else: ?>
        <button class="verify-button" data-id="<?php echo $row['id_srt']; ?>">
            <i class="fa-solid fa-check"></i> Verifikasi
        </button>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.memo-button').forEach(function(button) {
        button.addEventListener('click', function() {
            var suratId = this.getAttribute('data-id');

            // Cek apakah memo sudah ada untuk surat ini
            $.ajax({
                url: 'sql/cek_memo.php',
                method: 'POST',
                data: {
                    id_srt: suratId
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    var memoExists = data.exists;
                    var existingMemo = data.memo;

                    if (memoExists) {
                        Swal.fire({
                            title: 'Edit Memo',
                            input: 'textarea',
                            inputLabel: 'Isi Memo',
                            inputValue: existingMemo,
                            inputAttributes: {
                                readonly: true
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Edit Memo',
                            cancelButtonText: 'Batal',
                            preConfirm: () => {
                                return {
                                    memo: existingMemo,
                                    id_srt: suratId
                                };
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Edit Memo',
                                    input: 'textarea',
                                    inputLabel: 'Isi Memo',
                                    inputValue: existingMemo,
                                    showCancelButton: true,
                                    confirmButtonText: 'Simpan',
                                    cancelButtonText: 'Batal',
                                    preConfirm: (memo) => {
                                        if (!memo) {
                                            Swal.showValidationMessage('Memo tidak boleh kosong');
                                        }
                                        return {
                                            memo: memo,
                                            id_srt: suratId
                                        };
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Save the memo
                                        $.ajax({
                                            url: 'sql/tambah_memo.php',
                                            method: 'POST',
                                            data: {
                                                id_srt: result.value.id_srt,
                                                memo: result.value.memo
                                            },
                                            success: function(response) {
                                                if (response === 'success') {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Berhasil',
                                                        text: 'Memo berhasil disimpan'
                                                    }).then(() => {
                                                        location.reload();
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Gagal',
                                                        text: 'Terjadi kesalahan, coba lagi nanti'
                                                    });
                                                }
                                            },
                                            error: function() {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Gagal',
                                                    text: 'Terjadi kesalahan, coba lagi nanti'
                                                });
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Tambah Memo',
                            input: 'textarea',
                            inputLabel: 'Isi Memo',
                            showCancelButton: true,
                            confirmButtonText: 'Tambah',
                            cancelButtonText: 'Batal',
                            preConfirm: (memo) => {
                                if (!memo) {
                                    Swal.showValidationMessage('Memo tidak boleh kosong');
                                }
                                return {
                                    memo: memo,
                                    id_srt: suratId
                                };
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Save the memo
                                $.ajax({
                                    url: 'sql/tambah_memo.php',
                                    method: 'POST',
                                    data: {
                                        id_srt: result.value.id_srt,
                                        memo: result.value.memo
                                    },
                                    success: function(response) {
                                        if (response === 'success') {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil',
                                                text: 'Memo berhasil disimpan'
                                            }).then(() => {
                                                location.reload();
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Gagal',
                                                text: 'Terjadi kesalahan, coba lagi nanti'
                                            });
                                        }
                                    },
                                    error: function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: 'Terjadi kesalahan, coba lagi nanti'
                                        });
                                    }
                                });
                            }
                        });
                    }
                }
            });
        });
    });
});

    // Handle verify button click
    document.querySelectorAll('.verify-button').forEach(function(button) {
        button.addEventListener('click', function() {
            var suratId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Verifikasi Surat',
                text: "Apakah Anda yakin ingin memverifikasi surat ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Verifikasi',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: 'sql/verifikasi_surat.php',
                            method: 'POST',
                            data: {
                                id_srt: suratId
                            },
                            success: function(response) {
                                if (response === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: 'Surat berhasil diverifikasi'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Terjadi kesalahan, coba lagi nanti'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan, coba lagi nanti'
                                });
                            }
                        });
                    });
                }
            });
        });
    });
</script>