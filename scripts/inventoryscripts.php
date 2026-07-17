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
    formEdit.elements['id'].value = tr.getAttribute('data-id');
    formEdit.elements['asset_code'].value = tr.getAttribute('data-asset_code');
    formEdit.elements['asset_name'].value = tr.getAttribute('data-asset_name');
    formEdit.elements['category'].value = tr.getAttribute('data-category');
    formEdit.elements['location_name'].value = tr.getAttribute('data-location_name');
    formEdit.elements['status'].value = tr.getAttribute('data-status');
    formEdit.elements['assigned_user'].value = tr.getAttribute('data-assigned_user');

    // Show current image preview in Edit modal
    const currentImagePreview = document.getElementById('currentImagePreview');
    const imgCell = tr.querySelector('td img');
    if (imgCell) {
      currentImagePreview.innerHTML = `<img src="${imgCell.src}" alt="Current Image" style="max-width:150px; max-height:150px; border:1px solid #ccc; border-radius:4px;" />`;
    } else {
      currentImagePreview.innerHTML = '';
    }

    openModal('modalEdit');
  });
});

// Attach click event to all image thumbnails you want previewable
document.querySelectorAll('td img').forEach(img => {
  img.style.cursor = 'pointer';
  img.addEventListener('click', () => {
    const modal = document.getElementById('imgPreviewModal');
    const modalImg = document.getElementById('imgPreview');
    modalImg.src = img.src;
    modalImg.alt = img.alt || 'Image preview';
    modal.style.display = 'flex';
  });
});

// Close modal on close button click
document.getElementById('imgPreviewClose').addEventListener('click', () => {
  document.getElementById('imgPreviewModal').style.display = 'none';
});

// Close modal on clicking outside the image
document.getElementById('imgPreviewModal').addEventListener('click', e => {
  if (e.target === e.currentTarget) {
    e.currentTarget.style.display = 'none';
  }
});

</script>
