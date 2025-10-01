$(document).ready(function() {
    $('#registerBtn').click(function() {
        const username = $('#username').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();

        if (!username || !email || !password || !confirmPassword) {
            showAlert('Please fill in all fields', 'danger');
            return;
        }

        if (password !== confirmPassword) {
            showAlert('Passwords do not match', 'danger');
            return;
        }

        if (password.length < 6) {
            showAlert('Password must be at least 6 characters long', 'danger');
            return;
        }

        $.ajax({
            url: 'php/register.php',
            type: 'POST',
            dataType: 'json',
            data: {
                username: username,
                email: email,
                password: password
            },
            success: function(response) {
                if (response.success) {
                    showAlert('Registration successful! Redirecting to login...', 'success');
                    setTimeout(function() {
                        window.location.href = 'login.html';
                    }, 2000);
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function() {
                showAlert('Registration failed. Please try again.', 'danger');
            }
        });
    });

    // Guest signup functionality
    $('#guestBtn').click(function() {
        const guestSessionToken = 'guest_' + Math.random().toString(36).substr(2, 9);
        const guestUsername = 'Guest_' + Math.floor(Math.random() * 10000);
        
        localStorage.setItem('user_session', guestSessionToken);
        localStorage.setItem('username', guestUsername);
        localStorage.setItem('is_guest', 'true');
        
        showAlert('Guest registration successful! Redirecting to profile...', 'success');
        setTimeout(function() {
            window.location.href = 'profile.html';
        }, 1500);
    });

    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('#alertContainer').html(alertHtml);
    }
});