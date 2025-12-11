<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png" />
  <link rel="icon" type="image/png" href="./assets/img/favicon.png" />
  <title>Soft UI Dashboard - Users Management</title>
  <!--     Fonts and icons     -->
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700"
    rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script
    src="https://kit.fontawesome.com/42d5adcbca.js"
    crossorigin="anonymous"></script>
  <!-- Nucleo Icons -->
  <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Popper -->
  <script src="https://unpkg.com/@popperjs/core@2"></script>
  <!-- Main Styling -->
  <link
    href="./assets/css/soft-ui-dashboard-tailwind.css?v=1.0.5"
    rel="stylesheet" />
</head>

<body
  class="m-0 font-sans text-base antialiased font-normal leading-default bg-gray-50 text-slate-500">

  <?php
  require_once('C:\xampp\htdocs\projet_web\config\config.php');
  include('C:\xampp\htdocs\projet_web\app\controllers\BackOfficeController.php');

  // Variables pour les messages
  $success_message = null;
  $error_message = null;

  // Gérer les actions
  $controller = new BackOfficeController();

  // Gérer la suppression
  if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($controller->deleteUser($id)) {
      $success_message = "User deleted successfully!";
    } else {
      $error_message = "Error deleting user!";
    }
    // Redirection pour éviter la resoumission
    $redirect_url = str_replace("?action=delete&id=" . $id, "", $_SERVER['REQUEST_URI']);
    if ($success_message || $error_message) {
      $redirect_url .= (strpos($redirect_url, '?') === false ? '?' : '&');
      if ($success_message) {
        $redirect_url .= "success=" . urlencode($success_message);
      } else {
        $redirect_url .= "error=" . urlencode($error_message);
      }
    }
    header("Location: " . $redirect_url);
    exit();
  }

  // Gérer la modification
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = intval($_POST['id']);
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $address = htmlspecialchars($_POST['address']);

    if ($controller->updateUser($id, $name, $email, $address)) {
      $success_message = "User updated successfully!";
    } else {
      $error_message = "Error updating user!";
    }

    // Redirection avec les messages
    $redirect_url = $_SERVER['REQUEST_URI'];
    if ($success_message || $error_message) {
      $redirect_url .= (strpos($redirect_url, '?') === false ? '?' : '&');
      if ($success_message) {
        $redirect_url .= "success=" . urlencode($success_message);
      } else {
        $redirect_url .= "error=" . urlencode($error_message);
      }
    }
    header("Location: " . $redirect_url);
    exit();
  }

  // Récupérer les messages depuis l'URL
  if (isset($_GET['success'])) {
    $success_message = urldecode($_GET['success']);
  }
  if (isset($_GET['error'])) {
    $error_message = urldecode($_GET['error']);
  }

  // Récupérer les utilisateurs
  $users = $controller->showusers();
  ?>

  <!-- sidenav  -->
  <aside
    class="max-w-62.5 ease-nav-brand z-990 fixed inset-y-0 my-4 ml-4 block w-full -translate-x-full flex-wrap items-center justify-between overflow-y-auto rounded-2xl border-0 bg-white p-0 antialiased shadow-none transition-transform duration-200 xl:left-0 xl:translate-x-0 xl:bg-transparent">
    <div class="h-19.5">
      <i
        class="absolute top-0 right-0 hidden p-4 opacity-50 cursor-pointer fas fa-times text-slate-400 xl:hidden"
        sidenav-close></i>
      <a
        class="block px-8 py-6 m-0 text-sm whitespace-nowrap text-slate-700"
        href="javascript:;"
        target="_blank">
        <img
          src="./assets/img/logo-ct.png"
          class="inline h-full max-w-full transition-all duration-200 ease-nav-brand max-h-8"
          alt="main_logo" />
        <span
          class="ml-1 font-semibold transition-all duration-200 ease-nav-brand">Soft UI Dashboard</span>
      </a>
    </div>

    <hr
      class="h-px mt-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent" />

    <div
      class="items-center block w-auto max-h-screen overflow-auto h-sidenav grow basis-full">
      <ul class="flex flex-col pl-0 mb-0">
        <li class="mt-0.5 w-full">
          <a
            class="py-2.7 shadow-soft-xl text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap rounded-lg bg-white px-4 font-semibold text-slate-700 transition-colors"
            href="">
            <div
              class="bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5">
              <i class="ni leading-none ni-single-02 text-lg relative top-3.5 text-white"></i>
            </div>
            <span
              class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Users</span>
          </a>
        </li>
        <br>
        <br>
        <li class="mt-0.5 w-full">
          <a
            class="py-2.7 shadow-soft-xl text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap rounded-lg bg-white px-4 font-semibold text-slate-700 transition-colors"
            href="">
            <div
              class="bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5">
              <i class="ni leading-none ni-single-02 text-lg relative top-3.5 text-white"></i>
            </div>
            <span
              class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Eventes</span>
          </a>
        </li>
        <br>
        <br>
        <li class="mt-0.5 w-full">
          <a
            class="py-2.7 shadow-soft-xl text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap rounded-lg bg-white px-4 font-semibold text-slate-700 transition-colors"
            href="">
            <div
              class="bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5">
              <i class="ni leading-none ni-single-02 text-lg relative top-3.5 text-white"></i>
            </div>
            <span
              class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Our Shop</span>
          </a>
        </li>
        <br>
        <br>
        <li class="mt-0.5 w-full">
          <a
            class="py-2.7 shadow-soft-xl text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap rounded-lg bg-white px-4 font-semibold text-slate-700 transition-colors"
            href="">
            <div
              class="bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5">
              <i class="ni leading-none ni-single-02 text-lg relative top-3.5 text-white"></i>
            </div>
            <span
              class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Donation</span>
          </a>
        </li>
        <br>
        <br>
      </ul>
    </div>
  </aside>

  <!-- end sidenav -->

  <main
    class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200">
    <!-- Navbar -->
    <nav
      class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all shadow-none duration-250 ease-soft-in rounded-2xl lg:flex-nowrap lg:justify-start"
      navbar-main
      navbar-scroll="true">
      <div
        class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">
        <nav>
          <!-- breadcrumb -->
          <ol
            class="flex flex-wrap pt-1 mr-12 bg-transparent rounded-lg sm:mr-16">
            <li class="text-sm leading-normal">
              <a class="opacity-50 text-slate-700" href="javascript:;">Pages</a>
            </li>
            <li
              class="text-sm pl-2 capitalize leading-normal text-slate-700 before:float-left before:pr-2 before:text-gray-600 before:content-['/']"
              aria-current="page">
              Users
            </li>

          </ol>
          <h6 class="mb-0 font-bold capitalize">Users Management</h6>
        </nav>

        <div
          class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
          <div class="flex items-center md:ml-auto md:pr-4">
            <div
              class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease-soft">
              <span
                class="text-sm ease-soft leading-5.6 absolute z-50 -ml-px flex h-full items-center whitespace-nowrap rounded-lg rounded-tr-none rounded-br-none border border-r-0 border-transparent bg-transparent py-2 px-2.5 text-center font-normal text-slate-500 transition-all">
                <i class="fas fa-search"></i>
              </span>
              <input
                type="text"
                class="pl-8.75 text-sm focus:shadow-soft-primary-outline ease-soft w-1/100 leading-5.6 relative -ml-px block min-w-0 flex-auto rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 pr-3 text-gray-700 transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none focus:transition-shadow"
                placeholder="Search users..." />
            </div>
          </div>

          <ul
            class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full">
            <li class="flex items-center">
              <a
                href="/projet_web/app/views/FrontOffice/public/index.php"
                class="block px-0 py-2 text-sm font-semibold transition-all ease-nav-brand text-slate-500">
                <i class="fa fa-user sm:mr-1"></i>
                <span class="hidden sm:inline">frontwebsite</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Messages de notification -->
    <?php if (isset($success_message)): ?>
      <div class="mx-6 mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
        <i class="fas fa-check-circle mr-3 text-lg"></i>
        <span><?php echo htmlspecialchars($success_message); ?></span>
      </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
      <div class="mx-6 mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center">
        <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
        <span><?php echo htmlspecialchars($error_message); ?></span>
      </div>
    <?php endif; ?>

    <!-- end Navbar -->

    <!-- Main Content Area -->
    <div class="w-full px-6 py-6 mx-auto">
      <!-- Users Table -->
      <div class="flex flex-wrap -mx-3">
        <div class="w-full max-w-full px-3 flex-0">
          <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-6 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
              <h6 class="mb-1">Users List</h6>
              <p class="leading-normal text-sm">Manage all registered users in the system</p>
            </div>
            <div class="flex-auto p-6">
              <div class="overflow-x-auto">
                <table class="table-auto w-full text-slate-500">
                  <thead>
                    <tr class="bg-gray-50">
                      <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">ID</th>
                      <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Role</th>

                      <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Name</th>
                      <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Email</th>
                      <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Address</th>
                      <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Created At</th>
                      <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($users)): ?>
                      <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-700">
                            <?php echo htmlspecialchars($user['id'] ?? 'N/A'); ?>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?php echo htmlspecialchars($user['role'] ?? 'N/A'); ?>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                              <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-tl from-purple-700 to-pink-500 flex items-center justify-center text-white font-bold text-sm">
                                  <?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
                                </div>
                              </div>
                              <div class="ml-4">
                                <div class="text-sm font-medium text-slate-900">
                                  <?php echo htmlspecialchars($user['name'] ?? 'Unknown'); ?>
                                </div>
                              </div>
                            </div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900"><?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            <?php
                            if (isset($user['created_at'])) {
                              $date = new DateTime($user['created_at']);
                              echo $date->format('M j, Y \\a\\t g:i A');
                            } else {
                              echo 'N/A';
                            }
                            ?>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                              <!-- Edit Button - Blue -->
                              <a href="edit_user.php?id=<?php echo $user['id']; ?>"
                                class="text-blue-500 hover:text-blue-700 font-semibold">
                                Edit
                              </a>
                              <!-- Delete Button - Red -->
                              <a href="delete_user.php?id=<?php echo $user['id']; ?>"
                                onclick="return confirm('Are you sure you want to delete this user?')"
                                class="text-red-500 hover:text-red-700">
                                Delete
                              </a>

                              <a href="ban_user.php?id=<?php echo $user['id']; ?>"
                                onclick="return confirm('Are you sure you want to ban this user?')"
                                class="text-yellow-600 hover:text-yellow-800 font-semibold">
                                Ban
                              </a>

                              <!-- Nouveau bouton : visible seulement si rôle = user et patente_image != null -->
        <?php if ($user['role'] === 'user' && !empty($user['patente_image'])): ?>
            <a href="approve_association.php?id=<?php echo $user['id']; ?>"
               class="text-green-600 hover:text-green-800 font-semibold">
                Approve Association
            </a>
        <?php endif; ?>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                          <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-users text-4xl text-slate-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-slate-700 mb-2">No users found</h3>
                            <p class="text-slate-500 mb-4">There are no users in the system yet.</p>
                            <button class="px-4 py-2 text-sm font-bold text-white bg-gradient-to-tl from-purple-700 to-pink-500 rounded-lg shadow-soft-xl transition-all hover:scale-105">
                              Add First User
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <!-- Pagination and Stats -->
              <?php if (!empty($users)): ?>
                <div class="flex items-center justify-between mt-6 px-4">
                  <div class="text-sm text-slate-500">
                    Showing <span class="font-medium"><?php echo count($users); ?></span> user(s)
                  </div>
                  <div class="flex space-x-2">
                    <button class="px-3 py-1 text-xs border rounded-lg hover:bg-slate-50 transition-colors">Previous</button>
                    <button class="px-3 py-1 text-xs border rounded-lg bg-purple-500 text-white">1</button>
                    <button class="px-3 py-1 text-xs border rounded-lg hover:bg-slate-50 transition-colors">Next</button>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Modals Section -->
  <!-- Edit User Modal -->
  <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
      <!-- Header -->
      <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-2xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
              <i class="fas fa-user-edit text-xl"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold">Edit User</h3>
              <p class="text-blue-100 text-sm">Update user information</p>
            </div>
          </div>
          <button onclick="closeEditModal()" class="text-white hover:text-blue-200 transition-colors">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>
      </div>

      <!-- Form -->
      <form id="editForm" method="POST" class="p-6 space-y-4">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="id" id="editUserId">

        <!-- Name Field -->
        <div>
          <label for="editName" class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-user text-blue-500 mr-2"></i>
            Full Name
          </label>
          <input type="text" name="name" id="editName" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
        </div>

        <!-- Email Field -->
        <div>
          <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-envelope text-green-500 mr-2"></i>
            Email Address
          </label>
          <input type="email" name="email" id="editEmail" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
        </div>

        <!-- Address Field -->
        <div>
          <label for="editAddress" class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
            Address
          </label>
          <textarea name="address" id="editAddress" rows="3"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"></textarea>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3 pt-4">
          <button type="button" onclick="closeEditModal()"
            class="px-6 py-3 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-all duration-300 flex items-center">
            <i class="fas fa-times mr-2"></i>
            Cancel
          </button>
          <button type="submit"
            class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center">
            <i class="fas fa-save mr-2"></i>
            Update User
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete User Modal -->
  <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
      <!-- Header -->
      <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-t-2xl p-6 text-white">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <div class="bg-white bg-opacity-20 p-3 rounded-full mr-4">
              <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div>
              <h3 class="text-xl font-bold">Delete User</h3>
              <p class="text-red-100 text-sm">This action cannot be undone</p>
            </div>
          </div>
          <button onclick="closeDeleteModal()" class="text-white hover:text-red-200 transition-colors">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6">
        <div class="text-center mb-6">
          <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-trash-alt text-red-600 text-2xl"></i>
          </div>
          <p class="text-gray-700">
            Are you sure you want to delete user
            <span id="deleteUserName" class="font-bold text-red-600"></span>?
          </p>
          <p class="text-sm text-gray-500 mt-2">
            This will permanently remove the user from the system.
          </p>
        </div>

        <!-- Buttons -->
        <div class="flex justify-center space-x-4">
          <button onclick="closeDeleteModal()"
            class="px-6 py-3 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-all duration-300 flex items-center">
            <i class="fas fa-times mr-2"></i>
            Cancel
          </button>
          <button id="confirmDeleteBtn"
            class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center">
            <i class="fas fa-trash-alt mr-2"></i>
            Delete User
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="./assets/js/plugins/chartjs.min.js"></script>
  <script src="./assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="./assets/js/soft-ui-dashboard-tailwind.js?v=1.0.5"></script>
  <script>
    // Modal functions for Edit
    function openEditModal(id, name, email, address) {
      document.getElementById('editUserId').value = id;
      document.getElementById('editName').value = name;
      document.getElementById('editEmail').value = email;
      document.getElementById('editAddress').value = address;
      document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.add('hidden');
    }

    // Modal functions for Delete
    function confirmDelete(id, name) {
      document.getElementById('deleteUserName').textContent = name;

      const confirmBtn = document.getElementById('confirmDeleteBtn');

      // On enlève les anciens events (si user clique plusieurs fois)
      const newConfirmBtn = confirmBtn.cloneNode(true);
      confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

      // Ajoute le nouvel event
      newConfirmBtn.addEventListener('click', function() {
        window.location.href = "?action=delete&id=" + id;
      });

      document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.add('hidden');
    }
  </script>


  <style>
    /* Smooth animations for modals */
    #editModal,
    #deleteModal {
      transition: opacity 0.3s ease;
    }

    /* Button hover effects */
    button.bg-blue-500:hover,
    button.bg-red-500:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    /* Modal entrance animation */
    @keyframes modalEnter {
      from {
        opacity: 0;
        transform: scale(0.9) translateY(-20px);
      }

      to {
        opacity: 1;
        transform: scale(1) translateY(0);
      }
    }

    #editModal:not(.hidden)>div,
    #deleteModal:not(.hidden)>div {
      animation: modalEnter 0.3s ease-out;
    }
  </style>
</body>

</html>