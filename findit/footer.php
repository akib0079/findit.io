<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <span class="logo">FindIt</span>
            <p>Connecting lost items with their rightful owners.</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="browse.php">Search Items</a></li>
                <li><a href="report.php">Report Lost</a></li>
                <li><a href="report.php">Report Found</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h4>Legal</h4>
            <ul>
                <li><a href="#">Privacy Policy</a></li>
                <li><a href="#">Terms of Service</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 FindIt. All rights reserved.</p>
    </div>
</footer>

<div class="modal-overlay" id="authModal">
    <div class="modal">
        <div class="modal-header">
            <h2 id="modalTitle">Welcome Back</h2>
            <button class="modal-close" id="modalClose">×</button>
        </div>
        
        <div class="modal-tabs">
            <button class="modal-tab active" data-tab="login">Login</button>
            <button class="modal-tab" data-tab="register">Register</button>
        </div>
        
        <div class="modal-body">
            <div class="tab-content active" id="loginForm">
                <form action="auth_action.php" method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="modern-input" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="modern-input" required>
                    </div>
                    <button type="submit" class="form-submit" style="background: #007bff; color: white;">Login</button>
                    <p class="form-footer">Don't have an account? <a href="#" id="switchToRegister">Register here</a></p>
                </form>
            </div>

            <div class="tab-content" id="registerForm">
                <form action="auth_action.php" method="POST">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="role-selector" style="display: flex; gap: 10px; margin-bottom: 20px;">
                        <label style="flex: 1; cursor: pointer;">
                            <input type="radio" name="role" value="user" checked style="display: none;" onchange="updateRoleUI(this)">
                            <div class="role-card active" id="role-user" style="border: 2px solid #007bff; background: #e7f1ff; padding: 10px; text-align: center; border-radius: 8px; font-weight: 600; color: #007bff;">
                                👤 User
                            </div>
                        </label>
                        <label style="flex: 1; cursor: pointer;">
                            <input type="radio" name="role" value="restaurant" style="display: none;" onchange="updateRoleUI(this)">
                            <div class="role-card" id="role-restaurant" style="border: 2px solid #ddd; padding: 10px; text-align: center; border-radius: 8px; color: #666;">
                                🍽️ Restaurant
                            </div>
                        </label>
                    </div>

                    <div class="form-group">
                        <label id="nameLabel">Full Name</label>
                        <input type="text" name="full_name" class="modern-input" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="modern-input" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="tel" name="contact_no" class="modern-input" placeholder="017xxxxxxxx" pattern="[0-9]{11}" maxlength="11" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="modern-input" required minlength="6">
                    </div>
                    <button type="submit" class="form-submit" style="background: #007bff; color: white;">Create Account</button>
                    <p class="form-footer">Already have an account? <a href="#" id="switchToLogin">Login here</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="popup-overlay" id="generalPopup" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 3000; justify-content: center; align-items: center;">
    <div class="popup-content" style="background: white; padding: 30px; border-radius: 16px; text-align: center; max-width: 400px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: popIn 0.3s ease;">
        <div id="popupIcon" style="font-size: 3rem; margin-bottom: 15px;">✅</div>
        <h3 id="popupTitle" style="margin: 0 0 10px 0; color: #333;">Success</h3>
        <p id="popupMessage" style="color: #666; line-height: 1.5;">Operation completed.</p>
        <button onclick="document.getElementById('generalPopup').style.display='none'" style="margin-top: 20px; padding: 10px 25px; background: #007bff; color: white; border: none; border-radius: 50px; cursor: pointer; font-weight: 600;">Okay, Got it</button>
    </div>
</div>

<style>
    @keyframes popIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>

<script>
    // Role Switcher UI Logic
    function updateRoleUI(radio) {
        document.getElementById('role-user').style.border = '2px solid #ddd';
        document.getElementById('role-user').style.background = 'white';
        document.getElementById('role-user').style.color = '#666';
        
        document.getElementById('role-restaurant').style.border = '2px solid #ddd';
        document.getElementById('role-restaurant').style.background = 'white';
        document.getElementById('role-restaurant').style.color = '#666';
        
        const activeCard = document.getElementById('role-' + radio.value);
        activeCard.style.border = '2px solid #007bff';
        activeCard.style.background = '#e7f1ff';
        activeCard.style.color = '#007bff';

        const nameLabel = document.getElementById('nameLabel');
        if(radio.value === 'restaurant') nameLabel.innerText = "Manager Name";
        else nameLabel.innerText = "Full Name";
    }

    $(document).ready(function() {
        // Modal Logic
        const $modal = $('#authModal');
        const $modalClose = $('#modalClose');
        const $modalTabs = $('.modal-tab');
        
        function openModal(tab) {
            $modal.addClass('active');
            switchTab(tab);
        }
        function closeModal() {
            $modal.removeClass('active');
        }
        function switchTab(tab) {
            $modalTabs.removeClass('active');
            $(`.modal-tab[data-tab="${tab}"]`).addClass('active');
            $('.tab-content').removeClass('active');
            $(`#${tab}Form`).addClass('active');
            $('#modalTitle').text(tab === 'login' ? 'Welcome Back' : 'Create Account');
        }

        $('#loginBtn').on('click', () => openModal('login'));
        $modalClose.on('click', closeModal);
        $modalTabs.on('click', function() { switchTab($(this).data('tab')); });
        $('#switchToRegister').on('click', (e) => { e.preventDefault(); switchTab('register'); });
        $('#switchToLogin').on('click', (e) => { e.preventDefault(); switchTab('login'); });
        
        // PHP SESSION MESSAGE HANDLER
        <?php if (isset($_SESSION['status'])): ?>
            <?php 
                $msg = $_SESSION['message'];
                $status = $_SESSION['status'];
                $msgLower = strtolower($msg);
                
                // DETECT: Is this a Login/Register error?
                // If message contains "login", "password", "email", "account" -> It's Auth related
                $isAuthError = (
                    strpos($msgLower, 'password') !== false || 
                    strpos($msgLower, 'email') !== false || 
                    strpos($msgLower, 'login') !== false ||
                    strpos($msgLower, 'account created') !== false ||
                    strpos($msgLower, 'register') !== false
                );

                if ($isAuthError) {
                    // CASE 1: Open Login/Register Modal
                    $tab = (strpos($msgLower, 'account created') !== false || strpos($msgLower, 'register') !== false) ? 'register' : 'login';
                    echo "openModal('$tab');";
                    // You can optionally inject the error message into the modal here if you want
                } else {
                    // CASE 2: Open General Success Popup (For Claims, Reports, etc.)
                    echo "document.getElementById('generalPopup').style.display = 'flex';";
                    echo "document.getElementById('popupMessage').innerText = '" . addslashes($msg) . "';";
                    if ($status == 'error') {
                         echo "document.getElementById('popupTitle').innerText = 'Error';";
                         echo "document.getElementById('popupTitle').style.color = '#dc3545';";
                         echo "document.getElementById('popupIcon').innerText = '⚠️';";
                    } else {
                         echo "document.getElementById('popupTitle').innerText = 'Success!';";
                         echo "document.getElementById('popupIcon').innerText = '✅';";
                    }
                }
            ?>
            // Clear Session
            <?php unset($_SESSION['status']); unset($_SESSION['message']); ?>
        <?php endif; ?>
    });
</script>