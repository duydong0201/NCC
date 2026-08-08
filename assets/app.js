document.querySelectorAll('[data-confirm]').forEach(function (item) {
    item.addEventListener('click', function (event) {
        if (!confirm(item.dataset.confirm)) event.preventDefault();
    });
});
