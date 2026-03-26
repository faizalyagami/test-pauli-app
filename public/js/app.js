/**
 * Pauli Test System - Main Application Script
 */

// Global variables
window.PauliTest = window.PauliTest || {};

// DOM Ready
$(document).ready(function () {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Initialize popovers
    $('[data-toggle="popover"]').popover();

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function () {
        $(".alert").fadeOut("slow");
    }, 5000);

    // Handle form submission with loading state
    $("form").on("submit", function () {
        const $btn = $(this).find('button[type="submit"]');
        if ($btn.length) {
            $btn.prop("disabled", true);
            $btn.html('<i class="fas fa-spinner fa-spin"></i> Loading...');
        }
    });

    // Confirm delete
    $(".confirm-delete").on("click", function (e) {
        e.preventDefault();
        const $form = $(this).closest("form");

        Swal.fire({
            title: "Apakah Anda yakin?",
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                $form.submit();
            }
        });
    });

    // Handle enter key in forms
    $("form").on("keypress", function (e) {
        if (e.which === 13 && !$(e.target).is("textarea")) {
            e.preventDefault();
            return false;
        }
    });
});

// Helper Functions
window.PauliTest.showLoading = function () {
    const overlay = $(
        '<div class="spinner-overlay"><div class="spinner"></div></div>',
    );
    $("body").append(overlay);
    return overlay;
};

window.PauliTest.hideLoading = function (overlay) {
    if (overlay) {
        overlay.fadeOut("fast", function () {
            $(this).remove();
        });
    } else {
        $(".spinner-overlay").fadeOut("fast", function () {
            $(this).remove();
        });
    }
};

window.PauliTest.formatNumber = function (number) {
    return new Intl.NumberFormat("id-ID").format(number);
};

window.PauliTest.formatDate = function (dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

window.PauliTest.formatDuration = function (seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes.toString().padStart(2, "0")}:${remainingSeconds.toString().padStart(2, "0")}`;
};

// Toast notification
window.PauliTest.toast = function (message, type = "success") {
    const toastHtml = `
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    `;

    $("body").append(toastHtml);
    const toast = new bootstrap.Toast($(".toast").last());
    toast.show();

    setTimeout(() => {
        $(".toast-container").last().remove();
    }, 3000);
};

// Export functionality
window.PauliTest.exportData = function (url, data = {}) {
    const form = $("<form>", {
        method: "POST",
        action: url,
        target: "_blank",
    });

    form.append(
        $("<input>", {
            type: "hidden",
            name: "_token",
            value: $('meta[name="csrf-token"]').attr("content"),
        }),
    );

    $.each(data, function (key, value) {
        form.append(
            $("<input>", {
                type: "hidden",
                name: key,
                value: value,
            }),
        );
    });

    $("body").append(form);
    form.submit();
    form.remove();
};
