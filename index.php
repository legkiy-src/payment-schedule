<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'PaymentSchedule.php';

$paymentSchedule = new PaymentSchedule();

$paymentScheduleCalculation = $paymentSchedule->getSchedules();

echo json_encode($paymentScheduleCalculation);