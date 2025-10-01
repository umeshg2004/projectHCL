$(document).ready(function() {
    $('#loginBtn').click(function() {
        const username = $('#username').val().trim();
        const password = $('#password').val();

        if (!username || !password) {
            showAlert('Please fill in all fields', 'danger');
            return;
        }

        $.ajax({
            url: 'php/login.php',
            type: 'POST',
            dataType: 'json',
            data: {
                username: username,
                password: password
            },
            success: function(response) {
                if (response.success) {
                    localStorage.setItem('user_session', response.session_token);
                    localStorage.setItem('username', response.username);
                    showAlert('Login successful! Redirecting to profile...', 'success');
                    setTimeout(function() {
                        window.location.href = 'profile.html';
                    }, 2000);
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function() {
                showAlert('Login failed. Please try again.', 'danger');
            }
        });
    });

    // Guest login functionality
    $('#guestBtn').click(function() {
        const guestSessionToken = 'guest_' + Math.random().toString(36).substr(2, 9);
        const guestUsername = 'Guest_' + Math.floor(Math.random() * 10000);
        
        localStorage.setItem('user_session', guestSessionToken);
        localStorage.setItem('username', guestUsername);
        localStorage.setItem('is_guest', 'true');
        
        showAlert('Guest login successful! Redirecting to profile...', 'success');
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