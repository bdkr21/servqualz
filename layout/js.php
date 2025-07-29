<script src="assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>

<script src="assets/vendors/apexcharts/apexcharts.js"></script>
<script src="assets/js/pages/dashboard.js"></script>

<script src="assets/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
  <?php if (isset($_GET['status']) && $_GET['status'] == 'success_delete') { ?>
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: 'Data pengguna berhasil dihapus!'
    });
  <?php } ?>
  <?php if (isset($_GET['status']) && $_GET['status'] == 'success_add') { ?>
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: 'Data pengguna berhasil ditambahkan!'
    });
  <?php } ?>
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Apply Flatpickr to Tanggal Lahir (Birthdate)
        flatpickr("#tanggal_lahir", {
            dateFormat: "Y-m-d",  // Set the date format as YYYY-MM-DD
            maxDate: "today",     // Limit the date selection to today or earlier
        });

        // Apply Flatpickr to Tanggal Transaksi (Transaction Date) with Today's Date as Default
        flatpickr("#tanggal_transaksi", {
            dateFormat: "Y-m-d",  // Set the date format as YYYY-MM-DD
            defaultDate: "today", // Set today's date as default
        });
    });
</script>