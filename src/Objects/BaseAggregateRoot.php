<?php namespace Greenf\Quasar\Objects;

class BaseAggregateRoot extends AbstractObject {

    private $identityType;

    private $sharedModelDefinition;

    public function build()
    {
        $this->prepare();

        $this->class->setType('class')
            ->setAbstract()
            ->setExtends($this->config['domain']['extends']);

        $this->addTraits();
        $this->addProperties();
        $this->addGetters();
        $this->addConstructor();

        return $this;
    }

    protected function prepare()
    {
        preg_match('/[^\\\]+$/', $this->config['domain']['sharedModel'], $data);

        $this->sharedModelDefinition = head(array_filter($this->config['shared']['models'], function ($model) use ($data) {
            return $data[0] == $model['name'];
        }));

        $this->identityType = implode('\\', array_merge($this->config['sharedNamespace'], [$this->sharedModelDefinition['identity']['type']]));

        foreach ($this->config['domain']['properties'] as $k => $property) {
            $this->config['domain']['properties'][$k]['type'] = $this->fullClassName($property['type'], false);
        }

        foreach ($this->config['domain']['traits'] as $k => $trait) {
            $this->config['domain']['traits'][$k]['class'] = $this->fullClassName($trait['class'], false);
        }
    }

    protected function addTraits()
    {
        if (isset($this->config['domain']['sharedModel'])) {
            $this->class->addTrait($this->fullClassName($this->config['domain']['sharedModel']));
        }

        foreach ($this->config['domain']['traits'] as $trait) {
            $this->class->addTrait($trait['class']);
        }
    }

    protected function addProperties()
    {
        foreach ($this->config['domain']['properties'] as $property) {

            $this->class->addProperty($property['name'])
                ->setVisibility('private');
        }
    }

    protected function addGetters()
    {
        foreach ($this->config['domain']['properties'] as $property) {
            $this->class->addMethod($property['name'])
                ->setFinal()
                ->setVisibility('public')
                ->setReturnType($property['type'])
                ->setBody(sprintf('return $this->%s;', $property['name']));
        }
    }

    protected function addConstructor()
    {

        //public function __construct(
        //    OrderId $id,
        //    OrderFs1Id $fs1Id,
        //    ParcelId $parcelId,
        //    OrderSpecification $specification,
        //    OrderCreatedAt $createdAt,
        //    DeletedAt $deletedAt
        //)
        //{
        //    $this->initShares($id, $fs1Id, $parcelId, $createdAt);
        //
        //    $this->deletedAt = $deletedAt;
        //    $this->specification = $specification;
        //}

        $method = $this->class->addMethod('__construct')
            ->setVisibility('public');


        $method->addParameter('id')
            ->setTypeHint($this->identityType);

        foreach ($this->sharedModelDefinition['properties'] as $property) {
            $method->addParameter($property['name'])
                ->setTypeHint($this->fullClassName($property['type'], false));
        }

        foreach ($this->config['domain']['properties'] as $property) {
            $method->addParameter($property['name'])
                ->setTypeHint($this->fullClassName($property['type'], false));
        }

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