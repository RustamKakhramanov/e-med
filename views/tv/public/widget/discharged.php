<div class="tv-template-discharged">
    <div class="header">Сегодня выписываются</div>
    <div class="items">
        <?php foreach ($widget->strings as $row) { ?>
            <div class="item" style="background-image: url(/uploads/discharged/<?= $row['img']; ?>)">
                <span><?= nl2br($row['text']); ?></span>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.tv-template-discharged .items').owlCarousel({
                loop: true,
                margin: 0,
                nav: false,
                dots: true,
                items: 1,
                responsive: false,
                autoplay: true
            });
    });
</script>