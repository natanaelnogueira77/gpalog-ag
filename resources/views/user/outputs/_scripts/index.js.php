<script>
    $(function () {
        const app = new App();

        <?php if($dbPallets): ?>
        const pallets_list = $("#pallets-list");
        const include_all = $("#include-all");

        include_all.change(function () {
            if($(this).is(":checked")) {
                $("[name='pallets[]']").prop('checked', true);
            } else {
                $("[name='pallets[]']").prop('checked', false);
            }
        });

        app.form(pallets_list, function (response) {
            if(response.pdf) window.open(response.pdf, '_blank').focus();
            window.location.reload();
        });
        <?php endif; ?>
    });
</script>