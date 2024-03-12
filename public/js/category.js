document.addEventListener('DOMContentLoaded', () => {
    const createCategoryButton = document.querySelector('.create-category-block button');
    const createCategoryFormContainer = document.querySelector('.create-category-form');
    const createCategoryForm = document.querySelector('.create-category-form form');
    const categorySelectIncome = document.querySelector('.income-form select');
    const categorySelectExpense = document.querySelector('.expense-form select');
    const allCategoriesDiv = document.querySelector('.all-categories');
    const incomeCategories = allCategoriesDiv.querySelector('.income-ul');
    const expenseCategories = allCategoriesDiv.querySelector('.expense-ul');
    let spans = allCategoriesDiv.querySelectorAll('span');
    const successNote = document.querySelector('.success-note');
    const closeCategoryForm = document.querySelector('.close-create-category');

    spans.forEach(span => {
        span.addEventListener('click', () => {
            onClickDelete(span)
        })
    })

    const toggleCreateCategory = () => {
        createCategoryButton.classList.toggle('display-none');
        createCategoryFormContainer.classList.toggle('display-none');
    }

    createCategoryButton.addEventListener('click', toggleCreateCategory)
    closeCategoryForm.addEventListener('click', toggleCreateCategory)

    const makeHeaders = () => {
        return {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json'
        };
    };

    createCategoryForm.addEventListener("submit", (event) => {
        event.preventDefault();
        let select = createCategoryForm.querySelector("select");
        let income = select.options[select.selectedIndex].value;
        let name = createCategoryForm.querySelector("input#name").value;
        // const authHeaderName = document.querySelector('meta[name=_csrf_header]').content;
        // const authToken = document.querySelector('meta[name=_csrf]').content;
        const headers = makeHeaders();
        // headers[authHeaderName] = authToken;
        let request = `income=${income}&name=${name}`;

        fetch(createCategoryForm.action, {
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
                let option = `<option value="${data.id}">${data.name}</option>`;
                let li = `<li><a href="/profile/category/${data.id}">${data.name}</a><span data-category-id="${data.id}"> X</span></li>`;
                if (income === 'true') {
                    categorySelectIncome.insertAdjacentHTML('beforeend', option);
                    incomeCategories.insertAdjacentHTML('beforeend', li);
                    incomeCategories.lastChild.lastChild.addEventListener('click', (event) => onClickDelete(event.target));
                } else {
                    categorySelectExpense.insertAdjacentHTML('beforeend', option);
                    expenseCategories.insertAdjacentHTML('beforeend', li);
                    expenseCategories.lastChild.lastChild.addEventListener('click', (event) => onClickDelete(event.target));
                }
                spans = allCategoriesDiv.querySelectorAll('span');
            } else if (response.status === 403) {
                alert('FORBIDDEN OPERATION');
            } else if (response.status === 400) {
                alert('BAD REQUEST');
            } else {
                alert('SERVER ERROR');
                console.log
            }
        }).catch(error => {
            console.log(error);
            alert('UNKNOWN ERROR')
        });
    })

    const onClickDelete = (span) => {
        const categoryId = span.dataset.categoryId;
        // const authHeaderName = document.querySelector('meta[name=_csrf_header]').content;
        // const authToken = document.querySelector('meta[name=_csrf]').content;
        const headers = makeHeaders();
        // headers[authHeaderName] = authToken;
        fetch('/profile/delete-category/' + categoryId, {
            method: "DELETE",
            headers: headers
        }).then(async response => {
            if (response.ok) {
                let option = categorySelectIncome.querySelector(`option[value="${categoryId}"]`);
                if (option === null) {
                    option = categorySelectExpense.querySelector(`option[value="${categoryId}"]`);
                    categorySelectExpense.removeChild(option);
                } else {
                    categorySelectIncome.removeChild(option);
                }
                span.parentNode.remove();
            } else if (response.status === 403) {
                alert('FORBIDDEN OPERATION');
            } else if (response.status === 400) {
                alert('BAD REQUEST');
            } else if (response.status === 418) {
                alert('You cannot delete this category because there are money operations or limits associated with it.');
            } else {
                alert('SERVER ERROR');
            }
        }).catch(error => {
            console.log(error);
            alert('UNKNOWN ERROR')
        });

    }
})
