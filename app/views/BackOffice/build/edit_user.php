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
    <style>
      /* Styles pour la sidebar */
      .nav-item:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: white !important;
        border-left-color: #88b04b !important;
      }
      
      /* Style pour les champs de formulaire */
      .form-input {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 16px;
        transition: all 0.3s ease;
        background: #f8fafc;
      }
      
      .form-input:focus {
        border-color: #2c5f2d;
        background: white;
        box-shadow: 0 0 0 3px rgba(44, 95, 45, 0.1);
      }
      
      /* Style pour les labels */
      .form-label {
        color: #2c5f2d;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
      }
    </style>
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



    
    <!-- Sidebar avec le style du back office -->
    <aside class="sidebar" id="sidebar" style="position: fixed; width: 250px; height: 100vh; background: linear-gradient(135deg, #2c5f2d, #1a3a1b); color: white; overflow-y: auto;">
        <div class="sidebar-header" style="padding: 20px; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="logo" style="display: flex; align-items: center; gap: 10px;">
                <div class="logo-icon">
                    <img src="images/logo-ecomind.png" alt="EcoMind Logo" style="width: 50px; height: 50px; object-fit: contain;">
                </div>
                <div class="logo-text" style="font-size: 20px; font-weight: bold;">EcoMind</div>
            </div>
        </div>
        
        <nav class="sidebar-nav" style="padding: 20px 0;">
            <!-- Dashboard avec statistiques users -->
            <a href="index.php" class="nav-item active" style="display: flex; align-items: center; gap: 12px; padding: 15px 20px; color: white; text-decoration: none; background: rgba(255, 255, 255, 0.1); border-left: 3px solid #88b04b;">
                <i class="fas fa-chart-line" style="width: 20px; text-align: center;"></i>
                <span>Dashboard</span>
            </a>
            
            <!-- Event - Travail du camarade (vide pour l'instant) -->
            <a href="#" class="nav-item" onclick="alert('Section Event - Travail en cours par votre camarade')" style="display: flex; align-items: center; gap: 12px; padding: 15px 20px; color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: all 0.3s ease; border-left: 3px solid transparent;">
                <i class="fas fa-calendar-alt" style="width: 20px; text-align: center;"></i>
                <span>Event</span>
            </a>
            
            <!-- Shop - Travail du camarade (vide pour l'instant) -->
            <a href="#" class="nav-item" onclick="alert('Section Shop - Travail en cours par votre camarade')" style="display: flex; align-items: center; gap: 12px; padding: 15px 20px; color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: all 0.3s ease; border-left: 3px solid transparent;">
                <i class="fas fa-shopping-cart" style="width: 20px; text-align: center;"></i>
                <span>Shop</span>
            </a>
            
            <!-- Don - Travail du camarade (vide pour l'instant) -->
            <a href="#" class="nav-item" onclick="alert('Section Don - Travail en cours par votre camarade')" style="display: flex; align-items: center; gap: 12px; padding: 15px 20px; color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: all 0.3s ease; border-left: 3px solid transparent;">
                <i class="fas fa-heart" style="width: 20px; text-align: center;"></i>
                <span>Don</span>
            </a>
            
            <!-- Déconnexion -->
            <a href="logout.php" class="nav-item" style="display: flex; align-items: center; gap: 12px; padding: 15px 20px; color: rgba(255, 255, 255, 0.8); text-decoration: none; transition: all 0.3s ease; border-left: 3px solid transparent;">
                <i class="fas fa-sign-out-alt" style="width: 20px; text-align: center;"></i>
                <span>Déconnexion</span>
            </a>
        </nav>
    </aside>

    <!-- end sidenav -->

    <main
      class="ease-soft-in-out relative h-full max-h-screen rounded-xl transition-all duration-200" style="margin-left: 250px;">
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
                    <label for="name" class="form-label">
                      <i class="fas fa-user"></i>
                      Full Name
                    </label>
                    <input type="text" name="name" id="name" required
                           value="<?php echo htmlspecialchars($user['name']); ?>"
                           class="w-full form-input">
                  </div>
                  
                  <!-- Email Field -->
                  <div>
                    <label for="email" class="form-label">
                      <i class="fas fa-envelope"></i>
                      Email Address
                    </label>
                    <input type="email" name="email" id="email" required
                           value="<?php echo htmlspecialchars($user['email']); ?>"
                           class="w-full form-input">
                  </div>
                </div>
                
                <!-- Address Field -->
                <div>
                  <label for="address" class="form-label">
                    <i class="fas fa-map-marker-alt"></i>
                    Address
                  </label>
                  <textarea name="address" id="address" rows="3"
                            class="w-full form-input"><?php echo htmlspecialchars($user['address']); ?></textarea>
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
                         class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105 flex items-center">
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