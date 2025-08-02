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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jenisLayananSelect = document.getElementById('jenis_layanan');
    
    if (jenisLayananSelect) {
        jenisLayananSelect.addEventListener('change', function() {
            const selectedJenisLayanan = this.value;
            
            if (selectedJenisLayanan) {
                fetchPernyataanData(selectedJenisLayanan);
            } else {
                // Clear the table if no jenis layanan is selected
                const tbody = document.querySelector('#tablePernyataan tbody');
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">Pilih jenis layanan untuk melihat pernyataan</td></tr>';
            }
        });
    }
    
    function fetchPernyataanData(jenisLayanan) {
        fetch(`edit_kuesioner.php?ajax=get_pernyataan&jenis_layanan=${encodeURIComponent(jenisLayanan)}`)
            .then(response => response.json())
            .then(data => {
                updatePernyataanTable(data);
            })
            .catch(error => {
                console.error('Error fetching pernyataan data:', error);
            });
    }
    
    function updatePernyataanTable(data) {
        const tbody = document.querySelector('#tablePernyataan tbody');
        
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">Tidak ada pernyataan untuk jenis layanan ini</td></tr>';
            return;
        }
        
        let html = '';
        data.forEach(item => {
            html += `
                <tr>
                    <td>${item.id_data_pernyataan}</td>
                    <td>${item.dimensi_layanan}</td>
                    <td>${item.pernyataan}</td>
                    <td>${item.rekomendasi_perbaikan}</td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }
});
</script>