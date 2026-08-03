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
<style type="text/css">
	.password-field {
		position: relative;
		display: inline-block;
		width: 100%;
		max-width: 100%;
	}
	.password-field.password-field-inline {
		width: auto;
		vertical-align: middle;
		margin-right: 4px;
		margin-bottom: 4px;
	}
	.password-field input[type="password"],
	.password-field input[type="text"] {
		padding-right: 2.25rem;
	}
	.password-field.password-field-inline input {
		width: 9.5rem;
		display: inline-block;
		height: 28px;
		padding: 2px 2rem 2px 6px;
		box-sizing: border-box;
	}
	.password-toggle {
		position: absolute;
		right: 4px;
		top: 50%;
		transform: translateY(-50%);
		border: 0;
		background: transparent;
		padding: 2px;
		line-height: 1;
		cursor: pointer;
		color: #555;
	}
	.password-toggle:hover,
	.password-toggle:focus {
		color: #222;
		outline: none;
	}
	.password-toggle svg {
		width: 16px;
		height: 16px;
		display: block;
		pointer-events: none;
	}
</style>
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
					<div class="password-field">
						<input class="form-control" id="password" name="password" type="password" required minlength="8">
						<button type="button" class="password-toggle" aria-label="Show password" title="Show password"></button>
					</div>
				</div>
				<div class="form-group">
					<label for="confirm">Confirm password</label>
					<div class="password-field">
						<input class="form-control" id="confirm" name="confirm" type="password" required minlength="8">
						<button type="button" class="password-toggle" aria-label="Show password" title="Show password"></button>
					</div>
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
								<span class="password-field password-field-inline">
									<input type="password" name="password" placeholder="New password" required minlength="8">
									<button type="button" class="password-toggle" aria-label="Show password" title="Show password"></button>
								</span>
								<span class="password-field password-field-inline">
									<input type="password" name="confirm" placeholder="Confirm" required minlength="8">
									<button type="button" class="password-toggle" aria-label="Show password" title="Show password"></button>
								</span>
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
<script type="text/javascript">
(function () {
	var eyeOpen = '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>';
	var eyeOff = '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M2.1 3.5 3.5 2.1l18.4 18.4-1.4 1.4-3.1-3.1C15.7 19.6 13.9 20 12 20 5 20 2 13 2 13s1.3-3.1 4.2-5.3L2.1 3.5zM12 7a5 5 0 0 1 5 5c0 .7-.1 1.3-.4 1.9l-1.6-1.6A3 3 0 0 0 12 9c-.3 0-.5 0-.8.1L9.5 7.4C10.3 7.1 11.1 7 12 7zm-7.4 6S7.1 17 12 17c1.1 0 2.1-.2 3-.6l-2.1-2.1A5 5 0 0 1 7.7 9.1L4.6 13z"/></svg>';

	document.querySelectorAll('.password-field').forEach(function (wrap) {
		var input = wrap.querySelector('input');
		var button = wrap.querySelector('.password-toggle');
		if (!input || !button) {
			return;
		}
		button.innerHTML = eyeOpen;
		button.addEventListener('click', function () {
			var show = input.type === 'password';
			input.type = show ? 'text' : 'password';
			button.innerHTML = show ? eyeOff : eyeOpen;
			button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
			button.setAttribute('title', show ? 'Hide password' : 'Show password');
		});
	});
})();
</script>
</body>
</html>
