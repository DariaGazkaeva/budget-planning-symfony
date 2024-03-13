document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.delete-limit-btn');
    const availableSums = document.querySelectorAll('p.available-sum');
    const createLimitButton = document.querySelector('.create-limit-block button');
    const createLimitFormContainer = document.querySelector('.create-limit-form');
    const createLimitForm = document.querySelector('.create-limit-form form');
    const closeLimitForm = document.querySelector('.close-create-limit');
    const successNote = document.querySelector('.create-limit-block .success-note');
    const limitsBlock = document.querySelector('.limits-block');
    const makeHeaders = () => {
        return {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json'
        };
    };

    const toggleCreateLimit = () => {
        createLimitButton.classList.toggle('display-none');
        createLimitFormContainer.classList.toggle('display-none');
    }

    createLimitButton.addEventListener('click', toggleCreateLimit);
    closeLimitForm.addEventListener('click', toggleCreateLimit);

    deleteButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const headers = makeHeaders();
            fetch(button.href, {
                method: "DELETE",
                headers: headers
            }).then(async response => {
                if (response.ok) {
                    button.parentElement.parentElement.remove();
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
    });

    availableSums.forEach(p => {
        const currentSum = p.firstElementChild.textContent;
        if (currentSum === '0') {
            const limit = p.parentElement.style.backgroundColor = 'orange';
        }
        if (currentSum.startsWith('-')) {
            const limit = p.parentElement.style.backgroundColor = 'red';
        }
    });

    createLimitForm.addEventListener("submit", (event) => {
        event.preventDefault();
        let select = createLimitForm.querySelector("select");
        let selectedCategory = select.options[select.selectedIndex].value;
        let sum = createLimitForm.querySelector("input#total_sum").value;
        let date = createLimitForm.querySelector("input#start_date").value;
        // const authHeaderName = document.querySelector('meta[name=_csrf_header]').content;
        // const authToken = document.querySelector('meta[name=_csrf]').content;
        const headers = makeHeaders();
        // headers[authHeaderName] = authToken;
        let request = `category=${selectedCategory}&total_sum=${sum}&start_date=${date}`;

        fetch(createLimitForm.action, {
            method: "POST",
            headers: headers,
            body: request
        }).then(async response => {
            if (response.ok) {
                successNote.classList.toggle('display-none');
                setTimeout(() => {
                    successNote.classList.toggle('display-none');
                }, 3000);
                let data = await response.json();
                let divLimit = `<div class="limit">
                                    <span>${data.category}</span>
                                    <p class="available-sum">
                                        <span>${data.currentSum}</span> / <span>${data.totalSum}</span>
                                    </p>
                                    <div>
                                        <a href="/profile/limit/${data.id}">Edit</a>
                                        <a class="delete-limit-btn" href="/profile/delete-limit/${data.id}">Delete</a>
                                    </div>
                                </div>`;
                limitsBlock.insertAdjacentHTML('beforeend', divLimit);
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
