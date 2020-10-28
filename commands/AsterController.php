<?php

namespace app\commands;

use yii\console\Controller;
//use AGI_AsteriskManager;
use PAMI\Client\Impl\ClientImpl;
use PAMI\Listener\IEventListener;
use PAMI\Message\Event\EventMessage;
use PAMI\Message\Action\ListCommandsAction;
use PAMI\Message\Action\ListCategoriesAction;
use PAMI\Message\Action\CoreShowChannelsAction;
use PAMI\Message\Action\CoreSettingsAction;
use PAMI\Message\Action\CoreStatusAction;
use PAMI\Message\Action\StatusAction;
use PAMI\Message\Action\ReloadAction;
use PAMI\Message\Action\CommandAction;
use PAMI\Message\Action\HangupAction;
use PAMI\Message\Action\LogoffAction;
use PAMI\Message\Action\AbsoluteTimeoutAction;
use PAMI\Message\Action\OriginateAction;
use PAMI\Message\Action\BridgeAction;
use PAMI\Message\Action\CreateConfigAction;
use PAMI\Message\Action\GetConfigAction;
use PAMI\Message\Action\GetConfigJSONAction;
use PAMI\Message\Action\AttendedTransferAction;
use PAMI\Message\Action\RedirectAction;
use PAMI\Message\Action\DAHDIShowChannelsAction;
use PAMI\Message\Action\DAHDIHangupAction;
use PAMI\Message\Action\DAHDIRestartAction;
use PAMI\Message\Action\DAHDIDialOffHookAction;
use PAMI\Message\Action\DAHDIDNDOnAction;
use PAMI\Message\Action\DAHDIDNDOffAction;
use PAMI\Message\Action\AgentsAction;
use PAMI\Message\Action\AgentLogoffAction;
use PAMI\Message\Action\MailboxStatusAction;
use PAMI\Message\Action\MailboxCountAction;
use PAMI\Message\Action\VoicemailUsersListAction;
use PAMI\Message\Action\PlayDTMFAction;
use PAMI\Message\Action\DBGetAction;
use PAMI\Message\Action\DBPutAction;
use PAMI\Message\Action\DBDelAction;
use PAMI\Message\Action\DBDelTreeAction;
use PAMI\Message\Action\GetVarAction;
use PAMI\Message\Action\SetVarAction;
use PAMI\Message\Action\PingAction;
use PAMI\Message\Action\ParkedCallsAction;
use PAMI\Message\Action\SIPQualifyPeerAction;
use PAMI\Message\Action\SIPShowPeerAction;
use PAMI\Message\Action\SIPPeersAction;
use PAMI\Message\Action\SIPShowRegistryAction;
use PAMI\Message\Action\SIPNotifyAction;
use PAMI\Message\Action\QueuesAction;
use PAMI\Message\Action\QueueStatusAction;
use PAMI\Message\Action\QueueSummaryAction;
use PAMI\Message\Action\QueuePauseAction;
use PAMI\Message\Action\QueueRemoveAction;
use PAMI\Message\Action\QueueUnpauseAction;
use PAMI\Message\Action\QueueLogAction;
use PAMI\Message\Action\QueuePenaltyAction;
use PAMI\Message\Action\QueueReloadAction;
use PAMI\Message\Action\QueueResetAction;
use PAMI\Message\Action\QueueRuleAction;
use PAMI\Message\Action\MonitorAction;
use PAMI\Message\Action\PauseMonitorAction;
use PAMI\Message\Action\UnpauseMonitorAction;
use PAMI\Message\Action\StopMonitorAction;
use PAMI\Message\Action\ExtensionStateAction;
use PAMI\Message\Action\JabberSendAction;
use PAMI\Message\Action\LocalOptimizeAwayAction;
use PAMI\Message\Action\ModuleCheckAction;
use PAMI\Message\Action\ModuleLoadAction;
use PAMI\Message\Action\ModuleUnloadAction;
use PAMI\Message\Action\ModuleReloadAction;
use PAMI\Message\Action\ShowDialPlanAction;
use PAMI\Message\Action\ParkAction;
use PAMI\Message\Action\MeetmeListAction;
use PAMI\Message\Action\MeetmeMuteAction;
use PAMI\Message\Action\MeetmeUnmuteAction;
use PAMI\Message\Action\EventsAction;
use PAMI\Message\Action\VGMSMSTxAction;
use PAMI\Message\Action\DongleSendSMSAction;
use PAMI\Message\Action\DongleShowDevicesAction;
use PAMI\Message\Action\DongleReloadAction;
use PAMI\Message\Action\DongleStartAction;
use PAMI\Message\Action\DongleRestartAction;
use PAMI\Message\Action\DongleStopAction;
use PAMI\Message\Action\DongleResetAction;
use PAMI\Message\Action\DongleSendUSSDAction;
use PAMI\Message\Action\DongleSendPDUAction;

use PAMI\Message\Event\NewchannelEvent;

class AsterController extends Controller {

    public function actionIndex() {
        echo "start listening\n";

//        $manager = new \AGI_AsteriskManager();
//        $manager->connect('192.168.5.15', 'admin', 'Fr33M@nhl');
//        $manager->add_event_handler('*', 'dump_events');

        $options = array(
            'host' => '92.46.38.94',
            'scheme' => 'tcp://',
            'port' => 5038,
            'username' => 'API',
            'secret' => 'Fr33M@nhl',
            'connect_timeout' => 10,
            'read_timeout' => 10
        );
        $a = new ClientImpl($options);

//        $a->registerEventListener(
//                array(new A(), 'handle'), function ($event) {
//            echo $event->getName() == 'Newchannel' . "\n";
//            return $event instanceof DialEvent && $event->getName() == 'Newchannel';
//        }
//        );
        $a->registerEventListener(new A());
        $a->open();
        
        print_r($a->send(new QueueStatusAction(false,1))); //список всех
        //print_r($a->send(new QueueSummaryAction('905'))); //общая по одной
        exit;

        $time = time();
        while (time() - $time < 40) {
            usleep(1000); // 1ms delay
            // Since we declare(ticks=1) at the top, the following line is not necessary
            $a->process();
        }
        $a->close(); // send logoff and close the connection.
    }

}

class A implements IEventListener {

    public function handle(EventMessage $event) {
        echo $event->getName() . "\n";
        //if ($event->getName() == 'QueueStatus') {
            $this->_log(print_r($event, true));
        //}
    }

    /**
     * запись лога
     * @param type $text
     */
    protected function _log($text) {
        $fp = fopen(__DIR__ . '/../runtime/logs/aster.log', 'a');
        fwrite($fp, '[' . date('d.m.Y H:i:s') . '] ' . $text . "\n");
        fclose($fp);
    }

}
