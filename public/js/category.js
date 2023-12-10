document.addEventListener('DOMContentLoaded', () => {
    const createCategoryWidget = document.querySelector('.category-block');
    const createCategoryForm = createCategoryWidget.querySelector("form");
    const createCategoryButton = document.querySelector(".create-category-button");
    const categorySelectIncome = document.querySelector(".income-form select");
    const categorySelectExpense = document.querySelector(".expense-form select");
    const allCategoriesButton = document.querySelector('.show-all-categories-button');
    const allCategoriesDiv = document.querySelector('.all-categories');
    const incomeCategories = allCategoriesDiv.querySelector('.income-ul');
    const expenseCategories = allCategoriesDiv.querySelector('.expense-ul');
    // const deleteCategoryButton = document.querySelector(".delete-category-widget__button");
    // const deleteCategoryWidget = document.querySelector(".delete-category-widget");
    // let deleteCategoryWidgetOffers = deleteCategoryWidget.querySelectorAll(".delete-category-widget__a");

    createCategoryButton.addEventListener('click', () => {
        createCategoryForm.classList.remove('display-none');
    });

    allCategoriesButton.addEventListener('click', () => {
        allCategoriesDiv.classList.remove('display-none');
    })

    // deleteCategoryButton.addEventListener('click', () => {
    //     deleteCategoryWidget.classList.toggle('display-none');
    // });
    //
    // for (let i = 0; i < deleteCategoryWidgetOffers.length; i++) {
    //     deleteCategoryWidgetOffers[i]
    //         .addEventListener('click', (event) => onClickDelete(event, deleteCategoryWidgetOffers[i]));
    // }
    //
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
                createCategoryForm.classList.add('display-none');
                let data = await response.json();
                let option = `<option value="${data.id}">${data.name}</option>`;
                let li = `<li><a data-category-id="${data.id}" href="/profile/category/${data.id}">${data.name}</a> X</li>`;
                if (income === 'true') {
                    categorySelectIncome.insertAdjacentHTML('beforeend', option);
                    incomeCategories.insertAdjacentHTML('beforeend', li);
                } else {
                    categorySelectExpense.insertAdjacentHTML('beforeend', option);
                    expenseCategories.insertAdjacentHTML('beforeend', li);
                }
                // ul.lastChild.addEventListener('click', (event) => onClickDelete(event, event.target));
                // deleteCategoryWidgetOffers = deleteCategoryWidget.querySelectorAll(".delete-category-widget__a");
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
    //
    // const onClickDelete = (event, category) => {
    //     event.preventDefault();
    //     let categoryId = category.getAttribute("data-category-id");
    //     const authHeaderName = document.querySelector('meta[name=_csrf_header]').content;
    //     const authToken = document.querySelector('meta[name=_csrf]').content;
    //     const headers = makeHeaders();
    //     headers[authHeaderName] = authToken;
    //
    //     fetch(category.href, {
    //         method: "DELETE",
    //         headers: headers
    //     }).then(async response => {
    //         if (response.ok) {
    //             let option = categorySelect.querySelector(`option[value="${categoryId}"]`);
    //             category.remove();
    //             categorySelect.removeChild(option);
    //         } else if (response.status === 403) {
    //             alert('FORBIDDEN OPERATION');
    //         } else if (response.status === 400) {
    //             alert('BAD REQUEST');
    //         } else {
    //             alert('SERVER ERROR');
    //         }
    //     }).catch(error => {
    //         console.log(error);
    //         alert('UNKNOWN ERROR')
    //     });
    //
    // }
})
