<?php
/*
*******************************************************************
AdminUserMaintenance.php
Manage Steve Weeks Music admin login accounts.
Any logged-in admin may add, update, activate/deactivate, or delete users.
*******************************************************************
*/

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

include_once('root.inc.php');
include_once($ROOT . '/includes/websiteSettings.php');
include_once(SETTINGS_DIR . '/SteveWeeksMusicSettings.php');
include_once(ADMIN_DIR . '/includes/AdminSettings.php');
include_once(DATALAYER_DIR . '/Connection.php');
include_once(DATALAYER_DIR . '/AdminUser.php');
include_once(DATALAYER_DIR . '/AdminUserRepository.php');

$sActiveMenuItem = MISC_ACTIVE;
$sPageName = 'Admin Users';
$message = '';
$messageType = MESSAGE_TYPE_INFO;
$repo = new \Datalayer\AdminUserRepository();
$currentUserId = (int) ($_SESSION['adminUserId'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = $_POST['action'] ?? '';

	try {
		if ($action === 'add') {
			$username = trim((string) ($_POST['username'] ?? ''));
			$password = (string) ($_POST['password'] ?? '');
			$confirm = (string) ($_POST['confirm'] ?? '');

			if ($username === '' || !preg_match('/^[A-Za-z0-9._-]{3,64}$/', $username)) {
				$message = 'Username must be 3–64 characters (letters, numbers, . _ -).';
				$messageType = MESSAGE_TYPE_ERROR;
			} elseif (strlen($password) < 8) {
				$message = 'Password must be at least 8 characters.';
				$messageType = MESSAGE_TYPE_ERROR;
			} elseif ($password !== $confirm) {
				$message = 'Passwords do not match.';
				$messageType = MESSAGE_TYPE_ERROR;
			} elseif ($repo->findByUsername($username)) {
				$message = 'That username already exists.';
				$messageType = MESSAGE_TYPE_ERROR;
			} else {
				$user = new \Datalayer\AdminUser();
				$user->username = $username;
				$user->passwordHash = password_hash($password, PASSWORD_DEFAULT);
				$user->isActive = true;
				if ($repo->insert($user)) {
					$message = "Added admin user {$username}.";
				} else {
					$message = 'Failed to add user.';
					$messageType = MESSAGE_TYPE_ERROR;
				}
			}
		} elseif ($action === 'password') {
			$id = (int) ($_POST['id'] ?? 0);
			$password = (string) ($_POST['password'] ?? '');
			$confirm = (string) ($_POST['confirm'] ?? '');
			$user = $id > 0 ? $repo->findById($id) : null;

			if (!$user) {
				$message = 'User not found.';
				$messageType = MESSAGE_TYPE_ERROR;
			} elseif (strlen($password) < 8) {
				$message = 'Password must be at least 8 characters.';
				$messageType = MESSAGE_TYPE_ERROR;
			} elseif ($password !== $confirm) {
				$message = 'Passwords do not match.';
				$messageType = MESSAGE_TYPE_ERROR;
			} elseif ($repo->updatePassword($id, password_hash($password, PASSWORD_DEFAULT))) {
				$message = "Password updated for {$user->username}.";
			} else {
				$message = 'Failed to update password.';
				$messageType = MESSAGE_TYPE_ERROR;
			}
		} elseif ($action === 'toggle') {
			$id = (int) ($_POST['id'] ?? 0);
			$user = $id > 0 ? $repo->findById($id) : null;
			if (!$user) {
				$message = 'User not found.';
				$messageType = MESSAGE_TYPE_ERROR;
			} elseif ($id === $currentUserId) {
				$message = 'You cannot deactivate your own account.';
				$messageType = MESSAGE_TYPE_WARNING;
			} else {
				$newActive = !$user->isActive;
				if ($repo->setActive($id, $newActive)) {
					$message = ($newActive ? 'Activated' : 'Deactivated') . " {$user->username}.";
				} else {
					$message = 'Failed to update active status.';
					$messageType = MESSAGE_TYPE_ERROR;
				}
			}
		} elseif ($action === 'delete') {
			$id = (int) ($_POST['id'] ?? 0);
			$user = $id > 0 ? $repo->findById($id) : null;
			if (!$user) {
				$message = 'User not found.';
				$messageType = MESSAGE_TYPE_ERROR;
			} elseif ($id === $currentUserId) {
				$message = 'You cannot delete your own account.';
				$messageType = MESSAGE_TYPE_WARNING;
			} elseif ($repo->countAll() <= 1) {
				$message = 'Cannot delete the last admin account.';
				$messageType = MESSAGE_TYPE_WARNING;
			} elseif ($repo->delete($id)) {
				$message = "Deleted {$user->username}.";
			} else {
				$message = 'Failed to delete user.';
				$messageType = MESSAGE_TYPE_ERROR;
			}
		}
	} catch (\Throwable $e) {
		error_log('AdminUserMaintenance: ' . $e->getMessage());
		$message = 'A database error occurred.';
		$messageType = MESSAGE_TYPE_ERROR;
	}
}

$users = $repo->findAll();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php include(ADMIN_INCLUDE_DIR . '/HTMLHead.php'); ?>
<body>
<div class="container-fluid">
	<?php include(ADMIN_INCLUDE_DIR . '/AdminHeader-Responsive.php'); ?>

	<div class="row">
		<div class="col-xs-12">
			<h2>Admin Users</h2>
			<?php if ($message !== '') {
				$class = $messageType === MESSAGE_TYPE_ERROR ? 'text-danger' : ($messageType === MESSAGE_TYPE_WARNING ? 'text-warning' : 'text-success');
				echo '<p class="' . $class . '">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
			} ?>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-md-5">
			<h3>Add admin user</h3>
			<form method="post" action="AdminUserMaintenance.php" autocomplete="off">
				<input type="hidden" name="action" value="add">
				<div class="form-group">
					<label for="username">Username</label>
					<input class="form-control" id="username" name="username" type="text" required maxlength="64" pattern="[A-Za-z0-9._-]{3,64}">
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input class="form-control" id="password" name="password" type="password" required minlength="8">
				</div>
				<div class="form-group">
					<label for="confirm">Confirm password</label>
					<input class="form-control" id="confirm" name="confirm" type="password" required minlength="8">
				</div>
				<button type="submit" class="btn btn-primary">Add user</button>
			</form>
		</div>

		<div class="col-xs-12 col-md-7">
			<h3>Existing users</h3>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Username</th>
						<th>Active</th>
						<th>Last login</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($users as $user) { ?>
					<tr>
						<td><?php echo htmlspecialchars((string) $user->username, ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo $user->isActive ? 'Yes' : 'No'; ?></td>
						<td><?php echo htmlspecialchars((string) ($user->lastLogin ?? '—'), ENT_QUOTES, 'UTF-8'); ?></td>
						<td>
							<form method="post" action="AdminUserMaintenance.php" style="display:inline-block; margin-bottom:6px;" autocomplete="off">
								<input type="hidden" name="action" value="password">
								<input type="hidden" name="id" value="<?php echo (int) $user->id; ?>">
								<input type="password" name="password" placeholder="New password" required minlength="8">
								<input type="password" name="confirm" placeholder="Confirm" required minlength="8">
								<button type="submit" class="btn btn-default btn-xs">Set password</button>
							</form>
							<form method="post" action="AdminUserMaintenance.php" style="display:inline-block;">
								<input type="hidden" name="action" value="toggle">
								<input type="hidden" name="id" value="<?php echo (int) $user->id; ?>">
								<button type="submit" class="btn btn-default btn-xs"><?php echo $user->isActive ? 'Deactivate' : 'Activate'; ?></button>
							</form>
							<?php if ((int) $user->id !== $currentUserId) { ?>
							<form method="post" action="AdminUserMaintenance.php" style="display:inline-block;" onsubmit="return confirm('Delete this admin user?');">
								<input type="hidden" name="action" value="delete">
								<input type="hidden" name="id" value="<?php echo (int) $user->id; ?>">
								<button type="submit" class="btn btn-danger btn-xs">Delete</button>
							</form>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
</body>
</html>
