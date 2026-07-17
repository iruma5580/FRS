<script>
  $(function() {
    // Populate Edit modal with data from button attributes
    $('.btnEdit').on('click', function() {
      var $btn = $(this);
      var $modal = $('#modalEdit');
      $modal.find('input[name="id"]').val($btn.data('id'));
      $modal.find('input[name="username"]').val($btn.data('username'));
      $modal.find('input[name="email"]').val($btn.data('email'));
      $modal.find('input[name="fullname"]').val($btn.data('fullname'));
      $modal.find('select[name="user_type"]').val($btn.data('user_type'));
      $modal.find('select[name="status"]').val($btn.data('status'));
      // Clear password fields
      $modal.find('input[name="password"]').val('');
      $modal.find('input[name="password_confirm"]').val('');
      // Clear file input
      $modal.find('input[name="picture"]').val('');
    });

    // Update file input label on file select
    $('input[type="file"]').on('change', function() {
      var fileName = $(this).val().split('\\').pop();
      $(this).next('label').text(fileName || 'Choose file');
    });
  });
</script>