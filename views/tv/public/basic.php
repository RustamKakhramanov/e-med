<div class="tv-template-grid">
    <div class="tv-template-grid_col col-1" data-widget="schedule">
        <?=$this->render('widget/schedule', [
            'widget' => $widgets['schedule']
        ]);?>
    </div>
    <div class="tv-template-grid_col col-2" data-widget="weather">
        <?=$this->render('widget/oncall', [
            'widget' => $widgets['oncall']
        ]);?>
    </div>
    <div class="tv-template-grid_col col-3" data-widget="discharged">
        <?=$this->render('widget/discharged', [
            'widget' => $widgets['discharged']
        ]);?>
    </div>
</div>

<style>    
    .tv-template-grid {
        height: 100%;
    }
    
    .tv-template-grid_col {
        position: absolute;
        overflow: hidden;
        text-align: center;
        transition: all 0.2s linear;
    }
    
    .tv-template-grid .col-1 {        
        left: 0px;
        top: 0px;
        bottom: 0px;
        right: 50%;   
    }
    
    .tv-template-grid .col-2 {
        left: 50%;
        top: 0px;
        right: 0px;
        bottom: 50%;
    }
    
    .tv-template-grid .col-3 {
        left: 50%;
        top: 50%;
        bottom: 0px;
        right: 0px;
    }
    
    .tv-template-grid_col .title {
        display: inline-block;
    }
    
    /** override widgets */
    .tv-template-schedule {

    }

    .tv-template-schedule .item .photo {
        width: 105px;
        height: 85px;
    }
    
    .tv-template-schedule .item .photo-ctr {
        width: 85px;
        height: 85px;
    }
    
    .tv-template-schedule .item .name {
        width: 260px;
    }
    
    .tv-template-schedule .item .time-on span {
        display: block;
    }
    
    .tv-template-schedule .item .time-on {
        font-size: 26px;
    }
    
    .tv-template-schedule .item .time sup {
        top: 0px;
        left: 0px;
        font-size: 26px;
    }
    
    .tv-template-schedule .item .time sup:before {
        content: ":";
        margin-right: 2px;
    }
    
    .tv-template-schedule .item .time-on {
        padding: 5px 0px;
    }
    
    .tv-template-schedule .item .time-off {
            height: 80px;
    }
</style>

<script>
    var shortDays = {
        'Понедельник': 'Пн',
        'Вторник': 'Вт',
        'Среда': 'Ср',
        'Четверг': 'Чт',
        'Пятница': 'Пт',
        'Суббота': 'Сб',
        'Воскресенье': 'Вс'
    };
    
    function fitColumn() {
            var $youtube = $('.tv-template-grid_col.col-3');
            var $weather = $('.tv-template-grid_col.col-2');

            var h = $youtube.width() * 9 / 16;
            var th = $('.tv-template-grid').height();
            
            if (h > (th - 300)) {
                h = th - 300;
            }
            
            $youtube.height(h);
            $weather.height(th - h);
        }
        
    $(document).ready(function(){
        //fitColumn();
        
        $('.tv-template-schedule .time-off').text('-');
        $('.tv-template-schedule .dash').hide();
        $('.tv-template-schedule .item-header .day').each(function(){
            $(this).text(shortDays[$.trim($(this).text())]);
        });
        $('.tv-template-schedule .doctor-name').each(function(){
            $(this).html($(this).attr('data-short'));
        });
    });
</script>