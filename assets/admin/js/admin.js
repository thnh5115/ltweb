$(document).ready(function () {
    // Admin Sidebar Toggle
    $('#admin-sidebar-toggle').click(function () {
        $('.sidebar').toggleClass('open');
    });
});

function confirmDelete(id, type) {
    if (confirm('Bạn có chắc chắn muốn xóa ' + type + ' này không?')) {
        // Call API to delete
        return true;
    }
    return false;
}
