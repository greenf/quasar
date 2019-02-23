<?php namespace Greenf\Quasar\Objects;

class AggregateRoot extends AbstractObject {

    private $identityType;

    public function build()
    {
        $this->prepare();

        $this->class->setType('class');
        $this->addProperties();
        $this->addGetters();
        $this->addInit();

        return $this;
    }

    protected function prepare()
    {
        //dump('nn', $this->namespace->getName());
        //$this->identityType = (substr($this->config['identity']['type'], 0, 1) != '\\' ? $this->namespace->getName() . '\\' : '') . $this->config['identity']['type'];
        //
        foreach ($this->config['domain']['properties'] as $k => $property) {
            $this->config['domain']['properties'][$k]['type'] = (
                substr($property['type'], 0, 1) != '\\'
                    ? $this->namespace->getName() . '\\'
                    : ''
                ) . $property['type'];
        }
    }

    protected function addProperties()
    {
        foreach ($this->config['domain']['properties'] as $property) {

            $this->class->addProperty($property['name'])
                ->setVisibility('protected');
        }
    }

    protected function addGetters()
    {
        //$this->class->addMethod('id')
        //    ->setFinal()
        //    ->setVisibility('public')
        //    ->setReturnType($this->identityType)
        //    ->setBody('return $this->id;');

        foreach ($this->config['domain']['properties'] as $property) {
            $this->class->addMethod($property['name'])
                ->setFinal()
                ->setVisibility('public')
                ->setReturnType($property['type'])
                ->setBody(sprintf('return $this->%s;', $property['name']));
        }
    }


    protected function addInit()
    {
        //$method = $this->class->addMethod('initShares')
        //    ->setFinal()
        //    ->setVisibility('protected')
        //    ->setReturnType('void');
        //
        //
        //$method->addParameter('id')
        //    ->setTypeHint($this->identityType);
        //
        //foreach ($this->config['properties'] as $property) {
        //    $method->addParameter($property['name'])
        //        ->setTypeHint($property['type']);
        //}
        //
        //$method->setBody('$this->id = $id;' . PHP_EOL .
        //    implode(PHP_EOL, array_map(function($property) {
        //        return sprintf('$this->%s = $%s;', $property['name'], $property['name']);
        //    }, $this->config['properties']))
        //);


        //$this->identity = $id;
        //$this->fs1Id = $fs1Id;
        //$this->parcelId = $parcelId;
        //$this->createdAt = $createdAt;
    }
}