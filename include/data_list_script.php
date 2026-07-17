<script>
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

    

  // $(function () {
  //   $('#edit_due_date').datetimepicker({
  //     format: 'L', // localized format for user-friendly display
  //     minDate: moment().startOf('day'),
  //     icons: {
  //       time: 'far fa-clock',
  //       date: 'far fa-calendar',
  //       up: 'fas fa-arrow-up',
  //       down: 'fas fa-arrow-down',
  //       previous: 'fas fa-chevron-left',
  //       next: 'fas fa-chevron-right',
  //       today: 'far fa-calendar-check',
  //       clear: 'far fa-trash-alt',
  //       close: 'far fa-times-circle'
  //     }
  //   });
  // });




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
  });

</script>

<script>
  // When the modal is shown, set the min attribute of the date input to today
  $('#editModal').on('shown.bs.modal', function () {
    var input = document.getElementById('edit_due_date');
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
</script>