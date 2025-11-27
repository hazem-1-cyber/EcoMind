<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
    <title>Delete User - Soft UI Dashboard</title>
    <!--     Fonts and icons     -->
    <link
      href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700"
      rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script
      src="https://kit.fontawesome.com/42d5adcbca.js"
      crossorigin="anonymous"></script>
    <!-- Nucleo Icons -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Popper -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <!-- Main Styling -->
    <link
      href="../assets/css/soft-ui-dashboard-tailwind.css?v=1.0.5"
      rel="stylesheet" />
  </head>

  <body
    class="m-0 font-sans text-base antialiased font-normal leading-default bg-gray-50 text-slate-500">
    
    <?php
    require_once('C:\xampp\htdocs\projet_web\config\config.php');
    include('C:\xampp\htdocs\projet_web\app\controllers\BackOfficeController.php');

    $controller = new BackOfficeController();

/* ---------------------------
   Vérifier si l'ID est valide
--------------------------- */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?error=Invalid ID");
    exit();
}

$user_id = intval($_GET['id']);

/* ---------------------------
   Vérifier si l'utilisateur existe
--------------------------- */
$user = $controller->getUserById($user_id);

if (!$user) {
    header("Location: index.php?error=User not found");
    exit();
}

/* ---------------------------
   Supprimer l'utilisateur
--------------------------- */
if ($controller->deleteUser($user_id)) {
    header("Location: index.php?success=User deleted successfully");
    exit();
} else {
    header("Location: index.php?error=Error deleting user");
    exit();
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
            src="../assets/img/logo-ct.png"
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
              href="index.php">
              <div
                class="bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5">
                <i class="ni leading-none ni-single-02 text-lg relative top