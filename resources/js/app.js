import "./bootstrap";

import Alpine from "alpinejs";
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

window.Alpine = Alpine;

Alpine.start();

// ====== Tom Select for searchable dropdowns (Instansi & Prodi) ======
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector('form[method="GET"]');

    const initSearchSelect = (selector, placeholderText) => {
        const el = document.querySelector(selector);
        if (!el) return;

        const ts = new TomSelect(el, {
            create: false,
            allowEmptyOption: true,
            maxOptions: 2000,
            placeholder: placeholderText,

            onChange: function () {
                form?.submit();
            },
        });

        // 1) Saat mulai fokus / mengetik, kosongkan placeholder text di input
        const originalPlaceholder = placeholderText;

        const clearPlaceholder = () => {
            // hilangkan placeholder dari input search
            if (ts.control_input)
                ts.control_input.setAttribute("placeholder", "");
            // hilangkan placeholder dari control (biar tidak kelihatan di kotak)
            if (ts.control) ts.control.setAttribute("data-placeholder", "");
        };

        const restorePlaceholder = () => {
            // kalau belum ada item yang dipilih (artinya "Semua" / kosong), balikin placeholder
            if (ts.items.length === 0) {
                if (ts.control_input)
                    ts.control_input.setAttribute(
                        "placeholder",
                        originalPlaceholder,
                    );
                if (ts.control)
                    ts.control.setAttribute(
                        "data-placeholder",
                        originalPlaceholder,
                    );
            }
        };

        // saat klik/focus dropdown
        ts.on("focus", () => clearPlaceholder());
        ts.on("type", () => clearPlaceholder()); // saat user mengetik
        ts.on("blur", () => restorePlaceholder()); // saat keluar dari input

        // initial: pastikan placeholder ada
        restorePlaceholder();
    };

    initSearchSelect("#instansi_filter", "-- Semua Instansi --");
    initSearchSelect("#prodi_filter", "-- Semua Prodi --");
});
