<?php

declare(strict_types=1);

namespace Southaxis\RegiCare;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Southaxis\RegiCare\Container\PluginContainer;
use function array_unique;
use function collect;
use function json_decode;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNotEmpty;
use function Southaxis\Helpers\mapRegicareFilters;
use function Southaxis\Helpers\service;

/**
 * @internal
 *
 * @coversNothing
 */
final class ActiviteitenTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetAllActivities(): void
    {
        assertNotEmpty($this->getService()->getAllActivities(['startDatumTot' => '2023-01-30']));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @noinspection PhpVoidFunctionResultUsedInspection
     */
    public function testActivityLocation(): void
    {
        $post = [
            'groeperingID'   => '',
            'groeperingText' => 'Selecteer een leeftijd...',
            'locatieID'      => 3,
            'locatieText'    => 'de Ark',
            'dagID'          => '',
            'dagText'        => 'Selecteer een dag...',
            'tagText'        => 'Archery',
            'action'         => 'showFilterActivities',
        ];

        $activities = $this->getService()->getAllActivities(mapRegicareFilters($post));

        assertCount(1, collect($activities)->map(static fn ($a) => $a->locatie)->keyBy('locatie')->toArray());
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @noinspection PhpVoidFunctionResultUsedInspection
     */
    public function testActivityDay(): void
    {
        $post = [
            'groeperingID'   => '',
            'groeperingText' => 'Selecteer een leeftijd...',
            'locatieID'      => '',
            'locatieText'    => 'Selecteer een locatie...',
            'dagID'          => '2',
            'dagText'        => 'Selecteer een dag...',
            'tagText'        => 'Archery',
            'action'         => 'showFilterActivities',
        ];

        $activities = $this->getService()->getAllActivities(mapRegicareFilters($post));

        assertCount(1, collect($activities)->map(static fn ($a) => $a->dag)->keyBy('dag')->toArray());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getService(): Activiteiten
    {
        return PluginContainer::getInstance()->get(Activiteiten::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testAuth(): void
    {
        $children = service(Auth::class)->profielPersoonGekoppeld();
        dd($children);
    }
}
