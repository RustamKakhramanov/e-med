<div class="tv-template-grid">
    <div class="tv-template-grid_col col-1" data-widget="schedule">
        <div class="title">
            График работы
        </div>
    </div>
    <div class="tv-template-grid_col col-2" data-widget="oncall">
        <div class="title">
            Дежурные врачи
        </div>
    </div>
    <div class="tv-template-grid_col col-3" data-widget="discharged">
        <div class="title">
            Сегодня выписываются
        </div>
    </div>
</div>

<style>
    .tv-template-grid {
        position: relative;
        border: 1px #eceff3 solid;
        width: 800px;
        height: 450px;
    }

    .tv-template-grid_col {
        position: absolute;
        border: 1px #eceff3 solid;
        text-align: center;
        cursor: pointer;
        color: #666;
        font-size: 16px;
        transition: all 0.2s linear;
    }

    .tv-template-grid_col:before {
        display: inline-block;
        height: 100%;
        content: '';
        vertical-align: middle;
    }

    .tv-template-grid_col:hover {
        /*        color: #fff;*/
        /*        background: #416586;*/
        border-color: #416586;
        color: #416586;
    }

    .tv-template-grid .col-1 {        
        left: 10px;
        top: 10px;
        bottom: 10px;
        right: 40%;
        margin-right: 5px;        
    }

    .tv-template-grid .col-2 {
        left: 60%;
        top: 10px;
        bottom: 50%;
        right: 10px;
        margin-left: 5px;
        margin-bottom: 5px;
    }

    .tv-template-grid .col-3 {
        left: 60%;
        top: 50%;
        bottom: 10px;
        right: 10px;
        margin-left: 5px;
        margin-top: 5px;
    }

    .tv-template-grid_col .title {
        display: inline-block;
    }
</style>

<script>
    $(document).ready(function () {
        $('.tv-template-grid_col').off('click').on('click', function () {
            var name = $(this).attr('data-widget');
            $.ajax({
                url: '/tv/widget-form?w=' + name,
                type: 'post',
                data: {
                    data: JSON.stringify(tvData)
                },
                success: function (resp) {
                    openModal({
                        html: resp
                    });
                }
            });
        });
    });
</script>