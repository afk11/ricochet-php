<?php

namespace Ricochet\Tor\Control;


use Ricochet\Tor\HiddenServiceParams;
use TorControl\TorControl;

class TorService
{
    /**
     * @var TorControl
     */
    private $control;

    /**
     * TorService constructor.
     * @param TorControl $control
     */
    public function __construct(TorControl $control)
    {
        $this->control = $control;
    }

    public function parseResponse(array $fields, array $response)
    {
        $data = [];
        foreach ($response as $item) {
            if (strpos($item['message'], "=")) {
                list ($key, $value) = explode("=", $item['message']);
                $data[$key] = $value;
            }
        }

        foreach (array_keys($data) as $field) {
            if (!in_array($field, $fields)) {
                throw new \RuntimeException('Unexpected value');
            }
        }

        return $data;
    }


    public function createEphemeralHiddenService(HiddenServiceParams $params)
    {
        $command = $params->generateCommand();
        $res = $this->control->executeCommand($command);

        var_dump($res);
        $fields = ['ServiceID'];
        if ($params->getKeyType() === 'NEW') {
            $fields[] = 'PrivateKey';
        }

        $parsed = $this->parseResponse($fields, $res);
        return $parsed;
    }

    public function deleteOnion($serviceId)
    {
        $command = 'DEL_ONION '. $_SERVER;
        $res = $this->control->executeCommand($command);

    }
}