<!-- <script>
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
    btn.addEventListener('click', () => {
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

  // Open Edit modal and populate fields from button data attributes
  document.querySelectorAll('.btnEdit').forEach(button => {
    button.addEventListener('click', () => {
      const modal = document.getElementById('modalEdit');
      if (!modal) return;

      // Populate input fields with data attributes from the clicked button
      modal.querySelector('input[name="id"]').value = button.getAttribute('data-id') || '';
      modal.querySelector('input[name="username"]').value = button.getAttribute('data-username') || '';
      modal.querySelector('input[name="email"]').value = button.getAttribute('data-email') || '';
      modal.querySelector('input[name="fullname"]').value = button.getAttribute('data-fullname') || '';
      modal.querySelector('select[name="user_type"]').value = button.getAttribute('data-user_type') || '';
      modal.querySelector('select[name="status"]').value = button.getAttribute('data-status') || '';

      // Clear password fields
      modal.querySelector('input[name="password"]').value = '';
      modal.querySelector('input[name="password_confirm"]').value = '';

      // Clear file input
      modal.querySelector('input[name="picture"]').value = '';

      openModal('modalEdit');
    });
  });

  // Update file input label on file select
  document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', () => {
      const fileName = input.value.split('\\').pop();
      const label = input.nextElementSibling;
      if (label && label.tagName.toLowerCase() === 'label') {
        label.textContent = fileName || 'Choose file';
      }
    });
  });
</script> -->
<!-- 
<script>
    // Get the image URL from data attribute
  const pictureUrl = button.getAttribute('data-picture') || '';

  // Get the image element inside the modal
  const imgElement = modal.querySelector('#currentPicture');

  if (pictureUrl) {
    imgElement.src = pictureUrl;
    imgElement.style.display = 'block'; // Show the image
  } else {
    imgElement.src = '';
    imgElement.style.display = 'none'; // Hide the image if no picture
  }

    // Use event delegation on the table element or document
  document.getElementById('account2').addEventListener('click', function(event) {
    const target = event.target;

    // Check if the clicked element or its parent has the class 'btnEdit'
    if (target.classList.contains('btnEdit') || target.closest('.btnEdit')) {
      // Get the actual button element (in case a child element inside the button was clicked)
      const button = target.classList.contains('btnEdit') ? target : target.closest('.btnEdit');
      const modal = document.getElementById('modalEdit');
      if (!modal) return;

      // Populate modal fields from button data attributes
      modal.querySelector('input[name="id"]').value = button.getAttribute('data-id') || '';
      modal.querySelector('input[name="username"]').value = button.getAttribute('data-username') || '';
      modal.querySelector('input[name="email"]').value = button.getAttribute('data-email') || '';
      modal.querySelector('input[name="fullname"]').value = button.getAttribute('data-fullname') || '';
      modal.querySelector('select[name="user_type"]').value = button.getAttribute('data-user_type') || '';
      modal.querySelector('select[name="status"]').value = button.getAttribute('data-status') || '';

      // Clear password fields
      modal.querySelector('input[name="password"]').value = '';
      modal.querySelector('input[name="password_confirm"]').value = '';

      // Clear file input
      modal.querySelector('input[name="picture"]').value = '';

      openModal('modalEdit');
    }
  });

</script> -->

<script>
// // Open modal
// $('#modalEdit').modal('show');

// Ensure backdrop and body class cleanup on close
$('#modalEdit').on('hidden.bs.modal', function () {
  $('.modal-backdrop').remove();
  $('body').removeClass('modal-open');
});

document.getElementById('account2').addEventListener('click', function(event) {
  const target = event.target;

  if (target.classList.contains('btnEdit') || target.closest('.btnEdit')) {
    const button = target.classList.contains('btnEdit') ? target : target.closest('.btnEdit');
    const modal = document.getElementById('modalEdit');
    if (!modal) return;

    // Populate modal fields
    modal.querySelector('input[name="id"]').value = button.getAttribute('data-id') || '';
    modal.querySelector('input[name="username"]').value = button.getAttribute('data-username') || '';
    modal.querySelector('input[name="email"]').value = button.getAttribute('data-email') || '';
    modal.querySelector('input[name="fullname"]').value = button.getAttribute('data-fullname') || '';
    modal.querySelector('select[name="user_type"]').value = button.getAttribute('data-user_type') || '';
    modal.querySelector('select[name="status"]').value = button.getAttribute('data-status') || '';

    // Clear password fields
    modal.querySelector('input[name="password"]').value = '';
    modal.querySelector('input[name="password_confirm"]').value = '';

    // Clear file input
    modal.querySelector('input[name="picture"]').value = '';

    // Set current picture
    const pictureUrl = button.getAttribute('data-picture') || '';
    const imgElement = modal.querySelector('#currentPicture');

    if (pictureUrl && pictureUrl.trim() !== '') {
      imgElement.src = pictureUrl;
      imgElement.style.display = 'block';
    } else {
      imgElement.src = '';
      imgElement.style.display = 'none';
    }

    // Show the modal (Bootstrap jQuery)
    $('#modalEdit').modal('show');
  }
});
// Delete button handler - show confirm modal with correct ID
$(document).on('click', '.btnDelete', function() {
  var accountId = $(this).data('id');
  $('#deleteAccountId').val(accountId);
  $('#confirmDeleteModal').modal('show');
});

</script>

<!-- Confirm Delete Modal (Bootstrap 4) -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" action="accounts.php">
        <input type="hidden" name="action" value="delete" />
        <input type="hidden" name="id" id="deleteAccountId" value="" />
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteLabel">Confirm Delete</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you sure you want to <strong>permanently delete</strong> this user? This action cannot be undone.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>