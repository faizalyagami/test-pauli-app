/**
 * DataTable Configurations
 */

$(document).ready(function() {
    // Initialize all data tables with Indonesian language
    if ($.fn.DataTable) {
        $.extend(true, $.fn.DataTable.defaults, {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
                processing: "Sedang memproses...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
                loadingRecords: "Memuat...",
                zeroRecords: "Tidak ditemukan data",
                emptyTable: "Tidak ada data",
                paginate: {
                    first: "Pertama",
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                    last: "Terakhir"
                }
            },
            pageLength: 25,
            responsive: true,
            autoWidth: false
        });
    }
    
    // Custom data table with export buttons
    window.initDataTableWithExport = function(tableId, options = {}) {
        const defaultOptions = {
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="fas fa-copy"></i> Copy',
                    className: 'btn btn-sm btn-secondary'
                },
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-sm btn-success'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-sm btn-danger'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-sm btn-info'
                }
            ],
            responsive: true,
            pageLength: 25
        };
        
        const config = { ...defaultOptions, ...options };
        return $(`#${tableId}`).DataTable(config);
    };
    
    // Simple data table without buttons
    window.initDataTable = function(tableId, options = {}) {
        const defaultOptions = {
            pageLength: 25,
            ordering: true,
            searching: true,
            responsive: true
        };
        
        const config = { ...defaultOptions, ...options };
        return $(`#${tableId}`).DataTable(config);
    };
    
    // Reinitialize data table after AJAX load
    window.reinitDataTable = function(tableId, data, options = {}) {
        const table = $(`#${tableId}`).DataTable();
        if (table) {
            table.clear();
            table.rows.add(data);
            table.draw();
        } else {
            window.initDataTable(tableId, options);
        }
    };
});