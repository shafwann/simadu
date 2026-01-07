import './bootstrap';

import Alpine from 'alpinejs';
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.css';

window.Alpine = Alpine;

Alpine.start();

// ====== Tom Select for searchable dropdowns (Instansi & Prodi) ======
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form[method="GET"]');

  const instansi = document.querySelector('#instansi_filter');
  if (instansi) {
    new TomSelect(instansi, {
      create: false,
      allowEmptyOption: true,
      maxOptions: 2000,
      onChange: function () {
        form?.submit();
      },
    });
  }

  const prodi = document.querySelector('#prodi_filter');
  if (prodi) {
    new TomSelect(prodi, {
      create: false,
      allowEmptyOption: true,
      maxOptions: 2000,
      onChange: function () {
        form?.submit();
      },
    });
  }
});
