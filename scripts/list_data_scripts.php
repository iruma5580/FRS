<!-- <script>
// Utility to open modal using Bootstrap modal methods
function openModal(modalId) {
  const modal = $('#' + modalId);
  if (modal.length) {
    modal.modal('show');
    modal.attr('aria-hidden', 'false');
  }
}

// Utility to close modal using Bootstrap modal methods
function closeModal(modalId) {
  const modal = $('#' + modalId);
  if (modal.length) {
    modal.modal('hide');
    modal.attr('aria-hidden', 'true');
  }
}

$(document).ready(function() {
  // Close modals on clicking close button
  $('.modal-close').on('click', function() {
    const modalId = $(this).data('close');
    closeModal(modalId);
  });

  // Close modal on clicking outside modal content (Bootstrap handles this by default)
  // But if you want custom handling:
  $('.modal').on('click', function(e) {
    if ($(e.target).hasClass('modal')) {
      closeModal(this.id);
    }
  });

  // Open Add modal and reset form
  $('#btnAddAsset').on('click', function() {
    const formAdd = $('#formAdd')[0];
    formAdd.reset();

    // Set min date for due_date input if exists
    const dueDateInput = $('#formAdd #add_due_date');
    if (dueDateInput.length) {
      const today = new Date().toISOString().split('T')[0];
      dueDateInput.attr('min', today);
      if (dueDateInput.val() < today) {
        dueDateInput.val(today);
      }
    }

    openModal('modalAdd');
  });

  // Initialize DataTable
var table = $('#inventoryTable2').DataTable({
  lengthChange: false,
  autoWidth: false,
  responsive: true,
  buttons: [
    {
      extend: 'collection',
      text: 'Export',
      buttons: [
        'copyHtml5',
        'csvHtml5',
        'excelHtml5',
        'pdfHtml5',
        {
          extend: 'print',
          exportOptions: {
            columns: [0,1,2,3,4,5,6,7,8,9,10]
          },
          customize: function (win) {
            $(win.document.body).find('table thead tr th:last-child').remove();
            $(win.document.body).find('table tbody tr').each(function () {
              $(this).find('td:last-child').remove();
            });
          }
        }
      ]
    }
  ]
}).buttons().container().appendTo('#inventoryTable2_wrapper .col-md-6:eq(0)');


  // Append buttons dropdown to container
  // table.buttons().container().appendTo('#actionsDropdownContainer');

  // // Move search bar to searchContainer div
  // $('#inventoryTable2_filter').appendTo('#searchContainer');

  // Edit button click handler using jQuery and DataTables API
  $('#inventoryTable2 tbody').on('click', '.btnEdit', function() {
    var tr = $(this).closest('tr');

    // If child row, get parent row
    if (tr.hasClass('child')) {
      tr = tr.prev();
    }

    var rowData = table.row(tr).data();

    if (!rowData) {
      alert('No data found for this row.');
      return;
    }

    var id = tr.data('id');
    // Populate edit form fields
    var formEdit = $('#formEdit');
    formEdit.find('input[name="id"]').val(id);
    formEdit.find('#edit_asset_code').val(rowData[0]);
    formEdit.find('#edit_asset_name').val(rowData[1]);
    formEdit.find('#edit_category').val(rowData[2]);
    formEdit.find('#edit_location_name').val(rowData[3]);
    formEdit.find('#edit_status').val(rowData[4]);
    formEdit.find('#edit_assigned_user').val(rowData[5]);
    formEdit.find('#edit_notes').val(rowData[6]);
    formEdit.find('#edit_assigned_person_to_fix').val(rowData[7]);
    formEdit.find('#edit_due_date').val(rowData[8]);
    formEdit.find('#edit_work_order_number').val(rowData[9]);
    formEdit.find('#edit_priority_status').val(rowData[10]);
    // Set min attribute for due_date input to today
    const dueDateInput = formEdit.find('#edit_due_date');
    if (dueDateInput.length) {
      const today = new Date().toISOString().split('T')[0];
      dueDateInput.attr('min', today);
      if (dueDateInput.val() < today) {
        dueDateInput.val(today);
      }
    }
    openModal('modalEdit');
  });
});

</script>
 -->

 <script>
// Clean up modal backdrop and body class on modal close
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