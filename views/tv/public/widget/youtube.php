<div class="tv-template-youtube">
    
</div>

<script>
    $(document).ready(function(){
        setTimeout(function(){
            $('.tv-template-youtube').html('<iframe src="<?= $widget->url; ?>" frameborder="0"></iframe>');
        }, 1000);
    });
</script>