document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let username = document.getElementById('username').value;
    let password = document.getElementById('password').value;

    fetch('scripts/loginHandler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username: username, password: password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect or show success message
            window.location.href = 'dashboard.html';
        } else {
            // Display error message
            document.getElementById('error-message').innerText = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
