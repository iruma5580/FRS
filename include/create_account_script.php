  <script>
    // Initialize DataTables if you are using it
    $(document).ready(function() {
        $('#example1').DataTable();
    });

    // Function to open the edit modal and populate form fields from the clicked button's data attributes
    function openEditModal(button) {
      const modal = document.getElementById('editModal');
      if (!modal) {
        console.error('Edit modal element with id="editModal" not found.');
        return;
      }

      const form = modal.querySelector('form');
      if (!form) {
        console.error('Form inside edit modal not found.');
        return;
      }

      // Populate form fields
      form.querySelector('input[name="id"]').value = button.getAttribute('data-id') || '';
      // Username is readonly, so we just display it
      form.querySelector('input[name="username"]').value = button.getAttribute('data-username') || '';
      form.querySelector('input[name="email"]').value = button.getAttribute('data-email') || '';
      form.querySelector('input[name="fullname"]').value = button.getAttribute('data-fullname') || '';
      
      // Ensure user_type is correctly selected
      const userTypeSelect = form.querySelector('select[name="user_type"]');
      const dataUserType = button.getAttribute('data-user_type') || 'user';
      userTypeSelect.value = dataUserType.toLowerCase(); // Convert to lowercase to match form options

      // Clear password field for security
      form.querySelector('input[name="password"]').value = '';

      // Show the modal using Bootstrap's JavaScript API
      const bootstrapModal = new bootstrap.Modal(modal);
      bootstrapModal.show();
    }

    // Attach event listeners to all edit buttons after DOM content loaded
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => openEditModal(button));
      });
    });
  </script>