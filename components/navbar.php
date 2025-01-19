<nav class="bg-white shadow-lg fixed w-full z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="#" class="flex items-center space-x-3">
                <span class="font-playfair text-xl font-bold text-blue-900">Franco-Pascual</span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <div class="flex space-x-8">
                    <a href="#" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Home</a>
                    <a href="#dental-services" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Services</a>
                    <a href="#dental-details" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">About</a>
                    <a href="#contact" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors">Contact</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    
                    <a href="https://fpdentalclinic.com/Patient_Login/index" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">Book Now</a>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-600 focus:outline-none focus:text-gray-600" aria-label="toggle menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="md:hidden fixed inset-0 z-50 transform transition-all duration-300 ease-in-out opacity-0 -translate-y-full">
            <div class="absolute inset-0 bg-white">
                <div class="flex flex-col h-full">
                    <!-- Mobile Header -->
                    <div class="flex justify-between items-center p-6 border-b">
                        <span class="font-playfair text-xl font-bold text-blue-900">Franco-Pascual</span>
                        <button id="close-menu-button" class="text-gray-500 hover:text-gray-600 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Mobile Navigation Links -->
                    <div class="flex flex-col py-8 px-6 space-y-4 flex-grow">
                        <a href="#" class="text-gray-600 hover:bg-blue-50 hover:text-blue-600 px-6 py-4 text-lg font-medium transition-colors rounded-lg">Home</a>
                        <a href="#dental-services" class="text-gray-600 hover:bg-blue-50 hover:text-blue-600 px-6 py-4 text-lg font-medium transition-colors rounded-lg">Services</a>
                        <a href="#dental-details" class="text-gray-600 hover:bg-blue-50 hover:text-blue-600 px-6 py-4 text-lg font-medium transition-colors rounded-lg">About</a>
                        <a href="#contact" class="text-gray-600 hover:bg-blue-50 hover:text-blue-600 px-6 py-4 text-lg font-medium transition-colors rounded-lg">Contact</a>
                        
                        <!-- Mobile Auth Buttons -->
                        <div class="pt-6 space-y-4">
                            <a href="https://fpdentalclinic.com/Patient_Login/index.php" class="block text-center bg-blue-600 text-white px-6 py-4 rounded-lg hover:bg-blue-700 transition-colors text-lg font-medium">Book Now</a>                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeMenuButton = document.getElementById('close-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            function toggleMenu() {
                const isHidden = mobileMenu.classList.contains('-translate-y-full') && mobileMenu.classList.contains('opacity-0');
                
                if (isHidden) {
                    // Show menu
                    mobileMenu.classList.remove('opacity-0', '-translate-y-full');
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                } else {
                    // Hide menu
                    mobileMenu.classList.add('opacity-0', '-translate-y-full');
                    document.body.style.overflow = ''; // Restore scrolling
                }
            }

            mobileMenuButton.addEventListener('click', toggleMenu);
            closeMenuButton.addEventListener('click', toggleMenu);

            // Close menu when clicking on a link
            const mobileLinks = mobileMenu.querySelectorAll('a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', () => {
                    toggleMenu();
                });
            });
        });
    </script>
</nav>
