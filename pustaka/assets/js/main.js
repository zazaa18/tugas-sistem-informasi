/**
 * PUSTAKA - MAIN JAVASCRIPT
 * Interaktivitas dan penyempurnaan UI sisi klien
 */

document.addEventListener('DOMContentLoaded', function () {
    
    // -------------------------------------------------------------
    // 1. AUTO-DISMISS ALERT NOTIFICATIONS (Setelah 5 detik)
    // -------------------------------------------------------------
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000); // 5000ms = 5 detik
    });

    // -------------------------------------------------------------
    // 2. VALIDASI TANGGAL KEMBALI (Minimal H+1 dari hari ini)
    // -------------------------------------------------------------
    const dateInputs = document.querySelectorAll('input[type="date"][name="tanggal_kembali_rencana"]');
    dateInputs.forEach(function (input) {
        const today = new Date();
        today.setDate(today.getDate() + 1); // Besok
        
        // Format ke YYYY-MM-DD
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const minDate = `${yyyy}-${mm}-${dd}`;
        
        input.setAttribute('min', minDate);
    });

    // -------------------------------------------------------------
    // 3. TOOLTIPS INITIALIZATION (Opsional jika menggunakan Bootstrap Tooltip)
    // -------------------------------------------------------------
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    console.log("Pustaka JS loaded successfully.");
});