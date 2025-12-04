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
require_once 'C:/xampp/htdocs/projet_web/config/config.php';
require_once 'C:/xampp/htdocs/projet_web/app/controllers/BackOfficeController.php';

$controller = new BackOfficeController();

/* ---------------------------
   1. Vérification de l'ID
--------------------------- */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?error=Invalid user ID');
    exit();
}

$user_id = intval($_GET['id']);

/* ---------------------------
   2. Récupération d'un seul user
--------------------------- */
$user = $controller->getUserById($user_id);

if (!$user) {
    header('Location: index.php?error=User not found');
    exit();
}

/* ---------------------------
   3. Traitement du formulaire (POST)
--------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $address = trim($_POST['address']);

    if ($controller->updateUser($user_id, $name, $email, $address)) {
        header('Location: index.php?success=User updated successfully');
        exit();
    } else {
        $error_message = "Error updating user!";
    }
}
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
            class="ml-1 font-semibold transition-all duration-200 ease-nav-brand"
            >Soft UI Dashboard</span
          >
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
                class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft"
                >Users</span
              >
            </a>
          </li>
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
                <a class="opacity-50 text-slate-700" href="javascript:;"
                  >Pages</a
                >
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
      

      <!-- end Navbar -->

      <!-- Main Content Area -->
      <div class="w-full px-6 py-6 mx-auto">
        <!-- Users Table -->
        <div class="flex flex-wrap -mx-3">
          <div class="w-full max-w-full px-3 flex-0">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
              <div class="p-6 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                <h6 class="mb-1">Edit User</h6>
                <p class="leading-normal text-sm">Manage all registered users in the system</p>
              </div>
              <div class="flex-auto p-6">
                <div class="overflow-x-auto">
                  
      <!-- Form -->
              <form method="POST" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Name Field -->
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                      <i class="fas fa-user text-blue-500 mr-2"></i>
                      Full Name
                    </label>
                    <input type="text" name="name" id="name" required
                           value="<?php echo htmlspecialchars($user['name']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  </div>
                  
                  <!-- Email Field -->
                  <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                      <i class="fas fa-envelope text-green-500 mr-2"></i>
                      Email Address
                    </label>
                    <input type="email" name="email" id="email" required
                           value="<?php echo htmlspecialchars($user['email']); ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                  </div>
                </div>
                
                <!-- Address Field -->
                <div>
                  <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                    Address
                  </label>
                  <textarea name="address" id="address" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                
                <!-- User Info -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <h4 class="text-sm font-medium text-gray-700 mb-3">User Information</h4>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                      <span class="text-gray-500">User ID:</span>
                      <span class="font-medium"><?php echo htmlspecialchars($user['id']); ?></span>
                    </div>
                    <div>
                      <span class="text-gray-500">Created At:</span>
                      <span class="font-medium">
                        <?php 
                        $date = new DateTime($user['created_at']);
                        echo $date->format('M j, Y \\a\\t g:i A');
                        ?>
                      </span>
                    </div>
                  </div>
                </div>
                
                <!-- Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                  <a href="index.php"
                     class="px-6 py-3 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg transition-all duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Users
                  </a>
                  <button type="submit"
                         class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-black rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Update User
                  </button>
                </div>
              </form>

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

  </body>
</html>