<!-- registration_form.html -->
<form method="POST" action="registration.php">
    <label>Username: <input type="text" name="user_name" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <label>First Name: <input type="text" name="firstname" required></label><br>
    <label>Surname: <input type="text" name="surname" required></label><br>

    <select name="user_type" id="user_type" class="custom-select" required>
    <option value="">Choose Account</option>
        <option value="User" <?php echo isset($meta['user_type']) && $meta['user_type'] == 2 ? 'selected' : '' ?> >Staff</option>
        <option value="Administrator" <?php echo isset($meta['user_type']) && $meta['user_type'] == 1 ? 'selected' : '' ?> >Administrator</option> 
    </select>


    <button type="submit">Register</button>
</form>

<!-- login_form.html -->
<form method="POST" action="login.php">
    <label>Username: <input type="text" name="user_name" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <button type="submit">Login</button>
</form>


<?php
// Example: Fetch user data by ID (you must implement this in your PHP backend)
$user_id = $_GET['id'] ?? null;
$user = null;
if ($user_id) {
    // Fetch user data from database, e.g.:
    // $user = getUserById($user_id);
    // For demonstration, assume $user is an associative array:
    $user = [
        'user_name' => 'johndoe',
        'firstname' => 'John',
        'surname' => 'Doe',
        'user_type' => 2
    ];
}
?>

<form method="POST" action="edit_user.php">
    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
    
    <label>Username: 
        <input type="text" name="user_name" value="<?php echo htmlspecialchars($user['user_name'] ?? ''); ?>" required>
    </label><br>
    
    <label>Password: 
        <input type="password" name="password" placeholder="Leave blank to keep current password">
    </label><br>
    
    <label>First Name: 
        <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname'] ?? ''); ?>" required>
    </label><br>
    
    <label>Surname: 
        <input type="text" name="surname" value="<?php echo htmlspecialchars($user['surname'] ?? ''); ?>" required>
    </label><br>

    <select name="user_type" id="user_type" class="custom-select" required>
        <option value="">Choose Account</option>
        <option value="2" <?php echo (isset($user['user_type']) && $user['user_type'] == 2) ? 'selected' : ''; ?>>Staff</option>
        <option value="1" <?php echo (isset($user['user_type']) && $user['user_type'] == 1) ? 'selected' : ''; ?>>Administrator</option> 
    </select>

    <button type="submit">Update</button>
</form>