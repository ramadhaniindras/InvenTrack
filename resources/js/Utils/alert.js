import Swal from 'sweetalert2';

export const notify = (title, icon = 'success') => {
    Swal.fire({
        title: title,
        icon: icon,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
};

export const confirmDelete = (callback) => {
    Swal.fire({
        title: 'Yakin mau hapus?',
        text: "Data yang dihapus nggak bisa balik lagi lho!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
};