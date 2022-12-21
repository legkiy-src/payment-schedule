<?php


class PaymentSchedule
{
    public function getSchedules() : array
    {
        $schedules = $this->getApplications();
        $result = [];
        $totalPrice = 0;

        foreach ($schedules as $scheduleItem)
        {
            $monthInstallment = $scheduleItem['monthInstallment'];
            $schedule = [];
            $dateStart = new DateTime($scheduleItem['startDateInstallment']);
            $dateToInsert = new DateTime($scheduleItem['startDateInstallment']);
            $paymentDay = $dateStart->format('d');
            $totalPrice += $scheduleItem['price'];

            for ($i = 0; $i <= $monthInstallment; $i++)
            {
                if ($i === 0)
                {
                    continue;
                }

                $incrementedDate = $this->addMonthToDate($dateToInsert, $paymentDay);
                $schedule[$i]['date'] = $incrementedDate->format('Y-m-d');
                $dateToInsert = $incrementedDate;

                if ($i < $monthInstallment)
                {
                    $schedule[$i]['payment'] = (int)$scheduleItem['paymentInstallment'];
                }
                elseif ($i === $monthInstallment)
                {
                    $schedule[$i]['payment'] = $scheduleItem['price'] -
                        $scheduleItem['paymentInstallment'] * ($monthInstallment - 1);
                }

                $schedule[$i]['remain'] = $scheduleItem['price'] - $scheduleItem['paymentInstallment'] * $i;

                if ($i === 1)
                {
                    $schedule[$i]['remain'] = $scheduleItem['price'] - $scheduleItem['paymentInstallment'];
                }
                elseif ($i > 1)
                {
                    $schedule[$i]['remain'] = $schedule[$i - 1]['remain'] - $schedule[$i]['payment'];
                }
            }

            $result['paymentItem'][] = [
                'applicationId' => $scheduleItem['applicationId'],
                'applicationName' => $scheduleItem['applicationName'],
                'price' => $scheduleItem['price'],
                'startDateInstallment' => $scheduleItem['startDateInstallment'],
                'monthInstallment' => $monthInstallment,
                'schedule' => $schedule
            ];
        }

        return $result;
    }

    private function getApplications() : array
    {
        return [
            [
                'applicationId' => 234,
                'applicationName' => 'Пылесос',
                'price' => 5500,
                'paymentInstallment' => 1100,
                'monthInstallment' => 5,
                'startDateInstallment' => '2022-12-29'
            ],
            [
                'applicationId' => 235,
                'applicationName' => 'Телефон',
                'price' => 15000,
                'paymentInstallment' => 1250,
                'monthInstallment' => 12,
                'startDateInstallment' => '2022-12-29'
            ]
        ];
    }

    private function addMonthToDate(DateTime $dateToInsert, string $paymentDay) : DateTime
    {
        if (checkdate($dateToInsert->format('m'), $paymentDay, $dateToInsert->format('Y')))
        {
            $paymentDayInsert = $paymentDay;
        }
        else
        {
            $paymentDayInsert = $dateToInsert->format('t');
        }

        $incrementedDate = new DateTime(
            "{$dateToInsert->format('Y')}-{$dateToInsert->format('m')}-{$paymentDayInsert}"
        );

        $currentDayInDate = $incrementedDate->format('j');
        $incrementedDate->modify('first day of + 1 month');

        $incrementedDate->modify('+' . (min($currentDayInDate, $incrementedDate->format('t')) - 1) . ' days');

        if (checkdate($incrementedDate->format('m'), $paymentDay, $incrementedDate->format('Y')))
        {
            $incrementedDate->setDate(
                $incrementedDate->format('Y'),
                $incrementedDate->format('m'),
                $paymentDay
            );
        }

        return $incrementedDate;
    }
}