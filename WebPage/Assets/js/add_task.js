document.getElementById('add-task-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch('../Assets/php/add_task.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageDiv = document.getElementById('message');
        if (data.success) {
            messageDiv.innerHTML = `<p style="color: green;">${data.message}</p>`;
        } else {
            messageDiv.innerHTML = `<p style="color: red;">${data.message}</p>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('message').innerHTML = `<p style="color: red;">An error occurred</p>`;
    });
});