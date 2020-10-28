<div class="tv-template-grid">
    <div class="tv-template-grid_col col-1" data-widget="schedule">
        <?=$this->render('widget/schedule', [
            'widget' => $widgets['schedule']
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
        right: 0px;   
    }
    
    .tv-template-grid_col .title {
        display: inline-block;
    }
</style>

<script>
    $(document).ready(function(){
        
    });
</script>