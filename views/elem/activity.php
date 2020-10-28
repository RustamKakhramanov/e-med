<script>
    $(document).ready(function () {
        var activitySeconds = 60 * 10 * 6; //10 минут //TODO убрать 60 минут
        var activityDetected = false;
        var activityTimeout = setTimeout(inActive, activitySeconds * 1000);
        var activityInterval = false;
        var activityTime;

        function resetActive() {
            if (!activityDetected) {
                $('body').removeClass('activity-inactive');
                clearTimeout(activityTimeout);
                clearInterval(activityInterval);
                activityTimeout = setTimeout(inActive, activitySeconds * 1000);
            }
        }

        function inActive() {
            activityDetected = true;
            $('body').addClass('activity-inactive');
            openModal({
                closeButton: false,
                html: '<div class="activity-modal pl20 pr20 pb20"><h1>Отсутствие активности</h1>Вы не совершали никаких действий продолжительное время. Через <strong class="time"></strong> секунд будет выполнен выход из системы.<div class="mt10"><span class="btn btn-default btn-act-handler">Вернуться</span></div></div>',
                onOpen: function () {
                    activityTime = 60;
                    $('.activity-modal .time').text(activityTime);
                    activityInterval = setInterval(function () {
                        activityTime--;
                        $('.activity-modal .time').text(activityTime);
                        if (activityTime == 0) {
                            window.location.href = '/logout';
                            clearInterval(activityInterval);
                        }
                    }, 1000);
                    $('.activity-modal .btn-act-handler').on('click', function () {
                        $(this).closest('.modal-wrap').trigger('close');
                    });
                },
                onClose: function () {
                    activityDetected = false;
                    resetActive();
                }
            });
        }

        $(document).on('mousemove', function () {
            resetActive();
        });
    });
</script>