<?php

namespace Application\Service;

use Exception;

class SmsService
{
    public function sendSmsAsync(array $users, $message)
    {
        $processes = [];

        foreach ($users as $user) {
            $pid = pcntl_fork();

            if ($pid == -1) {
                throw new Exception("Não foi possível criar o processo.");
            } elseif ($pid) {
                $processes[] = $pid;
            } else {
                $this->sendSms($user, $message);
                exit(0);
            }
        }

        foreach ($processes as $process) {
            pcntl_waitpid($process, $status);
        }

        return true;
    }

    private function sendSms($user, $message)
    {
        sleep(2);
        echo "SMS enviado para $user: $message";
    }
}
