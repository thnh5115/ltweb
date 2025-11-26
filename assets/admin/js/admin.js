$(document).ready(function () {
    // Admin specific hooks can be added here when needed.
});

function confirmDelete(id, type) {
    return confirm(`Bạn có chắc chắn muốn xóa ${type} này không?`);
}
