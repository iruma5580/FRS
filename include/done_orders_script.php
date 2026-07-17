<script>
  $(document).ready(function () {
    // Populate modal fields on edit button click
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
      $('#editError').addClass('d-none').text('');
      $('#editForm .form-control').removeClass('is-invalid');
    });

    // Client-side validation for edit form
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

    // Clear validation on input
    $('#editForm input').on('input', function () {
      $(this).removeClass('is-invalid');
      $('#editError').addClass('d-none').text('');
    });

    // Client-side validation for add form
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

    // If PHP returned an edit error, open modal with submitted data
    <?php if ($modalData): ?>
    $('#editModal').modal('show');
    $('#edit_id').val(<?= json_encode($modalData['id']) ?>);
    // $('#edit_asset_code').val(<?= json_encode($modalData['asset_code']) ?>);
    // $('#edit_asset_name').val(<?= json_encode($modalData['asset_name']) ?>);
    // $('#edit_category').val(<?= json_encode($modalData['category']) ?>);
    // $('#edit_location_name').val(<?= json_encode($modalData['location_name']) ?>);
    $('#edit_status').val(<?= json_encode($modalData['status']) ?>);
    // $('#edit_assigned_user').val(<?= json_encode($modalData['assigned_user']) ?>);
    $('#edit_notes').val(<?= json_encode($modalData['notes']) ?>);
    $('#edit_assigned_person_to_fix').val(<?= json_encode($modalData['assigned_person_to_fix']) ?>);
    $('#edit_due_date').val(<?= json_encode($modalData['due_date']) ?>);
    $('#edit_work_order_number').val(<?= json_encode($modalData['work_order_number']) ?>);
    $('#edit_priority_status').val(<?= json_encode($modalData['priority_status']) ?>);
    $('#edit_date_finish').val(<?= json_encode($modalData['date_finish']) ?>);
    $('#editError').removeClass('d-none').text(<?= json_encode($editError) ?>);
    <?php endif; ?>
  });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const clearBtn = document.getElementById('clearFieldsBtn');
    const fieldsToClear = [
        'edit_notes',
        'edit_assigned_person_to_fix',
        'edit_due_date',
        'edit_work_order_number',
        'edit_priority_status',
        'edit_date_finish'
    ];

    clearBtn.addEventListener('click', function() {
        fieldsToClear.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const tag = el.tagName.toLowerCase();
        if (tag === 'select') {
            el.value = 'None';
            if (!Array.from(el.options).some(o => o.value === el.value)) {
            el.selectedIndex = 0;
            }
        } else {
            el.value = '';
        }
        });

        // Set Status to "In Service"
        const status = document.getElementById('edit_status');
        if (status) {
        status.value = 'In Service';
        if (!Array.from(status.options).some(o => o.value === status.value || o.text === status.value)) {
            for (let i = 0; i < status.options.length; i++) {
            if (status.options[i].text.trim() === 'In Service') {
                status.selectedIndex = i;
                break;
            }
            }
        }
        }
    });
    });
</script>