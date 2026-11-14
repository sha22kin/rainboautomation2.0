{{-- Student login / register pages: SHOW / HIDE buttons of the password boxes --}}
<script>
document.querySelectorAll('[data-sp-show]').forEach(function (button) {
    button.addEventListener('click', function () {
        var input = document.getElementById(button.dataset.spShow);
        input.type = input.type === 'password' ? 'text' : 'password';
        button.textContent = input.type === 'password' ? 'SHOW' : 'HIDE';
    });
});
</script>
