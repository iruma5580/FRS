<!-- <script>
  $(document).ready(function () {
    $('.edit-btn').on('click', function () {
      const btn = $(this);
      $('#edit_id').val(btn.data('id'));
      $('#edit_asset_code').val(btn.data('asset_code'));
      $('#edit_asset_name').val(btn.data('asset_name'));
      $('#edit_category').val(btn.data('category'));
      $('#edit_location_name').val(btn.data('location_name'));
      $('#edit_status').val(btn.data('status'));
      $('#edit_assigned_user').val(btn.data('assigned_user') || '');
      $('#edit_notes').val(btn.data('notes') || '');
      $('#edit_assigned_person_to_fix').val(btn.data('assigned_person_to_fix') || '');
      $('#edit_due_date').val(btn.data('due_date') || '');
      $('#edit_work_order_number').val(btn.data('work_order_number') || '');
      $('#edit_priority_status').val(btn.data('priority_status') || 'Medium');
      $('#edit_date_finish').val(btn.data('date_finish') || '');
      $('#edit_work_done').val(btn.data('work_done') || '');
      $('#edit_work_done_status').val(btn.data('work_done_status') || 'Not Started');
      $('#editError').addClass('d-none').text('');
      $('#editForm .form-control').removeClass('is-invalid');
    });

    $('.view-btn').on('click', function () {
      const btn = $(this);
      $('#view_id').val(btn.data('id'));
      $('#view_asset_code').val(btn.data('asset_code'));
      $('#view_asset_name').val(btn.data('asset_name'));
      $('#view_category').val(btn.data('category'));
      $('#view_location_name').val(btn.data('location_name'));
      $('#view_status').val(btn.data('status'));
      $('#view_assigned_user').val(btn.data('assigned_user') || '');
      $('#view_notes').val(btn.data('notes') || '');
      $('#view_assigned_person_to_fix').val(btn.data('assigned_person_to_fix') || '');
      $('#view_due_date').val(btn.data('due_date') || '');
      $('#view_work_order_number').val(btn.data('work_order_number') || '');
      $('#view_priority_status').val(btn.data('priority_status') || 'Medium');
      $('#view_date_finish').val(btn.data('date_finish') || '');
      $('#view_work_done').val(btn.data('work_done') || '');
      $('#view_work_done_status').val(btn.data('work_done_status') || 'Not Started');
      $('#viewError').addClass('d-none').text('');
      $('#viewForm .form-control').removeClass('is-invalid');
    });

    $('#editForm').on('submit', function (e) {
      let valid = true;
      $('#editForm input[required]').each(function () {
        if (!$(this).val().trim()) {
          valid = false;
          $(this).addClass('is-invalid');
        } else {
          $(this).removeClass('is-invalid');
        }
      });
      if (!valid) {
        e.preventDefault();
        $('#editError').removeClass('d-none').text('Please fill all required fields.');
      }
    });

    $('#editForm input').on('input', function () {
      $(this).removeClass('is-invalid');
      $('#editError').addClass('d-none').text('');
    });

    $('#addForm').on('submit', function (e) {
      let valid = true;
      $('#addForm input[required]').each(function () {
        if (!$(this).val().trim()) {
          valid = false;
          $(this).addClass('is-invalid');
        } else {
          $(this).removeClass('is-invalid');
        }
      });
      if (!valid) {
        e.preventDefault();
      }
    });

    $('#addForm input').on('input', function () {
      $(this).removeClass('is-invalid');
    });

    <?php if ($modalData): ?>
    $('#editModal').modal('show');
    $('#edit_id').val(<?= json_encode($modalData['id']) ?>);
    $('#edit_asset_code').val(<?= json_encode($modalData['asset_code']) ?>);
    $('#edit_asset_name').val(<?= json_encode($modalData['asset_name']) ?>);
    $('#edit_category').val(<?= json_encode($modalData['category']) ?>);
    $('#edit_location_name').val(<?= json_encode($modalData['location_name']) ?>);
    $('#edit_status').val(<?= json_encode($modalData['status']) ?>);
    $('#edit_assigned_user').val(<?= json_encode($modalData['assigned_user']) ?>);
    $('#edit_notes').val(<?= json_encode($modalData['notes']) ?>);
    $('#edit_assigned_person_to_fix').val(<?= json_encode($modalData['assigned_person_to_fix']) ?>);
    $('#edit_due_date').val(<?= json_encode($modalData['due_date']) ?>);
    $('#edit_work_order_number').val(<?= json_encode($modalData['work_order_number']) ?>);
    $('#edit_priority_status').val(<?= json_encode($modalData['priority_status']) ?>);
    $('#edit_date_finish').val(<?= json_encode($modalData['date_finish']) ?>);
    $('#edit_work_done').val(<?= json_encode($modalData['work_done']) ?>);
    $('#edit_work_done_status').val(<?= json_encode($modalData['work_done_status']) ?>);
    $('#editError').removeClass('d-none').text(<?= json_encode($editError) ?>);
    <?php endif; ?>

    <?php if ($modalData): ?>
    $('#viewModal').modal('show');
    $('#view_id').val(<?= json_encode($modalData['id']) ?>);
    $('#view_asset_code').val(<?= json_encode($modalData['asset_code']) ?>);
    $('#view_asset_name').val(<?= json_encode($modalData['asset_name']) ?>);
    $('#view_category').val(<?= json_encode($modalData['category']) ?>);
    $('#view_location_name').val(<?= json_encode($modalData['location_name']) ?>);
    $('#view_status').val(<?= json_encode($modalData['status']) ?>);
    $('#view_assigned_user').val(<?= json_encode($modalData['assigned_user']) ?>);
    $('#view_notes').val(<?= json_encode($modalData['notes']) ?>);
    $('#view_assigned_person_to_fix').val(<?= json_encode($modalData['assigned_person_to_fix']) ?>);
    $('#view_due_date').val(<?= json_encode($modalData['due_date']) ?>);
    $('#view_work_order_number').val(<?= json_encode($modalData['work_order_number']) ?>);
    $('#view_priority_status').val(<?= json_encode($modalData['priority_status']) ?>);
    $('#view_date_finish').val(<?= json_encode($modalData['date_finish']) ?>);
    $('#view_work_done').val(<?= json_encode($modalData['work_done']) ?>);
    $('#view_work_done_status').val(<?= json_encode($modalData['work_done_status']) ?>);
    $('#editError').removeClass('d-none').text(<?= json_encode($editError) ?>);
    <?php endif; ?>

  });

  // When the modal is shown, set the min attribute of the date input to today
  $('#editModal').on('shown.bs.modal', function () {
    var input = document.getElementById('edit_date_finish');
    if (input) {
      var today = new Date();
      var yyyy = today.getFullYear();
      var mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
      var dd = String(today.getDate()).padStart(2, '0');
      var minDate = yyyy + '-' + mm + '-' + dd;
      input.min = minDate;

      // Optional: If you want to clear or set a default date on modal open, do it here
      // input.value = minDate; // Uncomment to set default date to today
    }
  });
</script> -->

<script>
  $(document).ready(function () {
  // Use delegated event binding for dynamic content and mobile compatibility
  $(document).on('click', '.edit-btn', function () {
    const btn = $(this);
    $('#edit_id').val(btn.data('id'));
    $('#edit_asset_code').val(btn.data('assetCode'));
    $('#edit_asset_name').val(btn.data('assetName'));
    $('#edit_category').val(btn.data('category'));
    $('#edit_location_name').val(btn.data('locationName'));
    $('#edit_status').val(btn.data('status'));
    $('#edit_assigned_user').val(btn.data('assignedUser') || '');
    $('#edit_notes').val(btn.data('notes') || '');
    $('#edit_assigned_person_to_fix').val(btn.data('assignedPersonToFix') || '');
    $('#edit_due_date').val(btn.data('dueDate') || '');
    $('#edit_work_order_number').val(btn.data('workOrderNumber') || '');
    $('#edit_priority_status').val(btn.data('priorityStatus') || 'Medium');
    $('#edit_date_finish').val(btn.data('dateFinish') || '');
    $('#edit_work_done').val(btn.data('workDone') || '');
    $('#edit_work_done_status').val(btn.data('workDoneStatus') || 'Not Started');
    $('#editError').addClass('d-none').text('');
    $('#editForm .form-control').removeClass('is-invalid');

    // Open modal programmatically after data is set
    $('#editModal').modal('show');
  });

  $(document).on('click', '.view-btn', function () {
    const btn = $(this);
    $('#view_id').val(btn.data('id'));
    $('#view_asset_code').val(btn.data('assetCode'));
    $('#view_asset_name').val(btn.data('assetName'));
    $('#view_category').val(btn.data('category'));
    $('#view_location_name').val(btn.data('locationName'));
    $('#view_status').val(btn.data('status'));
    $('#view_assigned_user').val(btn.data('assignedUser') || '');
    $('#view_notes').val(btn.data('notes') || '');
    $('#view_assigned_person_to_fix').val(btn.data('assignedPersonToFix') || '');
    $('#view_due_date').val(btn.data('dueDate') || '');
    $('#view_work_order_number').val(btn.data('workOrderNumber') || '');
    $('#view_priority_status').val(btn.data('priorityStatus') || 'Medium');
    $('#view_date_finish').val(btn.data('dateFinish') || '');
    $('#view_work_done').val(btn.data('workDone') || '');
    $('#view_work_done_status').val(btn.data('workDoneStatus') || 'Not Started');
    $('#viewError').addClass('d-none').text('');
    $('#viewForm .form-control').removeClass('is-invalid');

    // Open modal programmatically after data is set
    $('#viewModal').modal('show');
  });

  // Edit form validation
  $('#editForm').on('submit', function (e) {
    let valid = true;
    $('#editForm input[required]').each(function () {
      if (!$(this).val().trim()) {
        valid = false;
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
      }
    });
    if (!valid) {
      e.preventDefault();
      $('#editError').removeClass('d-none').text('Please fill all required fields.');
    }
  });

  $('#editForm input').on('input', function () {
    $(this).removeClass('is-invalid');
    $('#editError').addClass('d-none').text('');
  });

  // Add form validation (if applicable)
  $('#addForm').on('submit', function (e) {
    let valid = true;
    $('#addForm input[required]').each(function () {
      if (!$(this).val().trim()) {
        valid = false;
        $(this).addClass('is-invalid');
      } else {
        $(this).removeClass('is-invalid');
      }
    });
    if (!valid) {
      e.preventDefault();
    }
  });

  $('#addForm input').on('input', function () {
    $(this).removeClass('is-invalid');
  });

  // PHP modal data population (if any)
  <?php if ($modalData): ?>
    $('#editModal').modal('show');
    $('#edit_id').val(<?= json_encode($modalData['id']) ?>);
    $('#edit_asset_code').val(<?= json_encode($modalData['asset_code']) ?>);
    $('#edit_asset_name').val(<?= json_encode($modalData['asset_name']) ?>);
    $('#edit_category').val(<?= json_encode($modalData['category']) ?>);
    $('#edit_location_name').val(<?= json_encode($modalData['location_name']) ?>);
    $('#edit_status').val(<?= json_encode($modalData['status']) ?>);
    $('#edit_assigned_user').val(<?= json_encode($modalData['assigned_user']) ?>);
    $('#edit_notes').val(<?= json_encode($modalData['notes']) ?>);
    $('#edit_assigned_person_to_fix').val(<?= json_encode($modalData['assigned_person_to_fix']) ?>);
    $('#edit_due_date').val(<?= json_encode($modalData['due_date']) ?>);
    $('#edit_work_order_number').val(<?= json_encode($modalData['work_order_number']) ?>);
    $('#edit_priority_status').val(<?= json_encode($modalData['priority_status']) ?>);
    $('#edit_date_finish').val(<?= json_encode($modalData['date_finish']) ?>);
    $('#edit_work_done').val(<?= json_encode($modalData['work_done']) ?>);
    $('#edit_work_done_status').val(<?= json_encode($modalData['work_done_status']) ?>);
    $('#editError').removeClass('d-none').text(<?= json_encode($editError) ?>);
  <?php endif; ?>

  <?php if ($modalData): ?>
    $('#viewModal').modal('show');
    $('#view_id').val(<?= json_encode($modalData['id']) ?>);
    $('#view_asset_code').val(<?= json_encode($modalData['asset_code']) ?>);
    $('#view_asset_name').val(<?= json_encode($modalData['asset_name']) ?>);
    $('#view_category').val(<?= json_encode($modalData['category']) ?>);
    $('#view_location_name').val(<?= json_encode($modalData['location_name']) ?>);
    $('#view_status').val(<?= json_encode($modalData['status']) ?>);
    $('#view_assigned_user').val(<?= json_encode($modalData['assigned_user']) ?>);
    $('#view_notes').val(<?= json_encode($modalData['notes']) ?>);
    $('#view_assigned_person_to_fix').val(<?= json_encode($modalData['assigned_person_to_fix']) ?>);
    $('#view_due_date').val(<?= json_encode($modalData['due_date']) ?>);
    $('#view_work_order_number').val(<?= json_encode($modalData['work_order_number']) ?>);
    $('#view_priority_status').val(<?= json_encode($modalData['priority_status']) ?>);
    $('#view_date_finish').val(<?= json_encode($modalData['date_finish']) ?>);
    $('#view_work_done').val(<?= json_encode($modalData['work_done']) ?>);
    $('#view_work_done_status').val(<?= json_encode($modalData['work_done_status']) ?>);
    $('#viewError').removeClass('d-none').text(<?= json_encode($editError) ?>);
  <?php endif; ?>

});

// Set min date for date input on modal show
$('#editModal').on('shown.bs.modal', function () {
  var input = document.getElementById('edit_date_finish');
  if (input) {
    var today = new Date();
    var yyyy = today.getFullYear();
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var dd = String(today.getDate()).padStart(2, '0');
    var minDate = yyyy + '-' + mm + '-' + dd;
    input.min = minDate;
  }
});

$('#editModal').on('shown.bs.modal', function () {
  var input = document.getElementById('edit_date_finish');
  if (input) {
    var today = new Date();
    var yyyy = today.getFullYear();
    var mm = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
    var dd = String(today.getDate()).padStart(2, '0');
    var minDate = yyyy + '-' + mm + '-' + dd;
    input.min = minDate;
  }
});


$('#editForm').on('submit', function (e) {
  var dateFinish = $('#edit_date_finish').val();
  var today = new Date();
  today.setHours(0,0,0,0); // Set to midnight for comparison

  if (dateFinish) {
    var selectedDate = new Date(dateFinish);
    if (selectedDate < today) {
      e.preventDefault();
      alert('Date Finish cannot be in the past.');
      $('#edit_date_finish').addClass('is-invalid');
      return false;
    }
  }
});

</script>