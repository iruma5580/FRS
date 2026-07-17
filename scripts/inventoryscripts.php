<!-- <script>
  //Inventory.php script
  // Utility to open modal
  function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.style.display = 'block';
      modal.setAttribute('aria-hidden', 'false');
    }
  }
  // Utility to close modal
  function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.style.display = 'none';
      modal.setAttribute('aria-hidden', 'true');
    }
  }

  // Close modals on clicking close button or outside modal content
  document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', e => {
      const modalId = btn.getAttribute('data-close');
      closeModal(modalId);
    });
  });
  window.addEventListener('click', e => {
    document.querySelectorAll('.modal').forEach(modal => {
      if (e.target === modal) {
        closeModal(modal.id);
      }
    });
  });

  // Open Add modal on button click
  document.getElementById('btnAddAsset').addEventListener('click', () => {
    // Clear Add form inputs
    const formAdd = document.getElementById('formAdd');
    formAdd.reset();
    openModal('modalAdd');
  });

  // Open Edit modal and populate fields
  document.querySelectorAll('.btnEdit').forEach(button => {
    button.addEventListener('click', e => {
      const tr = e.target.closest('tr');
      if (!tr) return;

      const formEdit = document.getElementById('formEdit');
      formEdit.id.value = tr.getAttribute('data-id');
      formEdit.asset_code.value = tr.getAttribute('data-asset_code');
      formEdit.asset_name.value = tr.getAttribute('data-asset_name');
      formEdit.category.value = tr.getAttribute('data-category');
      formEdit.location_name.value = tr.getAttribute('data-location_name');
      formEdit.status.value = tr.getAttribute('data-status');
      formEdit.assigned_user.value = tr.getAttribute('data-assigned_user');

      openModal('modalEdit');
    });
  });
</script> -->

<script>
// Inventory.php script

// Utility to open modal
function openModal(modalId) {
  $('#' + modalId).modal('show');
}

// Utility to close modal
function closeModal(modalId) {
  $('#' + modalId).modal('hide');
}

$(document).ready(function() {
  // Open Add modal on button click
  $('#btnAddAsset').on('click', function(e) {
    e.preventDefault();
    const formAdd = document.getElementById('formAdd');
    if (formAdd) formAdd.reset();
    openModal('modalAdd');
  });

  // Open Edit modal and populate fields using event delegation (for DataTables support)
  $(document).on('click', '.btnEdit', function(e) {
    e.preventDefault();
    const tr = $(this).closest('tr');
    
    const formEdit = document.getElementById('formEdit');
    if (formEdit) {
      formEdit.id.value = tr.data('id');
      formEdit.asset_code.value = tr.data('asset_code');
      formEdit.asset_name.value = tr.data('asset_name');
      formEdit.category.value = tr.data('category');
      formEdit.location_name.value = tr.data('location_name');
      formEdit.status.value = tr.data('status');
      formEdit.assigned_user.value = tr.data('assigned_user');
    }
    openModal('modalEdit');
  });

  // Image preview modal using event delegation
  $(document).on('click', 'td img.thumb', function() {
    const modalImg = document.getElementById('imgPreview');
    if (modalImg) {
      modalImg.src = this.src;
      modalImg.alt = this.alt || 'Image preview';
      $('#imgPreviewModal').modal('show');
    }
  });

  // Close preview modal on close button click
  const imgCloseBtn = document.getElementById('imgPreviewClose');
  if (imgCloseBtn) {
    imgCloseBtn.addEventListener('click', () => {
      closeModal('imgPreviewModal');
    });
  }
});
</script>
