<!DOCTYPE html>
<html lang="en">
  <head>
    <title>EcoMind - Small actions can make a big difference</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel="preconnect" href="https://fonts.gstatic.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&amp;display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"/>
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.svg"/>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer="defer"></script>
    <style>
      body {
        font-family: 'Figtree', sans-serif;
        background-color: #fff;
      }
      .hero-section {
        background: linear-gradient(135deg, #0B3D2E 0%, #134d3a 100%);
        border-radius: 20px;
        margin: 20px;
      }
      .green-card {
        background-color: #0B3D2E;
        border-radius: 16px;
      }
      .btn-green {
        background-color: #84cc16;
        color: #0B3D2E;
        font-weight: 600;
      }
      .btn-green:hover {
        background-color: #65a30d;
      }
      .footer-bg {
        background-color: #f5f5f0;
      }
    </style>
  </head>
  <body class="antialiased">
    <div>
      <!-- Top Banner -->
      <div>
        <p class="mb-0 py-3 bg-lime-500 text-center">Small actions can make a big difference for the planet</p>
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
                
                <ul class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 hidden lg:flex">
                  <li class="mr-3 lg:mr-6"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="index.php" style="color: #0B3D2E;">Home</a></li>
                  <li class="mr-3 lg:mr-6"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="addDon.php" style="color: #0B3D2E;">Our shop</a></li>
                  <li class="mr-3 lg:mr-6"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="#events" style="color: #0B3D2E;">Events</a></li>
                  <li class="mr-3 lg:mr-6"><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="#contact" style="color: #0B3D2E;">Contact us</a></li>
                  <li><a class="inline-block text-teal-900 hover:text-teal-700 font-medium" href="#blog" style="color: #0B3D2E;">Blog</a></li>
                </ul>
                
                <div class="flex items-center justify-end">
                  <div class="hidden lg:block">
                    <div class="hidden lg:flex items-center">
                      <a class="inline-flex py-2.5 px-3 mr-2 items-center justify-center text-sm font-medium text-teal-900 hover:text-white border border-teal-900 hover:bg-teal-900 rounded-full transition duration-200" href="#login" style="color: #0B3D2E; border-color: #0B3D2E;">Login</a>
                      <a class="inline-flex py-2.5 px-3 mr-2 items-center justify-center text-sm font-medium text-teal-900 hover:text-white border border-teal-900 hover:bg-teal-900 rounded-full transition duration-200" href="#register" style="color: #0B3D2E; border-color: #0B3D2E;">Register</a>
                      <a class="inline-flex py-2.5 px-3 mr-2 items-center justify-center text-sm font-medium text-teal-900 hover:text-white border border-teal-900 hover:bg-teal-900 rounded-full transition duration-200" href="consulterdonpersonnel.php" style="color: #0B3D2E; border-color: #0B3D2E;">Consulter mes dons</a>
                      <a class="inline-flex py-2.5 px-4 items-center justify-center text-sm font-medium text-white border border-teal-900 hover:border-black bg-teal-900 hover:bg-black rounded-full transition duration-200" href="addDon.php" style="background-color: #0B3D2E; border-color: #0B3D2E;">Faire un don</a>
                    </div>
                  </div>
                  
                  <button class="md:hidden text-teal-900 hover:text-teal-800" x-on:click="mobileNavOpen = !mobileNavOpen">
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
                  <a class="inline-flex py-2.5 px-4 items-center justify-center text-sm font-medium text-teal-900 hover:text-white border border-teal-900 hover:bg-teal-900 rounded-full transition duration-200" href="consulterdonpersonnel.php">Consulter</a>
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
                  <li class="mb-4 pt-4 border-t">
                    <a class="inline-flex py-3 px-6 items-center justify-center text-sm font-medium text-teal-900 hover:text-white border border-teal-900 hover:bg-teal-900 rounded-full transition duration-200 w-full" href="#login" style="color: #0B3D2E; border-color: #0B3D2E;">Login</a>
                  </li>
                  <li class="mb-4">
                    <a class="inline-flex py-3 px-6 items-center justify-center text-sm font-medium text-teal-900 hover:text-white border border-teal-900 hover:bg-teal-900 rounded-full transition duration-200 w-full" href="#register" style="color: #0B3D2E; border-color: #0B3D2E;">Register</a>
                  </li>
                  <li class="mb-4">
                    <a class="inline-flex py-3 px-6 items-center justify-center text-sm font-medium text-teal-900 hover:text-white border border-teal-900 hover:bg-teal-900 rounded-full transition duration-200 w-full" href="consulterdonpersonnel.php" style="color: #0B3D2E; border-color: #0B3D2E;">Consulter mes dons</a>
                  </li>
                  <li class="mb-6">
                    <a class="inline-flex py-3 px-6 items-center justify-center text-sm font-medium text-white bg-teal-900 hover:bg-black rounded-full transition duration-200 w-full" href="addDon.php" style="background-color: #0B3D2E;">Faire un don</a>
                  </li>
                </ul>
              </div>
              
              <div class="flex items-center justify-between">
                <a class="inline-flex items-center text-lg font-medium text-teal-900" href="#newsletter">
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
      
      <!-- Hero Section -->
      <section class="py-8 lg:py-12">
        <div class="hero-section">
          <div class="container mx-auto px-8 py-16 lg:py-24">
            <div class="max-w-3xl">
              <h1 class="font-bold text-4xl sm:text-5xl md:text-6xl mb-6 text-white leading-tight">Small actions can make a big difference for the planet</h1>
              <p class="text-lg text-white opacity-90 mb-8">Visit www.ecomind.store to a better live</p>
              <a class="inline-flex py-3 px-8 items-center justify-center text-base font-semibold rounded-full transition duration-200 btn-green" href="addDon.php">Get Started</a>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Footer -->
      <section class="relative py-12 lg:py-16 footer-bg">
        <div class="container px-4 mx-auto relative">
          <div class="flex flex-wrap mb-16 -mx-4">
            <div class="w-full lg:w-2/12 xl:w-2/12 px-4 mb-12 lg:mb-0">
              <a class="inline-block mb-4" href="index.php">
                <img src="images/logo-ecomind.png" alt="EcoMind Logo" style="height: 40px;"/>
              </a>
            </div>
            
            <div class="w-full lg:w-5/12 xl:w-5/12 px-4 mb-12 lg:mb-0">
              <div class="flex flex-wrap -mx-4">
                <div class="w-1/2 md:w-1/3 px-4 mb-8 md:mb-0">
                  <h3 class="mb-6 font-bold text-sm">Platform</h3>
                  <ul>
                    <li class="mb-3"><a class="inline-block text-gray-600 hover:text-lime-500 text-sm" href="#!">Solutions</a></li>
                    <li class="mb-3"><a class="inline-block text-gray-600 hover:text-lime-500 text-sm" href="#!">How it works</a></li>
                    <li><a class="inline-block text-gray-600 hover:text-lime-500 text-sm" href="#!">Pricing</a></li>
                  </ul>
                </div>
                
                <div class="w-1/2 md:w-1/3 px-4 mb-8 md:mb-0">
                  <h3 class="mb-6 font-bold text-sm">Resources</h3>
                  <ul>
                    <li class="mb-3"><a class="inline-block text-gray-600 hover:text-lime-500 text-sm" href="#!">Blog</a></li>
                    <li class="mb-3"><a class="inline-block text-gray-600 hover:text-lime-500 text-sm" href="#!">Help Center</a></li>
                    <li><a class="inline-block text-gray-600 hover:text-lime-500 text-sm" href="#!">Support</a></li>
                  </ul>
                </div>
                
                <div class="w-full md:w-1/3 px-4">
                  <h3 class="mb-6 font-bold text-sm">Company</h3>
                  <ul>
                    <li class="mb-3"><a class="inline-block text-gray-600 hover:text-lime-500 text-sm" href="#!">About</a></li>
                    <li class="mb-3"><a class="inline-block text-gray-600 hover:text-lime-500 text-sm" href="#!">Our Mission</a></li>
                    <li class="mb-3"><a class="inline-block text-gray-600 hover:text-lime-500 text-sm" href="#!">Careers</a></li>
                    <li><a class="inline-block text-gray-600 hover:text-lime-500 text-sm" href="consulterdonpersonnel.php">Contact</a></li>
                  </ul>
                </div>
              </div>
            </div>
            
            <div class="w-full lg:w-5/12 xl:w-5/12 px-4">
              <div class="green-card p-8 max-w-md ml-auto">
                <h5 class="text-xl font-semibold text-white mb-4">Your Source for Green Energy Updates</h5>
                <p class="text-sm text-white opacity-90 leading-relaxed mb-6">Stay in the loop with our Green Horizon newsletter, where we deliver bite-sized insights into the latest green energy solutions.</p>
                <div class="space-y-3">
                  <input class="w-full px-4 py-3 text-sm rounded-full border-0 focus:outline-none focus:ring-2 focus:ring-lime-500" type="email" placeholder="Your e-mail..."/>
                  <button class="w-full py-3 px-6 text-sm font-semibold rounded-full transition duration-200 btn-green">Get in touch</button>
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex flex-wrap items-center justify-between pt-8 border-t border-gray-300">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
              <a class="inline-block text-gray-700 hover:text-lime-500 transition" href="#!">
                <svg width="20" height="20" viewbox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g clip-path="url(#clip0_230_4832)">
                    <path d="M11.5481 19.9999V10.8776H14.6088L15.068 7.32147H11.5481V5.05138C11.5481 4.02211 11.8327 3.32067 13.3104 3.32067L15.1919 3.3199V0.139138C14.8665 0.0968538 13.7496 -9.15527e-05 12.4496 -9.15527e-05C9.735 -9.15527e-05 7.87654 1.65687 7.87654 4.69918V7.32147H4.80652V10.8776H7.87654V19.9999H11.5481Z" fill="currentColor"></path>
                  </g>
                </svg>
              </a>
              <a class="inline-block text-gray-700 hover:text-lime-500 transition" href="#!">
                <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M7.8 2H16.2C19.4 2 22 4.6 22 7.8V16.2C22 17.7383 21.3889 19.2135 20.3012 20.3012C19.2135 21.3889 17.7383 22 16.2 22H7.8C4.6 22 2 19.4 2 16.2V7.8C2 6.26174 2.61107 4.78649 3.69878 3.69878C4.78649 2.61107 6.26174 2 7.8 2ZM7.6 4C6.64522 4 5.72955 4.37928 5.05442 5.05442C4.37928 5.72955 4 6.64522 4 7.6V16.4C4 18.39 5.61 20 7.6 20H16.4C17.3548 20 18.2705 19.6207 18.9456 18.9456C19.6207 18.2705 20 17.3548 20 16.4V7.6C20 5.61 18.39 4 16.4 4H7.6ZM17.25 5.5C17.5815 5.5 17.8995 5.6317 18.1339 5.86612C18.3683 6.10054 18.5 6.41848 18.5 6.75C18.5 7.08152 18.3683 7.39946 18.1339 7.63388C17.8995 7.8683 17.5815 8 17.25 8C16.9185 8 16.6005 7.8683 16.3661 7.63388C16.1317 7.39946 16 7.08152 16 6.75C16 6.41848 16.1317 6.10054 16.3661 5.86612C16.6005 5.6317 16.9185 5.5 17.25 5.5ZM12 7C13.3261 7 14.5979 7.52678 15.5355 8.46447C16.4732 9.40215 17 10.6739 17 12C17 13.3261 16.4732 14.5979 15.5355 15.5355C14.5979 16.4732 13.3261 17 12 17C10.6739 17 9.40215 16.4732 8.46447 15.5355C7.52678 14.5979 7 13.3261 7 12C7 10.6739 7.52678 9.40215 8.46447 8.46447C9.40215 7.52678 10.6739 7 12 7ZM12 9C11.2044 9 10.4413 9.31607 9.87868 9.87868C9.31607 10.4413 9 11.2044 9 12C9 12.7956 9.31607 13.5587 9.87868 14.1213C10.4413 14.6839 11.2044 15 12 15C12.7956 15 13.5587 14.6839 14.1213 14.1213C14.6839 13.5587 15 12.7956 15 12C15 11.2044 14.6839 10.4413 14.1213 9.87868C13.5587 9.31607 12.7956 9 12 9Z" fill="currentColor"></path>
                </svg>
              </a>
              <a class="inline-block text-gray-700 hover:text-lime-500 transition" href="#!">
                <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M19 3C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H19ZM18.5 18.5V13.2C18.5 12.3354 18.1565 11.5062 17.5452 10.8948C16.9338 10.2835 16.1046 9.94 15.24 9.94C14.39 9.94 13.4 10.46 12.92 11.24V10.13H10.13V18.5H12.92V13.57C12.92 12.8 13.54 12.17 14.31 12.17C14.6813 12.17 15.0374 12.3175 15.2999 12.5801C15.5625 12.8426 15.71 13.1987 15.71 13.57V18.5H18.5ZM6.88 8.56C7.32556 8.56 7.75288 8.383 8.06794 8.06794C8.383 7.75288 8.56 7.32556 8.56 6.88C8.56 5.95 7.81 5.19 6.88 5.19C6.43178 5.19 6.00193 5.36805 5.68499 5.68499C5.36805 6.00193 5.19 6.43178 5.19 6.88C5.19 7.81 5.95 8.56 6.88 8.56ZM8.27 18.5V10.13H5.5V18.5H8.27Z" fill="currentColor"></path>
                </svg>
              </a>
            </div>
            <div class="text-center mb-4 md:mb-0">
              <p class="text-sm text-gray-600">© 2025 EcoMind. All rights reserved.</p>
            </div>
            <p class="text-sm text-gray-500"></p>
          </div>
        </div>
      </section>
    </div>
  </body>
</html>
