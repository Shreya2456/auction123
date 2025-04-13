</main>
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">BidPulse</h3>
                    <p class="text-gray-300 mb-4">Your trusted platform for online auctions. Find the best deals on clothes and watches from sellers around the world.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-white hover:text-accent transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white hover:text-accent transition-colors"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white hover:text-accent transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.linkedin.com/in/soumyosishpal/" class="text-white hover:text-accent transition-colors"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-300 hover:text-accent transition-colors">Home</a></li>
                        <li><a href="auctions.php" class="text-gray-300 hover:text-accent transition-colors">Auctions</a></li>
                        <li><a href="categories.php" class="text-gray-300 hover:text-accent transition-colors">Categories</a></li>
                        <li><a href="about.php" class="text-gray-300 hover:text-accent transition-colors">About Us</a></li>
                        <li><a href="contact.php" class="text-gray-300 hover:text-accent transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <li><a href="auctions.php?category=1" class="text-gray-300 hover:text-accent transition-colors">Clothes</a></li>
                        <li><a href="auctions.php?category=2" class="text-gray-300 hover:text-accent transition-colors">Watches</a></li>
                        <li><a href="auctions.php?category=3" class="text-gray-300 hover:text-accent transition-colors">Accessories</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-2 text-accent"></i>
                            <span>Lovely Professional University</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2 text-accent"></i>
                            <span>+91 9002990526</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-accent"></i>
                            <span>support@bidpulse.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p>&copy; 2025 BidPulse. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Alert message close button
        const alertCloseButtons = document.querySelectorAll('[role="alert"] svg');
        alertCloseButtons.forEach(button => {
            button.addEventListener('click', function() {
                this.closest('[role="alert"]').remove();
            });
        });
    </script>
</body>
</html>
