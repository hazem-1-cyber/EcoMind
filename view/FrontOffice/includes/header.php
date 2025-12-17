<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo isset($pageTitle) ? $pageTitle : 'EcoMind - Live Smarter'; ?></title>
  <meta name="theme-color" content="#0B3D2E">
  <link rel="icon" href="images/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.gstatic.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&amp;display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"/>
  <?php if (isset($additionalCSS)): ?>
    <?php foreach ($additionalCSS as $css): ?>
      <link rel="stylesheet" href="<?php echo $css; ?>">
    <?php endforeach; ?>
  <?php endif; ?>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer="defer"></script>
</head>
<body class="antialiased bg-body text-body font-body">
  <div>
    <!-- Top Banner -->
    <div>
      <p class="mb-0 py-3 text-center" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); color: #2e7d32; font-weight: 500; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">Small actions can make a big difference for the planet</p>
    </div>
    
    <!-- Navigation Header -->
    <div>
      <section class="overflow-hidden" x-data="{ mobileNavOpen: false }">
        <nav class="mx-4 py-6 border-b">
          <div class="container mx-auto px-4">
            <div class="relative flex items-center justify-between">
              <a class="inline-block" href="index.php">
                <img class="h-12" src="images/logo-ecomind.png" alt="EcoMind Logo" style="max-height: 56px;"/>
              </a>
              
              <ul class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 hidden md:flex">
                <li class="mr-4 lg:mr-8"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="index.php" style="color: #0B3D2E;">Home</a></li>
                <li class="mr-4 lg:mr-8"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="addDon.php" style="color: #0B3D2E;">Our shop</a></li>
                <li class="mr-4 lg:mr-8"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="#events" style="color: #0B3D2E;">Events</a></li>
                <li class="mr-4 lg:mr-8"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="#contact" style="color: #0B3D2E;">Contact us</a></li>
                <li><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="#blog" style="color: #0B3D2E;">Blog</a></li>
              </ul>
              
              <div class="flex items-center justify-end">
                <div class="hidden md:block">
                  <div class="hidden md:block">
                    <a class="inline-flex py-2.5 px-4 items-center justify-center text-sm font-medium text-teal-900 hover:text-white border border-teal-900 hover:bg-teal-900 rounded-full transition duration-200" href="consulterdonpersonnel.php" style="color: #0B3D2E; border-color: #0B3D2E;">Consulter mes dons</a>
                  </div>
                </div>
                
                <button class="md:hidden text-teal-900 hover:text-teal-800" x-on:click="mobileNavOpen = !mobileNavOpen" style="color: #0B3D2E;">
                  <svg width="32" height="32" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.19995 23.2H26.7999" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M5.19995 16H26.7999" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M5.19995 8.79999H26.7999" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </nav>
        
        <!-- Mobile Navigation -->
        <div class="hidden fixed top-0 left-0 bottom-0 w-full xs:w-5/6 xs:max-w-md z-50" :class="{'block': mobileNavOpen, 'hidden': !mobileNavOpen}">
          <div class="fixed inset-0 bg-violet-900 opacity-20" x-on:click="mobileNavOpen = !mobileNavOpen"></div>
          <nav class="relative flex flex-col py-7 px-10 w-full h-full bg-white overflow-y-auto">
            <div class="flex items-center justify-between">
              <a class="inline-block" href="index.php">
                <img class="h-12" src="images/logo-ecomind.png" alt="EcoMind Logo" style="max-height: 56px;"/>
              </a>
              <div class="flex items-center">
                <a class="inline-flex py-2.5 px-4 items-center justify-center text-sm font-medium text-teal-900 hover:text-white border border-teal-900 hover:bg-teal-900 rounded-full transition duration-200" href="consulterdonpersonnel.php" style="color: #0B3D2E; border-color: #0B3D2E;">Consulter</a>
                <button class="ml-6" x-on:click="mobileNavOpen = !mobileNavOpen">
                  <svg width="32" height="32" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M23.2 8.79999L8.80005 23.2M8.80005 8.79999L23.2 23.2" stroke="#1D1F1E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </button>
              </div>
            </div>
            
            <div class="pt-20 pb-12 mb-auto">
              <ul class="flex-col">
                <li class="mb-6"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="index.php" style="color: #0B3D2E;">Home</a></li>
                <li class="mb-6"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="addDon.php" style="color: #0B3D2E;">Our shop</a></li>
                <li class="mb-6"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="#events" style="color: #0B3D2E;">Events</a></li>
                <li class="mb-6"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="#contact" style="color: #0B3D2E;">Contact us</a></li>
                <li class="mb-6"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="#blog" style="color: #0B3D2E;">Blog</a></li>
                <li class="mb-6 pt-4 border-t">
                  <a class="inline-flex py-3 px-6 items-center justify-center text-sm font-medium text-teal-900 hover:text-white border border-teal-900 hover:bg-teal-900 rounded-full transition duration-200 w-full" href="consulterdonpersonnel.php" style="color: #0B3D2E; border-color: #0B3D2E;">Consulter mes dons</a>
                </li>
              </ul>
            </div>
            
            <div class="flex items-center justify-between">
              <a class="inline-flex items-center text-lg font-medium text-teal-900" href="#newsletter" style="color: #0B3D2E;">
                <span>
                  <svg width="32" height="32" viewbox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.4 6.39999H25.6C26.92 6.39999 28 7.47999 28 8.79999V23.2C28 24.52 26.92 25.6 25.6 25.6H6.4C5.08 25.6 4 24.52 4 23.2V8.79999C4 7.47999 5.08 6.39999 6.4 6.39999Z" stroke="#646A69" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M28 8.8L16 17.2L4 8.8" stroke="#646A69" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </span>
                <span class="ml-2">Newsletter</span>
              </a>
            </div>
          </nav>
        </div>
      </section>
    </div>
