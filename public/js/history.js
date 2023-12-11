document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.delete-button');

    const makeHeaders = () => {
        return {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json'
        };
    };

    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            fetch(button.href, {
                method: "DELETE",
                headers: makeHeaders(),
            }).then(async response => {
                if (response.ok) {
                    button.parentElement.parentElement.classList.add('display-none');
                } else if (response.status === 403) {
                    alert('FORBIDDEN OPERATION');
                } else if (response.status === 400) {
                    alert('BAD REQUEST');
                } else {
                    alert('SERVER ERROR');
                }
            }).catch(error => {
                console.log(error);
                alert('UNKNOWN ERROR')
            });
        })
    })
})
