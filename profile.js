$(document).ready(function() {
    const sessionToken = localStorage.getItem('user_session');
    const isGuest = localStorage.getItem('is_guest') === 'true';
    
    if (!sessionToken) {
        window.location.href = 'login.html';
        return;
    }

    if (isGuest) {
        setupGuestProfile();
    } else {
        loadProfile();
    }

    $('#updateBtn').click(function() {
        if (isGuest) {
            updateGuestProfile();
        } else {
            updateRegularProfile();
        }
    });

    $('#logoutBtn').click(function() {
        if (!isGuest) {
            // Only call logout.php for regular users
            $.ajax({
                url: 'php/logout.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    session_token: sessionToken
                },
                success: function() {
                    performLogout();
                },
                error: function() {
                    performLogout();
                }
            });
        } else {
            performLogout();
        }
    });

    function setupGuestProfile() {
        const guestUsername = localStorage.getItem('username');
        $('#username').val(guestUsername);
        $('#email').val('guest@example.com');
        
        // Load guest profile data from localStorage
        const guestProfile = JSON.parse(localStorage.getItem('guest_profile') || '{}');
        $('#age').val(guestProfile.age || '');
        $('#dob').val(guestProfile.dob || '');
        $('#contact').val(guestProfile.contact || '');
        $('#address').val(guestProfile.address || '');
        
        showAlert('You are logged in as a guest. Profile data will be saved locally.', 'info');
    }

    function updateGuestProfile() {
        const profileData = {
            age: $('#age').val(),
            dob: $('#dob').val(),
            contact: $('#contact').val(),
            address: $('#address').val()
        };
        
        // Save to localStorage for guest users
        localStorage.setItem('guest_profile', JSON.stringify(profileData));
        showAlert('Guest profile updated successfully! (Saved locally)', 'success');
    }

    function updateRegularProfile() {
        const profileData = {
            session_token: sessionToken,
            age: $('#age').val(),
            dob: $('#dob').val(),
            contact: $('#contact').val(),
            address: $('#address').val()
        };

        $.ajax({
            url: 'php/profile.php',
            type: 'PUT',
            dataType: 'json',
            data: JSON.stringify(profileData),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    showAlert('Profile updated successfully!', 'success');
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function() {
                showAlert('Failed to update profile. Please try again.', 'danger');
            }
        });
    }

    function loadProfile() {
        $.ajax({
            url: 'php/profile.php',
            type: 'GET',
            dataType: 'json',
            data: {
                session_token: sessionToken
            },
            success: function(response) {
                if (response.success) {
                    $('#username').val(response.data.username);
                    $('#email').val(response.data.email);
                    $('#age').val(response.data.age || '');
                    $('#dob').val(response.data.dob || '');
                    $('#contact').val(response.data.contact || '');
                    $('#address').val(response.data.address || '');
                } else {
                    showAlert(response.message, 'danger');
                    if (response.message === 'Invalid session') {
                        performLogout();
                    }
                }
            },
            error: function() {
                showAlert('Failed to load profile data.', 'danger');
            }
        });
    }

    function performLogout() {
        localStorage.removeItem('user_session');
        localStorage.removeItem('username');
        localStorage.removeItem('is_guest');
        localStorage.removeItem('guest_profile');
        window.location.href = 'login.html';
    }

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