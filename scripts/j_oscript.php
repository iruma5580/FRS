<script>
  //Inventory.php script
  // Utility to open modal
  // function openModal(modalId) {
  //   const modal = document.getElementById(modalId);
  //   if (modal) {
  //     modal.style.display = 'block';
  //     modal.setAttribute('aria-hidden', 'false');
  //   }
  // }
  // // Utility to close modal
  // function closeModal(modalId) {
  //   const modal = document.getElementById(modalId);
  //   if (modal) {
  //     modal.style.display = 'none';
  //     modal.setAttribute('aria-hidden', 'true');
  //   }
  // }

  // // Close modals on clicking close button or outside modal content
  // document.querySelectorAll('.modal-close').forEach(btn => {
  //   btn.addEventListener('click', e => {
  //     const modalId = btn.getAttribute('data-close');
  //     closeModal(modalId);
  //   });
  // });
  // window.addEventListener('click', e => {
  //   document.querySelectorAll('.modal').forEach(modal => {
  //     if (e.target === modal) {
  //       closeModal(modal.id);
  //     }
  //   });
  // });

  // // Open Add modal on button click
  // document.getElementById('btnAddAsset').addEventListener('click', () => {
  //   // Clear Add form inputs
  //   const formAdd = document.getElementById('formAdd');
  //   formAdd.reset();
  //   openModal('modalAdd');
  // });

  // // Open Edit modal and populate fields
  // document.querySelectorAll('.btnEdit').forEach(button => {
  //   button.addEventListener('click', e => {
  //     const tr = e.target.closest('tr');
  //     if (!tr) return;

  //     const formEdit = document.getElementById('formEdit');
  //     formEdit.id.value = tr.getAttribute('data-id');
  //     formEdit.asset_code.value = tr.getAttribute('data-asset_code');
  //     formEdit.asset_name.value = tr.getAttribute('data-asset_name');
  //     formEdit.category.value = tr.getAttribute('data-category');
  //     formEdit.location_name.value = tr.getAttribute('data-location_name');
  //     formEdit.status.value = tr.getAttribute('data-status');
  //       formEdit.assigned_user.value = tr.getAttribute('data-assigned_user');
  //       formEdit.notes.value = tr.getAttribute('data-notes');
  //       formEdit.assigned_person_to_fix.value = tr.getAttribute('data-assigned_person_to_fix');
  //       formEdit.due_date.value = tr.getAttribute('data-due_date');
  //       formEdit.work_order_number.value = tr.getAttribute('data-work_order_number');
  //       formEdit.priority_status.value = tr.getAttribute('data-priority_status');

  //     // Set min attribute of due_date input to today to prevent selecting past dates
  //     const dueDateInput = formEdit.querySelector('#edit_due_date');
  //     if (dueDateInput) {
  //       const today = new Date();
  //       const yyyy = today.getFullYear();
  //       const mm = String(today.getMonth() + 1).padStart(2, '0');
  //       const dd = String(today.getDate()).padStart(2, '0');
  //       const minDate = `${yyyy}-${mm}-${dd}`;
  //       dueDateInput.min = minDate;

  //       // Optional: if due_date value is before today, reset it to today
  //       if (dueDateInput.value && dueDateInput.value < minDate) {
  //         dueDateInput.value = minDate;
  //       }
  //     }

  //     openModal('modalEdit');
  //   });
  // });
</script>

<script>
// Ensure backdrop and body class cleanup on modal close
$('#modalEdit').on('hidden.bs.modal', function () {
  $('.modal-backdrop').remove();
  $('body').removeClass('modal-open');
});

// Event delegation on container with id 'inventoryTable2' for clicks on .btnEdit buttons
document.getElementById('inventoryTable2').addEventListener('click', function(event) {
  const target = event.target;

  if (target.classList.contains('btnEdit') || target.closest('.btnEdit')) {
    const button = target.classList.contains('btnEdit') ? target : target.closest('.btnEdit');
    const modal = document.getElementById('modalEdit');
    if (!modal) return;

    // Populate modal fields from data attributes on the button
    modal.querySelector('input[name="id"]').value = button.getAttribute('data-id') || '';
    modal.querySelector('#edit_asset_code').value = button.getAttribute('data-asset_code') || '';
    modal.querySelector('#edit_asset_name').value = button.getAttribute('data-asset_name') || '';
    modal.querySelector('#edit_category').value = button.getAttribute('data-category') || '';
    modal.querySelector('#edit_location_name').value = button.getAttribute('data-location_name') || '';
    modal.querySelector('#edit_status').value = button.getAttribute('data-status') || '';
    modal.querySelector('#edit_assigned_user').value = button.getAttribute('data-assigned_user') || '';
    modal.querySelector('#edit_notes').value = button.getAttribute('data-notes') || '';
    modal.querySelector('#edit_assigned_person_to_fix').value = button.getAttribute('data-assigned_person_to_fix') || '';
    modal.querySelector('#edit_due_date').value = button.getAttribute('data-due_date') || '';
    modal.querySelector('#edit_work_order_number').value = button.getAttribute('data-work_order_number') || '';
    modal.querySelector('#edit_priority_status').value = button.getAttribute('data-priority_status') || '';

    // Set min attribute for due_date input to today if needed
    const dueDateInput = modal.querySelector('#edit_due_date');
    if (dueDateInput) {
      const today = new Date().toISOString().split('T')[0];
      dueDateInput.setAttribute('min', today);
      if (dueDateInput.value < today) {
        dueDateInput.value = today;
      }
    }

    // Show the modal using Bootstrap's jQuery method
    $('#modalEdit').modal('show');
  }
});

</script>