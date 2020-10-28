<script>
    function sess_prolong() {
        $.ajax({
            url: '/site/prolong'
        });
    }
    $(document).ready(function () {
        sess_prolong();
        setInterval(sess_prolong, 1000 * 15);
    });
</script>