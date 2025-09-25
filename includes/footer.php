    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>FindMyRead</h3>
                    <p>Discover your next favorite book with our intelligent recommendation system. Join thousands of readers who have found their perfect match.</p>
                    <div class="social-links">
                        <a href="https://www.facebook.com/yabsira.yitagesu" title="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.instagram.com/nilu20207?igsh=MXhmaHZmcXdlZzlmYQ==" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="books.php">Browse Books</a></li>
                        <li><a href="genres.php">Genres</a></li>
                        <li><a href="recommendations.php">Recommendations</a></li>
                        <li><a href="search.php">Search</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Account</h4>
                    <ul>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="reading-lists.php">My Lists</a></li>
                            <li><a href="profile.php">Profile</a></li>
                            <li><a href="analytics.php">Analytics</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Login</a></li>
                            <li><a href="register.php">Sign Up</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="help.php">Help Center</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 FindMyRead. All rights reserved. | Built with ❤️ for book lovers everywhere</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/recommendations.js"></script>
    <script src="assets/js/reading-lists.js"></script>
    <script src="assets/js/search.js"></script>
</body>
</html>
