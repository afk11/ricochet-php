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

    /**
     * @param array $fields
     * @param array $response
     * @return array
     */
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

    /**
     * @param HiddenServiceParams $params
     * @return array
     */
    public function createEphemeralHiddenService(HiddenServiceParams $params)
    {
        $command = $params->generateCommand();
        $res = $this->control->executeCommand($command);

        $fields = ['ServiceID'];
        if ($params->getKeyType() === 'NEW') {
            $fields[] = 'PrivateKey';
        }

        $parsed = $this->parseResponse($fields, $res);
        return $parsed;
    }

    /**
     * @param string $serviceId
     * @return array
     */
    public function deleteEphemeralHiddenService($serviceId)
    {
        $command = 'DEL_ONION '. $serviceId;
        $res = $this->control->executeCommand($command);
        return $res;
    }
}